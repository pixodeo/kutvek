<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="cart" data-currency="<?=$currency_code;?>">
<head>
	<!-- Google Tag Manager -->	
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= $this->meta_title();?>
	<?= $this->meta_description();?>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/grid-layout.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/checkout.css'); ?>" media="screen">
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.css' rel='stylesheet' />  
	<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.css' type='text/css' /> 	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>	
</head>
<body>	
	<?= $this->_content; ?>	
	<!-- Google Tag Manager (noscript) -->	
	<!-- End Google Tag Manager (noscript) -->	
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script type="module" src="<?= $this->auto_version('/js/modules/delivery.js') ?>"></script>
	<!-- Geocoder plugin -->	
	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v2.9.2/mapbox-gl.js'></script>
	<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v2.0.1/mapbox-gl-geocoder.js'></script>
	<!-- Turf.js plugin -->
	<script src='https://npmcdn.com/@turf/turf/turf.min.js'></script>
	<template id="listing-tpl">
	    <div class="item" id="listing-6">
	    	<div id="">
	    		<a href="#" class="title"></a>
	    		<p class="line-1"></p>
	        	<p class="city"></p>
	    	</div>	        
	        <div class="opening">            
	        </div>
	        <div class="details">
	        	<input type="radio" name="delivery[cost]" data-ctrl="delivery.setAddress" value="0" id="" class="onchange" data-address="" required form="checkout-next" /> - <b>Gratuit</b>
	        </div>
	    </div>
	</template>
</body>	
</html>