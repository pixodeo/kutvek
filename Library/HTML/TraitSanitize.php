<?php
declare(strict_types=1);
namespace Library\HTML;

trait TraitSanitize {
	public function sanitizeSpecialChars(?string $string = null, bool $double_encode = true): ?string
	{
		if($string === null) return '';
		return !empty(trim($string)) ? htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, null , $double_encode) : null;
	}

	public function sanitizeEntities(?string $string = null, bool $double_encode = true): ?string
	{
		if($string === null) return '';
		return !empty(trim($string)) ? htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, null, $double_encode) : null;
	}

	public function entities_decode(?string $string = null): ?string
	{
		if($string === null) return '';
		return !empty(trim($string)) ? html_entity_decode($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5) : null;
	}

	public function specialchars_decode(?string $string = null): ?string
	{	
		if($string === null) return '';
		return !empty(trim($string)) ? htmlspecialchars_decode($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5) : null;
	}
	/**
	 * on prend le titre qu'on "slugifie" à savoir tout mettre en minuscule, virer tous les espace et caractères spéciaux qu'on remplace par des tirets
	 */
	public function slugify(?string $slug = null):?string {
		if($slug === null) return null;
		$translit = \Transliterator::createFromRules("::Latin-ASCII; ::Lower; [^[:L:][:N:]]+ > '-';");
        $slug = trim($translit->transliterate($slug), '-');  
        return $slug;
	}
}