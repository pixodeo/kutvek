<aside id="cart-preview" class="visible cart-0" data-order="<?=$cart->id;?>">
	<div class="header">
        <img src="/img/charter/logo-black.png" class="logo" alt="logo kutvek">
        <img src="/img/charter/kutvek-black.png" alt="">
        <a href="" class="click close" data-ctrl="cart.closeOverview">
            <span class="icon material-symbols-rounded">close</span>
        </a>
    </div>
    <div id="empty-cart" class="invisible">
        <span data-i18n="empty-cart">Aucun article dans le panier</span>  <a href="/" class="link" data-i18n="shop">Continuer mes achats</a>      
    </div>
    <div id="cart-filled">
    	<div class="row">
    		<div class="col-s-12 col-m-6 col-m-pull-1">
    			<p data-i18n="my-cart" class="titles top">Mon panier</p>
    			<div id="items" data-qty="<?=count($cart->items);?>">  
                    <?php foreach($cart->items as $item): ?>
                        <div class="cart-item" id="i-<?=$item->item_id?>" data-uri="<?=$this->uri('cart.item.read', ['queries'=>['order'=>$cart->id, 'item'=>$item->item_id]])?>">                            
                            <img src="<?=$item->img ?? '/img/blank.png'?>" alt="">                   
                            <div class="cart-item-info">                                
                                <p class="item-designation"><?=$item->designation;?></p>                                
                                <p class="txt-r">
                                    <span class="item-price"><?=$item->value_format;?></span>
                                    <?= $this->form->qty('qty',['selected' => $item->qty]);?>                                
                                    <button class="square item-delete click" data-ctrl="item.delete" data-id="<?=$item->item_id;?>"><i class="material-symbols-rounded">&#xe872;</i></button>
                                </p>                                
                            </div>                                               
                        </div>                                                                               
                    <?php endforeach; ?>    				
    			</div>
    		</div>
    		<div id="checkout" class="col-s-12 col-m-5">
    			<p class="titles sub-total">
    				<span data-i18n="sub-total">Sous-total</span>
    				<span id="item-total"><?=$cart->amount->breakdown->item_total->format;?></span>
    			</p>
    			<p class="titles sub-total">
    				<small data-i18n="delivery">Livraison</small>
    				<small id="shipping-amount" data-i18n="shipping-on-checkout">à l'étape suivante</small>
    			</p>    			
    			<div class="invoice">
		            <p class="sub-total"><b>Total HT : </b><span><?= $cart->amount->breakdown->item_total->format;?></span></p> 
                    <?php if($cart->amount->breakdown->discount->value > 0): $c = count($cart->discounts);?>
                    <p class="sub-total discounts"><b>Réductions : </b><span class="amount negative"><?= $cart->amount->breakdown->discount->format;?></span></p>
            		<?php for ($i=0; $i < $c; $i++): $discount = $cart->discounts[$i];?>
            			<p class="sub-total">		                                				
            				<small class="discount"><?= $discount->designation; ?></small> 
            				<small class="negative"><?= $discount->value_format; ?></small>
            			</p>
            		<?php endfor; ?>                          
                    <?php endif; ?>
                    <p class="sub-total"><b>Taxes : </b><span><?= $cart->amount->breakdown->tax_total->format;?></span></p>
                    <p class="sub-total"><b>Frais de port : </b><span><?= $cart->amount->breakdown->shipping->format;?></span></p>    
                    <hr>      
                    <p class="total_amount titles sub-total"><b>Total : </b><b class="bold"><?= $cart->amount->value_format;?></b></p>
                </div>                
                <div class="accordion_tabs accordion_voucher">                    
                    <input type="checkbox" id="voucher">
                    <label for="voucher" class="pointer title"><span class="title" data-i18n="you-have-a-promocode">Vous avez un code promo ?</span><span class="material-symbols-rounded"></span></label>
                    <div>                            
                       <form method="post" class="row i-center" data-ctrl="cart.promoCode">  
                            <div class="promocode-error">
                                <p class="h5"></p>
                                <div></div>
                            </div>
                            <div class="promocode-wrapper">                           
                              <input name="code" id="write-promocode" type="text" class="field-input" required="" data-i18n="write-promocode">
                            <button type="submit" formaction="/api/promocode/orders/:order/apply" class="contained dark apply" data-i18n="apply-voucher">Appliquer le code</button>
                            <button type="submit" formaction="/orders.applyPromoCode" class="contained warning delete hide" disabled="" data-i18n="supprimer">Supprimer</button>                                
                            </div>                          
                        </form>
                    </div>
                </div>                    
                		
    			<div class="row">
                    <div class="col-s-12 col-m-4 col-m-offset-1 col-m-push-1"><a href="/" class="btn outlined white wide continue" data-i18n="shop">Continuer mes achats</a></div>
                    <div class="col-s-12 col-m-4 col-m-offset-1">
                       
                        <a href="<?=$this->uri('orders.checkout');?>" class="btn contained dark wide  click" >
                            <span class="icon material-symbols-rounded"></span>
                            <span data-i18n="shipping-and-pay">Livraison et Paiement</span>
                        </a>                        
                    </div>
                </div>
                <div class="tranquility">
                	<div class="accordion_tabs">
                        <p data-i18n="return-policy" class="titles">Politique de de retour et remboursement</p>
                        <input type="checkbox" id="return-policy">
                        <label for="return-policy" class="pointer"><span class="material-symbols-rounded"></span></label>
                        <div>                            
                            <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/retour-et-remboursement~c27.html" data-i18n="return-policy" class="link">sur notre politique de retour</a>.</p>
                        </div>
                    </div>
                    <div class="accordion_tabs">
                        <p data-i18n="secure-payment" class="titles">paiements sécurisés</p>
                        <input type="checkbox" id="pay-secure">
                        <label for="pay-secure" class="pointer"><span class="material-symbols-rounded"></span></label>
                        <div>
                             <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/paiement-securise~c3.html" data-i18n="secure-payment" class="link">sur les modes de paiement acceptés</a>.</p>
                        </div>
                    </div>
                </div>
    		</div>
    	</div>
    	<div class="our-terms">
    		<span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/cgv~c17.html" data-i18n="our-terms" class="link">Nos conditions générales de vente</a>.
    	</div>
    </div>    
</aside>
<pre>
<?php print_r($this->data); ?>
</pre>