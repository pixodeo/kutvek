<aside id="cart-preview" data-order="<?=$cart->id;?>">
    <div class="checkout-header">
        <a href="/" class="btn outlined white continue" data-i18n="shop">Continuer mes achats</a>   
        <span>  
        <img src="/img/charter/logo-black.png" class="logo" alt="logo kutvek">
        <img src="/img/charter/kutvek-black.png" alt="">
        </span>
        <a href="" class="click close" data-ctrl="cart.closeOverview">
        <span class="icon material-symbols-rounded">close</span>
        </a>
    </div>            
    <p data-i18n="my-cart" class="titles top">Mon panier <?=$cart->status?></p>
    <div class="grid-checkout">
        <div id="items" class="checkout-items" data-qty="<?=count($cart->items);?>">
            <?php foreach($cart->items as $item):?>
                <div class="item" id="i-<?=$item->item_id;?>">
                    <picture><img src="<?=$item->img ?? '/img/blank.png'?>" alt="" /></picture> 
                    <div class="item-infos">
                        <a class="item-desc" href="#"><?=$item->designation;?></a>
                        <span class="item-price"><?=$item->value_format;?></span>
                        <a href="#item-info" class="item-info link" data-modal="item-info" data-fetch="" data-ctrl="item.info"><span class="icon  material-symbols-rounded">info</span> détails</a>
                    </div> 
                    <div class="item-actions">
                        <select name="qty" data-i18n="qty-short" id="qty-<?=$item->item_id;?>" class="field-input select item-qty onchange" data-ctrl="item.updateQuantity" data-uri="/api/items/:id/qty/:qty">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                        </select>
                        <a href="/api/items/<?=$item->item_id;?>" class="delete-item click" data-ctrl="item.delete" data-item="item-<?=$item->item_id;?>"><span class="material-symbols-rounded">close</span></a>
                    </div>
                </div>
            <?php endforeach; ?>      
        </div>              
        <div id="checkout" class="checkout-info">
            <p class="cost-line">
                <b data-i18n="sub-total">Sous-total</b>
                <span id="item-total"><?=$cart->amount->breakdown->item_total->format;?></span>
            </p>
            <p class="cost-line">
                <b data-i18n="delivery">Livraison</b>
                <small id="shipping-amount" data-i18n="shipping-on-checkout">à l'étape suivante</small>
            </p>
            <p class="cost-line">
                <b>Total HT :</b>
                <span><?= $cart->amount->breakdown->item_total->format;?></span>
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
            <p class="cost-line"><b>Taxes : </b><span><?= $cart->amount->breakdown->tax_total->format;?></span></p>
            <p class="cost-line"><b>Frais de port : </b><span><?= $cart->amount->breakdown->shipping->format;?></span></p>    
            <hr>      
            <p class="cost-line cost-total"><b>Total : </b><b><?= $cart->amount->value_format;?></b></p>            
            <div class="checkout-actions">
                <?php $url = base64_encode($this->uri($cart->status, ['queries'=>['id'=> $cart->id]]));?> 
                <span  data-obf="<?=$url;?>" class="btn contained dark wide pointer click" data-ctrl="checkout.next">
                    <span class="icon material-symbols-rounded"></span>
                    <span data-i18n="shipping-and-pay">Livraison et Paiement</span>
                </span> 
            </div>
            <div class="accordion_tabs checkout-voucher" id="discount-tabs">
                <p data-i18n="voucher" class="titles">Ajouter un code promo</p><input type="checkbox" id="apply-voucher">
                <label class="titles link" for="apply-voucher"><span class="material-symbols-rounded"></span></label>
                <div>
                    <form method="post" class="row i-center" data-ctrl="cart.promoCode">  
                        <div class="promocode-error"><p class="h5"></p><div></div></div>
                        <div class="field-wrapper">
                            <label class="required" for="write-promocode">Saisir le code</label>
                            <input name="code" id="write-promocode" type="text" class="field-input" required="" data-i18n="write-promocode">
                        </div>
                        <div class="field-wrapper">
                            <button type="submit" formaction="/api/promocode/orders/:order/apply" class="contained dark apply" data-i18n="apply-voucher">Appliquer le code</button>
                            <button type="submit" formaction="/orders.applyPromoCode" class="contained warning delete hide" disabled="" data-i18n="supprimer">Supprimer</button>
                        </div>                          
                    </form>
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
    <div class="our-terms"><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/cgv~c17.html" data-i18n="our-terms" class="link">Nos conditions générales de vente</a>.</div>
</aside>