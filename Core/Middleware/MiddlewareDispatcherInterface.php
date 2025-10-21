<?php

namespace Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface MiddlewareDispatcherInterface
{
    /**
     * Un middleware est une méthode / action du controller / un composant
     * chargé de faire qqchose (des vérifs, des modifs...)
     * sur la requête avant l'appel à méthode demandée dans la route
     * Ex: un composant d'authentification / gestion des droits utilisateur     * 
     * 
     * @param  callable | MiddlewareInterface $middleware
     * @return [type]             [description]
     */
    public function pipe($middleware);


    public function handle(ServerRequestInterface $request): ResponseInterface;

    
}