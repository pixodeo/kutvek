<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="cart">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
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
	<link rel="stylesheet" href="<?= $this->auto_version('/css/cart.css');?>" type="text/css" media="screen">	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>	
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->
	
	
	<?= $this->_content; ?>

	
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script src="<?= $this->auto_version('/js/section.js') ?>"></script>
	<script>
		const dfLayerOptions = {
			installationId: 'ef4ab15c-fa64-4915-8a0e-fbd8e65db9f0',
			zone: 'eu1',
			currency: '<?= $this->getCurrency(); ?>'
		};
		(function (l, a, y, e, r, s) {
			r = l.createElement(a); r.onload = e; r.async = 1; r.src = y;
			s = l.getElementsByTagName(a)[0]; s.parentNode.insertBefore(r, s);
		})(document, 'script', 'https://cdn.doofinder.com/livelayer/1/js/loader.min.js', function () {
			doofinderLoader.load(dfLayerOptions);
		});
	</script>	
	<!-- $this->fetch('dedicatedScripts'); -->
	<!-- $this->fetch('scriptBottom'); --> 

</body>
</html>