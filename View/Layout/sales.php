<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="section">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->getTitle(); ?></title>
	<meta name="description" content="<?= $this->getDescription();?>">
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/section.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/products.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	<?= $this->app->alternate($slugs); ?>
	<?= $this->fetch('css'); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	<style>
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
	<?= $this->app->mainHeader($slugs); ?>
	<main>
		<?= $this->view; ?>
	</main>
	<?= $this->app->mainFooter(); ?>
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
				<input name="color[]" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-design">
		<li>
			<div class="field-wrapper checkbox">
				<input name="design[]" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-vehicle">
		<li>
			<div class="field-wrapper checkbox">
				<input name="vehicle[]" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-family">
		<li>
			<div class="field-wrapper checkbox">
				<input name="family[]" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-brand">
		<li>
			<div class="field-wrapper checkbox">
				<input name="brand[]" type="checkbox" class="field-input onchange" value="" >
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-model">
		<li>
			<div class="field-wrapper checkbox">
				<input type="checkbox" name="model[]" class="field-input onchange" value="" >
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
	<?= $this->fetch('dedicatedScripts'); ?>
	<?= $this->fetch('scriptBottom'); ?>
</body>

</html>