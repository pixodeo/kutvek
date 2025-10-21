<!DOCTYPE html>
<html lang="<?= $this->app->getLang(); ?>" data-layout="product">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow, noarchive">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Housse de selle personnalisée |Designs By KUTVEK  </title>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/tabs.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/fields-v2.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/front.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/header.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/footer.css'); ?>" type="text/css" media="screen">
	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<?= $this->fetch('css'); ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
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

		.load {animation: 1s cubic-bezier(.36,.07,.57,.99) infinite load_rotate;}
		@keyframes load_rotate {
		  from {
		     transform: rotate(0deg);
		  }
		  to {
		     transform: rotate(360deg);
		  }
		}
		.banner {
		    height: 32rem;
		    margin-top: 3.2rem;
		    text-align: center;
		}
		.banner img {
		    height: 32rem;
		    width: 100%;
		    object-fit: cover;
		}
		.section-title {
		    font-family: 'Oswald';
		    text-transform: uppercase;
		    font-weight: 600;
		    font-size: 3.2rem;
		}
		.short-description {
		    font-size: 1.6rem;
		    font-family: 'Montserrat';
		    margin: 0 0 0.8rem;
		}

		.row.vehicle .field-wrapper {margin-right: .8rem;}
		.custom-bloc {
		    padding: 1.6rem;
		    border: 1px solid rgba(0,0,0,.67);
		    border-radius: 0.4rem;
		    margin-bottom: 3.2rem;
		    margin-top:3.2rem;
		    opacity: 1;
		    transition: opacity .6s;
		}
		.custom-bloc > .header {
			width:100%;
		    text-transform: uppercase;
		    font-size: 2.4rem;
		    font-family: 'Oswald';
		    font-weight: 300;
		    border-bottom: 1px inset rgba(0,0,0,.67);		   
		    line-height: 1;
		    margin-bottom: 1.6rem;
    		margin-top: 2.8rem;
    		padding-bottom:.4rem;
		}
		.custom-bloc > .header span:first-of-type {
    		color: red;    		
    		padding-right: .8rem;
		}

		.custom-bloc.disabled {
			opacity: .3;
		}

		.custom-bloc.disabled:hover {
			cursor: not-allowed;
		}

		.opt-list > li {height: 4.8rem;}

		.color-element {height: 4rem;}
		.color-element.red {
			background-color:#ff0000; color: #ffffff;
		}
		.color-element.green {
			background-color:#57a839; color: #ffffff;
		}
		.color-element.white {
			background-color:#ffffff; color: black; border-color:#1d1d1b;
		}
		.color-element.black {
			background-color:#000000; color: #FFFFFF;
		}
		.color-element.orange {
			background-color:#E8501A; color: #FFFFFF;
		}
		.color-element.yellow {
			background-color:#ffea0c; color: #000000;
		}
		.color-element.grey {
			background-color:#839292; color: #ffffff;
		}
		.color-element.cyan {
			background-color:#00b3e9; color: #ffffff;
		}
		.color-element.yamaha-blue {
			background-color:#0e3d89; color: #ffffff;
		}
		.color-element.husqvarna-blue {
			background-color:#102033; color: #ffffff;
		}	

		.designation {
			display: flex;
			align-items: baseline;
			justify-content: space-between;
			font-family: Oswald;
			
    		
    		margin-bottom: 2.4rem;
		}

		.designation small {
			font-weight: 500;
			font-size: 1.4rem;
			font-family: Montserrat;
			padding-left:.8rem;
		}

		#p-cart .header {
		    font-weight: 500;
		    font-size: 2.4rem;
		    text-transform: uppercase;
		    color: #212121;
		    font-family: 'Oswald';
		    text-align: right;
		    padding-bottom: 1.6rem;
		}

		span.total {
			font-size: 2.4rem;
		}


		@media only screen and (min-width: 1024px){
			.custom-bloc > .header {font-size: 3.6rem;}
			.shopping-cart {
    			padding-left: 4.8rem;
    			padding-top:3.2rem;
			}
			#p-cart{position: sticky;
			    top: 3.2rem;
			    background-color: #FAFAFA;
			    margin-right: 3.2rem;
			    padding-left: 1.6rem;
			    padding-right: 0.8rem;
			    padding-bottom: 1.6rem;
			}
		}
	</style>
</head>
<body>
	
	<?= $this->app->mainHeader($slugs); ?>
	
	<div class="main-row">
		<?= $this->view; ?>
	</div>

	<?= $this->app->mainFooter(); ?>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>		
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
	        	<div  class="col-s-12 col-m-6 col-m-push-1">
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
										<div class="field-wrapper"><label class="required" for="write-promocode">Saisir le code</label><input name="code" id="write-promocode" type="text" class="field-input" required="" data-i18n="write-promocode"></div>								</div>
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