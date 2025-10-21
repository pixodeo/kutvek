<?php
declare(strict_types=1);
namespace Domain\Decorator;
use \App;
use Core\Library\{TraitCookie,TraitResponse,TraitL10n};
use Core\Routing\{RouteInterface, RouterInterface, TraitRequest};

abstract class Decorator implements Component
{ 
    use TraitCookie, TraitResponse, TraitL10n, TraitRequest; 

    /**
     * Constructs a new instance.
     *
     * @param      protected Component  $component  The component
     */
    public function __construct(protected Component $component, protected RouteInterface $_route){}

    protected function _setDb($dbConf = null) {
            $app = App::getInstance();
            return $app->setDb($dbConf);           
    }

    public function setRouter(RouterInterface $router){ $this->_router = $router;}    

    /**
     * Récupérer toutes les infos nécessaires pour construire le titre en fonction du decorateur (kit déco, housse, sportwear, accessoire...)
     *
     * @return     string  ( description_of_the_return_value )
     */
    abstract public function title(): string;
    abstract public function slug():string;

    public function getId():int {return (int)$this->component->getId();}
    public function hasContent():bool {return $this->component->hasContent() !== null;}
}
