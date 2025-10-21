<?php

namespace Core\Routing;

interface RouterInterface
{
    public function get($path, $callable, $name = null);       

    public function post($path, $callable, $name = null);

    public function put($path, $callable, $name = null);

    

    public function delete($path, $callable, $name = null);        

    public function url(string $name, array $params = [], string $method = 'GET'): string;   
    public function uri(string $name, $params = [], string $method = 'GET'): string;
    public function uri_2(string $name, $params = [], string $method = 'GET'): string;	

 	public function getRoutes(): array;
    public function getRoute():RouteInterface; 	    
}