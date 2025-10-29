<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="graphic-kits">
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
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/cart-overview.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/product.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/options.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/gallery.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/modal.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/grid-layout.css'); ?>" type="text/css" media="screen">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<style></style>
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	
	<!-- End Google Tag Manager (noscript) -->
	<header class="primary-header">
	    <div class="message-bar">    	
	        <a href="https://www.youtube.com/user/kutvekkitgraphik" target="_blank"><img src="/img/pictos/youtube.png"></a>
	        <a href="https://www.facebook.com/Kutvek" target="_blank"><img src="/img/pictos/facebook.png"></a>
	        <a href="https://www.instagram.com/kutvek" target="_blank"><img src="/img/pictos/instagram.png"></a>
	    </div>
	    <div class="infos">
	        <div>
	            <div class="trust"></div>
	            <div class="action contact-us click" data-ctrl="app.contact">
	                <img src="/img/pictos/phone.png" class="picto phone" alt="">
	                <a href="tel:<?= $this->intlphone;?>"><span class="phone"><?=$this->phone; ?></span></a>
	            </div>
	        </div>
	        <div>
	            <span data-i18n="lang" class="choose-lang">Langue</span>
	            <div class="action trads-container">
	                <?= $this->l10ns(); ?>
	            </div>
	            <?= $this->widgetCountries(); ?>
	        </div>        
	    </div>
	    <div class="baseline">
	        <a href="<?= $this->url('page.homepage', []) ?>" class="logo"><img class="logo-kutvek" src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>" alt="Logo KUTVEK"></a>
	        <div class="red">
	            <img class="logo-footer" src="/img/charter/logo-footer.png" />
	            <?=$this->topNav();?>
	            
	            <!-- $this->topMenu(); -->   
	            <div class="user-actions">
	                <div class="action search" id="search-doofinder">                 
	                <span class="icon material-symbols-rounded"></span>
	                </div>
	                <span class="obflink pointer" id="user-name" data-i18n="login" data-obf="<?= base64_encode($this->url('customer.dashboard')) ?>"><span class="icon material-symbols-rounded account-circle">&#xe853;</span></span>
	                <div class="action contact-us">
	                    <label class="icon material-symbols-rounded" for="see-phone"></label>
	                    <input type="checkbox" id="see-phone" hidden />
	                    <a class="phone" href="tel:<?= $this->intlphone;?>"><span ><?=$this->phone; ?></span></a>
	                </div>   <!--  $this->widgetSlugs($this->getContent()->slugs); -->
	                </div>          
	                <?php $obf = base64_encode($this->url('cart.overview', ['queries'=>['id'=>':id']])); ?>
	                 <a id="shopping-cart" class="click shopping-cart" data-obf="<?=$obf;?>" data-count="0" href="#" data-ctrl="cart.overview"><span class="icon material-symbols-rounded">&#xe8cc;</span>  </a>
	                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
	            </div>
	        </div>
	    </div>  
	    <?= $this->megamenu(); ?>
	</header>
	<main>
		<?= $this->_content; ?>
	</main>	
	<aside id="cart-preview" class="cart-overview"></aside>
	<?= $this->footer(); ?>	
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script src="<?= $this->auto_version('/js/section.js') ?>"></script>
	<script>
		const dfLayerOptions = {
			installationId: 'ef4ab15c-fa64-4915-8a0e-fbd8e65db9f0',
			zone: 'eu1',
			currency: '<?= $this->getCurrencyCode(); ?>'
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