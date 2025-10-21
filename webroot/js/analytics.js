"use strict";

const analytics = {
	_elem: null,
  	_ev: null,
  	_store: JSON.parse(localStorage.getItem('cart')),
	setElem: function (elem) {
	    this._elem = elem;
	},
	setEvent: function (event) {
	    this._ev = event;
	},
	uniquid: function(){
		const uid = Math.floor(Math.random() * Date.now()).toString(16);
		return 'T_'+ uid;
	},
	purchase: async function() {		
		if(this._store === null) return;

		console.log(dataLayer); 
		dataLayer.push({ ecommerce: null });
		let url = `/api/orders/${this._store.id}/events/purchase`;
		const request = await fetch(`/api/orders/${this._store.id}/events/purchase`);
		if (request.ok) {
        	
        	const eventPurchase = await request.json();
        	eventPurchase.transaction_id = this.uniquid();
        	dataLayer.push(eventPurchase);

      	}


	}
}



window.addEventListener("DOMContentLoaded", function(e) { 
	
	//analytics.purchase();  
});

export default analytics;