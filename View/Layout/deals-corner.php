<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="deals-corner">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= $this->meta_title();?>
	<?= $this->meta_description();?>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<?=$this->canonical();?>
	<?=$this->alternate($this->editorial->l10ns);?>
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css');?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/gallery.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<style>
		.card {position: relative;}
		.card a {position: absolute; top:0; right:0; bottom: 0; left: 0;}
		.card > div {padding:1.2rem;}
		.card img {background-color: #eee; height: auto;}
		.card .designation {font-size: 1.6rem;}
		.card .prices {/*text-align: right;*/font-size: 1.4rem;}
		.prices .old {
			text-decoration-color: #ff0000;    
    		margin-right: 1.2rem;
    		text-decoration-line: line-through;
    		text-decoration-thickness: .2rem;
		}
		.prices .new {font-weight: 600;}
		@media only screen and (min-width: 1280px) {
			.filters-modal {
				display: none;
			}
		}
	</style>
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->
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
                <?= $this->l10ns($this->editorial->l10ns); ?>
            </div>
            <?= $this->widgetCountries(); ?>
        </div>        
    </div>
    <div class="baseline">
        <a href="<?= $this->uri('pages.index', []) ?>" class="logo"><img class="logo-kutvek" src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>" alt="Logo KUTVEK"></a>
        <div class="red">
            <img class="logo-footer" src="/img/charter/logo-footer.png" />
            <?= $this->topNav(); ?>   
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
                <span id="cart-btn" data-count="0" class="cart action click" data-ctrl="cart.read" data-obf="<?= base64_encode($obf) ?>">
                    <span class="icon material-symbols-rounded">&#xe8cc;</span>                   
                </span>
                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
            </div>
        </div>
    </div>  
    <?= $this->megamenu();?>
</header>
	<main>
		<?= $this->_content; ?>
	</main>
	<?= $this->footer(); ?>
	<template id="item-template">
		<div class="item">
			<img src="" alt="">
			<div>
				<span class="item-desc"></span>
				<div><span class="item-quantity"></span><span class="item-price"></span></div>
			</div>
			<a class="detete-item click" href="" data-ctrl="cart.removeItem"><span class="material-symbols-rounded">delete</span></a>
		</div>
	</template>
	<template id="filter-color">
		<li>
			<div class="field-wrapper checkbox">
				<input name="color" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-design">
		<li>
			<div class="field-wrapper checkbox">
				<input name="design" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-vehicle">
		<li>
			<div class="field-wrapper checkbox">
				<input name="vehicle" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-family">
		<li>
			<div class="field-wrapper checkbox">
				<input name="family" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-brand">
		<li>
			<div class="field-wrapper checkbox">
				<input name="brand" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-model">
		<li>
			<div class="field-wrapper checkbox">
				<input type="checkbox" name="model" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<?php foreach (['vehicles', 'families', 'designs', 'colors', 'brands', 'models'] as $filter): ?>
		<aside class="modal click filters-modal" id="<?= $filter; ?>" data-modal="<?= $filter; ?>" data-ctrl="app.modal">
			<div class="popup mx-w45 widget-filter-modal">
				<header class="close">
					<p class="title" data-i18n="<?= $filter; ?>"><?= $filter; ?></p>
					<a href="#<?= $filter; ?>" class="click" data-modal="<?= $filter; ?>" data-ctrl="app.modal"><span class="icon material-symbols-rounded">close</span></a>
				</header>
				<div>
					<ul></ul>
				</div>
				<div class="btns-group btns-popup">
					<a href="#<?= $filter; ?>" data-modal="<?= $filter; ?>" data-ctrl="app.modal" class="click btn contained dark" data-i18n="close">Fermer</a>
				</div>
			</div>
		</aside>
	<?php endforeach; ?>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script src="<?= $this->auto_version('/js/section.js') ?>"></script>
	<script>
		const dfLayerOptions = {
			installationId: 'ef4ab15c-fa64-4915-8a0e-fbd8e65db9f0',
			zone: 'eu1',
			currency: '<?= $this->getCurrency(); ?>'
		};
		(function (l, a, y, e, r, s) {
			r = l.createElement(a); r.onload = e; r.async = 1; r.src = y;
			s = l.getElementsByTagName(a)[0]; s.parentNode.insertBefore(r, s);
		})(document, 'script', 'https://cdn.doofinder.com/livelayer/1/js/loader.min.js', function () {
			doofinderLoader.load(dfLayerOptions);
		});
	</script>	
	<!-- $this->fetch('dedicatedScripts'); -->
	<!-- $this->fetch('scriptBottom'); --> 

</body>
</html>