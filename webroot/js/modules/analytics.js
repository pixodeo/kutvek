const analytics = {
	_elem: null,
  	_ev: null,
  	_store: JSON.parse(localStorage.getItem('cart')),
  	_counter: document.getElementById('nbItems'),
  	_transactionId: null,
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
		this.test();		
		if(this._store === null) return;
		/*const res = await fetch(`https://dev.kutvek.com/api/kutvek/orders/${this._store.id}/events/purchase`, { method: 'GET', mode: 'cors', credentials: 'include'});
		const json = await res.json();
		//purchaseEvent.ecommerce.transaction_id = this.uniquid();        	
		if(res.ok){
			dataLayer.push({ecommerce: null });
			dataLayer.push(json.purchase);
		}else{
			
		}       	
        localStorage.removeItem('cart');
        this._store = null;
        this._counter.textContent = 0;*/
        document.getElementById('transaction-id').textContent = this._store.id;
        //document.getElementById('transaction-email').textContent =  json.transactionEmail;   
	},
	beginCheckout: async function(){
		// check begin_checkout infos to send in dataLayer
		console.log('begin_checkout');
		if(this._store === null) return;
		const res = await fetch(`https://dev.kutvek.com/api/kutvek/orders/${this._store.id}/events/begin-checkout`, { method: 'GET', mode: 'cors', credentials: 'include'});
		const json = await res.json();
		if(res.ok){
			dataLayer.push({ecommerce: null });
			dataLayer.push(json.beginCheckout);
			console.log(json);
		}
    	const link = decodeURIComponent(window.atob(this._elem.getAttribute('data-obf')));    
    	// dataLayer
    	console.log(dataLayer);
    	//if(this._store.id == 480506) return;
    	window.location.assign(link);
	},
	test: function(){
		console.info('analytics ok');
	}
}
export default analytics;