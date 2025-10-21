<?php
declare(strict_types=1);
namespace Library\HTML;

trait TraitView {
	public null|string $meta_title = null;
	public null|string $meta_description = null;
	public array $l10ns = [];
	

	public function meta_title():string
	{

		return $this->meta_title !== null ? "<title>{$this->meta_title}</title>" : "";
	}


	public function getTitle(){return $this->meta_title;}

	public function getDescription() { return $this->meta_description;}

	public function meta_description():string
	{

		return $this->meta_description !== null ? "<meta name=\"description\" content=\"$this->meta_description\" />" : "";
	}

	/**
	 * Liens vers les différentes traduction de la page
	 *
	 * @param      array   $slugs  les urls
	 *
	 * @return     string  la liste ul
	 */
	public function l10ns(array $slugs = []):string
	{	
		if(!empty($slugs)) $this->l10ns = $slugs;
		$items = array();
		foreach($this->l10ns as $slug)
		{
			$item = '<li class="'. $slug->class .'">';					
			$item .= '<a href="' .  URL_SITE . $slug->slug . '" class="trad-link" data-l10n="'. $slug->l10n .'">';			
			$item .= '<span class="short-desc">' . $slug->_locale . '</span>';			
			$item .= '</a>';
			$item .= '</li>';
			$items[] = $item;
		}
		$ul = '<ul data-l10n="' . $this->getL10nId() . '">';		
		$ul .= implode('', $items);
		$ul .= '</ul>';		
		return $ul;
	}

	public function fetch($type){
        $str='';
        foreach ($this->$type ?? [] as $v) {
            $str.= "$v\n";
        }
       return $str;
    }
}