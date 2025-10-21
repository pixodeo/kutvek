<!doctype html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="product">
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
	<title><?= $this->getTitle(); ?></title>
	<meta name="description" content="<?= $this->getDescription();?>">	
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/product-page.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
	<?= $this->fetch('css'); ?>
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	
	<style>
		.load {animation: 1s cubic-bezier(.36,.07,.57,.99) infinite load_rotate;}
		@keyframes load_rotate {
		  from {
		     transform: rotate(0deg);
		  }
		  to {
		     transform: rotate(360deg);
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
	<?= $this->app->productHeader($slugs); ?>	
	<?= $this->view; ?>	
	<?= $this->app->productFooter(); ?>
	<script src="<?= $this->auto_version('/js/app.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/cart.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/locale.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/user.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/tabs.js'); ?>"></script>	
	<script src="<?= $this->auto_version('/js/analytics/ga4-events.js'); ?>"></script>	
	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>
	<?php if(isset($_GET['debug'])): ?>
	<pre><?php print_r($slugs)?></pre>
	<?php endif; ?>
</body>
</html>