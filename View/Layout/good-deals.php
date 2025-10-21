<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="good-deals">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Designs By KUTVEK </title>

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
				<input name="color[]" type="checkbox" class="field-input onchange" value="" data-i18n="color">
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-design">
		<li>
			<div class="field-wrapper checkbox">
				<input name="design[]" type="checkbox" class="field-input onchange" value="" data-i18n="design">
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-vehicle">
		<li>
			<div class="field-wrapper checkbox">
				<input name="vehicle[]" type="checkbox" class="field-input onchange" value="" data-i18n="vehicle">
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-family">
		<li>
			<div class="field-wrapper checkbox">
				<input name="family[]" type="checkbox" class="field-input onchange" value="" data-i18n="family">
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="filter-brand">
		<li>
			<div class="field-wrapper checkbox">
				<input name="brand[]" type="checkbox" class="field-input onchange" value="" data-i18n="brand">
				<label class="checkbox no-colon"></label>
			</div>
		</li>
	</template>
	<template id="card-tpl">
		<figure class="product-item">
			<img loading="lazy" class="visual" />	
			<hr>	
			<figcaption>			
				<p class="item"></p>
				<p class="prices">
					<span class="price stroke"></span>
					<span class="price sale"></span>
				</p>
			</figcaption>
			<a></a>
		</figure>
	</template>
	<?php foreach (['vehicles', 'families', 'designs', 'colors', 'brands'] as $filter): ?>
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
	<script src="<?= $this->auto_version('/js/front/filter-good-deals.js') ?>"></script>	
	<?= $this->fetch('dedicatedScripts'); ?>
	<?= $this->fetch('scriptBottom'); ?>
</body>

</html>