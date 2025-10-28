<?php $firstCountry = $countries[0]; ?>
<aside class="modal click fullscreen" id="new-address" data-modal="new-address" data-ctrl="app.modal">
    <div class="popup">
        <header class="close">
            <p class="title" data-i18n="modify-shipping">Modifier l'addresse de livraison</p>
            <a href="#new-address" class="click" data-modal="new-address" data-ctrl="app.modal"><span class="icon material-symbols-rounded">&#xe5cd;</span></a>
        </header>
        <div>
            <form action="<?=$this->url('checkout.addShippingAddress', ['queries' => ['id'=>$this->orderId]]);?>" method="post" data-ctrl="delivery.saveShippingAddress" class="form-address">
                 <div class="country-code">
                    <?=$this->form->select('country_code', [
                        'label'     => 'Pays',
                        'values'    =>  $countries,
                        'required',
                        'class' => ['column'],
                        'keys' =>  ['country_name', 'country_code'],
                        'dataset' => ['with_states', 'country_id'],
                        'attributes' => ['data-ctrl="delivery.countryWithStates"', 'class="onchange"']

                    ]);?>
                    <input type="hidden" id="country-id" name="country_id" value="<?=$firstCountry->country_id;?>" />
                </div>
                <div class="firstname">
                    <?=$this->form->text('firstname',[                        
                        'label' =>  'Prénom',                        
                        'class' => ['column'],
                        'required'
                    ]);?> 
                </div>
                <div class="lastname">
                    <?=$this->form->text('lastname',[                        
                        'label' =>  'NOM',                        
                        'class' => ['column'],
                        'required'
                    ]);?> 
                </div>
                <div class="company">
                    <?=$this->form->text('company',[                        
                        'label' =>  'Entreprise/Société',                        
                        'class' => ['column']                        
                    ]);?> 
                </div>
                <div class="address-line-1">
                    <?=$this->form->text('address_line_1',[
                        'id' => 'address-line-1',
                        'label' =>  'Adresse',                        
                        'class' => ['column'],
                        'required'
                    ]);?> 
                </div>
                <div class="address-line-2">
                    <?=$this->form->text('address_line_2',[
                        'id' => 'address-line-2',
                        'label' =>  'Complement d\'adresse',                        
                        'class' => ['column']
                    ]);?>                    
                </div>                
                <div class="postal-code">
                    <?=$this->form->text('postal_code',[
                        'id' => 'postal-code',
                        'label' =>  'Code postal',
                        'required',
                        'class' => ['column']
                    ]);?>                   
                </div>
                <div class="admin-area-1 <?= $firstCountry->with_states > 0 ? '' : 'hide';?>">
                    <?php $opts = [
                        'id' => 'admin-area-1',                        
                        'label' =>  'Etat / Province / Territoire',                        
                        'class' => ['column']
                    ];
                        if($firstCountry->with_states <= 0)  $opts[] = 'disabled';
                    ?>
                    <?=$this->form->text('admin_area_1', $opts);?>                    
                </div>
                <div class="city">
                    <?=$this->form->text('admin_area_2',[
                        'id' => 'city',
                        'label' =>  'Ville',
                        'required',
                        'class' => ['column']
                    ]);?>                   
                </div>
                <div class="phone">
                    <?=$this->form->text('cellphone',[                       
                        'label' =>  'Téléphone',
                        'required',
                        'class' => ['column']
                    ]);?> 
                </div>               
                <div class="address-preferences">
                     <?=$this->form->checkbox('is_billing', [
                        'label'     => 'Définir comme adresse de facturation',
                        'value'    =>  1,                        
                        'class' => ['label-right']
                    ]);?>               
                    <button class="contained primary save"><span class="material-symbols-rounded"></span><span data-i18n="modify-address">Enregistrer</span></button>
                </div>
            </form>            
        </div>           
    </div>
</aside>