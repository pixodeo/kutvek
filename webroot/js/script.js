// Helper / Utility functions
let current_customer_id;
let order_id;
let global_apple_pay_config;
let current_ap_session;
let applepay;
let apple_pay_email;
let pp_order_id;
let applepay_payment_event;
const paypal_sdk_url = "https://www.paypal.com/sdk/js";
let currency = "EUR";
const intent = "capture";
const baseRequest = {
  apiVersion: 2,
  apiVersionMinor: 0,
};

let paymentsClient = null,
  allowedPaymentMethods = null,
  merchantInfo = null;

let script_to_head = (attributes_object) => {
	return new Promise((resolve, reject) => {
		const script = document.createElement('script');
		for (const name of Object.keys(attributes_object)) {
			script.setAttribute(name, attributes_object[name]);
		}
		document.head.appendChild(script);
		script.addEventListener('load', resolve);
		script.addEventListener('error', reject);
	});
}

let reset_purchase_button = () => {
	document.querySelector("#purchase").removeAttribute("disabled");
	document.querySelector("#purchase").value = "Purchase";
}

const is_user_logged_in = () => {
	return new Promise((resolve) => {
		customer_id = 123 || "";
		resolve();
	});
}

let handle_close = (event) => {
	event.target.closest(".ms-alert").remove();
}
let handle_click = (event) => {
	if (event.target.classList.contains("ms-close")) {
		handle_close(event);
	}
}

// Apple Pay
let check_applepay = async () => {
	return new Promise((resolve, reject) => {
		let error_message = "";
		if (!window.ApplePaySession) {
			error_message = "This device does not support Apple Pay";
		} else
		if (!ApplePaySession.canMakePayments()) {
			error_message = "This device, although an Apple device, is not capable of making Apple Pay payments";
		}
		if (error_message !== "") {
			reject(error_message);
		} else {
			resolve();
		}
	});
};

let ap_validate = (event) => {
	applepay.validateMerchant({
		validationUrl: event.validationURL,
		displayName: "SARL KUTVEK KIT GRAPHIK"
	})
	.then(validateResult => {
		current_ap_session.completeMerchantValidation(validateResult.merchantSession);
	})
	.catch(validateError => {
		console.error(validateError);
		current_ap_session.abort();
	});
};

let handle_applepay_clicked = (event) => {
	const payment_request = {
		countryCode: global_apple_pay_config.countryCode,
		merchantCapabilities: global_apple_pay_config.merchantCapabilities,
		supportedNetworks: global_apple_pay_config.supportedNetworks,
		currencyCode: currency,
		requiredShippingContactFields: ["name", "phone", "email", "postalAddress"],
		requiredBillingContactFields: ["postalAddress"],
		total: {
			label: "SARL KUTVEK KIT GRAPHIK",
			type: "final",
			amount: "1.0",
		}
	};
	current_ap_session = new ApplePaySession(4, payment_request);
	current_ap_session.onvalidatemerchant = ap_validate;
	current_ap_session.onpaymentauthorized = ap_payment_authed;
	current_ap_session.begin()
};

let ap_payment_authed = (event) => {
	applepay_payment_event = event.payment;
	fetch("/checkout/paypal/create_order", {
		method: "POST", 
		headers: { "Content-Type": "application/json; charset=utf-8" },
		body: JSON.stringify({ "intent": intent, "order": cart_id})
	})
	.then((response) => response.json())
	.then((pp_data) => {
		pp_order_id = pp_data.id;
		apple_pay_email = applepay_payment_event.shippingContact.emailAddress;
		applepay.confirmOrder({
			orderId: pp_order_id,
			token: applepay_payment_event.token,
			billingContact: applepay_payment_event.billingContact
		})
		.then(confirmResult => {
			fetch(`/checkout/paypal/complete_order`, {
				method: 'POST',
				headers: { "Content-Type": "application/json; charset=utf-8" },
				body: JSON.stringify({
					"intent": intent,
					"order_id": pp_order_id
				})
			})
			.then((response) => response.json())
			.then((order_details) => {
				let intent_object = intent === "authorize" ? "authorizations" : "captures";
				if (order_details.purchase_units[0].payments[intent_object][0].status === "COMPLETED") {
					current_ap_session.completePayment(ApplePaySession.STATUS_SUCCESS);
					display_success_message({"order_details": order_details, "paypal_buttons": paypal_buttons});
				} else {
					current_ap_session.completePayment(ApplePaySession.STATUS_FAILURE);
					console.log(order_details);
					throw error("payment was not completed, please view console for more information");
				}
			})
			.catch((error) => {
				console.log(error);
				display_error_alert();
			});
		})
		.catch(confirmError => {
			if (confirmError) {
				console.error('Error confirming order with applepay token');
				console.error(confirmError);
				current_ap_session.completePayment(ApplePaySession.STATUS_FAILURE);
				display_error_alert();
			}
		});
	});
};

// Google Pay
let onGooglePayLoaded = async () => {
  const paymentsClient = getGooglePaymentsClient();
  const { allowedPaymentMethods } = await getGooglePayConfig();
  paymentsClient
    .isReadyToPay(getGoogleIsReadyToPayRequest(allowedPaymentMethods))
    .then(function (response) {
      if (response.result) {
        addGooglePayButton();
      }
    })
    .catch(function (err) {
      console.error(err);
    });
}

let getGooglePaymentsClient = () => {
  if (paymentsClient === null) {
    paymentsClient = new google.payments.api.PaymentsClient({
      environment: "TEST",
      paymentDataCallbacks: {
        onPaymentAuthorized: onPaymentAuthorized,
      },
    });
  }
  return paymentsClient;
};

let addGooglePayButton = () => {
  const paymentsClient = getGooglePaymentsClient();
  const button = paymentsClient.createButton({
    onClick: onGooglePaymentButtonClicked,
  });
  document.getElementById("googlepay-container").appendChild(button);
};

let getGoogleTransactionInfo =() => {
  return {
    displayItems: [
      {
        label: "Subtotal",
        type: "SUBTOTAL",
        price: "100.00",
      },
      {
        label: "Tax",
        type: "TAX",
        price: "10.00",
      },
    ],
    countryCode: "US",
    currencyCode: currency,
    totalPriceStatus: "FINAL",
    totalPrice: "110.00",
    totalPriceLabel: "Total",
  };
};

let getGoogleIsReadyToPayRequest = (allowedPaymentMethods) => {
  return Object.assign({}, baseRequest, {
    allowedPaymentMethods: allowedPaymentMethods,
  });
}

let getGooglePayConfig = async () => {
  if (allowedPaymentMethods == null || merchantInfo == null) {
    const googlePayConfig = await paypal.Googlepay().config();
    allowedPaymentMethods = googlePayConfig.allowedPaymentMethods;
    merchantInfo = googlePayConfig.merchantInfo;
  }
  return {
    allowedPaymentMethods,
    merchantInfo,
  };
};

let getGooglePaymentDataRequest = async () =>  {
  const paymentDataRequest = Object.assign({}, baseRequest);
  const { allowedPaymentMethods, merchantInfo } = await getGooglePayConfig();
  paymentDataRequest.allowedPaymentMethods = allowedPaymentMethods;
  paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
  paymentDataRequest.merchantInfo = merchantInfo;
  paymentDataRequest.callbackIntents = ["PAYMENT_AUTHORIZATION"];
  return paymentDataRequest;
}

let onGooglePaymentButtonClicked =  async () => {
  const paymentDataRequest = await getGooglePaymentDataRequest();
  paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
  const paymentsClient = getGooglePaymentsClient();
  paymentsClient.loadPaymentData(paymentDataRequest);
}

let processPayment = async (paymentData) => {
  try {
    const { currencyCode, totalPrice } = getGoogleTransactionInfo();
    const order = {
      intent: "CAPTURE",
      purchase_units: [
        {
          amount: {
            currency_code: currencyCode,
            value: totalPrice,
          },
        },
      ],
    };
    /* Create Order */
    const { id } = await fetch(`/orders`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(order),
    }).then((res) => res.json());
    const { status } = await paypal.Googlepay().confirmOrder({
      orderId: id,
      paymentMethodData: paymentData.paymentMethodData,
    });
    if (status === "APPROVED") {
      /* Capture the Order */
      const captureResponse = await fetch(`/orders/${id}/capture`, {
        method: "POST",
      }).then((res) => res.json());
      return { transactionState: "SUCCESS" };
    } else {
      return { transactionState: "ERROR" };
    }
  } catch (err) {
    return {
      transactionState: "ERROR",
      error: {
        message: err.message,
      },
    };
  }
}

let onPaymentAuthorized = (paymentData) => {
  return new Promise(function (resolve, reject) {
    processPayment(paymentData)
      .then(function (data) {
        resolve({ transactionState: "SUCCESS" });
      })
      .catch(function (errDetails) {
        resolve({ transactionState: "ERROR" });
      });
  });
}

let display_error_alert = () => {
	window.scrollTo({
		top: 0,
		left: 0,
		behavior: "smooth"
	});
	document.getElementById("alerts").innerHTML = `<div class="ms-alert ms-action2 ms-small"><span class="ms-close"></span><p>An Error Ocurred! (View console for more info)</p>  </div>`;
}

let display_success_message = (object) => {
	order_details = object.order_details;
	paypal_buttons = object.paypal_buttons;
	console.log(order_details); //https://developer.paypal.com/docs/api/orders/v2/#orders_capture!c=201&path=create_time&t=response
	let intent_object = intent === "authorize" ? "authorizations" : "captures";
	window.scrollTo({
		top: 0,
		left: 0,
		behavior: "smooth"
	});
	//Custom Successful Message
	document.getElementById("alerts").innerHTML = `<div class=\'ms-alert ms-action\'>Thank you ` + (order_details?.payer?.name?.given_name || ``) + ` ` + (order_details?.payer?.name?.surname || ``) + ` for your payment of ` + order_details.purchase_units[0].payments[intent_object][0].amount.value + ` ` + order_details.purchase_units[0].payments[intent_object][0].amount.currency_code + `!</div>`;

	//Close out the PayPal buttons that were rendered
	paypal_buttons.close();
	document.getElementById("card-form").classList.add("hide");
	document.getElementById("applepay-container").classList.add("hide");
}

document.addEventListener("click", handle_click);

document.addEventListener('DOMContentLoaded', (e)=> {
	const client_id =  document.querySelector('#pay').getAttribute('data-client-id');
	const client_token = document.querySelector('#pay').getAttribute('data-client-token');
	const cart_id = document.querySelector('#pay').getAttribute('data-cart-id');
	currency = document.querySelector('#pay').getAttribute('data-currency');
	
	//PayPal Code
	is_user_logged_in()
	.then(() => {
		//https://developer.paypal.com/sdk/js/configuration/#link-queryparameters
		return script_to_head({"src": paypal_sdk_url + "?client-id=" + client_id + "&enable-funding=paylater&currency=" + currency + "&components=buttons,hosted-fields,applepay,googlepay", "data-client-token": client_token}) //https://developer.paypal.com/sdk/js/configuration/#link-configureandcustomizeyourintegration
	})
	.then(() => {
		//Handle loading spinner
		document.getElementById("loading").classList.add("hide");
		document.getElementById("content").classList.remove("hide");
        
    //PayPal buttons Code  
		let paypal_buttons = paypal.Buttons({ // https://developer.paypal.com/sdk/js/reference
			style: { //https://developer.paypal.com/sdk/js/reference/#link-style
				shape: 'rect',
				color: 'gold',
				layout: 'vertical',
				label: 'paypal'
			},
			createOrder: async function(data, actions) {			
			  //https://developer.paypal.com/docs/api/orders/v2/#orders_create          
				const res = await fetch(`/checkout/paypal/create_order`, {
					method: 'POST', 
					headers: { "Content-Type": "application/json; charset=utf-8" },
					body: JSON.stringify({
						"intent": intent,
						"order": cart_id
					})
				});
				if(res.ok){
					const json = await res.json();
					order_id = json.id;
					return json.id;
				}            
			},
			onApprove: async function (data, actions) {
				order_id = data.orderID;
				console.log(data);
				const res = await fetch(`/checkout/paypal/complete_order`, {
					method: 'POST',
					headers: { "Content-Type": "application/json; charset=utf-8" },
					body: JSON.stringify({
						"intent": intent,
						"order_id": order_id
					})
				});
				if(res.redirected)
				{ 
				//window.location.assign(res.url); // on a la valeur de location en 3xx
					return;
				}           
				if(!res.ok){
					const json = await res.json();
					alert(json.error);
					return;
				}       
				if(res.ok){
					const order_details = await res.json();
					let intent_object = intent === "authorize" ? "authorizations" : "captures";
					if (order_details.purchase_units[0].payments[intent_object][0].status === "COMPLETED") {
						display_success_message({"order_details": order_details, "paypal_buttons": paypal_buttons});
					} else {
						console.log(order_details);
						throw error("payment was not completed, please view console for more information");
					}

				}
			},
			onCancel: function (data) {
				document.getElementById("alerts").innerHTML = `<div class="ms-alert ms-action2 ms-small"><span class="ms-close"></span><p>Order cancelled!</p>  </div>`;
			},
			onError: function(err) {
				console.log(err);
			}
		});
		paypal_buttons.render('#payment_options');

		//Hosted Fields Code
		if (paypal.HostedFields.isEligible()) {
			// Renders card fields
			paypal_hosted_fields = paypal.HostedFields.render({
			  // Call your server to set up the transaction
				createOrder: async function () { 
					const request = await fetch(`/api/demo/paypal/orders/${cart_id}`, {method: 'GET'});
					if(request.ok){
						const json = await request.json();
						order_id = json.id;
						return json.id;
					}               
				},
				styles: {
					'.valid': {
						color: 'green'
					},
					'.invalid': {
						color: 'red'
					},
					'input': {
						'font-size': '16pt',
						'color': '#ffffff'
					},
				},
				fields: {
					number: {
						selector: "#card-number",
						placeholder: "4111 1111 1111 1111"
					},
					cvv: {
						selector: "#cvv",
						placeholder: "123"
					},
					expirationDate: {
						selector: "#expiration-date",
						placeholder: "MM/YY"
					}
				}
			}).then((card_fields) => {
				document.querySelector("#card-form").addEventListener("submit", (event) => {
					event.preventDefault();
					document.querySelector("#card-form").querySelector("input[type='submit']").setAttribute("disabled", "");
					document.querySelector("#card-form").querySelector("input[type='submit']").value = "Loading...";
					card_fields.submit(
					//Customer Data BEGIN
					//This wasn't part of the video guide originally, but I've included it here
					//So you can reference how you could send customer data, which may
					//be a requirement of your project to pass this info to card issuers
					{
					  // Cardholder's first and last name
					  //cardholderName: document.getElementById('card-holder-name').value, 
						cardholderName: "Raúl Uriarte, Jr.",
					  // Billing Address
						billingAddress: {
						// Street address, line 1
							streetAddress: "123 Springfield Rd",
						// Street address, line 2 (Ex: Unit, Apartment, etc.)
							extendedAddress: "",
						// State
							region: "",
						// City
							locality: "CHANDLER",
						// Postal Code
							postalCode: "85224",
						// Country Code
							countryCodeAlpha2: "FR",
						},
					}
					//Customer Data END
					).then((payload) => {

						return fetch(`/api/demo/paypal/orders/${cart_id}/capture`, {
							method: 'GET', headers: { "Content-Type": "application/json; charset=utf-8" }

						}).then((response) => response.json())
						.then((order_details) => {
							let intent_object = intent === "authorize" ? "authorizations" : "captures";
							if (order_details.purchase_units[0].payments[intent_object][0].status === "COMPLETED") {
								display_success_message({"order_details": order_details, "paypal_buttons": paypal_buttons});
							} else {
								console.log(order_details);
								throw error("payment was not completed, please view console for more information");
							}
						})
						.catch((error) => {
							console.log(error);
							display_error_alert();
						});
					})
					.catch((err) => {
						console.log(err);
						reset_purchase_button();
						display_error_alert();
					});
				});
			});
		}
	  
	  //ApplePay Code	  	
		check_applepay()
		.then(() => {
			applepay = paypal.Applepay();
			applepay.config()
			.then(applepay_config => {
				if (applepay_config.isEligible) {
					document.getElementById("applepay-container").innerHTML = '<apple-pay-button id="applepay_button" buttonstyle="black" type="plain" locale="fr">';
					global_apple_pay_config = applepay_config;
					document.getElementById("applepay_button").addEventListener("click", handle_applepay_clicked);
				}
			})
			.catch(applepay_config_error => {
				console.error('Error while fetching Apple Pay configuration:');
				console.error(applepay_config_error);
			});
		})
		.catch((error) => {
			console.error(error);
		});

		// Google Pay Code
		if (google && paypal.Googlepay) {
      //onGooglePayLoaded().catch(console.log);
			console.log('Ready for Pay with Googlepay');
			onGooglePayLoaded().catch(console.log);
    }
		
		
		/*let handle_pointerdown = (event) => {
			switch(event.target.id) {
			case "applepay_button":
				handle_applepay_clicked(event);
				break;
			}
		}
		document.addEventListener("pointerdown", handle_pointerdown);*/
})
.catch((error) => {
	reset_purchase_button();
});
})