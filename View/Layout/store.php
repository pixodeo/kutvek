<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="store">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= $this->app->canonical(); ?>
	<?= $this->app->alternate($slugs); ?>
	<title><?=$this->getTitle();?> | Designs By KUTVEK </title>
	<meta name="description" content="<?=$this->getDescription();?>">

	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/products.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/store.css'); ?>" type="text/css" media="screen">
	<?= $this->fetch('css'); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>	
</head>
<body class="lv2">
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?= $this->app->mainHeader($slugs); ?>
	<div class="main-row">
		<?= $this->view; ?>
	</div>
	<?= $this->app->mainFooter(); ?>
	<template id="item-template">
		<div class="item">
			<img src="" alt="">
			<div>
				<span class="item-desc"></span>
				<div><span class="item-quantity"></span><span class="item-price"></span></div>
			</div>
			<a class="detete-item click" href="" data-ctrl="cart.removeItem">
				<span class="material-symbols-rounded">&#xe872;</span>
			</a>
		</div>
	</template>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>	
	<script>
		const dfLayerOptions = {
			installationId: 'ef4ab15c-fa64-4915-8a0e-fbd8e65db9f0',
			zone: 'eu1',
			currency: '<?= $this->app->getCurrency(); ?>'
		};

		(function (l, a, y, e, r, s) {
			r = l.createElement(a); r.onload = e; r.async = 1; r.src = y;
			s = l.getElementsByTagName(a)[0]; s.parentNode.insertBefore(r, s);
		})(document, 'script', 'https://cdn.doofinder.com/livelayer/1/js/loader.min.js', function () {
			doofinderLoader.load(dfLayerOptions);
		});
	</script>	
	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>
</body>

</html>