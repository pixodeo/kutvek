<?php
declare(strict_types=1);
namespace Domain\Decorator;

class Basics extends Decorator
{
    public function title(): string { return 'srt ' . $this->getId();}
    public function slug(): string { 
        $url = $this->_data->url; 

        if($this->hasContent()):
            //$sql = "UPDATE item_l10ns SET item_slug = :url WHERE item = :item_id AND l10n = :l10n_id;";
            //$this->_table->query($sql,['url'=>$this->_data->item_slug,'item_id'=>$this->getId(), 'l10n_id'=>$this->getL10nId()]);
        //else: 

        endif;
        return '#';      
        return $url;
    }
}