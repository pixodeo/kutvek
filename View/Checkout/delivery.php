<header class="checkout-header ">
    <a href="<?=$this->url('page.homepage');?>" class="logo back-to-site"><img class="logo-kutvek" src="/img/charter/logo_kutvek.png" alt="Logo KUTVEK"></a>
</header>
<?php $url = base64_encode($this->uri('checkout.cart', ['queries'=>['id'=> $cart->id]]));?> 
<main>

<nav id="progress">
    <a href="<?=$url?>"><span class="step ok">1</span><span>Panier</span></a>
    <span><span class="step">2</span><span>Livraison</span></span>
    <span><span class="step disabled">3</span><span>Paiement</span></span>
</nav>
<h1 data-i18n="my-cart" class="titles top"><span class="step">2</span><span>Livraison</span></h1>
<div class="grid-checkout">        
<div class="delivery-methods">
<div class="accordion">  
<input type="checkbox" name="shipping_method[]" id="pickup-method" />                 
<label for="pickup-method" class="header">
    <span class="icon material-symbols-rounded">store</span>
    <span data-i18n="" >Retrait sur place</span>
    <i class="icon material-symbols-rounded">&#xe316;</i>
</label>                                          
<div class="content">  
     <p class="cost-line">
        <input type="radio" name="delivery[cost]" data-ctrl="delivery.setAddress" value="0" id="pickup" class="onchange" data-type="2" data-address="52742" required form="checkout-next" />
        <label for="pickup">Récupérez votre colis directement en magasin</label>
        <b class="cost">Gratuit</b>
    </p> 
    <?=$cart->pickup_address;?>        
</div>
</div>
<div class="accordion">
<input type="checkbox" name="shipping_method[]" id="relay-method" />
<label for="relay-method" class="header">
    <span class="icon material-symbols-rounded">&#xe0c8;</span>
    <span data-i18n="">Livraison en point relais</span>
    <span class="icon material-symbols-rounded">&#xe316;</span>
</label> 
<div class="content">                            
    <form id="search-place" data-ctrl="delivery.points">
        <div class="field-wrapper column">
            <label for="search-zipcode" class="required">Code postal</label>
            <input name="postal_code" id="search-zipcode" type="text" class="field-input" data-i18n="zipcode" value="<?=$cart->default_zipcode;?>" required>
        </div>
        <div class="field-wrapper column">
            <label for="search-city" class="required">Ville</label>
            <input name="admin_area_2" id="search-city" type="text" class="field-input" data-i18n="city" value="<?=$cart->default_city;?>" required />
        </div>
        <input type="hidden" name="country_code" value="FR"/>                       
        <button class="contained dark" type="submit"><span class="icon material-symbols-rounded">&#xe8b6;</span> Rechercher</button>    
    </form>
    <div class="map-container">
        <div class="sidebar">                              
            <h3 data-i18n="listing-relay">Liste des points relais</h3>
            <div id="listings" class="listings"></div>
        </div>
        <div id="map" class="map"></div>
    </div>
</div>                   
</div>     
<div class="accordion">
    <input type="checkbox" name="shipping_method[]"  id="home-method" />    
    <label for="home-method" class="header">
        <span class="icon material-symbols-rounded">&#xe558;</span>
        <span data-i18n="">Livraison à domicile</span>
        <span class="icon material-symbols-rounded">&#xe316;</span>
    </label>                         
    <div class="content">
        <p class="cost-line">
            <input type="radio" name="delivery[cost]" data-ctrl="delivery.setAddress" value="13" id="classic" class="onchange" data-type="4" data-address="<?=$cart->customer_address_id;?>" form="checkout-next" required />
            <label for="classic">livraison en boite à lettre</label>
            <b class="cost">13 €</b>
        </p>
        <p class="cost-line">
            <input type="radio" name="delivery[cost]" data-ctrl="delivery.setAddress" value="18" id="signature" class="onchange" data-type="4" data-address="<?=$cart->customer_address_id;?>" form="checkout-next" required />
            <label for="signature">remise contre signature</label>
            <b class="cost">18 €</b>
        </p>
        <p class="cost-line">
            <input type="radio" name="delivery[cost]" data-ctrl="delivery.setAddress" value="22" id="express" class="onchange" data-type="4" data-address="<?=$cart->customer_address_id;?>" form="checkout-next" required />
            <label for="express">livraison express</label>
            <b class="cost">22 €</b>
        </p>                         
        <?=$cart->customer_address;?>
        <div class="edit-address">
            <a href="<?=$this->url('checkout.addShippingAddress', ['queries' => ['id'=>$cart->id]]);?>" class="btn outlined primary click" data-ctrl="delivery.addShippingAddress"><span class="material-symbols-rounded">&#xe745;</span><span data-i18n="modify-address">Modifier l'adresse</span></a>
        </div>                         
    </div>
</div>
<div class="accordion">  
<input type="checkbox" id="debug-method" />                 
<label for="debug-method" class="header">
    <span class="icon material-symbols-rounded">&#xe868;</span>                    
    <span data-i18n="" >Debug</span>
    <i class="icon material-symbols-rounded">&#xe316;</i>
</label>                                          
<div class="content">  
    <pre><?php print_r($cart);?></pre>
</div>
</div>            
</div>             
<div id="checkout" class="checkout-info">
    <?php if($cart->amount->breakdown->discount->value > 0):?>
        <p class="cost-line">
            <b data-i18n="sub-total">Sous-total</b>
            <span id="item-total"><?=$cart->amount->breakdown->item_total->format;?></span>
        </p>
        <p class="cost-line"><b data-i18n="taxes-rate">Taxes / TVA : </b><span><?= $cart->amount->breakdown->tax_total->format;?></span></p>   
    <?php endif; ?>
<p class="cost-line"><b data-i18n="delivery">Livraison</b><span class="shipping-cost"><?= $cart->amount->breakdown->shipping->format;?></span></p>
<?php if($cart->amount->breakdown->discount->value > 0): $c = count($cart->discounts);?>
<p class="cost-line discounts">
    <b>Réductions : </b>
    <b class="amount negative"><?= $cart->amount->breakdown->discount->format;?></b>
</p>
<?php for ($i=0; $i < $c; $i++): $discount = $cart->discounts[$i];?>
    <p class="cost-line">                                                       
        <small class="discount"><?= $discount->designation; ?></small> 
        <small class="negative"><?= $discount->value_format; ?></small>
    </p>
<?php endfor; ?>
<?php endif; ?>  
<div class="shipping-address"></div>  
<p class="cost-line cost-total"><b>Total : </b><b id="total-amount"><?= $cart->amount->value_format;?></b></p>
<div>
<input type="hidden" class="input-amount" name="amount[shipping]" value="<?=$cart->amount->breakdown->shipping->value;?>">
<input type="hidden" class="input-amount" name="amount[shipping_discount]" value="<?=$cart->amount->breakdown->shipping_discount->value;?>">
<input type="hidden" class="input-amount" name="amount[handling]" value="<?=$cart->amount->breakdown->handling->value;?>">
<input type="hidden" class="input-amount" name="amount[discount]" value="<?=$cart->amount->breakdown->discount->value;?>">
<input type="hidden" class="input-amount" name="amount[total]" value="<?=$cart->amount->breakdown->item_total->value;?>">
<input type="hidden" class="input-amount" name="amount[tax]" value="<?=$cart->amount->breakdown->tax_total->value;?>">
</div>            
<div class="checkout-actions">
<?php $url = base64_encode($this->uri('checkout.payment', ['fqdn' => 1,'queries'=>['id'=> $cart->id]]));?> 
<form id="checkout-next" data-obf="<?=$url;?>" data-ctrl="checkout.next">
    <input type="hidden" id="address-id" name="delivery_address" />
    <input type="hidden" id="type-id" name="delivery_type" />
    <button type="submit" class="contained dark wide pointer checkout-next" >
        <span class="icon material-symbols-rounded"></span>
        <span data-i18n="go-to-pay">Passer au paiement</span>
    </button> 
</form>                
</div>            
<div class="checkout-tranquility">                    
<div class="accordion">
    <input type="checkbox" id="return-policy">                   
    <label for="return-policy" class="header"><span data-i18n="return-policy" class="titles">Politique de retour et remboursement</span><span class="icon material-symbols-rounded"></span></label>
    <div class="content">                            
        <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/retour-et-remboursement~c27.html" data-i18n="return-policy" class="link">sur notre politique de retour</a>.</p>
    </div>
</div>
<div class="accordion">                    
    <input type="checkbox" id="pay-secure">
    <label for="pay-secure" class="header">
        <span data-i18n="secure-payment">paiements sécurisés</span>
        <span class="icon material-symbols-rounded"></span>
    </label>
    <div class="content">
        <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/paiement-securise~c3.html" data-i18n="secure-payment" class="link">sur les modes de paiement acceptés</a>.</p>
    </div>
</div>
<div class="accordion">                    
    <input type="checkbox" id="our-cgv">
    <label for="our-cgv" class="header">
        <span data-i18n="our-terms">Nos conditions générales de vente</span>
        <span class="icon material-symbols-rounded"></span>
    </label>
    <div class="content">
        <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/paiement-securise~c3.html" data-i18n="secure-payment" class="link">Nos CGV</a>.</p>
    </div>
</div>
</div>
</div>
</div>
</main>
