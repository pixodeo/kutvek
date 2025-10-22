<div class="overview-header">
    <a href="#" data-ctrl="cart.closeOverview" class="click"><span class="material-symbols-rounded">&#xef7d;</span></a><span class="overview-title" data-i18n="my-cart">Mon panier</span>     
</div>
<div class="grid-checkout">
    <div id="items" class="checkout-items" data-qty="<?=count($cart->items);?>">
        <?php foreach($cart->items as $item):?>
            <div class="item" id="i-<?=$item->item_id;?>">
                <picture><img src="<?=$item->img ?? '/img/blank.png'?>" alt="" /></picture> 
                <div class="item-infos">
                    <a class="item-desc" href="#"><?=$item->designation;?></a>                    
                    <a href="#item-info" class="item-info link" data-modal="item-info" data-fetch="" data-ctrl="item.info"><span class="icon  material-symbols-rounded">info</span> détails</a>
                    <span class="item-price"><?=$item->value_format;?></span>
                    <div class="actions">  
                        <?php
                            $uri = $this->url('cart.updateItemQty',['queries'=>['id' => $cart->id, 'item' =>$item->item_id]], 'PUT');
                            $dataUri = <<<EOT
                            data-uri="{$uri}"
                            EOT;
                        ;?>
                        <label for="aqty-<?=$item->item_id;?>" data-i18n="qty">Qté</label>
                        <?=$this->form->qty('qty',[
                            'id' => "aqty-{$item->item_id}",
                            'class' => ['onchange'],
                            'selected' => $item->qty,
                            'attributes' => ['data-ctrl="cart.updateItemQty"', $dataUri]
                        ]);?>                        
                        <a href="<?=$this->url('cart.deleteItem',['queries'=>['id' => $cart->id, 'item' =>$item->item_id]], 'DELETE');?>" class="item-delete click" data-ctrl="cart.deleteItem"><span class="material-symbols-rounded">&#xe92b;</span><span data-i18n="delete">supprimer</span></a>
                </div>
                </div> 
                <div class="item-actions">
                    <span class="item-price"><?=$item->value_format;?></span>
                    <label for="qty-<?=$item->item_id;?>" data-i18n="qty">Qté</label>
                    <?=$this->form->qty('qty',[
                        'id' => "qty-{$item->item_id}",
                        'class' => ['onchange'],
                        'selected' => $item->qty,
                        'attributes' => ['data-ctrl="cart.updateItemQty"', $dataUri]
                    ]);?>  
                    <a href="<?=$this->url('cart.deleteItem',['queries'=>['id' => $cart->id, 'item' =>$item->item_id]], 'DELETE');?>" class="item-delete click" data-ctrl="cart.deleteItem"><span class="material-symbols-rounded">&#xe92b;</span><span data-i18n="delete">supprimer</span></a>
                </div>
            </div>
        <?php endforeach; ?>      
    </div>              
    <div id="checkout" class="checkout-info">
        <?php if($cart->amount->breakdown->discount->value > 0):?>
        <p class="cost-line">
            <b data-i18n="sub-total">Sous-total</b>
            <span id="item-total"><?=$cart->amount->breakdown->item_total->format;?></span>
        </p>
        <p class="cost-line"><b>Taxes : </b><span><?= $cart->amount->breakdown->tax_total->format;?></span></p>   
    <?php endif; ?>
        
       <!--  <p class="cost-line">
            <b>Total HT :</b>
            <span><?= $cart->amount->breakdown->item_total->format;?></span>
        </p>  -->
        <?php if($cart->amount->breakdown->discount->value > 0): $c = count($cart->discounts);?>

            <p class="cost-line discounts">
                <b>Réductions : </b>
                <b class="amount negative"><?= $cart->amount->breakdown->discount->format;?></b>
            </p>
            <?php for ($i=0; $i < $c; $i++): $discount = $cart->discounts[$i];?>
                <p class="cost-line">                                                       
                    <small class="discount"><?= $discount->designation; ?></small> 
                    <small class="amount negative"><?= $discount->value_format; ?></small>
                </p>
            <?php endfor; ?>                          
        <?php endif; ?>
        <p class="cost-line">
            <b data-i18n="delivery">Livraison</b>
            <small id="shipping-amount" data-i18n="shipping-on-checkout">à l'étape suivante</small>
        </p>
        <p class="cost-line"><b>Total : </b><b><?= $cart->amount->value_format;?></b></p>        
        <div class="accordion" id="discount-tabs">
            <input type="checkbox" id="apply-voucher">
            <label class="header" for="apply-voucher"><span data-i18n="voucher">ajouter un code promo</span><span class="icon material-symbols-rounded"></span></label>            
            <form method="post" class="content form-voucher" data-ctrl="cart.voucher">  
                <div class="promocode-error"><p class="h5"></p><div></div></div>         
                <input name="code" id="write-promocode" type="text" class="voucher" required="" data-i18n="write-promocode" placeholder="EN MAJUSCULES" />                         
                <button type="submit" formaction="<?=$this->url('cart.addVoucher',['queries'=>['id' => $cart->id], 'errors'=>1], 'PUT');?>" class="apply primary" data-i18n="add">Ajouter</button>
                <button type="submit" formaction="<?=$this->url('cart.deleteVoucher',['queries'=>['id' => $cart->id]], 'PUT');?>" class="warning delete hide" disabled="" data-i18n="delete">Supprimer</button>                 
            </form>        
        </div> 
        <?php $url = base64_encode($this->uri($cart->status, ['queries'=>['id'=> $cart->id]]));?> 
        <span  data-obf="<?=$url;?>" class="btn contained dark wide pointer checkout-next click" data-ctrl="checkout.next">
            <span class="icon material-symbols-rounded"></span>
            <span data-i18n="shipping-and-pay">Livraison et Paiement</span>
        </span>                
        <div class="tranquility"> 
            <div class="accordion">
                <input type="checkbox" id="debug-cart">
                <label for="debug-cart" class="header">
                    <span data-i18n="return-policy">Debug</span>
                    <span class="icon material-symbols-rounded"></span>
                </label>               
                <div class="content">                            
                    <pre><?php print_r($cart);?></pre>
                </div>
            </div>                   
            <div class="accordion">
                <input type="checkbox" id="return-policy">
                <label for="return-policy" class="header">
                    <span data-i18n="return-policy">Politique de retour et remboursement</span>
                    <span class="icon material-symbols-rounded"></span>
                </label>               
                <div class="content">                            
                <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/retour-et-remboursement~c27.html" data-i18n="return-policy" class="link">sur notre politique de retour</a>.</p>
                </div>
            </div>
            <div class="accordion">                
                <input type="checkbox" id="pay-secure">
                <label for="pay-secure" class="header"><span data-i18n="secure-payment" class="titles">paiements sécurisés</span><span class="icon material-symbols-rounded"></span></label>
                <div class="content">
                <p><span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/paiement-securise~c3.html" data-i18n="secure-payment" class="link">sur les modes de paiement acceptés</a>.</p>
                </div>
            </div>
            <div class="accordion">                
                <input type="checkbox" id="link-cgv">
                <label for="link-cgv" class="header"><span data-i18n="our-terms" >Nos conditions générales de vente</span><span class="icon material-symbols-rounded"></span></label>
                <div class="content">
                <span data-i18n="see-page">Consultez cette page pour en savoir plus</span> <a href="/cgv~c17.html" data-i18n="our-terms" class="link">Lien vers nos CGV</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="update-screen">
    <span class="icon material-symbols-rounded load">&#xe9d0;</span>
</div>