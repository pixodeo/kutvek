<?php
declare(strict_types=1);
namespace App\Sportswear\TypeOfProduct;

class Context {
	public function __construct(private Strategy $_strategy){}

    public function setStrategy(Strategy $_strategy){
    	$this->_strategy = $_strategy;
    }    

    public function getName(){
        return $this->_strategy->getName();
    }

    public function getView():string{
        return $this->_strategy->getView();
    }

    public function  product(int $id){
        return $this->_strategy->__invoke($id);
    }

    public function vehicles():array{
        return $this->_strategy->vehicles();
    }

    public function suitableFor():string{
        return $this->_strategy->suitableFor();
    } 

    public function colours():string {
         return $this->_strategy->colours();
    } 

    public function getSizes():string {
        return $this->_strategy->getSizes();
    }  
}