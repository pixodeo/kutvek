const graphics = {
	_ev: null,
	_elem:null,
	lang:document.documentElement.lang,
	currency: document.getElementById('item-currency') ? document.getElementById('item-currency').getAttribute('content') || 'EUR' : 'EUR',
	_items: document.querySelector('#nbItems'),
	setElem: function (elem) {
		this._elem = elem;
	},
	setEvent: function (event) {
		this._ev = event;
	},
	vehicles: function(){
		const vehicle = document.getElementById('vehicle');
		if(!this._elem.value)return;   
		for (const option of vehicle.options) {
			if(option.hasAttribute('data-brand-id')){
				if(option.getAttribute('data-brand-id') == this._elem.value){option.classList.remove('hide');} 
				else {option.classList.add('hide');}
			} 
	  //console.log(option.label); // "Option 1" and "Option 2"
		}

		vehicle.options[0].selected = true;
		vehicle.disabled = false;

	},
	years: async function(){
		if(!this._elem.value)return;
		const selected = this._elem.options[this._elem.selectedIndex];
		const sku = this._elem.getAttribute('data-sku');		
		const url = new URL(selected.getAttribute('data-uri'));    
		const urlParams = new URLSearchParams;
		//console.log(url,sku); 
		urlParams.append('sku', sku); 
		url.search = urlParams.toString();
		const req = await fetch(url); 
		if(req.ok) {
			const text = await req.text();
			let frag = document.createRange().createContextualFragment(text);
			const old = document.getElementById('millesim');
			const old_types = document.getElementById('kit-type');
			old.parentNode.replaceChild(frag.querySelector('select#millesim'), old);
			old_types.parentNode.replaceChild(frag.querySelector('select#kit-type'),old_types);			
			this.updateGallery();
			this.updateContent();			
		}
	},
	year: async function() {   
		const selected = this._elem.options[this._elem.selectedIndex];     
		document.getElementById('item-sku').value = selected.getAttribute('data-sku');
		const url = selected.getAttribute('data-uri');
		
		const req = await fetch(url);
		if(req.ok){
			const text = await req.text();
			let frag = document.createRange().createContextualFragment(text);
			const old = document.getElementById('kit-type');  
			const select = frag.querySelector('select#kit-type');
			old.parentNode.replaceChild(select, old);
			console.log(select.options);
			document.getElementById('type-id').value = select.options[select.selectedIndex].getAttribute('data-id');
			document.getElementById('type-name').value = select.options[select.selectedIndex].getAttribute('data-designation');
			this.updateCost(); 
		}     
	},
	/**
	 * On récupère les visuels d'un produit existant
	 */
	updateGallery: async function(){
		const selected = this._elem.options[this._elem.selectedIndex];
		const productID = selected.getAttribute('data-item');		
		if(productID === null || productID.length === 0) return;

		const old = document.querySelector('div.gallery');
		const oldThumbs = old.parentNode.querySelector('div.thumbnails');
		const url = old.getAttribute('data-uri').replace(':id', productID);

		const req = await fetch(url);
		if(req.ok){
			const text = await req.text();
			const frag = document.createRange().createContextualFragment(text);
			old.parentNode.replaceChild(frag.querySelector('div.gallery'), old);
			oldThumbs.parentNode.replaceChild(frag.querySelector('div.thumbnails'), oldThumbs);
		}
	},
	/**
	 * On met à jour le bloc des options disponibles pour un produit
	 * prendre en compte le véhicule, le gabarit ...
	 */
	updateContent: async function(){
		const selected = this._elem.options[this._elem.selectedIndex];
		const productID = selected.getAttribute('data-item');		
		if(productID === null || productID.length === 0) return;

		const old = document.getElementById('bloc-options');		
		const url = old.getAttribute('data-uri').replace(':id', productID);
		const req = await fetch(url);

		if(req.ok){
			const text = await req.text();
			const frag = document.createRange().createContextualFragment(text);
			let old;
			const title = frag.querySelector('h1.designation');
			if(title){
				old = document.querySelector('h1.designation');
				old.parentNode.replaceChild(title, old);
			}
			const desc = frag.querySelector('.short-desc');
			if(desc){
				old = document.querySelector('.short-desc');
				old.parentNode.replaceChild(desc, old);
			}
			const info = frag.querySelector('#product-description');
			if(info){
				old = document.querySelector('#product-description');
				old.parentNode.replaceChild(info, old);
			}
			const features = frag.querySelector('#product-features');
			if(features){
				old = document.querySelector('#product-features');
				old.parentNode.replaceChild(features, old);
			}
			const rendering = frag.querySelector('.best_rendering');
			if(rendering){
				old = document.querySelector('.best_rendering');
				old.parentNode.replaceChild(rendering, old);
			}
			this.updateOptions(frag);		
		}
	},
	updateOptions: function(frag)
	{
		const old = document.getElementById('bloc-options');
		const blocOptions = frag.querySelector('#bloc-options');
		if(blocOptions) old.parentNode.replaceChild(blocOptions, old);
		this.updateCost();
	},
  /**
   * Choix du kit std, full ...
   */
	price: function(){    
		const selected = this._elem.options[this._elem.selectedIndex];
		document.getElementById('type-id').value = selected.getAttribute('data-id');
		document.getElementById('type-name').value = selected.getAttribute('data-designation');
		const btn = document.getElementById('button-cart');
		btn.disabled = false;
		this.updateCost();
	},
	finish: function(){
	// on update les inputs #finish-id et #finish-name 
		const selected = this._elem.options[this._elem.selectedIndex];
		document.getElementById('finish-id').value = selected.getAttribute('data-id');
		document.getElementById('finish-name').value = selected.getAttribute('data-name');
		this.updateCost();
	},
	premium: function(){
		const selected = this._elem.options[this._elem.selectedIndex];
		document.getElementById('premium-id').value = selected.getAttribute('data-id');
		document.getElementById('premium-name').value = selected.getAttribute('data-name');
		this.updateCost();
	},
	filters:async function(){
		this.refreshFilters();
		const universes = Array.from(document.querySelectorAll('input[name="universe[]"]:checked'), (x) => x.value).join(',');
		const brands = Array.from(document.querySelectorAll('input[name="brand[]"]:checked'), (x) => x.value).join(',');
		const vehicles = Array.from(document.querySelectorAll('input[name="vehicle[]"]:checked'), (x) => x.value).join(',');

		const url = new URL(this._elem.form.action);    
		const urlParams = new URLSearchParams;

		if(universes.length > 0 )urlParams.append('universes', universes);
		if(brands.length > 0 )urlParams.append('brands', brands);
		if(vehicles.length > 0 )urlParams.append('vehicles', vehicles);
		url.search = urlParams.toString();

		console.log(url);
		console.log(urlParams.toString());

		const request = await fetch(url.href);
		if(request.ok){
			const text = await request.text();
			let frag = document.createRange().createContextualFragment(text);
			const parent = document.getElementById('products').parentNode;
			const products = document.getElementById('products');      
			const pagination = parent.querySelector('.pagination');           
			parent.replaceChild(frag.querySelector('#products'), products);      
			parent.replaceChild( frag.querySelector('.pagination'), pagination);
			return;
		}
		const json = await request.json();
	},
	refreshFilters: async function(){
		const universes = Array.from(document.querySelectorAll('input[name="universe[]"]:checked'), (x) => x.value).join(',');
		const brands = Array.from(document.querySelectorAll('input[name="brand[]"]:checked'), (x) => x.value).join(',');
		const vehicles = Array.from(document.querySelectorAll('input[name="vehicle[]"]:checked'), (x) => x.value).join(',');

		const url = new URL(this._elem.form.getAttribute('data-refresh'));    
		const urlParams = new URLSearchParams;    
		if(universes.length > 0 )urlParams.append('universes', universes);
		if(brands.length > 0 )urlParams.append('brands', brands);
		if(vehicles.length > 0 )urlParams.append('vehicles', vehicles);
		url.search = urlParams.toString();

		const request = await fetch(url.href);
		if(request.ok){
			const json = await request.json();
			const arrayB =Array.from(json.brands);
			const brands = document.querySelectorAll('input[name="brand[]"]');
			brands.forEach(b => {
				if(arrayB.indexOf(parseInt(b.value, 10)) < 0)  {
					b.disabled = true;
					b.checked = false;
				} else {
					b.disabled = false;            
				}
		  /*console.log( `value : ${b.value} : exists : ${array.indexOf(parseInt(b.value, 10))}`);*/
			});
			const list = document.getElementById('vehicles-list');
			const frag = document.createRange().createContextualFragment(json.vehicles);
			list.parentNode.replaceChild(frag.querySelector('#vehicles-list'), list);
			return;
		}
		const json = await request.json();
	},  
	addToCart: async function(){
	//console.log(this._elem.action);
		const form = this._elem;
		const button = form.querySelector('button[type="submit"]');
		button.disabled = 'disabled';
		button.querySelector('span.load').classList.toggle('hidden');
		button.querySelector('span.text').style.opacity = '.5';

		const formData = new FormData(form);
		const _stored = JSON.parse(localStorage.getItem('cart'));
		if(_stored !== null) formData.append('item[id_order]', _stored.id);   
		const req = await fetch(form.action, {method: 'POST', body: formData,mode: 'cors', credentials: 'include'});
		if(req.status !== 201){
			const json = await req.json();
			return;
		}
		const text = await req.text();
		let frag = document.createRange().createContextualFragment(text);
		const aside = frag.firstChild;
		const items = aside.querySelector('#items').getAttribute('data-qty');
		if(_stored !== null) {
			_stored.qty = items;
			localStorage.setItem('cart', JSON.stringify(_stored));
		} else {
			const cart = {id: aside.getAttribute('data-order'), qty: items};
			localStorage.setItem('cart', JSON.stringify(cart));
		} 
		this.updateCartIcon(items);  
		const cart = document.getElementById('cart-preview'); 
		if(cart) cart.parentNode.replaceChild(aside, cart);  
		else document.body.append(aside);
		button.disabled = false;
		button.querySelector('span.load').classList.toggle('hidden');
		button.querySelector('span.text').style.opacity = '1';
		return;
	},
	miniPlates: function(){
		this.updateCost();
	},
	hubs: function(){
		this.updateCost();
	},
	updateCost: function(){
		const prices = document.querySelectorAll('.cost');
		var cost = 0.00;
	 //console.log(prices);
		for(const e of Array.from(prices)){
			if(e.nodeName == 'SPAN') continue;
			const inputQty = document.getElementById('item-qty');
			const q = inputQty ? inputQty.value : 1;
			const qty = e.classList.contains('qty-depend') ? Number(q) : 1; 
			if(e.nodeName == 'INPUT' && e.checked) cost = cost + (Number(e.value) * qty);
			if(e.nodeName == 'SELECT') cost = cost + (Number(e.value) * qty);
	  //console.log(e.nodeName);
		}
		document.getElementById('item-cost').textContent = this.monetary(cost);   
	},
	updateCartIcon: function (nb_items) {
		if (this._items) this._items.textContent = nb_items;
	},
	monetary: function (number,  maximumFractionDigits = 2) {
  // ex i18n : 'de_DE'on remplace le "_" par "-"
  // ex currency : 'EUR'  
		if (this.lang == 'fr') this.lang = 'fr_FR';
		return new Intl.NumberFormat(this.lang.replace('_', '-'), { style: 'currency', currency: this.currency, maximumFractionDigits: maximumFractionDigits }).format(number);
	}
}

export default graphics;