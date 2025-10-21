<?php
declare(strict_types=1);
namespace App\Checkout;

use Core\Action;

class MapboxPoints extends Action {
	public function __invoke()
	{		
		try {
			$queries = (object)$this->getRequest()->getQueryParams();
			$country_code = $queries->countryCode;
			$productCode = $queries->countryCode === 'FR' ? 86 : 49;
			$zipcode = $queries->zipcode;
			$address = $queries->address ?? '';
			$city = $queries->city;
			$date = \date('d/m/Y');
			
			$service = new \SoapClient("https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS?wsdl");
			$post_data = <<<SOAP
			<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cxf="http://cxf.rechercheBt.soap.chronopost.fr/">
			<soapenv:Header/>
			<soapenv:Body>
			<cxf:recherchePointChronopostInter>
			<!--Optional:-->
			<accountNumber>12256703</accountNumber>
			<!--Optional:-->
			<password>807807</password>
			<!--Optional:-->
			<address>{$address}</address>
			<!--Optional:-->
			<zipCode>{$zipcode}</zipCode>
			<!--Optional:-->
			<city>{$city}</city>
			<!--Optional:-->
			<countryCode>{$country_code}</countryCode>
			<!--Optional:-->
			<type>P</type>
			<!--Optional:-->
			<productCode>{$productCode}</productCode>
			<!--Optional:-->
			<service>D</service>
			<!--Optional:-->
			<weight>?</weight>
			<!--Optional:-->
			<shippingDate>{$date}</shippingDate>
			<!--Optional:-->
			<maxPointChronopost>20</maxPointChronopost>
			<!--Optional:-->
			<maxDistanceSearch>10</maxDistanceSearch>
			<!--Optional:-->
			<holidayTolerant>1</holidayTolerant>
			<!--Optional:-->
			<language>FR</language>
			<!--Optional:-->
			<version>2.0</version>
			</cxf:recherchePointChronopostInter>
			</soapenv:Body>
			</soapenv:Envelope>
			SOAP;
			$lst_prod = $service->__doRequest($post_data,'https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS','recherchePointChronopostInter',2);
			//return $lst_prod;
			$search = [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">',
				'<soap:Body>',
				'<ns2:recherchePointChronopostInterResponse xmlns:ns2="http://cxf.rechercheBt.soap.chronopost.fr/">',
				'</ns2:recherchePointChronopostInterResponse>',
				'</soap:Body>',
				'</soap:Envelope>'
			];
			$replace = ['','','','','',''];
			$return = str_replace($search,$replace, $lst_prod);

			$xml = simplexml_load_string($return);
			$points = json_decode(json_encode($xml));
			$relays = [];
			/**
			 * {
				"type": "Feature",
				"geometry": {
					"type": "Point",
					"coordinates": [4.854234, 45.845342]
				},
				"properties": {
					"name": "A vos ongles", 
					"phoneFormatted": "(202) 234-7336",
					"phone": "2022347336",
					"address": "1 Place De L'église",
					"city": "Fontaines-Saint-Martin",
					"country": "France",             
					"postalCode": "69270",
					"state": "D.C."
				}
			},
			 */
		foreach($points->listePointRelais as $relai)
		{
			if((bool)$relai->actif !== true) continue;
			$push = (object) [
				'type'  => 'Feature',
				'geometry'  => (object)[
					'type'          => 'Point',
					'coordinates'   => [$relai->coordGeolocalisationLongitude, $relai->coordGeolocalisationLatitude]
				],
				'properties'    => (object)[
					'id'            => $relai->identifiant,
					'name'          => $relai->nom,
					'address_line_1'     => !is_object($relai->adresse1) ? $relai->adresse1 : null,
					'address_line_2'     => !is_object($relai->adresse2)   ? $relai->adresse2 : null,
					'address_line_3'     => !is_object($relai->adresse3)   ? $relai->adresse3 : null,
					'city'          => $relai->localite,
					'postalCode'    => $relai->codePostal,
					'countryCode'   => $relai->codePays,
					'type'          => $relai->typeDePoint,
					'op'            => $relai->listeHoraireOuverture                        
				]                
			];

			$txtHoraire = '';
			foreach(array_reverse($relai->listeHoraireOuverture) as $horaire){        
				$jour='';
				if($horaire->jour==1) $jour = "<b>Lundi</b> ";
				if($horaire->jour==2) $jour = "<b>Mardi</b> ";
				if($horaire->jour==3) $jour = "<b>Mercredi</b> ";
				if($horaire->jour==4) $jour = "<b>Jeudi</b> ";
				if($horaire->jour==5) $jour = "<b>Vendredi</b> ";
				if($horaire->jour==6) $jour = "<b>Samedi</b> ";
				if($horaire->jour==7) $jour = "<b>Dimanche</b> ";
				//if($horaire->jour>0 && $horaire->jour<7) $txtHoraire.="<br />".$jour." ".$horaire->horairesAsString;
				$txtHoraire.= $jour." ".$horaire->horairesAsString."<br />";
			}
			$push->opening = $txtHoraire;
			$relays[] = $push;
		}    
		$json = json_encode(["type" => "FeatureCollection","features"  => $relays]); 
		$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');
		$this->_response->getBody()->write($json);
		return $this->_response;	
	}
	catch(\Exception $e){
		$this->_response = $this->_response->withStatus(400); 
		$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8'); 
		$body = json_encode(['msg' => $e->getMessage()]);			
		$this->_response->getBody()->write($body);
		return $this->_response;
	}	
}
}