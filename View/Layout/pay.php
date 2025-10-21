<!DOCTYPE html>
<html lang="<?=$this->getLang();?>" data-layout="pay">
<head>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-58SGQCK');</script>
	<!-- End Google Tag Manager -->
	<meta charset="utf-8">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">	
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/app.css') ?>" type="text/css" media="screen">	
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
	<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id=<?= $paypal->getClientID(); ?>&enable-funding=paylater&currency=<?= $paypal->currency(); ?>" data-client-token="<?= $paypal->getClientToken(); ?>"></script>
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
		#items {font-size: 1.4rem;}
		#cart-filled img {max-width: 100%;}
		#cart-filled img.product {
    		max-width: 8rem;
    		margin-right: .4rem;
		}	
		.payment-items.paypal{text-align: center;}
		#paypal-button-container {display: inline-flex;width: 28rem;}

		.discounts {margin-bottom:1.6rem;}
		/*.sub-total > small {font-size: .8em;}*/
		.amount.negative {color: #ff0000;font-weight: bold;}
		.bloc-address, .bloc-discounts {padding: 0 1.2rem 1.2rem !important; font-size: 1.4rem;}
    	.address-head {font-weight: 600;}
		.negative::before {content: '- ';}
		span.item-price{display: block;padding-top: .4rem;font-weight: 600;}
		h2.titles {text-transform: uppercase;font-size: 2.4rem;}
		h2.titles + p {margin-bottom:2.4rem;}
		h3.titles {display: inline-flex; align-items: center;}
		h3.titles .icon {font-size: 3.2rem; margin-right: 1.2rem;}
		h3.titles .icon + span {font-size: 2rem;}
		.accordion_tabs{background-color: #ffffff; padding: .8rem .4rem .8rem 1.2rem; border-radius: .6rem;}
		.accordion_tabs:not(:last-of-type):not(.hide){margin-bottom: 1.6rem;}
		.accordion_tabs > p:first-of-type {margin:0;}
		.accordion-title {font-size: 1.6rem;font-family: 'Montserrat';font-weight: 600;}
		.accordion-title.pay {display: flex;flex: 1;justify-content: flex-end; padding-right: .8rem;}
		.accordion-title.pay > span {margin: 0 auto 0 0; }
		.sub-total {display: flex; align-items: center;justify-content: space-between;}
		.sub-total span.titles {font-family: Oswald;text-transform: uppercase;}
		.card-form {padding: 1.6rem;}
		.card-form label {display: block;margin-top: 1.6rem;margin-bottom: .4rem;font-weight: 500; font-size: 1.4rem;}
		.card_field {
		    display: flex;
		    margin: 0;
		    padding: 0 0 0 0.8rem;
		    border: 1px solid #000000;
		    border-radius: .4rem;
		    box-sizing: border-box;
		    resize: vertical;
		    height: 4.8rem;
		    background-color: #FFFFFF;
		    color: #3a3a3a;
		    outline: none;
		    font-size: 1.6rem;
		}
		.btn.paypal {max-width: 28rem;margin-top: 2.4rem;}
    	@media only screen and (min-width: 1024px) {
			#cart-filled {background: linear-gradient(to right, #E0E0E0 58%, #FFFFFF 58% 100%);}
			#checkout {padding-left:3.6rem;}
			.card-form {padding: 1.6rem 5.6rem 3.6rem;}
		}
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
</header>
	<main>
		<?= $this->_content; ?>
	</main>
	<!-- $this->footer(); -->
	
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script src="<?= $this->auto_version('/js/section.js') ?>"></script>
	<script>	   		
		let orderId;
		const storage = JSON.parse(localStorage.getItem('cart'));
		const thanksUrl = document.getElementById('thanks').value;
		const xsrfToken = localStorage.getItem('xsrfToken');		
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		let lang = document.documentElement.lang;
		paypal.Buttons({
		   	style: {
		     	layout: 'vertical'
		   	},
		   	createOrder: function () { 
		        return fetch('/checkout/paypal/purchase/' + storage.id, {
		            method: 'GET'
		        }).then(function(res) {
		            	return res.json();
		        }).then(function(orderData) {
		        	//console.log('resultat de orders dans action');
		        	//console.log(orderData)		        	
		            orderId = orderData.id;
		            return orderId;
		        });
		    },
		    onApprove: function (data, actions) {
			    return fetch('/api/paypal/orders/' + orderId + '/capture', {
			        method: 'GET'
			    })
			    .then((response) => response.json())
			    .then((orderData) => {
					console.log('orderData', orderData);
					
					// Cas ou la commande a été capturée et complétée
					if (!orderData.hasOwnProperty('debug_id')) {				
						const formData = new FormData();							
						formData.append('paypalOrderId', orderId);
						// Envoyer en method post les 2 ids de commandes
						fetch('/api/paypal/orders/'+ storage.id +'/approve', {
							method: 'post',
							body: formData,							 
							headers
						}).then((res) => {
							return res.status;
						}).then((status) => {
							//console.log('response orders/approve', json);
							if (status === 200) {
								window.location.assign(thanksUrl);
							}
						}).catch((error) => {
							console.log('fetch error: ' + error);
						});
					}
				});
			}
		}).render("#paypal-button-container");

		// If this returns false or the card fields aren't visible, see Step #1.
		if (paypal.HostedFields.isEligible())
		{     
		  	// Renders card fields
		  	paypal.HostedFields.render({
		        // Call your server to set up the transaction
		        createOrder: function () { 
		          	return fetch( '/api/paypal/orders/' + storage.id, {
		              	method: 'GET' 
		          	}).then(function(res) {
		            	return res.json();
		          	}).then(function(orderData) {
		          		console.log('resultat de orders dans action');
		        		console.log(orderData)
		            	orderId = orderData.id;
		            return orderId;
		          	});
		        },
		        styles: {
		        	'input': {
			          	'font-family': '"Open Sans", "Roboto", "Helvetica Neue", Helvetica, sans-serif',
			          	'transition': 'color 160ms linear',
			          	'-webkit-transition': 'color 160ms linear'
		        	},
		            '.valid': {
		              	'color': 'green'
		            },
		            '.invalid': {
		              	'color': 'red'
		            }
		        },
		        fields: {
		           	number: {
		             	selector: "#card-number",
		             	placeholder: "1000 2222 3333 4444"
		           	},
		           	cvv: {
		             	selector: "#cvv",
		             	placeholder: "123"
		           	},
		           	expirationDate: {
		             	selector: "#expiration-date",
		             	placeholder: "01/24"
		           	}
		        }
		  	})
		  	.then(function (cardFields) {
		      	document.querySelector("#card-form").addEventListener('submit', (event) => {
		        	event.preventDefault();
		        	cardFields.submit({
		         	contingencies: ['SCA_WHEN_REQUIRED'],
		          	// Cardholder's first and last name
		          	cardholderName: document.getElementById('card-holder-name').value                      
		        	}).then(function (payload) {		          		
		          		// Needed only when 3D Secure contingency applied
		          		if (payload.liabilityShift === "POSSIBLE") {
		                	// 3D Secure passed successfully
			                return fetch('/checkout/paypal/purchase/' + orderId + '/capture', {
			                  method: 'GET',
			                  headers: {"Content-Type": "application/json"}                 
			                }).then(function(res) {
			                  return res.json();
			                }).then(function (orderData) {
				                // Three cases to handle:
				                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
				                //   (2) Other non-recoverable errors -> Show a failure message
				                //   (3) Successful transaction -> Show confirmation or thank you
				                var errorDetail = Array.isArray(orderData.details) && orderData.details[0];
		                  		if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
		                    		return actions.restart(); // Recoverable state, per:
		                    		// https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
			                  	}
			                  	if (errorDetail) {
				                    var msg = lang == 'en' ? 'Sorry, your transaction could not be processed. Please try again or choose another payment method' : 'La transaction n\'a pas abouti. Veuillez réessayer ou choisissez un autre mode de paiement' ;
				                    //if (errorDetail.description) msg += '\n\n' + errorDetail.description;
				                    //if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
				                    return alert(msg); // Show a failure message
			                  	}
			                  	console.log(orderData);			                  	
			                  	const formData2 = new FormData();							
								formData2.append('paypalOrderId', orderId);					
								// Envoyer en method post les 2 ids de commandes
								fetch('/api/paypal/orders/' + storage.id + '/approve', {
									method: 'post',
									body: formData2,									
									headers
								}).then((res) => {
									return res.status;
								}).then((status) => {
									
									if (status === 200) {
										window.location.assign(thanksUrl);
									}
								}).catch((error) => {
									console.log('fetch error: ' + error);
								});
		                	});
		          		}
		              	if (payload.liabilityShift === "NO") {
		                 // Handle buyer confirmed 3D Secure successfully
		              		console.debug(payload);
		              	}            
		          	}).catch(function (err) {
		          		 var msg = lang == 'en' ? 'Sorry, your transaction could not be processed. Please refresh your brower and try again or choose another payment method' : 'La transaction n\'a pas abouti. Veuillez rafraichir / relancer cette page et réessayer ou choisissez un autre mode de paiement' ;
		          		alert(msg); 
		            	console.log('Payment could not be captured! ' + JSON.stringify(err));
		          	});
		     	});
		  	});
		}
		else 
		{
		   // Hides card fields if the merchant isn't eligible
		   document.querySelector("#card-form").style = 'display: none';
		}
</script>	
	<!-- $this->fetch('dedicatedScripts'); -->
	<!-- $this->fetch('scriptBottom'); --> 

</body>
</html>