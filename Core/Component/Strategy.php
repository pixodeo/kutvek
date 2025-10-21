<?php
namespace Core\Component;

use Core\Interfaces\ComponentStrategyInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Strategy implements ComponentStrategyInterface 
{ 
	protected $_request;
	

	use \App\Library\TraitModel, \Core\Library\TraitCore, TraitView; 

	
	
	public function setRequest(ServerRequestInterface $request)
    {
        $this->_request = $request;
    }

    public function getRequest(): ServerRequestInterface {
    	return $this->_router->getRequest();
    }

  



    /**
     * @return mixed
     */
    public function getWorkspace()
    {
        return $this->_workspace;
    }

    /**
     * @param mixed $_workspace
     *
     * @return self
     */
    public function setWorkspace($_workspace)
    {
        $this->_workspace = $_workspace;

        return $this;
    }
}