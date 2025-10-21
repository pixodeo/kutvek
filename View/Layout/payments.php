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
	<style>
		.trads-container > ul > li.current > a { pointer-events: none;}
		div.contact {margin-top:1.6rem;}
		div.contact > p {display: flex; justify-content: space-between; align-items: center;}
		div.contact > p > span:first-child {font-family: 'Oswald'; font-size: 2rem;}
		.bloc-items > div {flex-direction: row; align-items: center;}
		.bloc-items > div > img {max-width: 12rem; order:1;}
		span.label {font-family: 'Oswald';   font-size: 1.8rem;}
		span.label + span {   font-size: 1.4rem;}		
		.btn.contained.paypal, .btn.contained.pro {margin-top: 1.6rem;width: 100%;height: 4.8rem;max-width: 36rem;border-radius: .4rem !important;}
		iframe.component-frame .paypal-button {border-radius:.1rem !important}	

	</style>
	<link rel="icon" type="image/x-icon" href="/favicon.ico">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/basics.css') ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/event/tabs.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/popup.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/front/grid.css'); ?>" type="text/css" media="screen">	
	<link rel="stylesheet" href="<?= $this->auto_version('/css/grid-layout.css'); ?>" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= $this->auto_version('/css/checkout.css'); ?>" media="screen">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;1,300&amp;family=Oswald:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id=<?= $this->paypal->getClientID(); ?>&enable-funding=paylater&currency=<?= $this->paypal->getCurrencyCode(); ?>" data-client-token="<?= $this->paypal->getClientToken(); ?>"></script>
</head>
<body>
	<!-- Google Tag Manager (noscript) -->
	
	<!-- End Google Tag Manager (noscript) -->	
	<?= $this->_content; ?>
	<script type="module" src="<?= $this->auto_version('/js/main.js') ?>"></script>
	<script type="module" src="<?= $this->auto_version('/js/modules/paypal.js') ?>"></script>
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
		   	onInit: function(data, actions) {
		      // Disable the buttons
		      actions.disable();
		      // Listen for changes to the checkbox
		      document.querySelector('#cgv')
		        .addEventListener('change', function(event) {
		          // Enable or disable the button when it is checked or unchecked
		          if (event.target.checked) {
		            actions.enable();
		            document.querySelector('#cgv-error').classList.add('hide');

		          } else {
		            actions.disable();
		          }
		        });
		    },
		    // onClick is called when the button is clicked
		    onClick: function() {
		    	const btn = document.querySelector('#cgv');
		    	const label = document.querySelector('label[for="cgv"]');
		      // Show a validation error if the checkbox is not checked
		      if (!btn.checked) {
		        document.querySelector('#cgv-error').classList.remove('hide');
		       }
		    },
		   	createOrder: async function () { 
		   		const req = await fetch(`/checkout/${storage.id}/paypal`, {method: 'POST', mode: 'cors', credentials: 'include'});
		   		const json = await req.json();
		   		if(req.ok) {		   			
		   			orderId = json.id;
		   			return orderId;
		   		}		       
		    },
		    onApprove: async function (data, actions) {
		    	const body = new FormData;
		    	body.append('paypalOrderId', orderId);
		    	const req = await fetch(`/checkout/${storage.id}/paypal/capture`, {method: 'POST', mode: 'cors', credentials: 'include', body: body});
		    	const json = await req.json();
		    	if(req.ok) {
		    		console.log('ok');		   			
		   			//window.location.assign(thanksUrl);
		   		}else {
		   			console.log(json);
		   		}
			    
			    
			    /*.then((orderData) => {
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
				});*/
			}
		}).render("#paypal-button-container");		
		// If this returns false or the card fields aren't visible, see Step #1.
		if (paypal.HostedFields.isEligible())
		{     
		  	// Renders card fields
		  	paypal.HostedFields.render({		  		
		        // Call your server to set up the transaction
		        createOrder: async function () { 
		          	const req = await fetch(`/checkout/${storage.id}/paypal`, {method: 'POST', mode: 'cors', credentials: 'include'});
		   			const json = await req.json();
			   		if(req.ok) {		   			
			   			orderId = json.id;
			   			return orderId;
			   		}
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
		             	placeholder: "MM/AA"
		           	}
		        }
		  	})
		  	.then(function (cardFields) {
		      	document.querySelector("#card-form").addEventListener('submit', (event) => {
		      		event.preventDefault();
		      		const btn = document.querySelector('#cgv');
		      		if (!btn.checked) {
		        		document.querySelector('#cgv-error').classList.remove('hide');
		        		return;
		       		}		        	
		        	cardFields.submit({
		         	contingencies: ['SCA_WHEN_REQUIRED'],
		          	// Cardholder's first and last name
		          	cardholderName: document.getElementById('card-holder-name').value                      
		        	}).then(function (payload) {		          		
		          		// Needed only when 3D Secure contingency applied
		          		if (payload.liabilityShift === "POSSIBLE") {
		                	// 3D Secure passed successfully
			                return fetch('/api/paypal/orders/' + orderId + '/capture', {
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
		else {document.querySelector("#card-form").style = 'display: none';}		
	</script>
</body>	
</html>