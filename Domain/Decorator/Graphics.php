<?php
declare(strict_types=1);
namespace Domain\Decorator;

use Core\Routing\RouteInterface;
use Domain\Entity\Cards\Graphic as CardGraphic;
use Domain\Table\Graphic;

class Graphics extends Decorator
{
    private false|Graphic $_table = false;
    private CardGraphic $_data;

    public function __construct(protected Component $component, protected RouteInterface $_route)
    {
        if(!$this->_table) {
            $this->_table = new Graphic($this->_setDb(), null);
            $this->_table->setRoute($_route);
            $this->_table->unsetConstructorArgs();

        }
        $id = $this->getId();
        $this->_data = $this->_table->cardData($id);
    }

    public function slug(): string { 
        $url = $this->_data->url; 

        if($this->hasContent()):
            $sql = "UPDATE item_l10ns SET item_slug = :url WHERE item = :item_id AND l10n = :l10n_id;";
            $this->_table->query($sql,['url'=>$this->_data->item_slug,'item_id'=>$this->getId(), 'l10n_id'=>$this->getL10nId()]);
        //else: 

        endif;      
        return $url;
    }

    /**
     * Récupérer toutes les infos nécessaires pour construire le titre 
     *
     * @return     string  ( description_of_the_return_value )
     */
    public function title(): string {
        $title = $this->_data->title;
        if($this->hasContent()):
            $sql = "UPDATE item_l10ns SET full_designation = :des WHERE item = :item_id AND l10n = :l10n_id;";
            $this->_table->query($sql,['des'=>$title,'item_id'=>$this->getId(), 'l10n_id'=>$this->getL10nId()]);
        //else: 

        endif;      
        return $title;
    }
    
}