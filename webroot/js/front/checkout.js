const checkout = {
	_elem: null,
	_ev: null,		
	setElem: function(elem){
	this._elem = elem;
	},	
	setEvent: function(event){
		this._ev = event;
	},
	purchase: function(){
		// push cart to dataLayer after payment ok
		let store = JSON.parse(localStorage.getItem('cart'));
		dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
		let _purchaseLayer = {
			event : "purchase",
			transaction_id: "O_" + store.id,
			value: parseFloat(store.amount.value),
			currency: store.amount.currency_code,
			tax: parseFloat(store.amount.breakdown.tax_total.value),
			shipping: parseFloat(store.amount.breakdown.shipping.value),
			items: []
		};
		store.items.forEach(itm => {
			_purchaseLayer.items.push({
				item_id: itm.sku,
				item_name: itm.name,
				price: parseFloat(itm.unit_amount.value),
				currency: itm.unit_amount.currency_code,
				quantity: parseInt(itm.quantity,10)
			});			
		});
		dataLayer.push(_purchaseLayer);
		console.log('_purchaseLayer : ');
		console.log(_purchaseLayer);
		localStorage.removeItem('cart');
	},
	checkout: function() {
		if (sessionStorage.getItem('user') === null) {
			const obf = this._elem.parentNode.getAttribute('data-obf');
			const url = decodeURIComponent(window.atob(obf));
			document.location.href = url + '?r=checkout';
		} else {
			const obf = this._elem.getAttribute('data-obf');
			document.location.href = decodeURIComponent(window.atob(obf));
		}
	}
}