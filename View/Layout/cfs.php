<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= $this->meta_title();?>
	<?= $this->meta_description();?>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/section.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/products.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
</head>
<body>
	<header class="primary-header">
    <div class="message-bar">    	
        <a href="https://www.youtube.com/user/kutvekkitgraphik" target="_blank"><img src="/img/pictos/youtube.png"></a>
        <a href="https://www.facebook.com/Kutvek" target="_blank"><img src="/img/pictos/facebook.png"></a>
        <a href="https://www.instagram.com/kutvek" target="_blank"><img src="/img/pictos/instagram.png"></a>
    </div>
    <div class="infos">
        <div>
            <div class="trust"></div>
            <div class="action contact-us click" data-ctrl="app.contact">
                <img src="/img/pictos/phone.png" class="picto phone" alt="">
                <a href="tel:<?= $this->intlphone;?>"><span class="phone"><?=$this->phone; ?></span></a>
            </div>
        </div>
        <div>
            <span data-i18n="lang" class="choose-lang">Langue</span>
            <div class="action trads-container">
                <?= $this->l10ns($slugs); ?>
            </div>
            <?= $this->widgetCountries(); ?>
        </div>        
    </div>
    <div class="baseline">
        <a href="<?= $this->uri('pages.index', []) ?>" class="logo"><img class="logo-kutvek" src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>" alt="Logo KUTVEK"></a>
        <div class="red">
            <img class="logo-footer" src="/img/charter/logo-footer.png" />
            <?= $this->topNav();?>   
            <div class="user-actions">
                <div class="action search" id="search-doofinder">                 
                <span class="icon material-symbols-rounded"></span>
                </div>
                <span data-obf="<?= base64_encode($this->uri('identities.login')) ?>" class="action user-account">
                    <span class="click obf" id="user-name" data-i18n="login" data-obf="<?= base64_encode($this->uri('customers.dashboard')) ?>" data-ctrl="user.dashboard"><span class="icon material-symbols-rounded account-circle">&#xe853;</span></span>
                </span>
                <div class="action contact-us">
                    <label class="icon material-symbols-rounded" for="see-phone"></label>
                    <input type="checkbox" id="see-phone" hidden />
                    <a class="phone" href="tel:<?= $this->intlphone;?>"><span ><?=$this->phone; ?></span></a>
                </div>   <!--  $this->widgetSlugs($this->getContent()->slugs); -->
                </div>          
                <?php $obf = $this->uri('cart.read', ['queries'=>['order'=>':order']]); ?>
                <span id="cart-btn"  data-count="0" class="cart action click" data-ctrl="cart.read" data-obf="<?= base64_encode($obf) ?>">
                    <span class="icon material-symbols-rounded">&#xe8cc;</span>                    
                </span>
                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
            </div>
        </div>
    </div>  
    <?= $this->megamenu();?>
	</header>
	<main><?=$this->_content;?></main>
	<footer class="main-footer">
    <div class="grid-footer">
                                    <div class="item-footer" style="grid-area:1/1">
                <p>Reçois nos offres spéciales, actualités et bien plus ...</p>
                <form action="#" method="POST"><input id="email" name="email" type="email" placeholder="Entre ton adresse e-mail" data-i18n="give-email"> <button disabled="disabled" type="submit">OK!</button></form> 
            </div>
                                            <div class="item-footer" style="display: inline-flex;grid-area: 1 / 2 / 1 / 4;justify-content: center; flex-direction: column;align-items: center;">
                <p>Rejoins notre communauté</p>
                <ul class="social-medias">
<li><a href="https://www.facebook.com/Kutvek" target="_blank" rel="noopener"><img src="https://www.kutvek-kitgraphik.com/img/pictos/facebook_w40.png"></a></li>
<li><a href="https://www.instagram.com/kutvek/" target="_blank" rel="noopener"><img src="https://www.kutvek-kitgraphik.com/img/pictos/instagram_w40.png"></a></li>
<li><a href="https://www.youtube.com/user/kutvekkitgraphik" target="_blank" rel="noopener"><img src="https://www.kutvek-kitgraphik.com/img/pictos/youtube_w40.png"></a></li>
</ul>   
            </div>
                                                <div class="item-footer" style="grid-area:1/4">
                <p>Une question ?</p>
                <p data-i18n="open-hours">Une équipe vous répond du lundi au vendredi de 9h00 à 18h00</p>
<div class="flex contact"><img src="../../img/pictos/phone.png"> <a class="phone" href="tel:+33385303024">03 85 30 30 24</a></div>  
            </div>
                                                <div class="item-footer" style="grid-area:2/1">
                <p>kutvek kit graphik</p>
                <ul>
<li><span class="obflink obf" data-obf="L21vdG8tY3Jvc3MuaHRtbA==">motocross</span></li>
<li><span class="obflink obf" data-obf="L3F1YWQuaHRtbA==">quad</span></li>
<li><span class="obflink obf" data-obf="L3Nzdi5odG1s">ssv</span></li>
<li><span class="obflink obf" data-obf="LzUwY2MuaHRtbA==">50cc</span></li>
<li><span class="obflink obf" data-obf="L21vdG8uaHRtbA==">moto</span></li>
<li><span class="obflink obf" data-obf="L21heGlzY29vdGVyLmh0bWw=">maxiscooter </span></li>
<li><span class="obflink obf" data-obf="L3Njb290ZXIuaHRtbA==">scooter</span></li>
<li><span class="obflink obf" data-obf="L2pldC1za2kuaHRtbA=="> jet-ski</span></li>
<li><span class="obflink obf" data-obf="L2h5YnJpZGUuaHRtbA==">hybride</span></li>
<li><span class="obflink obf" data-obf="L2tpdHMtZGVjby1nb2xmLWNhcnQ=">golf cart</span></li>
</ul>   
            </div>
                                                <div class="item-footer" style="grid-area:2/2">
                <p>les marques</p>
                <ul>
<li><a href="../../kits-deco-yamaha" data-slug="/kits-deco-yamaha">yamaha</a></li>
<li><a href="../../kits-deco-honda" data-slug="/kits-deco-honda">honda</a></li>
<li><a href="../../kits-deco-suzuki" data-slug="/kits-deco-suzuki">suzuki</a></li>
<li><a href="../../kits-deco-ktm" data-slug="/kits-deco-ktm">ktm</a></li>
<li><a href="../../kits-deco-kawasaki" data-slug="/kits-deco-kawasaki">kawasaki</a></li>
<li><a href="../../kits-deco-husqvarna" data-slug="/kits-deco-husqvarna">husqvarna</a></li>
<li><a href="../../kits-deco-sherco" data-slug="/kits-deco-sherco">sherco</a></li>
<li><a href="../../kits-deco-gasgas" data-slug="/kits-deco-gasgas">gasgas</a></li>
<li>polaris</li>
<li>can-am</li>
<li>cf moto</li>
<li><a href="../../kits-deco-beta" data-slug="/kits-deco-beta">beta</a></li>
<li><a href="../../kits-deco-rieju" data-slug="/kits-deco-rieju">rieju</a></li>
<li><a href="../../kits-deco-fantic" data-slug="/kits-deco-fantic">fantic</a></li>
</ul>   
            </div>
                                                <div class="item-footer" style="grid-area:2/3">
                <p>Modes de paiement</p>
                <ul>
<li>cb</li>
<li>visa</li>
<li>mastercard</li>
<li>crédit agricole</li>
<li>paypal</li>
<li>american express</li>
</ul>   
            </div>
                                                <div class="item-footer" style="grid-area:2/4">
                <p>Informations</p>
                <ul>
<li><span class="obflink obf" data-obf="L3BhaWVtZW50LXNlY3VyaXNlfmMzLmh0bWw=">paiement sécurisé</span></li>
<li><a href="../../cgv~c17.html" data-slug="/cgv~c17.html">conditions générales de vente</a></li>
<li><a href="../../retour-et-remboursement~c27.html" data-slug="/retour-et-remboursement~c27.html">retour et remboursement</a></li>
<li><a href="../../mentions-legales~c15.html" data-slug="/mentions-legales~c15.html">mentions légales</a></li>
<li><a href="../../contact.html" data-slug="/contact.html">contact</a></li>
</ul>   
            </div>
                        </div>  
    <img class="logo-footer" src="https://www.kutvek-kitgraphik.com/img/charter/logo-footer.png" alt="">
</footer>
<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script> 
    <script>
        const dfLayerOptions = {
            installationId: 'ef4ab15c-fa64-4915-8a0e-fbd8e65db9f0',
            zone: 'eu1',
            currency: '<?= $this->getCurrencySymbol(); ?>'
        };

        (function (l, a, y, e, r, s) {
            r = l.createElement(a); r.onload = e; r.async = 1; r.src = y;
            s = l.getElementsByTagName(a)[0]; s.parentNode.insertBefore(r, s);
        })(document, 'script', 'https://cdn.doofinder.com/livelayer/1/js/loader.min.js', function () {
            doofinderLoader.load(dfLayerOptions);
        });
    </script>   	
</body>
</html>