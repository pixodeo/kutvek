<?php
declare(strict_types=1);
namespace App\SaddleCover;
use App\AppAction;

use Domain\Table\SaddleCover;
use Exception;
use Library\TraitPagination;

final class RefreshFilter extends AppAction {
	use TraitPagination;
	protected SaddleCover $_table;	
	protected $category;

	public function __invoke(){		
		$l10n = new \stdClass();
		$l10n->id = $this->getL10nId();
		$l10n->code = $this->getL10n();
		$queries = $this->getRequest()->getQueryParams();
		$slug = isset($queries['slug']) ? $queries['slug'] : '';
		$this->_response = $this->_response->withHeader('Content-Type', 'application/json;charset=utf-8');		
		try{
			$this->_table = new SaddleCover($this->_setDb());
			$this->_table->setL10n($l10n);
			$this->_table->setCountryCode($this->_country);
			$this->_table->setCurrencyCode($this->_currency);
			$this->_table->setCurrencyId($this->_currencyId);	
			$this->_table->setRequest($this->getRequest());				
			$this->category = $this->_table->store($slug);	
			$vehicles = $this->vehicles();
			$this->_path = dirname(__FILE__);
			$this->_view = 'vehicles-list';
			$selected = array_key_exists('vehicles', $queries) ? explode(',', $queries['vehicles']) : [];
			$partial = $this->partial(compact('vehicles', 'selected'));		
			$data = ['brands' =>  array_column($this->brands(), 'brand_id'), 'vehicles' => $partial];		
			$json = json_encode($data, JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES);
			$this->_response->getBody()->write($json);
			return $this->_response;
		}
		catch(Exception $e){			
			$this->_response = $this->_response->withStatus('400');
			$this->_response->getBody()->write($e->getMessage());
			return $this->_response;
		}		
	}

	public function brands(){

		$queries = $this->getRequest()->getQueryParams();
		$params = ['l10n' => $this->getL10nId()];
		$sql = "SELECT
			v.brand AS 'brand_id',
			v.brand_name
			FROM `item_vehicles` i_v
			JOIN saddle_covers s_c ON s_c.id = i_v.item
			JOIN vue_vehicle_2 v ON (v.id = i_v.vehicle AND v.l10n = :l10n)
			WHERE 1 = 1			
		";
		foreach($queries as $k => $values)
        {
            switch($k){
                case 'universes':
                	// filtre des marques inutiles	   
                	$universes = explode(',', $values); 
                	$plh = $this->_table->namedPlaceHolder($universes, $k);
                	$params = array_merge($params, $plh->values);
                	$sql .= " AND v.universe IN({$plh->place_holder})";               	
                    break;	                
                /*case 'brands':
                    $brands = explode(',', $values);                   
                    $plh = $this->namedPlaceHolder($brands, $k);
                    $params = array_merge($params, $plh->values);
                    $sql .= " AND v.brand IN({$plh->place_holder})";  
                    break;*/
            }                              
        }
		$sql .= " GROUP BY v.brand ORDER BY v.brand_name;";
		$brands = $this->_table->query($sql,$params);		
		return $brands;
	}

	public function vehicles(){
		$queries = $this->getRequest()->getQueryParams();
		$params = ['l10n' => $this->getL10nId()];
		$sql = "SELECT
			v.id AS 'vehicle_id',
			v.name AS 'vehicle_name'
			FROM `item_vehicles` i_v
			JOIN saddle_covers s_c ON s_c.id = i_v.item
			JOIN vue_vehicle_2 v ON (v.id = i_v.vehicle AND v.l10n = :l10n)
			WHERE 1 = 1			
		";
		foreach($queries as $k => $values)
        {
            switch($k){
                case 'universes':
                	// filtre des marques inutiles	   
                	$universes = explode(',', $values); 
                	$plh = $this->_table->namedPlaceHolder($universes, $k);
                	$params = array_merge($params, $plh->values);
                	$sql .= " AND v.universe IN({$plh->place_holder})";  
                	break;                 	                
                case 'brands':
                    $brands = explode(',', $values);                   
                    $plh = $this->_table->namedPlaceHolder($brands, $k);
                    $params = array_merge($params, $plh->values);
                    $sql .= " AND v.brand IN({$plh->place_holder})";  
                    break;
            }                              
        }
		$sql .= " GROUP BY v.id ORDER BY v.position, v.name;";
		$vehicles = $this->_table->query($sql,$params);		
		return $vehicles;
	}

	public function colours(){
		$colours = [];		
		return $colours;
	}
}