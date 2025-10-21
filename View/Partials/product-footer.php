<?php 
	$nodes = [];
	$func = function($node) use (&$nodes)
    {   
    $r = '';   
        for ($i = 0; $i < count($nodes) ; $i++) {
            // ne pas se tromper de parent
            if( $nodes[$i]->node_left <  $node->node_left  && ($node->node_right <  $nodes[$i]->node_right && $node->node_right > $nodes[$i]->node_left)){
                $nodes[$i]->leafs = $nodes[$i]->leafs - 1;
                if($nodes[$i]->leafs == 0 && $nodes[$i]->node_left <  $node->node_left  && ($node->node_right <  $nodes[$i]->node_right && $node->node_right > $nodes[$i]->node_left))  
                {
                    $r .= '</ul></li>';
                }
            }             
        }
        return $r;
    };   
?>
<footer class="main-footer product-footer">   
    <div class="footer-top">
        <?php foreach ($infos as $info): ?>
            <div class="info-card p">
                <a href="/<?= $info->link ?? '#' ?>">
                    <img src="<?= $info->icon; ?>" alt="">
                    <div>
                        <p class="title"><?= $info->designation; ?></p>
                        <p><?= $info->body; ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row top">
        <div id="newletter" class="bloc">
            <p class="title">REÇOIS NOS OFFRES SPÉCIALES, ACTUALITÉS ET BIEN PLUS ...</p>
            <form action="#" method="POST">
                <input type="email" name="email" id="email" placeholder="Entre ton adresse e-mail">
                <button type="submit">OK!</button>
            </form>
        </div>
        <div class="bloc">
            <?= $this->socialsMedias(); ?>
        </div>
        <div class="bloc">
            <p class="title" data-i18n="ask-us">NOUS SOMMES À VOTRE ÉCOUTE</p>
            <p data-i18n="opening-hours">Une équipe vous répond du lundi au vendredi de 9h00 à 18h00</p>
            <div class="flex contact">
                <img src="/img/pictos/phone.png" alt="" />
                <span class="phone"><?=$this->phone; ?></span>
            </div>
        </div>
    </div>
    <div class="red-line"></div>    
    <div class="footer-bottom">
        <nav id="main-links" style="color:white;">
            <ul class="row">
                <?php foreach($items as $node):
                    $depth = (int)$node->depth;
                    if($depth > 0) $uri = $node->external_link ?? $node->slug; 
                ?>
                    <?php if($node->leafs > 0) : $nodes[] = clone($node); ?>         
                        <li class="col-s-12 col-m-3">
                            <p><?= $node->name; ?></p>
                            <ul>
                    <?php else: ?>
                        <li>
                            <?php if ($node->obfuscated && $node->active) : ?>
                                <span class="obflink obf" data-obf="<?= base64_encode($node->slug) ?>">
                                    <?= $node->name; ?>
                                </span>
                            <?php elseif (!$node->active): ?>
                                <span><?= $node->name; ?></span>
                            <?php else : ?>
                                <a href="<?= $uri; ?>" data-slug="<?= $node->slug; ?>"><?= $node->name; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?= $func($node); ?>    
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>

    <p class="logo"><img src="/img/charter/logo-footer.png" alt="Kutvek Logo" /></p>
     <div class="promocodes">
        <?php if(HALLOWEEN): ?>
        <img src="/img/pictos/HALL10.png" alt="">
        <?php endif;?>
    </div> 
</footer>
<?php
    $startSale = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $endSale = new DateTime('2025-02-04 23:59:59', new DateTimeZone('Europe/Paris'));
    $suffixLang = strtoupper($this->getI18n(false));
    if ($startSale->getTimestamp() <  $endSale->getTimestamp()):
    $file = (object) ['img' => '/img/slider/SLIDER-SOLDES-HIVER-2025-'.$suffixLang.'.webp', 'portrait'=> '/img/slider/square/SQUARE-SOLDES-HIVER-2025-'.$suffixLang.'.webp'];       
?>
<aside class="modal click" id="promo-info-2" data-modal="promo-info" data-ctrl="popin.close" data-module="popin">   
    <div class="popin img">
        <header class="close">            
            <a href="#promo-info" class="click" data-modal="promo-info" data-ctrl="popin.close" data-module="popin"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div class="content">
            <picture>                       
                <source srcset="<?= $file->portrait ?>"  type="image/webp"/>    
                <img src="<?= str_replace('webp', 'jpg', $file->portrait) ?>">                   
            </picture>                
        </div>
    </div>
</aside>
<?php endif; ?>
<aside id="cart-preview">
    <div class="header">
        <img src="/img/charter/logo-black.png" class="logo" alt="logo kutvek">
        <img src="/img/charter/kutvek-black.png" alt="">
        <a href="" class="click close" data-ctrl="cart.closeOverview">
            <span class="icon material-symbols-rounded">close</span>
        </a>
    </div>
    <div id="empty-cart">
        <span data-i18n="empty-cart">Aucun article dans le panier</span>  <a href="<?=$this->uri('pages.index')?>" class="link" data-i18n="shop">Continuer mes achats</a>      
    </div>
    <div id="cart-filled">        
        <div class="row">
            <div class="col-s-12 col-m-6 col-m-pull-1">
                <p data-i18n="my-cart" class="titles top">Mon panier</p>
                <div id="items"></div>
            </div>
            <div id="checkout" class="col-s-12 col-m-5">
                <p class="titles sub-total"><span data-i18n="sub-total">Sous-total</span><span id="item-total"></span></p>
                <p class="titles sub-total"><small data-i18n="delivery">Livraison</small><small id="shipping-amount" data-i18n="shipping-on-checkout">à l'étape suivante</small></p>
                
                
                    
                    <div>
                        <ul class="tabs">
                            <li class="active"><a href="#voucher"><span class="icon material-symbols-rounded voucher">&#xe892;</span><span data-i18n="voucher">Code promo</span></a></li>               
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
                                            <button type="submit" formaction="<?=$this->uri('orders.applyPromoCode', ['queries' => ['order' => ':order']], 'POST')?>" class="contained dark apply"  data-i18n="apply-voucher">Appliquer le code</button>
                                            <button type="submit" formaction="<?=$this->uri('orders.applyPromoCode')?>" class="contained warning delete hide" disabled  data-i18n="supprimer">Supprimer</button>
                                        </div>                          
                                </form>
                            </div>                           
                        </div>
                    </div>
                
                <div id="discount" class="hidden"></div>  
                <hr>
                <p class="titles sub-total"><span data-i18n="total">Total</span><b id="total-to-pay"></b></p>

                <div class="row">
                    <div class="col-s-12 col-m-4 col-m-offset-1 col-m-push-1"><a href="<?=$this->uri('pages.index')?>" class="btn outlined white wide continue" data-i18n="shop">Continuer mes achats</a></div>
                    <div class="col-s-12 col-m-4 col-m-offset-1">
                        <span data-obf="<?= base64_encode($this->uri('orders.checkout')) ?>" class="btn contained dark wide obflink click" data-ctrl="analytics.beginCheckout" data-module >
                            <span class="icon material-symbols-rounded">&#xe870;</span>
                            <span data-i18n="shipping-and-pay">Livraison et Paiement</span>
                        </span>                        
                    </div>
                </div>
                <div class="tranquility">
                    <!-- <div class="accordion_tabs">
                        <p data-i18n="shipping-fees" class="titles">Délais et frais de livraison</p>
                        <input type="checkbox" id="shipping-info">
                        <label for="shipping-info" class="pointer"><span class="material-symbols-rounded"></span></label>
                        <div>
                            <div class="delivery-method chrono-classic hide">
                                <p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Classic</small></p>
                                <p><span data-i18n="cost">Coût</span> <small class="cost"></small></p>
                                <p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="3-working-days">3 jours ouvrables</span></p>
                            </div>
                            <div class="delivery-method chrono-express hide">
                                <p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Express</small></p>
                                <p><span data-i18n="cost">Coût</span> <small class="cost"></small></p>
                                <p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="1-working-days">1 jour ouvrable</span></p>
                            </div>
                            <div class="delivery-method chrono-relay hide">
                                <p><span data-i18n="shipping-method">Méthode de livraison</span> <small>CHRONO Relay</small></p>
                                <p><span data-i18n="cost">Coût</span> <small class="cost"></small></p>
                                <p><span data-i18n="delivery-time">Délai de livraison</span> <span data-i18n="3-working-days">3 jours ouvrables</span></p>
                            </div>
                        </div>
                    </div> -->
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
    </div>
</aside>
<template id="discount-tpl">
    <p class="titles"><span data-i18n="discount">Réduction:</span> <small class="discount"></small><a href="<?= $this->uri('orders.deletePromoCode', ['queries'=>['order'=>':order']], 'DELETE');?>" data-i18n="delete" class="hide p-code delete click" data-module data-ctrl="order.deletePromoCode">supprimer</a> <small class="amount"></small></p>
</template>
<template id="item-tpl">
    <div class="item">
        <picture>
            <img src="/img/blank.png" alt="">
        </picture>
        <div class="item-infos">
            <a class="item-desc" href="#"></a>
            <span class="item-price"></span>
            <a href="#item-info" class="item-info link click" data-modal="item-info" data-fetch="<?=$this->uri('orderItems.info', ['queries' => ['item' => ':item']]);?>" data-ctrl="item.info"><span class="icon  material-symbols-rounded">info</span> détails</a>
        </div>
        <div class="item-actions">
            <select name="qty" data-i18n="qty-short" id="qty" class="field-input select item-qty onchange" data-ctrl="item.updateQuantity" data-uri="<?=$this->uri('orderItems.updateQty', ['queries' => ['id' => ':id', 'qty' => ':qty']], 'PUT')?>">
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
            <a href="#" class="delete-item">
                <span class="material-symbols-rounded">close</span>
            </a>
        </div>
    </div>
</template>
<aside class="modal click" id="item-info" data-modal="item-info" data-ctrl="item.info">
    <div class="popup mx-w45">
        <header class="close">
            <p class="title" data-i18n="item-info">Détails de l'article</p>
            <a href="#item-info" class="click" data-modal="item-info" data-ctrl="item.info"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div class="content">
            
        </div>
    </div>
</aside>
<aside class="modal click" id="countries" data-modal="countries" data-ctrl="app.modal">
    <div class="popup preferences">
        <header class="close">
            <p class="title" data-i18n="choose-country">Préférences. Merci de choisir le pays et la devise associée pour votre panier</p>
            <a href="#countries" class="click" data-modal="countries" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
        </header>
        <div class="modal-content"></div>
    </div>
</aside>