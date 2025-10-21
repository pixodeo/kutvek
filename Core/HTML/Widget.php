<?php

namespace Core\HTML;

/**
 * Class Widget
 * Permet de mettre en forme différents éléménts tel l'option nom + numéro d'une commande
 */

 class Widget
 {
    /**
     *  
     *
     * @param object $plate  stdClass Object
     * (
     *   [name] => Sébastien
     *   [color] => blanc
     *   [name_typo] => https://www.kutvek-kitgraphik.com/img/typo/name3.png
     *   [number] => 121
     *   [number_color] => carbon
     *   [number_typo] => https://www.kutvek-kitgraphik.com/img/typo/num3.png
     * )
     * @return string
     */

    private $_extensions = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'JPG', 'PNG', 'JPEG', 'GIF', 'SVG'];


    public function widgetNumberFont($selectname, $placeholder = 'Typo du numéro')
    {
        $dir =  WEBROOT. DS . 'pictures' . DS . 'typo' .DS;
        $web_path = URL_SITE .'pictures' . DS . 'typo' .DS;
        $widget = '<div class="field-input select number" tabindex="0">';
        $widget .= '<span class="value">'. $placeholder .'</span>';
        $widget .= '<ul class="optList hidden">'; 
        $imgs = array();
        $found = glob($dir . "num*.png", GLOB_BRACE);
        $widget .= '<li class="option" data-value="">' . $placeholder . '</li>';
        foreach($found as $value)
        {
            $name = basename($value);
            $widget .= '<li class="option" data-value="'. $web_path . $name . '">';
            $widget .= ' <img src="' . $web_path . $name . '" />';
            $widget .= '</li>';            	
        }
        $widget .= '</ul>';
        $widget .='</div>';
        $widget .= '<select name="'. $selectname .'" >';
        $widget .= '<option value="">' . $placeholder . '</option>';
        foreach ($found as $value):
            $name = basename($value);
			$widget .= '<option value="'. $web_path . $name . '">'. $name .'</option>';		
		endforeach;			
        $widget .= '</select>';
        return $widget;
    }
    public function widgetLogo($selectname, $placeholder = 'Logo')
    {
        $dir =  WEBROOT. DS . 'pictures' . DS . 'plate-logo' .DS;
        $web_path = URL_SITE . 'pictures' . DS . 'plate-logo' .DS;
        $widget = '<div class="field-input select number" tabindex="0">';
        $widget .= '<span class="value">'. $placeholder .'</span>';
        $widget .= '<ul class="optList hidden">'; 
        $imgs = array();
        $found = glob($dir . "*.png", GLOB_BRACE);
        $widget .= '<li class="option" data-value="">' . $placeholder . '</li>';
        foreach($found as $value)
        {
            $name = basename($value);
            $widget .= '<li class="option" data-value="'. $web_path . $name . '">';
            $widget .= ' <img src="' . $web_path . $name . '" />';
            $widget .= '</li>';            	
        }
        $widget .= '</ul>';
        $widget .='</div>';
        $widget .= '<select name="'. $selectname .'">';
        $widget .= '<option value="">' . $placeholder . '</option>';
        foreach ($found as $value):
            $name = basename($value);
			$widget .= '<option value="'. $web_path . $name . '">'. $name .'</option>';		
		endforeach;			
        $widget .= '</select>';
        return $widget;
    }

    public function widgetNameFont($selectname, $placeholder= 'Typo du nom')
    {
        $dir =  WEBROOT. DS . 'pictures' . DS . 'typo' .DS;
        $web_path = URL_SITE .'pictures' . DS . 'typo' .DS;
        $widget = '<div class="field-input select number" tabindex="0">';
        $widget .= '<span class="value">'. $placeholder .'</span>';
        $widget .= '<ul class="optList hidden">'; 
        $widget .= '<li class="option" data-value="">' . $placeholder . '</li>';
        $found = glob($dir . "name*.png", GLOB_BRACE);       
        foreach($found as $value)
        {
            $name = basename($value);
            $widget .= '<li class="option" data-value="'. $web_path . $name . '">';
            $widget .= ' <img src="' . $web_path . $name . '" />';
            $widget .= '</li>';            	
        }
        $widget .= '</ul>';
        $widget .='</div>';
        $widget .= '<select name="'. $selectname .'">';
        $widget .= '<option value="">' . $placeholder . '</option>';
        foreach ($found as $value):
            $name = basename($value);
			$widget .= '<option value="'. $web_path . $name . '">'. $name .'</option>';		
		endforeach;			
        $widget .= '</select>';
        return $widget;
    }

    public function widgetColor($colors, $name, $placeholder = 'Selection couleur')
    {
        $widget = '<div class="field-input select" tabindex="0">';
        $widget .= '<span class="value">'. $placeholder .'</span>';
        $widget .= '<ul class="optList hidden">';          
        $widget .= '<li class="option color" data-value="">'.$placeholder.'</li>';       
        foreach ($colors as $color): $color = (object) $color;
            $widget .= '<li class="option color" data-value="'. $color->value . '" style="'. $color->style .'">'. $color->text .'</li>';                
        endforeach;				
        $widget .= '</ul>';
        $widget .='</div>';
        $widget .= '<select name="'. $name .'">';
        $widget .= '<option value="">'. $placeholder . '</option>';
		foreach ($colors as $color): $color = (object) $color;
			$widget .= '<option value="'. $color->value. '">'. $color->text .'</option>';		
		endforeach;			
        $widget .= '</select>';
        return $widget;
    }

    public function widgetFinish($data, $form = null, string $prefix = '')
    {
        $parent = array_pop($data);
        $premiums = array();
        $widget = '';       
        $widget .= '<select name="kit[finish]" id="' . $prefix. 'kit-finish" class="field-input onchange" data-ctrl="option.finish" required ';

        if($form !== null)
            $widget .= 'form="'.$form.'" >';
        else
        $widget .= '>';
        if(isset($parent->placeholder))
            $widget .= "<option value=\"\">{$parent->placeholder}</option>";
        foreach($data as $option)
        {
            if($option->is_premium == 1) {
                $premiums[] = $option;
                continue;
            }
            $widget .= '<option value="' . $option->value . '" data-price="' . $option->option_price . '" data-desc="' .$option->name. '" >';
            $widget .= $option->name;
            if($option->option_price > 0)
            {
                $formated = $this->getIntlPrice($option->trad, $option->option_price, $option->currency_lib);  
                $widget .= ' +' . $formated;
            }
            $widget .= '</option>';
        }
        $widget .= '</select>';
        $widget .= '</div>';
        // Options premium
        $widget .= '<div class="area finish-area">';
       
        $widget .= '<div class="header"><h4> OPTION CHROMES </h4></div>';

        $p = $premiums;

        $widget .= '<div class="premium" data-price="0">';
        foreach($p as $premium)
        {            
            $formated = $this->getIntlPrice($premium->trad, $premium->option_price, $premium->currency_lib);  
            if($premium->option_price > 0)
            {
                $widget .= '<p><input type="radio" class="onchange" name="kit[premium]" id="' . $prefix. 'premium-' . $premium->option_id . '"  value="' .$premium->option_id. '" data-price="' . $premium->option_price . '" data-ctrl="option.premium" form="'.$form.'" />'; 
                $widget .= '<label class="accessory-label" for="' . $prefix. 'premium-' . $premium->option_id . '">' .  $premium->name . '<span>' . $formated . '</span></label></p>';
            }
                
            else
            {
                $widget .= '<p><input type="radio" class="onchange" name="kit[premium]" id="' . $prefix. 'premium-' . $premium->option_id . '"  value="' .$premium->option_id. '" data-price="' . $premium->option_price . '" data-ctrl="option.premium" form="'.$form.'" checked />'; 
                $widget .= '<label class="accessory-label" for="' . $prefix. 'premium-' . $premium->option_id . '">' .  $premium->name . '</label></p>';
            }               
        }
        $widget .= '</div>';
        return $widget;

    }
    public function getIntlPrice($trad = 'fr', $price = 0.00, $currency = 'EUR') {
        $a = new \NumberFormatter($trad, \NumberFormatter::CURRENCY);
        return $a->formatCurrency($price, $currency); // outputs €12.345,12
    }

    public function widgetCustomSponsors(?object $sponsor, $sponsors = 12, $img = 'https://www.kutvek-kitgraphik.com/images/charte/gabarits-motocross.png')
    {        
        $widget  = '<p class=template><img id="tpl-sponsors" src="' . $img . '" /></p>';
        $widget .= '<div class="sponsors">';
        $widget .= $this->getSponsors($sponsors, $sponsor->prefix);
        $widget .= '</div>';          
        $widget .= '<div id="sponsor-preview" class="preview"></div>';
        //$widget .= '<p class="supported-extensions">';
        //$widget .= $sponsor->supported_files;
        //$widget .= '</p>';
        //$widget .= '<p class="sponsors-adjust">' . $sponsor->info . '</p>';       
        return $widget;
    }

    public function getSponsors($nb = 12, string $prefix)
    {
        $sponsors = '';
        for ($i=1; $i <= $nb; $i++) { 
            $sponsors .= '<p class="sponsor">';
            $sponsors .= '<span class="place">'. $i .'</span>';
            $sponsors .= '<input type="text" class="field-input" name="options[sponsor]['. $i .'][text]"  />';
            $sponsors .= '<input type="hidden"  name="options[sponsor]['. $i .'][place]"  value="' . $i . '"/>';
            $sponsors .= '<input type="file" name="sponsor['. $i .']" id="' . $prefix. 'sponsor-' . $i. '"  class="onchange" data-preview="sponsor-preview" data-ctrl="app.thumb" />';
            $sponsors .= '<label class="field-input" for="' . $prefix. 'sponsor-' . $i. '" ><i class="icon fas fa-file-download"></i></label>';
            $sponsors .= '</p>';
        }
        return $sponsors;
    }

    /**
     * Créé un formulaire de validation housse de selle
     *
     * @param integer $id
     * @return void
     */
    public function widgetSeatCover(int $id)
    {
        $widget = '<div class="widget widget-seat-cover"> ';
        $widget .= '<div class="header">';
        $widget .= '<h4> Validation housse de selle <span class="switch-container check mt-s-0">  
        <input id="check-seat-cover" name="validate[seatcover]" type="checkbox" class="required" value="1" disabled form="printing" >
        <label for="check-accessories"></label></span></h4>';
        $widget .= '</div>';       
        $widget .= '<form action="/orderItems/'. $id .'" method="PUT" class="submit" data-ctrl="orderItem.update">';
        
        $widget .= '<p><select class="field-input select" name="webshop_price" required >';
        $widget  .= '<option value="">Prix</option>';
        $widget  .= '<option value="99.00">99 €</option><option value="109.00">109 €</option>';
        $widget .= '</select></p>';
        $widget .= '<div class="textarea"><textarea name="item_comment" placeholder="Infos à communiquer pour la production (Morgan)"></textarea></div>';
        

    
        /*    <input type="file" name="mockup" id="i-mockup" class="onchange" data-ctrl="app.preUpload" />
            <label for="i-mockup" class="btn action circle" title="Ajouter une maquette"><span class="icon material-icons">insert_photo</span></label>
            <button type="submit" class="btn action circle green" title="Envoyer"><span class="icon material-icons">send</span></button>
         */ 
        $widget .= '<p class="txt-right"><button type="submit" class="btn action circle validate" title="Enregistrer la housse de selle"><span class="icon material-icons">done</span></button></p>'; 
             
        $widget .= '</form>'; 
        $widget .= '</div>';
        return $widget;
    }
    public function widgetPlate(object $plate): string
    {
        $widget = '<div class="widget widget-plate">';
        $widget .= '<div class="row-element">';
        $widget .= '<p>';
        $widget .= '<span>Nom</span>';
        $widget .= $plate->name;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Couleur plaque</span>';
        $widget .= $plate->color;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Typo nom</span>';
        $widget .= $plate->name_typo;
        $widget .= '</p>';
        $widget .= '</div>';
        $widget .= '<div class="row-element">';
        $widget .= '<p>';
        $widget .= '<span>Numéro</span>';
        $widget .= $plate->number;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Couleur numéro</span>';
        $widget .= $plate->number_color;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Typo numéro</span>';
        $widget .= $plate->number_typo;
        $widget .= '</p>';
        $widget .= '</div>'; 
        $widget .= '<div class="row-element">';
        $widget .= '<p>';
        $widget .= '<span>Logo</span>';
        $widget .= $plate->logo;
        $widget .= '</p>';       
        $widget .= '</div>';       
        $widget .= '</div>';
        return $widget;
    }

    public function widgetEmbedPlate(object $plate): string
    {
        $widget = '<div class="widget widget-plate">'; 
        $widget .= '<div class="header">';
        $widget .= '<h4><img src="/pictures/pictos/picto-plate.jpg"> Option Plaque</h4>';
        $widget .= '</div>';
        $widget .= '<div class="row-element">';
        $widget .= '<p>';
        $widget .= '<span>Nom</span>';
        $widget .= $plate->name;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Couleur plaque</span>';
        $widget .= $plate->color;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Typo nom</span>';
        $widget .= $plate->name_typo;
        $widget .= '</p>';
        $widget .= '</div>';
        $widget .= '<div class="row-element">';
        $widget .= '<p>';
        $widget .= '<span>Numéro</span>';
        $widget .= $plate->number;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Couleur numéro</span>';
        $widget .= $plate->number_color;
        $widget .= '</p>';
        $widget .= '<p>';
        $widget .= '<span>Typo numéro</span>';
        $widget .= $plate->number_typo;
        $widget .= '</p>';
        $widget .= '</div>'; 
        $widget .= '<div class="row-element">';
        $widget .= '<p>';
        $widget .= '<span>Logo</span>';
        $widget .= $plate->logo;
        $widget .= '</p>';       
        $widget .= '</div>';       
        $widget .= '</div>';
        return $widget;
    }

    public function widgetEmbedSwitch($switch): string
    {
        $widget = '<div class="widget widget-plate">'; 
        $widget .= '<div class="header">';
        $widget .= '<h4><img src="/pictures/pictos/picto-switch.jpg">Switch couleur</h4>';
        $widget .= '</div>';
        foreach($switch as $line)
        {
            $widget .= '<p>';
            $widget .= $line;
            $widget .= '</p>';
        }
        $widget .= '</div>';
        return $widget;
    }


    public function widgetSponsors(array $sponsors)
    {
        $opts = array_pop($sponsors);
        $exists = array_flip(array_column($sponsors, 'place'));
        $widget = '<div class="widget widget-sponsors">';     
        $widget .= '<div class="header">';
        $widget .= '<h4><span class="material-icons">fiber_manual_record</span>Les sponsors</h4>';
        $widget .= '</div>';
        $widget .= '<p class="sponsors-visual"><img id="tpl-sponsors" src=" ' . $opts->visual . '"></p>';
        $widget .= '<div class="sponsors">';
        for ($i=1; $i <= $opts->nb_sponsor  ; $i++) { 
            
            if(array_key_exists($i, $exists))
            {
                $widget .= '<p class="sponsor" ';                
                $this->place = $i;
                $exist = array_filter($sponsors, [$this, 'getSponsor']);
                $sponsor = array_pop($exist);
                if($sponsor->file)
                $widget .= 'style="background-image:url(' . $sponsor->file . ');">';
                else
                $widget .= '>';
                $widget .= '<span class="place">' . $i. '</span>';
                if($sponsor->text && $sponsor->text !=='')
                $widget .= '<span class="text">' . $sponsor->text. '</span>';   
                $widget .= '</p>';
            }
            
        }       
        $widget .= '</div>';
        $widget .= '</div>';
        
        return $widget;
    }

    public function widgetEmbedSponsors(array $sponsors){
        $opts = array_pop($sponsors);
        $nb_sponsor = $opts->nb_sponsor;
        $widget = '<div class="widget widget-sponsors es">';     
        $widget .= '<div class="header">';
        $widget .= '<h4><img src="/pictures/pictos/picto-sponsor.jpg"> Sponsors</h4>';
        $widget .= '</div>';
        $widget .= '<p class="sponsors-visual"><img id="tpl-sponsors" src=" ' . $opts->visual . '"></p>';
        $widget .= '<div class="sponsors">';
        $others = [];
        foreach($sponsors as $k => $sponsor)
        {
            $title = '';
            
            if((int)$sponsor->place > (int) $nb_sponsor) {
                $others[] = $sponsor;
                continue;
            } 
            $widget .= '<p class="sponsor';
            if(property_exists($sponsor, 'file') && $sponsor->file !== null)
            {
                if(property_exists($sponsor, 'text') && $sponsor->text !=='' && $sponsor->text !== null) $title = $sponsor->text;

                if(in_array(substr($sponsor->file, strrpos($sponsor->file, '.')+1), $this->_extensions)) { 
                    $widget .= '" style="background-image:url(' . $sponsor->file . ');"';
                    $widget .= '>';
                    $widget .= '<a href="' . $sponsor->file . '" target="_blank" title="' . $title. '"> </a>';
                      
                } elseif(in_array(substr($sponsor->file, strrpos($sponsor->file, '.')+1), ['pdf']))
                {
                    $widget .= ' pdf">';               
                    $widget .= '<a href="' . $sponsor->file . '" target="_blank" title="' . $title. '">' . '</a>';
    
                } elseif(in_array(substr($sponsor->file, strrpos($sponsor->file, '.')+1), ['ai', 'eps']))
                {
                    $widget .= ' ai">';
                    $widget .= '<a href="' . $sponsor->file . '" target="_blank" title="' . $title. '">' .  '</a>';
                }                
                else {
                    $widget .= '">';
                    $widget .= '<a href="' . $sponsor->file . '" target="_blank" title="' . $title. '">' . substr($sponsor->file, strrpos($sponsor->file, '.')+1). '</a>';
                    
                }
            } else {
                $widget .= ' fulltext">';
                if(property_exists($sponsor, 'text') && $sponsor->text !== null && $sponsor->text !=='')
                $widget .= '<span class="text">' . $sponsor->text. '</span>';  
            }
            
            $widget .= '<span class="place">' . $sponsor->place. '</span>';
            $widget .= '</p>';
        }
        $widget .= '</div>';
        if(count($others) > 0) {
            $widget .= '<div>';
            $widget .= '<h5> Autres Fichiers :</h5>';
            foreach($others as $f)
            {
                if($f->place <= $opts->nb_sponsor) continue;
                $widget .= '<p>';
                $widget .= '<a href="' . $f->file . '" target="_blank" > Fichier_' . $f->place. '</a>';
                $widget .= '</p>';
            }
            $widget .= '</div>';
        }
       
        $widget .= '</div>';
        return $widget;
    }
    public function widgetEmbedSponsors2(array $sponsors)
    {
        $opts = array_pop($sponsors);
       
        $exists = array_flip(array_column($sponsors, 'place'));
        $widget = '<div class="widget widget-sponsors">';     
        $widget .= '<div class="header">';
        $widget .= '<h4><img src="/pictures/pictos/picto-sponsor.jpg"> Option sponsors</h4>';
        $widget .= '</div>';
        $widget .= '<p class="sponsors-visual"><img id="tpl-sponsors" src=" ' . $opts->visual . '"></p>';
        $widget .= '<div class="sponsors">';
        for ($i=1; $i <= $opts->nb_sponsor  ; $i++) {             
            if(array_key_exists($i, $exists))
            {
                $widget .= '<p class="sponsor" ';                
                $this->place = $i;
                $exist = array_filter($sponsors, [$this, 'getSponsor']);
                //unset($sponsors[$exist]);
                $sponsor = array_pop($exist);
                $widget .= ' data-mime="' . $sponsor->type .'"';
                if($sponsor->file) {                   
                    $link = '';
                   if(strpos($sponsor->type, 'image') !== false)
                   {
                    $link = '';
                    $widget .= 'style="background-image:url(' . $sponsor->file . ');"';
                    
                    $widget .= '>';
                   }
                    else {
                    if(strpos($sponsor->type, 'pdf') !== false)    
                    $link = '<span class="material-icons">picture_as_pdf</span>';
                    elseif(strpos($sponsor->type, 'illustrator') !== false) 
                    $link = '<span class="ai">.Ai</span>';
                    $widget .= '>';
                    
                   }
                   $widget .= '<a href="' . $sponsor->file . '" target="_blank" >' . $link. '</a>';
                  
                } else {
                    $widget .= '>';
                    if($sponsor->text && $sponsor->text !=='')
                    $widget .= '<span class="text">' . $sponsor->text. '</span>';  
                }            
                
                $widget .= '<span class="place">' . $i. '</span>';
                 
                $widget .= '</p>';
            }            
        }       
        $widget .= '</div>';
        if(count($sponsors) > 0)
        $widget .= '<div>';
        $widget .= '<h5> Autres Fichiers :</h5>';
        foreach($sponsors as $f)
        {
            if($f->place <= $opts->nb_sponsor) continue;
            $widget .= '<p>';
            $widget .= '<a href="' . $f->file . '" target="_blank" > Fichier_' . $f->place. '</a>';
            $widget .= '</p>';
        }
        $widget .= '</div>';
        $widget .= '</div>';   
        // comment on traite les fichiers qui ne sont pas des sponsors ?

        return $widget;
    }
    
    public function widgetEmbedVehicleInfos($details)
    {
        $widget = '<div class="widget widget-vehicle">'; 
        $widget .= '<div class="header">';
        $widget .= '<h4>Infos véhicule</h4>';
        $widget .= '</div>';
        $widget .= '<p><b>' . $details->vehicle. '<b></p>';
        $widget .= '</div>';
        return $widget;

    }
    public function widgetVehicleInfos($details)
    {
        $widget = '';
        $widget .= '<p><b>' . $details->vehicle. '</b></p>';
        
        return $widget;

    }
    public function widgetEmbedItemComment(?string $comment, bool $header = true)
    {
        if(!$header)
            return '<div class="order-item-comment">' . $comment. '</div>'; 

        $widget = '<div class="widget widget-item-comment">'; 
        $widget .= '<div class="header">';
        $widget .= '<h4>Commentaire</h4>';
        $widget .= '</div>';
        $widget .= '<div class="order-item-comment">' . $comment. '</div>';
        $widget .= '</div>';

        return $widget;

    }

    public function widgetEmbedGraphicKitInfos($details)
    {
        $widget = '<div class="widget widget-graphic-kit">'; 
        $widget .= '<div class="header">';
        $widget .= '<h4><img src="/pictures/pictos/picto-switch.jpg"> Graphisme et finition</h4>';
        $widget .= '</div>';

        if(property_exists($details, 'kit') && property_exists($details->kit, 'design'))
            $widget .= '<p><b>Graphisme : </b>' .$details->kit->design. '</p>';         
        else
            $widget .= '<p><b>Graphisme : </b>' .'100% PERSO' . '</p>'; 
        if(property_exists($details, 'kit') && property_exists($details->kit, 'color'))
            $widget .= '<p><b>Couleur : </b>' .$details->kit->color. '</p>';   
        if(property_exists($details, 'kit') && property_exists($details->kit, 'finish'))
            $widget .= '<p><b>Finition : </b>' .$details->kit->finish. '</p>';  

        if(property_exists($details, 'premium'))   
            $widget .= '<p><b>Finition PREMIUM : </b>' .$details->premium. '</p>';  
       
        $widget .= '</div>';
        return $widget;
    }
    public function widgetEmbedDesign($details)
    {
        $widget = '';    

        if(property_exists($details, 'kit') && property_exists($details->kit, 'design'))
            $widget .= '<p class="table-line"><b>Graphisme : </b>' .$details->kit->design. '</p>';         
        else
            $widget .= '<p class="table-line"><b>Graphisme : </b>' .'100% PERSO' . '</p>'; 
        if(property_exists($details, 'kit') && property_exists($details->kit, 'color'))
            $widget .= '<p class="table-line"><b>Couleur : </b>' .$details->kit->color. '</p>';   
        if(property_exists($details, 'kit') && property_exists($details->kit, 'finish'))
            $widget .= '<p class="table-line"><b>Finition : </b>' .$details->kit->finish. '</p>';  

        if(property_exists($details, 'premium'))   
            $widget .= '<p class="table-line"><b>Finition PREMIUM : </b>' .$details->premium. '</p>';    
       
        return $widget;
    }
    public function widgetEmbedDesign2($details)
    {
        $widget = '';    

        if(property_exists($details, 'kit') && property_exists($details->kit, 'design'))
            $widget .= '<p class="table-line"><b >Graphisme : </b>' .$details->kit->design. '</p>';         
        else
            $widget .= '<p class="table-line"><b>Graphisme : </b>' .'100% PERSO' . '</p>'; 
        if(property_exists($details, 'kit') && property_exists($details->kit, 'color'))
            $widget .= '<p class="table-line"><b>Couleur : </b>' .$details->kit->color. '</p>';   
        if(property_exists($details, 'kit') && property_exists($details->kit, 'finish'))
            $widget .= '<p class="table-line"><b>Finition : </b>';
            
            $widget .= '<select name="finish" class="onchange" data-ctrl="orderItem.setFinish" data-item="' . $details->id . '">';
            $widget .= '<option value="Brillant"'. ($details->kit->finish == "Brillant" ? "selected" : "") . ' >Brillant</option>';
            $widget .= '<option value="Mat"'. ($details->kit->finish == "Mat" ? "selected" : "") .  '>Mat</option>';
            $widget .= '</select>';
            $widget .= '</p>';  

        if(property_exists($details, 'premium'))   
            $widget .= '<p class="table-line"><b>Finition PREMIUM : </b>' .$details->premium. '</p>';    
       
        return $widget;
    }
    // Infos graphisme et finition
    public function widgetDesign($details){
        $widget = '';
        if(property_exists($details, 'kit'))
        {
            if(property_exists($details->kit, 'design'))
                $widget .= '<p class="table-row"><b >Graphisme : </b><span>' .$details->kit->design. '</span></p>';         
            else
                $widget .= '<p class="table-row"><b>Graphisme : </b><span>' .'100% PERSO' . '</span></p>';  
            if(property_exists($details->kit, 'color'))
                $widget .= '<p class="table-row"><b>Couleur : </b><span>' .$details->kit->color. '</span></p>'; 
            if(property_exists($details->kit, 'finish'))
            {
                $widget .= '<p class="table-row"><b>Finition : </b>';                
                $widget .= '<select name="finish" class="onchange" data-ctrl="orderItem.setFinish" data-item="' . $details->id . '">';
                $widget .= '<option value="Brillant"'. ($details->kit->finish == "Brillant" ? "selected" : "") . ' >Brillant</option>';
                $widget .= '<option value="Mat"'. ($details->kit->finish == "Mat" ? "selected" : "") .  '>Mat</option>';
                $widget .= '</select>';
                $widget .= '</p>';
            }         
        }
        if(property_exists($details, 'premium'))   
            $widget .= '<p class="table-row"><b>Finition PREMIUM : </b>' .$details->premium. '</p>';  
        return $widget;    

    }
    
    // Lien du produit et visuel
    public function widgetEmbedProductInfos($details)
    {
        $widget = '';        
        if(property_exists($details, 'product'))
        {
            $product = json_decode($details->product);
            if(property_exists($product, 'visual') && $product->visual !== null)
            {
                $widget  .= '<figure class="product-item">';
		        $widget .= '<img class="visual" src="' . $product->visual. '">';	
		        //$widget .= '<img class="visual" src="https://www.kutvek-kitgraphik.com/img/ranges/322/116/53/arctic-cat-arctic-mauve-standard.jpg">';
                $widget .= '<hr>';	
                $widget .= '<figcaption>';
                //$widget .= '<h3 class="item">' . $details->description. '</h3>';                
                $widget .= '</figcaption>';	                
                $widget .= '</figure>';               
            }            
        }
        if(property_exists($details, 'visual') && $details->visual !== null)
        {
            $widget  .= '<figure class="product-item">';
            $widget .= '<img class="visual" src="' . $details->visual. '">';	
            //$widget .= '<img class="visual" src="https://www.kutvek-kitgraphik.com/img/ranges/322/116/53/arctic-cat-arctic-mauve-standard.jpg">';
            $widget .= '<hr>';	
            $widget .= '<figcaption>';
            //$widget .= '<h3 class="item">' . $details->description. '</h3>';                
            $widget .= '</figcaption>';	                
            $widget .= '</figure>';            
        } 
        return $widget;
    }

    public function widgetGraphicKitInfos($details)
    {
        $widget = '';
        if(property_exists($details, 'kit') && property_exists($details->kit, 'design'))
            $widget .= '<p><b>Graphisme : </b>' .$details->kit->design. '</p>';         
        else
            $widget .= '<p><b>Graphisme : </b>' .'100% PERSO' . '</p>';
        if(property_exists($details, 'kit') && property_exists($details->kit, 'finish'))
            $widget .= '<p><b>Finition : </b>' .$details->kit->finish. '</p>';
        if(property_exists($details, 'premium'))   
            $widget .= '<p><b>Finition PREMIUM : </b>' .$details->premium. '</p>';        
        return $widget;
    }

    public function widgetEmbedAccessories($accessories)
    {
        
       $widget = '';
        foreach($accessories as $accessory)
        {
            $widget .= '<p class="accessory">';
            $widget .= $accessory->name;
            $widget .= '<span>';
            $widget .= $accessory->cost;
            $widget .= '</span>';
            $widget .= '</p>';
        }
        
        return $widget;
    }

    public function widgetAccessories($accessories)
    {
        $widget = '';        
        foreach($accessories as $accessory)
        {
            $widget .= '<p class="accessory">';
            $widget .= $accessory->name;
            $widget .= '<span>';
            $widget .= $accessory->cost;
            $widget .= '</span>';
            $widget .= '</p>';
        }        
        return $widget;
    }

    public function widgetCustomColors($custom)
    {
        $widget = ''; 
        if(property_exists($custom, 'options'))
        {
            $options = $custom->options;
            if(property_exists($options, 'custom_colors'))
            {
                $widget .= '<div class="widget widget-full-custom">';
                $widget .= '<div class="header"><h4>Couleurs PERSO</h4></div>';
                $widget .= '<div class="row-element">';
                $colors = $custom->options->custom_colors;       
                foreach($colors as $color)
                {
                    $widget .= '<span class="std-element">' . $color. '</span>';
                }        
                $widget .= '</div>';  
                $widget .= '</div>';  
            }
        }
        return $widget;
    }
    public function widgetCustomFiles($custom)
    {
        $widget = '';
        if(property_exists($custom, 'vehicle'))
        {
            $vehicle = $custom->vehicle;
            if(property_exists($vehicle, 'files') && !empty($vehicle->files))
            {

                $widget .= '<div class="widget widget-full-custom">';
                $widget .= '<div class="header"><h4>Photos du véhicule</h4><hr></div>';
                $widget .= '<div class="sponsors">';
                foreach($vehicle->files as $visual)
                {   
                    if($visual == null) continue;
                    $widget .= '<p class="sponsor" style="background-image:url(' . $visual . ');">';
                    $widget .= '<a href="' . $visual .'" target="_blank"></a>';                  
                    $widget .= '</p>';
                }
                $widget .= '</div>';
                $widget .= '</div>';
            }
        }
        return $widget;    
    }
    public function widgetEmbedFullCustom($attached, $custom, ?string $comment = null)
    {
        $widget = '';              
        $widget .= '<h5>Couleurs perso</h5>';        
        $widget .= '<div class="row-element">';
        $colors = $custom->options->custom_colors;       
        foreach($colors as $color)
        {
            $widget .= '<span class="std-element">' . $color. '</span>';
        }        
        $widget .= '</div>';       
        $widget .= '<h5>Photos du véhicule</h5>';       
        $visuals = $custom->vehicle->files;
        $widget .= '<div class="sponsors">';
            foreach($visuals as $visual)
            {   
                if($visual == null) continue;
                $widget .= '<p class="sponsor" style="background-image:url(' . $visual . ');">';
                $widget .= '<a href="' . $visual .'" target="_blank"></a>';                  
                $widget .= '</p>';
            }
        $widget .= '</div>';      
        $widget .= '<h5>Fichiers pour inspiration</h5>';       
        $files = json_decode($attached);        
        $widget .= '<div class="sponsors">';
        if($files)
        {
            foreach($files as $file)
            {   
            if($file == null) continue;
            if(is_object($file))
            {
                $widget .= '<p class="sponsor" style="background-image:url(' . $file->file . ');">';
                $widget .= '<a href="' . $file->file .'" target="_blank"></a>';                  
                $widget .= '</p>';
            }
            else{
                $widget .= '<p class="sponsor" style="background-image:url(' . $file . ');">';
            $widget .= '<a href="' . $file .'" target="_blank" title="'. $file .'"></a>';                  
            $widget .= '</p>';
            }
            
            }
        }
        $widget .= '</div>';           
      
        return $widget;
    }

    public function widgetAttachedFiles($attached)
    {
        $files = json_decode($attached);        
        $widget = '<div class="sponsors">';
        if($files)
        {
            foreach($files as $file)
            {   
            if($file == null) continue;
            if(is_object($file))
            {
                $widget .= '<p class="sponsor" style="background-image:url(' . $file->file . ');">';
                $widget .= '<a href="' . $file->file .'" target="_blank"></a>';                  
                $widget .= '</p>';
            }
            else{
                $widget .= '<p class="sponsor" style="background-image:url(' . $file . ');">';
            $widget .= '<a href="' . $file .'" target="_blank" title="'. $file .'"></a>';                  
            $widget .= '</p>';
            }
            
            }
        }
        $widget .= '</div>';           
      
        return $widget;
    }
    
    public function widgetFullCustom($attached, $custom, $comment)
    {
        $widget = '<div class="widget widget-full-custom">'; 
        $widget .= '<div class="header">';
        $widget .= '<h4><span class="material-icons">fiber_manual_record</span>Option 100% PERSO</h4>';
        $widget .= '</div>';
        
        $widget .= '<div class="custom-colors">';
        $widget .= '<div class="header">';
        $widget .= '<h4><span class="material-icons">fiber_manual_record</span>Couleurs perso</h4>';
        $widget .= '</div>';
        $widget .= '<div class="row-element">';
        $colors = $custom->options->custom_colors;
        $widget .= '<p>';
        foreach($colors as $color)
        {
            $widget .= '<span class="std-element">' . $color. '</span>';
        }
        $widget .= '<p>';
        $widget .= '</div>';
        $widget .= '</div>';

        $widget .= '<div class="vehicle-visuals">';
        $widget .= '<div class="header">';
        $widget .= '<h4><span class="material-icons">fiber_manual_record</span>Photos du véhicule</h4>';
        $widget .= '</div>';
        $visuals = $custom->vehicle->files;
        foreach($visuals as $visual)
        {   
            if($visual == null) continue;
            $widget .= '<p class="vehicle-visual" ';                
            $widget .= 'style="background-image:url(' . $visual . ');">';          
            $widget .= '</p>';
        }
        $widget .= '</div>'; 

        $widget .= '<div class="custom-description">';
        $widget .= '<div class="header">';
        $widget .= '<h4><span class="material-icons">fiber_manual_record</span>Descriptif client</h4>';
        $widget .= '</div>';
        $widget .= '<div class="order-item-comment">';
        $widget .= $comment;
        $widget .= '</div>';
        $widget .= '</div>';

        $widget .= '<div class="custom-files">';
        $widget .= '<div class="header">';
        $widget .= '<h4><span class="material-icons">fiber_manual_record</span>Fichiers joints</h4>';
        $widget .= '</div>';
        $files = json_decode($attached);
        foreach($files as $file)
        {   
            if($file == null) continue;
            $widget .= '<p class="vehicle-visual" ';                
            $widget .= 'style="background-image:url(' . $file . ');">';          
            $widget .= '</p>';
        }
        $widget .= '</div>';

       
      
        
        $widget .= '</div>';
        return $widget;
    }
    public function getSponsor($sponsor){
        return $sponsor->place === $this->place;       
    }

 }