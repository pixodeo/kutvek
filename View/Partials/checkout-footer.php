<aside class="modal click" id="new-address" data-modal="new-address" data-ctrl="app.modal">
    <div class="popup mx-w45">
        <header class="close">
            <p class="title" data-i18n="modify-shipping">Modifier l'addresse de livraison</p>
            <a href="#new-address" class="click" data-modal="new-address" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div>
            <form action="<?=$this->uri('orders.address', [], 'POST')?>" method="post" data-ctrl="cart.address">
                <div class="row">
                    <div class="col-s-12 col-m-7 col-m-center">
                        <div class="field-wrapper ">
                            <label for="lastname" class="required">Nom</label>
                            <input name="customer[lastname]" id="lastname" data-i18n="lastname" type="text" class="field-input" required />
                        </div>
                        <div class="field-wrapper ">
                            <label for="firstname" class="required" >Prénom</label>
                            <input name="customer[firstname]" id="firstname" data-i18n="firstname" type="text" class="field-input" required />
                        </div>
                        <div class="field-wrapper"><label for="company">Société</label><input name="customer[company]" id="company" type="text" data-i18n="company" class="field-input"></div>
                        <div class="field-wrapper">
                            <label for="phone">Téléphone fixe</label>
                            <input name="customer[phone]" id="phone" type="text" class="field-input" data-i18n="phone">
                        </div>
                        <div class="field-wrapper">
                            <label for="cellphone" class="required">Téléphone portable</label>
                            <input name="customer[cellphone]" id="cellphone" type="text" class="field-input" data-i18n="cellphone" required>
                        </div>
                        <div class="field-wrapper ">
                            <label for="address_line_1" class="required">Rue, numéro...</label>
                            <input name="address[address_line_1]" id="address_line_1" type="text" class="field-input" data-i18n="line1" required>
                        </div>
                        <div class="field-wrapper ">
                            <label for="address_line_2">Complément d'adresse</label>
                            <input name="address[address_line_2]" id="address_line_2" type="text" class="field-input" data-i18n="line2" />
                        </div>
                        <div class="field-wrapper ">
                            <label for="admin_area_1">Etat / Région</label>
                            <input name="address[admin_area_1]" id="admin_area_1" type="text" class="field-input" data-i18n="line4">
                        </div>         
                        <div class="field-wrapper ">
                            <label for="admin_area_2" class="required">Ville</label>
                            <input name="address[admin_area_2]" id="admin_area_2" type="text" class="field-input" data-i18n="city" required>
                        </div>           
                         <div class="field-wrapper ">
                            <label for="postal_code" class="required">Code Postal</label>
                            <input name="address[postal_code]" id="postal_code" type="text" class="field-input" data-i18n="zipcode" required>
                        </div>             
                        <?= $this->form->select('address[country_code]', [
                            'label' => 'Pays',
                            'id' => 'country_code',
                            'attributes' => [
                                'class="field-input select onchange"',
                                'data-i18n="country-code"',
                                'required'
                            ],
                            'values' => $countries,
                            'selected' => 'FR'
                            ]); 
                        ?> 
                    </div>
                </div>
                <div class="btns">
                   <button class="btn contained primary" type="submit" data-i18n="send">Enregistrer</button>
                </div>
             </form>            
        </div>           
    </div>
</aside><aside class="modal click" id="new-address" data-modal="new-address" data-ctrl="app.modal">
    <div class="popup mx-w45">
        <header class="close">
            <p class="title" data-i18n="modify-shipping">Modifier l'addresse de livraison</p>
            <a href="#new-address" class="click" data-modal="new-address" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div>
            <form action="<?=$this->uri('orders.address', [], 'POST')?>" method="post" data-ctrl="cart.address">
                <div class="row">
                    <div class="col-s-12 col-m-7 col-m-center">
                        <div class="field-wrapper ">
                            <label for="lastname" class="required">Nom</label>
                            <input name="customer[lastname]" id="lastname" data-i18n="lastname" type="text" class="field-input" required />
                        </div>
                        <div class="field-wrapper ">
                            <label for="firstname" class="required" >Prénom</label>
                            <input name="customer[firstname]" id="firstname" data-i18n="firstname" type="text" class="field-input" required />
                        </div>
                        <div class="field-wrapper"><label for="company">Société</label><input name="customer[company]" id="company" type="text" data-i18n="company" class="field-input"></div>
                        <div class="field-wrapper">
                            <label for="phone">Téléphone fixe</label>
                            <input name="customer[phone]" id="phone" type="text" class="field-input" data-i18n="phone">
                        </div>
                        <div class="field-wrapper">
                            <label for="cellphone" class="required">Téléphone portable</label>
                            <input name="customer[cellphone]" id="cellphone" type="text" class="field-input" data-i18n="cellphone" required>
                        </div>
                        <div class="field-wrapper ">
                            <label for="address_line_1" class="required">Rue, numéro...</label>
                            <input name="address[address_line_1]" id="address_line_1" type="text" class="field-input" data-i18n="line1" required>
                        </div>
                        <div class="field-wrapper ">
                            <label for="address_line_2">Complément d'adresse</label>
                            <input name="address[address_line_2]" id="address_line_2" type="text" class="field-input" data-i18n="line2" />
                        </div>
                        <div class="field-wrapper ">
                            <label for="admin_area_1">Etat / Région</label>
                            <input name="address[admin_area_1]" id="admin_area_1" type="text" class="field-input" data-i18n="line4">
                        </div>         
                        <div class="field-wrapper ">
                            <label for="admin_area_2" class="required">Ville</label>
                            <input name="address[admin_area_2]" id="admin_area_2" type="text" class="field-input" data-i18n="city" required>
                        </div>           
                         <div class="field-wrapper ">
                            <label for="postal_code" class="required">Code Postal</label>
                            <input name="address[postal_code]" id="postal_code" type="text" class="field-input" data-i18n="zipcode" required>
                        </div>             
                        <?= $this->form->select('address[country_code]', [
                            'label' => 'Pays',
                            'id' => 'country_code',
                            'attributes' => [
                                'class="field-input select onchange"',
                                'data-i18n="country-code"',
                                'required'
                            ],
                            'values' => $countries,
                            'selected' => 'FR'
                            ]); 
                        ?> 
                    </div>
                </div>
                <div class="btns">
                   <button class="btn contained primary" type="submit" data-i18n="send">Enregistrer</button>
                </div>
             </form>            
        </div>           
    </div>
</aside>
<footer class="main-footer"><img class="logo-footer" src="/img/charter/logo-footer.png" alt="Kutvek Logo" />
</footer>
<template id="item-tpl">
    <div class="item">
        <picture>
            <img src="/img/blank.png"  alt="">
        </picture>        
        <div class="item-infos">
             <a class="item-desc" href="#"></a>
            <span class="item-price"></span>                   
        </div>
        <div class="item-actions">              
           <select name="qty" data-i18n="qty-short" id="qty" class="field-input select item-qty"  disabled ><option value="1">1</option> <option value="2">2</option> <option value="3">3</option> <option value="4">4</option> <option value="5">5</option> <option value="6">6</option> <option value="7">7</option> <option value="8">8</option> <option value="9">9</option> <option value="10">10</option></select>     
        </div>
    </div>
</template>
<template id="listing-tpl">
    <div class="item" id="listing-6">
        <a href="#" class="title"></a>
        <p class="city"></p>
        <div class="opening">            
        </div>
        <div class="details">
            <label class="btn contained xs validate carrier">Choisir</label>
            <input type="radio" name="relay" value="1" data-i18n="choose" form="form-relay" >   

        </div>
    </div>
</template>