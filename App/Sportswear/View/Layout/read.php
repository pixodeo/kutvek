<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="graphics-section">
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
	<!-- <link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css') ?>" type="text/css" media="screen"> -->
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/product-page.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css');?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/gallery.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">	
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<style>
		.designation {text-align: justify;max-width: 68rem;margin: 0 auto 2.4rem;}		
		.suitable-for {list-style: none;list-style-type: none;}
		.prices {display: block;margin:0;text-align: right;}
		.prices .old {
			text-decoration-color: #000000;
		    margin-right: 1.2rem;
		    text-decoration-line: line-through;
		    text-decoration-thickness: .16rem;
		    font-weight: 600;
		    opacity: .6;
		}
		.prices .new {font-weight: 600;}
		.cart-action {display: flex;align-items: center;justify-content: center;margin-top:1.6rem;}
		.cart-action button {border-radius: .6rem !important;height: 4.8rem !important;font-size: 1.6rem !important;}
		.cart-action button:disabled {cursor:not-allowed;}
		.gallery {
			max-width: 680px;
    		max-height: 510px;
    		margin: auto;
		}
		.thumbnails {text-align: center;margin-right: 0;margin-bottom: 3.2rem;margin-top: 0;}
		.thumbnails .thumbnail {position: relative; width: 72px;height: auto;}
		div.options > p {margin:0;}
		div.header{margin-top: 3.6rem;}
		.load {animation: 1s cubic-bezier(.36,.07,.57,.99) infinite load_rotate;opacity: 1 !important;margin-right: .8rem;}
		.load.hidden {text-indent: -100px; margin-right:0;}
		@keyframes load_rotate {
		  from {
		     transform: rotate(0deg);
		  }
		  to {
		     transform: rotate(360deg);
		  }
		}	
		.discounts {margin-bottom:1.6rem;}
		.sub-total > small {font-size: .8em;}
		.amount.negative {color: #ff0000;font-weight: bold;}

		.negative::before {content: '- ';}

		@media only screen and (min-width: 1280px) {
			.filters-modal {display: none;}
		}
	</style>
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-58SGQCK" height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
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
        <a href="<?= $this->uri('pages.index', []) ?>" class="logo"><img class="logo-kutvek" src="<?= HALLOWEEN === 1 ? '/img/charter/logo_kutvek_orange.png' : '/img/charter/logo_kutvek.png';?>" alt="Logo KUTVEK"></a>
        <div class="red">
            <img class="logo-footer" src="/img/charter/logo-footer.png" />
            <!-- $this->topMenu(); -->   
            <div class="user-actions">
                <div class="action search" id="search-doofinder">                 
                <span class="icon material-symbols-rounded"></span>
                </div>
                <span data-obf="<?= base64_encode($this->uri('identities.login')) ?>" class="action user-account">
                    <span class="click obf" id="user-name" data-i18n="login" data-obf="<?= base64_encode($this->uri('customers.dashboard')) ?>" data-ctrl="user.dashboard"><span class="icon material-symbols-rounded account-circle">&#xe853;</span></span>
                </span>
                <div class="action contact-us">
                    <label class="icon material-symbols-rounded" for="see-phone"></label>
                    <input type="checkbox" id="see-phone" hidden />
                    <a class="phone" href="tel:<?= $this->intlphone;?>"><span ><?=$this->phone; ?></span></a>
                </div>   <!--  $this->widgetSlugs($this->getContent()->slugs); -->
                </div>          
                <?php $obf = $this->uri('cart.read', ['queries'=>['order'=>':order']]); ?>
                <span id="cart-btn" class="cart action click" data-ctrl="cart.read" data-obf="<?= base64_encode($obf) ?>">
                    <span class="icon material-symbols-rounded">&#xe8cc;</span>
                    <span class="counter"><span id="nbItems"></span></span>
                </span>
                <a href="#" class="menu click" data-ctrl="app.menu" data-target="main-nav"><span class="icon material-symbols-rounded">&#xe5d2;</span></a>
            </div>
        </div>
    </div>  
    <?= $this->megamenu(); ?>
</header>
	<main>
		<?= $this->_content; ?>
	</main>
	<!-- $this->footer(); -->
	
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