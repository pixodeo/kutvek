<?php
namespace Core\Routing;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteInterface extends RequestHandlerInterface
{
    
    public function call();

    public function getMiddlewares(): array;

    public function getMatches(): array;

    public function getL10n():\stdClass;

    public function getRequest():ServerRequestInterface;

    public function __get(string $match):mixed;
    
}