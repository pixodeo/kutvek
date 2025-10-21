<?php 

namespace Core\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Core\Controller\Controller;

/**
 * Adapter les controllers pour qu'ils puissent faire office de middlewares
 */
class ControllerAdapter implements MiddlewareInterface
{
		
	public Controller $_controller;

	/**
	 *  tableau des paramètres retourné par la route,
	 *  correspondance dans l'url 
	 * @var array
	 */	
	private $_matches = [];

	public function __construct(Controller $controller)
	{
		$this->_controller = $controller;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface 
	{			
		

		// Il faut pouvoir passer des paramètres aux controllers
		// faut-il déporter ici le travail de la route ? 
		$reflexion = new \ReflectionClass($this->_controller);
        $method = $reflexion->getMethod('__invoke'); 
        $parameters = $method->getParameters();

        // il faut envoyer les paramètres attendus les matchs de la route
        $this->setRequestParam($parameters);  

		$content = $method->invokeArgs($this->_controller, $this->_matches);
					
		$response = $handler->handle($request); 
		$response->getBody()->write($content);    
		return $response; 
	}

	public function setMatches(array $matches): void
	{
		$this->_matches = $matches;
	}

	 /**
     * On vérifie que les paramatres requis pour la methode appelée sur un controller existent 
     * dans la REQUEST
     *
     * @param array $parameters
     * @return void
     */
    private function setRequestParam(array $parameters): void
    {
         /* 
                On a plusieurs paramètres
                chaque parametre a une propriete name
                [0] => ReflectionParameter Object
                (
                    [name] => universe
                )
                [1] => ReflectionParameter Object
                (
                    [name] => brand
                )
        */
               
        $requestParams = $this->_buildParams();
        
        if(count($parameters) > 0)
        {            
            foreach($parameters as $k => $parameter)
            {
                // !! Attention de bien vérifier que ces paramètres sont envoyés via REQUEST et non pas dans l'url sinon ça les écrase !!
                if(!array_key_exists($parameter->getName(), $this->_matches)) {

                    // si null autorisé et pas de correspondance dans la request, ou  null 
                    if(($parameter->allowsNull() && !array_key_exists($parameter->getName(), $requestParams)) || ($parameter->allowsNull() && !$requestParams[$parameter->getName()]))
                        $this->_matches[$parameter->getName()] = null;
                    // si la clé existe
                    if(array_key_exists($parameter->getName(), $requestParams) && $requestParams[$parameter->getName()])
                        $this->_matches[$parameter->getName()] = $requestParams[$parameter->getName()];
                } 
            }
        } 
    }

	/**
     * Renvoie la data reçue selon les différentes méthodes
     * $_SERVER['REQUEST_METHOD']
     * pour DELETE on a rien en REQUEST ou php://input,
     * le parametre est dans l'url
     * @return array parameters
     */
    
    private function _buildParams(): array {
        
        if($_SERVER['REQUEST_METHOD'] == 'PUT'):
            $_PUT = file_get_contents('php://input');
            // En cas de parametres ds l'url
            return array_merge($_REQUEST, json_decode($_PUT, true));
        elseif($_SERVER['REQUEST_METHOD'] == 'POST'):
            return $_REQUEST;
        elseif($_SERVER['REQUEST_METHOD'] == 'GET'):
            return $_REQUEST;
        elseif($_SERVER['REQUEST_METHOD'] == 'COOKIE'):
            return $_REQUEST;
        else:
            return $_REQUEST;
        endif;
    }
}