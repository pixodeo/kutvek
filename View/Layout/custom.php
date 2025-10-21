<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="custom">
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
	<title><?= $this->getTitle(); ?> | Designs By KUTVEK </title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/cart.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/product-page.css'); ?>" type="text/css" media="screen">

	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<?= $this->fetch('css'); ?>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital@0;1&amp;Kalam:wght@700&amp;family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,500;1,600&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async=""></script>
	<!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	<style>
		/*.material-symbols-rounded {
			font-variation-settings:
				'FILL' 0,
				'wght' 400,
				'GRAD' 0,
				'opsz' 48
		}*/
		.account-circle {
			font-variation-settings:
				'FILL' 1
		}

		.load {
			animation: 1s cubic-bezier(.36, .07, .57, .99) infinite load_rotate;
		}

		@keyframes load_rotate {
			from {
				transform: rotate(0deg);
			}

			to {
				transform: rotate(360deg);
			}
		}

		

		.custom > img {
			width: 100%;
		}

		.custom > a {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.custom .custom-kit-button {
			font-family: 'Oswald';
			position: absolute;
			font-size: 2.4rem;
			height: 7.2rem !important;
			border-bottom-right-radius: 25%;
			border-top-left-radius: 25%;
			background-color: red;
			color: white;
		}

		

		p.total {text-align: right;font-weight: bold;}
		p.total + form {text-align: center;}

		.graphic-kit {
			display: flex;
			justify-content: center;
			margin: 3.2rem 0;
		}
		
		.graphic-kit > a {
			position: relative;
			display: flex;
			align-items: center;
		}
		
		.graphic-kit > a > p.title {
			position: absolute;
			color: white;
			font-size: 3.2rem;
			font-family: 'Oswald';
			padding-left: 3.2rem;
		}

		img.cover {
			display: block;
			margin: auto;
		}

		.bloc-infos {
			border-left: 0.2rem solid #ff0000;
			margin: 2.4rem 0 1.6rem 0.8rem;
			padding: 1.2rem 0 1.2rem 0.8rem;
		}

		.info-plastics {
			background-color: #c8dadf;
			color: black;
			font-family: 'din_procondensed_bold';
			letter-spacing: .5px;
			display: inline-flex;
			align-items: center;
			padding: 0.4rem 0.8rem;
			border-radius: 0.2rem;
			margin-top: 0.8rem;
			line-height: 1.6rem;
		}

		.info-plastics .icon {
			padding-right: 0.4rem;
		}

		.main-row {
			margin: 3.2rem 0 3.2rem 0;
		}

		@media only screen and (min-width: 1024px) {
			.graphic-kit > a > p.title {
				font-size: 8rem;
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
	<?= $this->app->mainHeader($slugs); ?>
	<div class="main-row row">
		<?= $this->view; ?>
	</div>

	<?= $this->app->mainFooter(); ?>
	<script src="<?= $this->auto_version('/js/app.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/locale.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/cart.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/vehicle.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/user.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/tabs.js'); ?>"></script>
	<script src="<?= $this->auto_version('/js/front/utils.js'); ?>"></script>
	<?= $this->fetch('scriptBottom'); ?>
	<?= $this->fetch('dedicatedScripts'); ?>
	<aside id="cart-preview">
		<div id="empty-cart" class="close">
			<span data-i18n="empty-cart">Aucun article dans le panier</span>
			<a href="" class="click" data-ctrl="cart.closeOverview">
				<span class="icon material-symbols-rounded">close</span>
			</a>
		</div>
		<div id="cart-filled">
			<div class="close">
				<a href="" class="click" data-ctrl="cart.closeOverview">
					<span class="icon material-symbols-rounded">close</span>
				</a>
			</div>
			<div class="row">
				<div class="col-s-12 col-m-6 col-m-push-1">
					<h5 data-i18n="my-cart" class="titles">Mon panier</h5>
					<div id="items"></div>
				</div>
				<div id="checkout" class="col-s-12 col-m-5">
					<h5 class="titles sub-total"><span data-i18n="sub-total">Sous-total</span><span id="item-total"></span></h5>
					<p class="titles sub-total"><small data-i18n="delivery">Livraison</small><small id="shipping-amount"></small></p>
					<div class="accordion_tabs">
						<input type="checkbox" id="apply-voucher">
						<label data-i18n="voucher-or-gift-card" class="titles link" for="apply-voucher">Code promo ou bon d'achat</label>
						<div>
							<ul class="tabs">
								<li class="active"><a href="#voucher"><span class="icon material-symbols-rounded voucher">label</span><span data-i18n="voucher">Code promo</span></a></li>
								<li class="hide"><a href="#gift-card"><span class="icon material-symbols-rounded">redeem</span><span data-i18n="gift-card">Bon d'achat / Carte cadeau</span></a></li>
							</ul>
							<div class="tabs_content">
								<div class="tab_content active" id="voucher">
									<form action="" method="post" class="row i-center">
										<div class="col-s-12 col-l-4">
											<div class="field-wrapper"><label class="required" for="write-promocode">Saisir le code</label><input name="code" id="write-promocode" type="text" class="field-input" required="" data-i18n="write-promocode"></div>
										</div>
										<div class="col-s-12 col-l-6">
											<button type="submit" class="contained dark apply" form="form-voucher" data-i18n="apply-voucher">Appliquer le code</button>
											<button type="submit" class="contained warning delete hide" disabled="" form="form-voucher" data-i18n="supprimer">Supprimer</button>

										</div>
									</form>
								</div>
								<div class="tab_content" id="gift-card">
									<a href="/cart/test/12354/testPut" class="click" data-ctrl="cart.testPut">test carte cadeau</a>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<h5 class="titles sub-total"><span data-i18n="total">Total</span><span id="total-to-pay"></span></h5>
					<div class="row">
						<div class="col-s-12 col-m-6">
							<a href="/" class="btn outlined white wide" data-i18n="shop">Continuer mes achats</a>
						</div>
						<div class="col-s-12 col-m-6">
							<span data-obf="<?= base64_encode($this->uri('identities.signin')) ?>" class="btn contained dark wide">
								<span data-obf="<?= base64_encode($this->uri('orders.checkout', [], 'GET')); ?>" class="btn click contained dark" data-i18n="pay" data-ctrl="checkout.checkout">Paiement</span>
							</span>
						</div>
					</div>
					<div class="tranquility">
						<div class="accordion_tabs">
							<p data-i18n="billing-address" class="titles">Expédition en 3 à 5 jours ouvrés</p>
							<input type="checkbox" id="shipping-info">
							<label for="shipping-info" class="pointer"><span class="material-symbols-rounded"></span></label>
							<div>
								<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Molestiae aliquid placeat, natus at fuga ab sunt provident delectus rerum perferendis. Ab fugiat iure ducimus unde optio laborum enim aspernatur distinctio!</p>
								<p>Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Maiores accusamus nulla fuga. Eveniet, reprehenderit voluptas cumque id quisquam mollitia minus quis exercitationem facere nihil consequatur praesentium, at blanditiis corrupti corporis!</p>
								<div class="delivery-method chrono-classic hide">
									<p>Méthode de livraison <small>CHRONO Classic</small></p>
									<p><span>Coût</span> <small class="cost"></small></p>
									<p><span>Délai de livraison</span> 3 jours ouvrables</p>
								</div>
								<div class="delivery-method chrono-express hide">
									<p>Méthode de livraison <small>CHRONO Express</small></p>
									<p><span>Coût</span> <small class="cost"></small></p>
									<p><span>Délai de livraison</span> 1 jours ouvrable</p>
								</div>
								<div class="delivery-method chrono-relay hide">
									<p>Méthode de livraison <small>CHRONO Relay</small></p>
									<p><span>Coût</span> <small class="cost"></small></p>
									<p><span>Délai de livraison</span> 3 jours ouvrables</p>
								</div>
							</div>
						</div>
						<div class="accordion_tabs">
							<p data-i18n="billing-address" class="titles">Retour gratuits sous 14 jours ouvrés</p>
							<input type="checkbox" id="return-policy">
							<label for="return-policy" class="pointer"><span class="material-symbols-rounded"></span></label>
							<div>
								<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Molestiae aliquid placeat, natus at fuga ab sunt provident delectus rerum perferendis. Ab fugiat iure ducimus unde optio laborum enim aspernatur distinctio!</p>
								<p>Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Maiores accusamus nulla fuga. Eveniet, reprehenderit voluptas cumque id quisquam mollitia minus quis exercitationem facere nihil consequatur praesentium, at blanditiis corrupti corporis!</p>
								<p>Consultez cette page pour en savoir plus sur notre <a href="#" class="link">politique de retour</a>.</p>
							</div>
						</div>
						<div class="accordion_tabs">
							<p data-i18n="billing-address" class="titles">paiements sécurisés</p>
							<input type="checkbox" id="pay-secure">
							<label for="pay-secure" class="pointer"><span class="material-symbols-rounded"></span></label>
							<div>
								<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Molestiae aliquid placeat, natus at fuga ab sunt provident delectus rerum perferendis. Ab fugiat iure ducimus unde optio laborum enim aspernatur distinctio!</p>
								<p>Lorem ipsum, dolor sit amet consectetur, adipisicing elit. Maiores accusamus nulla fuga. Eveniet, reprehenderit voluptas cumque id quisquam mollitia minus quis exercitationem facere nihil consequatur praesentium, at blanditiis corrupti corporis!</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="our-terms">Pour en savoir plus sur nos conditions d’achat, <a href="#" class="link">cliquez ici</a>.</div>
		</div>
	</aside>
</body>

</html>