<?php
declare(strict_types=1);
namespace App\Product;

use Core\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Context {
	public function __construct(private Department $_strategy){}

    public function __invoke(int $id){
        return $this->_strategy->__invoke($id);
    }

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

    public function departments(int $category_id): array {
        return $this->_strategy->departments($category_id);
    }

    public function setRequest(ServerRequestInterface $request){
        $this->_strategy->setRequest($request);
    } 
    public function setResponse(ResponseInterface $response){
        $this->_strategy->setResponse($response);
    } 

    public function setRouter(RouterInterface $router){
        $this->_strategy->setRouter($router);
    }
}