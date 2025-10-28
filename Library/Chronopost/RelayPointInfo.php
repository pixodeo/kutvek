<?php
declare(strict_types=1);
namespace Library\Chronopost;
use Core\Component;
use Exception;
use SimpleXMLElement;
use SoapClient;

class RelayPointInfo extends Component {

	public function __invoke(string $id): SimpleXMLElement|Exception
	{
		try {
			$service = new SoapClient("https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS?wsdl");
			$post_data = <<<SOAP
			<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cxf="http://cxf.rechercheBt.soap.chronopost.fr/">
			<soapenv:Header/>
			<soapenv:Body>
			<cxf:rechercheDetailPointChronopostInter>
			<!--Optional:-->
			<accountNumber>12256703</accountNumber>
			<!--Optional:-->
			<password>807807</password>
			<identifiant>{$id}</identifiant>			
			</cxf:rechercheDetailPointChronopostInter>
			</soapenv:Body>
			</soapenv:Envelope>
			SOAP;

			//$res = $service->__doRequest($post_data,'https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS','rechercheDetailPointChronopostInter',2);
			$request = $service->__doRequest($post_data,'https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS','rechercheDetailPointChronopostInter',2);
			
			$search = [
				'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">',
				'<soap:Body>',
				'<ns2:rechercheDetailPointChronopostInterResponse xmlns:ns2="http://cxf.rechercheBt.soap.chronopost.fr/">',
				'</ns2:rechercheDetailPointChronopostInterResponse>',
				'</soap:Body>',
				'</soap:Envelope>'
			];
			$replace = ['','','','','',''];
			$request = str_replace($search,$replace, $request);
			$xml = simplexml_load_string($request);
			$relay = $xml->listePointRelais;			
			return $relay;		
		}
		catch(Exception $e){
			throw $e;
		}
	}
}