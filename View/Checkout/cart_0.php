<aside id="cart-preview" class="visible" data-order="<?=$cart->id;?>">
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
    				<table class="jil">
					<tbody>
						<?php foreach($cart->items as $item): ?>
							<tr id="i-<?=$item->item_id?>" data-uri="<?=$this->uri('cart.item.read', ['queries'=>['order'=>$cart->id, 'item'=>$item->item_id]])?>">
								<td>
									<img src="<?=$item->img ?? '/img/blank.png'?>" alt="">									
								</td>
								<td><span class="cart-designation"><?=$item->designation;?></span></td>
								<td><div class="options"><!--item->options()--></div></td>
								<td><?=$item->qty;?></td>	
								<td class="opts"><span class="price"><?=$item->value_format;?></span></td>
								<td><button class="square click" data-ctrl="item.delete" data-id="<?=$item->item_id;?>"><i class="material-symbols-rounded">&#xe872;</i></button></td>							
							</tr>
							<tr><td class="no-border" colspan="5"><div class="comment"><?= $item->item_comment;?></div></td></tr>													
						<?php endforeach; ?>
					</tbody>
					<thead>
						<tr>
							<th colspan="2"></th>						
							<th>Options</th>
							<th>Qté</th>
							<th>Prix</th>
							<th></th>														
						</tr>
					</thead>
					</table>
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
    			<div>
    				<ul class="tabs">
                        <li class="active"><a href="#voucher"><span class="icon material-symbols-rounded voucher"></span><span data-i18n="voucher">Code promo</span></a></li>               
                    </ul>
                    <div class="tabs_content">
                        <div class="tab_content active" id="voucher">
                            <form method="post" class="row i-center" data-ctrl="cart.promoCode">  
                                <div class="promocode-error">
                                    <p class="h5"></p>
                                    <div></div>
                                </div>
                                    <div class="field-wrapper"><label class="required" for="write-promocode">Saisir le code</label><input name="code" id="write-promocode" type="text" class="field-input" required="" data-i18n="write-promocode"></div>
                                    <div class="field-wrapper">
                                        <button type="submit" formaction="/api/promocode/orders/:order/apply" class="contained dark apply" data-i18n="apply-voucher">Appliquer le code</button>
                                        <button type="submit" formaction="/orders.applyPromoCode" class="contained warning delete hide" disabled="" data-i18n="supprimer">Supprimer</button>
                                    </div>                          
                            </form>
                        </div>                           
                    </div>
    			</div>
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
    			<div class="row">
                    <div class="col-s-12 col-m-4 col-m-offset-1 col-m-push-1"><a href="/" class="btn outlined white wide continue" data-i18n="shop">Continuer mes achats</a></div>
                    <div class="col-s-12 col-m-4 col-m-offset-1">
                       
                        <a href="<?=$this->uri('cart.onePageCheckout', ['queries'=>['id'=> $cart->id]]);?>" class="btn contained dark wide  click" >
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