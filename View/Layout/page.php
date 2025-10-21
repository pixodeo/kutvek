<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="page">
<head>
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $this->getTitle(); ?></title>
	<meta name="description" content="<?= $this->getDescription();?>">	
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<?=$this->app->canonical();?>
	<?= $this->app->alternate($slugs); ?>
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
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
</head>

<style>
	.payment-logo { display: flex; justify-content: center;	}
	.payment-logo img { height: 6.4rem; margin: 3.2rem 0; }
	.bloc-cb img { height: 3.2rem; margin: 1.6rem 0; }
</style>

<body class="lv2">
	<?= $this->app->mainHeader($slugs); ?>
	<div class="main-row">
		<?= $this->view ?? $this->_content; ?>
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
	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>
</body>

</html>