<?php
namespace Core\Library;
use \App;

trait TraitModel {
/**
	 * Charge un modele \ table dans les controllers et components
	 *
	 * @param [type] $model_name
	 * @return void
	 */
	public function loadModel($model_name) {
		// Infos : un alias pratique permet d'instancier plusieurs fois un même model / table avec une base différente
		if ( (array) $model_name !== $model_name ) {    
		  if(is_object($model_name)) { // si on a un objet  
		  	// Si on a un alias
		  	if(isset($model_name->alias)) {
					$this->{$model_name->alias} = App::getInstance()->getTable($model_name);

				} else {
					$this->{$model_name->table} = App::getInstance()->getTable($model_name); 
				}
			
		  } else { 
			$this->$model_name = App::getInstance()->getTable($model_name); 
		  } 
		// $model_name est un tableau     
		} else {    
		  foreach ($model_name as $v) {
			if(is_object($v)) {
				// Si on a un alias	
				if(isset($v->alias)) {
					$this->{$v->alias} = App::getInstance()->getTable($v);

				} else {
					$this->{$v->table} = App::getInstance()->getTable($v); 
				}
			  
			} 
			else { $this->$v = App::getInstance()->getTable($v); }
		  }
		}
		}	

		protected function _setDb($dbConf = null){
			$app = App::getInstance();
			return $app->setDb($dbConf);           
    }	
	}