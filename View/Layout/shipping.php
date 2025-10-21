<!doctype html>
<html lang="<?= $this->app->getLang(); ?>" data-obf="<?= base64_encode($this->uri('identities.login',['queries' => ['r' => 'dashboard']])) ?>" data-layout="checkout">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>KUTVEK| Checkout</title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/checkout.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/page-locator.css'); ?>" media="screen">
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />  
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.css' type='text/css' />        
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id=<?= $paypal->getClientID(); ?>&enable-funding=paylater&currency=<?= $paypal->currency(); ?>" data-client-token="<?= $paypal->getClientToken(); ?>"></script>	
	<style>
		.trads-container > ul > li.current > a { pointer-events: none;}
		div.contact {margin-top:1.6rem;}
		div.contact > p {display: flex; justify-content: space-between; align-items: center;}
		div.contact > p > span:first-child {font-family: 'Oswald'; font-size: 2rem;}
		.bloc-items > div {flex-direction: row; align-items: center;}
		.bloc-items > div > img {max-width: 12rem; order:1;}
		span.label {font-family: 'Oswald';   font-size: 1.8rem;}
		span.label + span {   font-size: 1.4rem;}
		.card-form {justify-content: initial;}
		.card-form label {
			font-family: 'Oswald';    
			text-transform: uppercase;
			display: block;
			margin-top: 0.8rem;
			letter-spacing: .2px;
		}
		.card_field {
			display: flex;
			margin:0;
			padding: 0 0 0 0.8rem;
			border: 1px solid #000000;
			border-radius: 0.1rem;
			box-sizing: border-box;
			resize: vertical;
			height: 4rem;
			background: white;
			color: #3a3a3a;
			outline: none;
		}
		.btn.paypal {margin-top: 1.6rem; border-radius: .1rem;}
		iframe.component-frame .paypal-button {border-radius:.1rem !important}	
	</style>
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->
	<?= $this->app->checkoutHeader($slugs);?>	
	<section id="order" data-obf="L21vbi1wYW5pZXI=">		
		<?= $this->view; ?>			
	</section>
	<?= $this->app->checkoutFooter($countries);?>
	<!-- Geocoder plugin -->
	<script type="module" src="<?= $this->auto_version('/js/shipping.js') ?>"></script>
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.js'></script>
	<!-- Turf.js plugin -->
	<script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>	
	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>	
</body>
</html>