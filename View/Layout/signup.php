<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-obf="<?= base64_encode($this->uri('identities.login',['queries' => ['r' => 'dashboard']])) ?>" data-layout="signup">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>KUTVEK| Checkout</title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/signup.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" media="screen">
	
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />  
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.css' type='text/css' />        
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300;1,500&amp;family=Oswald:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<?= $this->fetch('css'); ?>
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>	
</head>
<body>	
	<?= $this->app->mainHeader($slugs);?>	
	<div class="main-row">
		<?= $this->view; ?>
	</div>			
	<?= $this->app->mainFooter();?>
	
	<!-- Geocoder plugin -->
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.js'></script>
	<!-- Turf.js plugin -->
	<script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>	
	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>	
</body>
</html>