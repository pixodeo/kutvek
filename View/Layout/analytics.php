<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-obf="<?= base64_encode($this->uri('identities.login',['queries' => ['r' => 'dashboard']])) ?>" data-layout="checkout">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-56HDCDCV');</script>
	<!-- End Google Tag Manager -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Testing GA4 Purchase Event</title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/checkout.css'); ?>" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/page-locator.css'); ?>" media="screen">	

	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />  
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.css' type='text/css' />        
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital@0;1&amp;Kalam:wght@700&amp;family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,500;1,600&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<?= $this->fetch('css'); ?>
	
	
	<!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	<style>
		.material-symbols-rounded {
			font-variation-settings:
				'FILL' 0,
				'wght' 400,
				'GRAD' 0,
				'opsz' 48
		}
		.account-circle {
			font-variation-settings:
			'FILL' 1
		}
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
		div.contact-us {
			display: inline-flex;
			align-items: center;
			margin-right: 0.8rem;
		}
		span.phone {
			font-family: 'din_promedium';
			padding: 0 0.4rem;
		}
		.test-analytics {

			display: flex;
    		align-items: center;
    		justify-content: center;
    		height: 100vh;
    		width: 100vw;
		}
	</style>
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-56HDCDCV"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	<section class="test-analytics">		
		<?= $this->view; ?>	
		
	</section>	
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	
	
	
</body>
</html>