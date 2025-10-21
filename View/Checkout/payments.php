<header class="checkout-header ">
    <a href="<?=$this->url('page.homepage');?>" class="logo back-to-site"><img class="logo-kutvek" src="/img/charter/logo_kutvek.png" alt="Logo KUTVEK"></a>
</header>
<main>
    <nav id="progress">
    <a href="<?=$url?>"><span class="step ok">1</span><span>Panier</span></a>
    <span><span class="step ok">2</span><span>Livraison</span></span>
    <span><span class="step">3</span><span>Paiement</span></span>
    </nav>   
    <h1  class="titles top"><span class="step">3</span><span data-i18n="choose-payment-method">Sélectionnez votre moyen de paiement</span></h1>
    <div class="grid-checkout">
        <div class="payment-methods">      
            <div class="accordion">
                <input type="radio" name="pay[]" id="pro-method" class="onchange" data-ctrl="checkout.showButton" data-btn="btn-pro"/>
                <label for="pro-method" class="header">                    
                    <span data-i18n="">Compte pro, payez à l'expédition</span>
                    <span class="icon material-symbols-rounded">&#xe316;</span>
                </label>              
                <div class="content">
                    <div class="pro-features">
                        <img src="/img/payment/pro.jpg" width="56" height="56" alt="">
                        <span data-i18n="pro-conditions">Avantage PRO</span>
                    </div>  
                    <p>Avec votre compte pro, validez votre commande et réglez plus tard.<br>Nous vous recontactons au moment de l'expédition pour le règlement.</p>           
                </div>
            </div>
            <div class="accordion">
                <input type="radio" name="pay[]" id="card-method" class="onchange" data-ctrl="checkout.showButton" data-btn="paypal-creditcard"/>               
                <label for="card-method" class="header">
                    <span data-i18n="">Carte bancaire</span>
                    <i class="icon material-symbols-rounded">&#xe316;</i>
                </label>
                <div class="content"> 
                    <div class="creditcard-logos">
                        <img src="/img/pictos/cartebancaire-logo.svg" alt="Logo CB">
                        <img src="/img/pictos/mastercard-logo.svg" alt="Logo Mastercard">
                        <img src="/img/pictos/visa-logo.svg" alt="Logo Visa">                        
                        <img src="/img/pictos/amex-logo.svg" alt="Logo American Express">
                    </div>                                
                    <div id="card-fields" class="card_container">
                        <form id="card-form" class="card-form">
                            <div>
                                <label for="card-number" data-i18n="card-number">Numéro de la carte</label>
                                <div id="card-number" class="col-s-12 card_field"></div> 
                            </div>                                                        
                            <div>
                              <label for="expiration-date" data-i18n="expiration-date">Expire fin</label>
                              <div id="expiration-date" class="card_field"></div>
                            </div>
                            <div>
                              <label for="cvv" data-i18n="cvv">CVC / CVV</label>
                              <div id="cvv" class="card_field"></div>                                         
                            </div> 
                              <div>
                                <label for="card-holder-name" data-i18n="name-on-card">Nom sur la carte</label>
                                <input type="text" id="card-holder-name" name="card-holder-name"  data-i18n="name-on-card" placeholder="Ex: Jean DUPONT" class="card_field col-s-12"/>                                      
                            </div> 
                            <div>
                                
                            </div>                                   
                        </form>
                    </div>                        
                </div>
            </div> 
            <div class="accordion">
                <input type="radio" name="pay[]" id="paypal-method" class="onchange" data-ctrl="checkout.showButton" data-btn="paypal-button-container"/>
                <label for="paypal-method" class="header">
                    <span class="icon"><img src="/img/pictos/paypal-logo.svg"  alt="Logo ¨Paypal"></span>
                    <span data-i18n="" >PayPal</span>
                    <i class="icon material-symbols-rounded">&#xe316;</i>
                </label>                                             
                <div class="content">                                                         
                    <p>Payez avec votre compte paypal.</p>
                    <p>Réglez en 4 échéances pour toute commande d'un montant supérieur à xx euros.</p>
                </div>
            </div> 
             <div class="accordion">
                <input type="radio" name="pay[]" id="redeem-method" />
                <label for="redeem-method" class="header">
                    <span class="icon material-symbols-rounded">&#xe8b1;</span>
                    <span data-i18n="" >Utilisez une carte cadeau</span>
                    <span class="logo"></span>
                    <span class="icon material-symbols-rounded">&#xe316;</span>
                </label>               
                <div class="content"></div>
            </div>
            <div class="accordion">
                <input type="checkbox" id="debug-method" />               
                <label for="debug-method" class="header">
                    <span data-i18n="" >Debug</span>
                    <i class="icon material-symbols-rounded">&#xe316;</i>
                </label>                                      
                <div class="content">                                       
                    <pre><?php print_r($cart);?></pre>
                </div>
            </div> 
        </div>             
        <div id="checkout" class="checkout-info">
            <h2>Récapitulatif</h2>
            <p class="cost-line">
                <b data-i18n="sub-total">Sous-total</b>
                <span id="item-total"><?=$cart->amount->breakdown->item_total->format;?></span>                
            </p>
            <?php if($cart->amount->breakdown->discount->value > 0): $c = count($cart->discounts);?>
                <p class="cost-line discounts">
                    <b>Réductions : </b>
                    <span class="amount negative"><?= $cart->amount->breakdown->discount->format;?></span>
                </p>
                <?php for ($i=0; $i < $c; $i++): $discount = $cart->discounts[$i];?>
                    <p class="cost-line">                                                       
                        <small class="discount"><?= $discount->designation; ?></small> 
                        <small class="negative"><?= $discount->value_format; ?></small>
                    </p>
                <?php endfor; ?>
            <?php endif; ?>
            <p class="cost-line"><b>Taxes : </b>
                <span><?= $cart->amount->breakdown->tax_total->format;?></span>
            </p>
            <p class="cost-line">
                <b>Livraison</b>
                <span class="shipping-cost"><?= $cart->amount->breakdown->shipping->format;?></span>
            </p>  
            <div class="shipping-address"></div>  
            <hr>      
            <p class="cost-line cost-total"><b>Total : </b><b id="total-amount"><?= $cart->amount->value_format;?></b></p>
            <div>
                <input type="hidden" class="input-amount" name="amount[shipping]" value="<?=$cart->amount->breakdown->shipping->value;?>">
                <input type="hidden" class="input-amount" name="amount[shipping_discount]" value="<?=$cart->amount->breakdown->shipping_discount->value;?>">
                <input type="hidden" class="input-amount" name="amount[handling]" value="<?=$cart->amount->breakdown->handling->value;?>">
                <input type="hidden" class="input-amount" name="amount[discount]" value="<?=$cart->amount->breakdown->discount->value;?>">
                <input type="hidden" class="input-amount" name="amount[total]" value="<?=$cart->amount->breakdown->item_total->value;?>">
                <input type="hidden" class="input-amount" name="amount[tax]" value="<?=$cart->amount->breakdown->tax_total->value;?>">
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
            </div>    
        </div> 
        <div class="checkout-actions pay">                
                <input type="checkbox" name="cgv" id="cgv" data-url="/order/:id/cgv" class="" data-ctrl="checkout.cgv" form="checkout-validate">
                <label data-i18n="accept-cgv" for="cgv">Je reconnais avoir lu et accepté les <a target="_blank" href="/cgv~c17.html" class="link">Conditions générales de vente</a>.</label>                
                <div id="cgv-error" data-i18n="check-cgv" class="warning hide">Veuillez accepter les conditions générales de vente pour poursuivre
                </div>                
                <div id="paypal-creditcard" class="payment-btn hide">
                    <button  value="submit" id="submit" form="card-form" class="btn contained dark paypal">
                            <span class="icon material-symbols-rounded">credit_card</span>
                            <span data-i18n="pay-creditcard">Payer avec votre carte</span>
                    </button>                    
                </div>                   
                <div id="paypal-button-container" class="payment-btn hide"></div> 
                <div id="btn-pro" class="payment-btn hide">
                    <button type="submit" class="btn contained dark pro"  data-url="/api/payLater/orders/:order/approve" form="checkout-validate">
                        <span class="icon material-symbols-rounded"></span>
                        <span data-i18n="pay-later">Terminer la commande</span>
                    </button>
                </div>
                <form  id="checkout-validate" data-ctrl="checkout.pay">
                    <?php $url = base64_encode($this->uri('checkout.pay', ['fqdn' => 1,'queries'=>['id'=> $cart->id]]));?>  
                    <input type="hidden" id="thanks" value="/checkout/thanks" />  
                </form>

        </div>         
    </div>
</main>