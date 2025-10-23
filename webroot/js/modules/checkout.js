const checkout = {
	_elem: null,
	_ev: null,
	_store: JSON.parse(localStorage.getItem('cart')),
	_userInfo: false,
	_customer: false,
	_connected: false,
	_items: document.querySelector('#nbItems'),
	setElem: function (elem) {
		this._elem = elem;
	},
	setEvent: function (event) {
		this._ev = event;
	},
	cgv: async function(){	
		let body;
		const url = this._elem.getAttribute('data-url').replace(':id', this._store.id);	
		if(this._elem.checked) body = {checked : 'on'};
		else  body = {checked : 'off'};
		const req = await fetch(url, {method: 'PATCH', mode: 'cors', credentials: 'include', body: JSON.stringify(body)});		
		if(req.status === 204){document.getElementById('accept-cgv').classList.toggle('off'); return;}
		else{const json = await req.json();}
	},
	lottery: async function(){	
		let body = {event:8};
		const url = this._elem.getAttribute('data-url').replace(':id', this._store.id);		
		if(this._elem.checked) body.checked = 'on';		
		else  body.checked = 'off';
		const req = await fetch(url, {method: 'PATCH', mode: 'cors', credentials: 'include', body: JSON.stringify(body)});		
		if(req.status !== 204){ const json = await req.json();}
	},
	cart: async function() {
		const decode = decodeURIComponent(window.atob(this._elem.getAttribute("data-obf")));		
		const url = this._store !== null ? decode.replace(':id', this._store.id) : decode.replace(':id', 0);  
		const req = await fetch(url, {method: 'GET', mode: 'cors', credentials: 'include'});
		if(req.ok){
			const text = await req.text();
			const old = document.querySelector('#cart-preview');
			let cart = document.createRange().createContextualFragment(text);			
			if(old) old.parentNode.replaceChild(cart, old);
			else document.body.appendChild(cart);

			document.querySelector('#cart-preview').classList.add('visible');
		}
	},
	next: function(){
		// si on est sur du submit form
		if(this._ev.type === 'submit') return this.validate();
		
		const url = decodeURIComponent(window.atob(this._elem.getAttribute("data-obf")));
		document.location.assign(url);
	},
	validate: async function() {		
		const url = decodeURIComponent(window.atob(this._elem.getAttribute("data-obf")));
		console.log(url);
	},
	showButton: function(){
		const btn = document.getElementById(this._elem.getAttribute('data-btn'));
		document.querySelectorAll('.payment-btn').forEach(b=>{
			b.classList.add('hide');
		});
		btn.classList.toggle('hide');		
	},
	pay: async function(){
		const btn = document.querySelector('#cgv');
		if (!btn.checked) {
		    document.querySelector('#cgv-error').classList.remove('hide');
		    return;
		}
	}

}
export default checkout;