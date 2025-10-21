const _defaultLang = 'fr';
const monetary = function (number, l10n, currency, maximumFractionDigits = 2) {
	// ex i18n : 'de_DE'on remplace le "_" par "-"
	// ex currency : 'EUR'
	if (currency == '€') currency = 'EUR';
	if (currency == '£') currency = 'GBP';
	if (currency == '$') currency = 'USD';
	if (currency == '$ CAN') currency = 'CAD';
	if (l10n == 'fr') l10n = 'fr_FR';
	return new Intl.NumberFormat(l10n.replace('_', '-'), { style: 'currency', currency: currency, maximumFractionDigits: maximumFractionDigits }).format(number);
};
const cart = {
	_elem: null,
	_ev: null,
	_userInfo: false,
	_customer: false,
	_connected: false,
	_items: document.querySelector('#nbItems'),
	_store: JSON.parse(localStorage.getItem('cart')),
	_url : decodeURIComponent(window.atob(document.querySelector('#shopping-cart').getAttribute("data-obf"))),
	_overview : document.body.querySelector('#cart-preview'),
	setElem: function (elem) {
		this._elem = elem;
	},
	setEvent: function (event) {
		this._ev = event;
	},
	overview: async function(){
		const id = this._store !== null ? this._store.id : 0;
		const url = this._url.replace(':id', id); 
		const req =  await fetch(url, {method: 'GET', mode: 'cors', credentials: 'include'});
		if(req.ok){		
			const text = await req.text();
			const data = document.createRange().createContextualFragment(text);
			this._overview.setAttribute('data-order', id);
			this._overview.appendChild(data);
			this._overview.classList.add('visible');
		}
	},
	voucher: async function() {		
		this._overview.classList.add('process');
		const submitter = this._ev.submitter || document.activeElement;
		const url = submitter.getAttribute('formaction');
		const data = new FormData(this._elem);
		const body = this.formDataToJson(data);
		console.log(body);
		const req = await fetch(url,{method: 'PUT', mode: 'cors', credentials: 'include', body: body});
		// réponse : le panier actualisé ou message d'erreur 
		if(req.status === 200){			
			const text = await req.text();
			this.updateOverview(text);			
			return;
		}
		const json = await req.json();
		this._overview.classList.remove('process');		
	},
	updateItemQty: async function(){
		this._overview.classList.add('process');
		const url = this._elem.getAttribute('data-uri');
		const data = new FormData();
		data.append('qty', this._elem.value);
		const body = this.formDataToJson(data);
		const req = await fetch(url,{method: 'PUT', mode: 'cors', credentials: 'include', body: body});
		if(req.status === 200){			
			const text = await req.text();
			this.updateOverview(text);			
			return;
		}
		const json = await req.json();
		this._overview.classList.remove('process');
	},
	deleteItem: async function(){		
		let lang = document.documentElement.lang;
    	let msg_confirm = lang == 'fr' ? 'Vous confirmez la suppression ? ' : 'Are you sure ?';
    	if(!confirm(msg_confirm)) return;
    	this._overview.classList.add('process');
    	const url = this._elem.href;
    	const req = await fetch(url,{method: 'DELETE', mode: 'cors', credentials: 'include'});
    	if(req.status === 200){			
			const text = await req.text();
			this.updateOverview(text);			
			return;
		}
		const json = await req.json();
		this._overview.classList.remove('process');
	},
	updateOverview: function(data){
		this._overview.innerHTML = data;
		this._overview.classList.remove('process');
	},
	formDataToObject: function(formData){
		const object = {};
		formData.forEach((value, key) => {
	  	// si la clé est un tableau
			const words = key.split('[');
	  	// si words a plus d'un index
			if(words.length > 1)
			{
		// suprimer le ] de l'index 1
				if(!Reflect.has(object, words[0])){
					object[words[0]] = {}; 
					object[words[0]][words[1].slice(0, -1)] = value;
					return;
				} else {
					object[words[0]][words[1].slice(0, -1)] = value;
					return;
				}
			} else {
		// Reflect.has in favor of: object.hasOwnProperty(key)
				if(!Reflect.has(object, key)){
					object[key] = value;
					return;
				}
				if(!Array.isArray(object[key])){
					object[key] = [object[key]];    
				}
				object[key].push(value);
			}   
		});
		return object;
	},
	formDataToJson: function(formData){
		const object = this.formDataToObject(formData);
		return JSON.stringify(object);     
	}
}
export default cart;