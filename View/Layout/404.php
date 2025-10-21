<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="404">
<head>
	<meta charset="utf-8">	
	<!-- Google Tag Manager -->	
	<!-- End Google Tag Manager -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->getTitle(); ?> | Designs By KUTVEK </title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<style>
		.main {text-align: center;}
	</style>
</head>
<body>
	<?= $this->app->goneHeader($slugs); ?>
	
	<div>
		<?= $this->view; ?>
	</div>
	
	<?= $this->app->basicFooter(); ?>

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

	<?= $this->fetch('scriptBottom'); ?>
	<script src="<?= $this->auto_version('/js/app.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/cart.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/tabs.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/user.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/checkout.js'); ?>"></script>
	<?= $this->fetch('dedicatedScripts'); ?>
</body>
</html>