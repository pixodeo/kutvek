/**
 * Import dynamiques des modules (objets) js
 * Ici on part du principe qu'on a un export default dans chaque module
 * @param      {<type>}  params  object.action : category.test
 * @param      {<type>}  elem    The element
 * @param      {<type>}  event   The event
 */


const _defaultLang = 'fr';

const run = async function(params, elem, event) 
{ 
	let p = params.split(".");
	let _module = p[0];
	let _action = p[1];
	let _asset;
	// test fetch asset
	let response = await fetch(`/asset/js/modules/${_module}.js`, {method: 'GET'});
	if(response.ok){
		_asset = await response.text();

	}  
	else {_asset = `/js/modules/${_module}.js`;} 
	
	const Module = await import(`..${_asset}`);    
	Module.default.setEvent(event);
	if(elem !== null && elem !== undefined)
		Module.default.setElem(elem);
	Module.default[_action]();

};

function _click(event)
{   
	var target = event.target; 

  // doofindr
	if(target.classList.contains('df-card__main'))
	{
		event.preventDefault();
		console.log('dfidr');
	}
  // aria-expanded
	if(target.hasAttribute('aria-expanded'))
	{
		var expanded = target.getAttribute('aria-expanded')
		if (expanded === 'false') {
			target.setAttribute('aria-expanded', 'true')
		} else {
			target.setAttribute('aria-expanded', 'false')
		}
		var cid = target.getAttribute('aria-controls');
		var content = document.getElementById(cid);

		if (content) {
			content.getAttribute('hidden')
			if (typeof content.getAttribute('hidden') === 'string') {
				content.removeAttribute('hidden')
			} else {
				content.setAttribute('hidden', 'hidden')
			}
		}
		return;
	}

	const el = target.hasAttribute('data-ctrl')  ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
	if(!el) return;

	const click = el.classList.contains('click');
	if(!click) return;  
	event.preventDefault();
	var params = el.getAttribute('data-ctrl');  
	run(params, el, event);   
}

function _change(event)
{
	const target = event.target;  

  //let acces = target.hasAttribute('data-event') &&  target.getAttribute('data-event') === 'change' ? true : target.parentNode.hasAttribute('data-event') &&  target.parentNode.getAttribute('data-event') === 'change'? true : false;

  //if(!acces) return;
	const el = target.hasAttribute('data-ctrl')  ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
	if(!el) return;   

	const onchange = el.classList.contains('onchange');
	if(!onchange) return;

	event.preventDefault();
	var params = el.getAttribute('data-ctrl');    
	run(params, el, event);   
}
/**
 * Event submit listener
 * Only when submit an html form without .unsubmit class
 * @param      {<type>}  event   The event
 */
function _submit(event) {
	var target = event.target; 
	const el = target.hasAttribute('data-ctrl')  ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;    
	if(!el || el.nodeName.toLowerCase() !== 'form' || el.classList.contains('unsubmit')) return;  
	event.preventDefault();    
	var params = el.getAttribute('data-ctrl');  
	run(params, el, event);   
}

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

const scrollToE = function(to, duration) {
	var
	element = document.body || document.documentElement,
	start = element.scrollTop,    
	change = to - start,
	startDate = +new Date(),
	// t = current time
	// b = start value
	// c = change in value
	// d = duration
	easeInOutQuad = function(t, b, c, d) {
		t /= d/2;
		if (t < 1) return c/2*t*t + b;
		t--;
		return -c/2 * (t*(t-2) - 1) + b;
	},
	animateScroll = function() {
		var currentDate = +new Date();
		var currentTime = currentDate - startDate;
		element.scrollTop = parseInt(easeInOutQuad(currentTime, start, change, duration));
		if(currentTime < duration) {
			requestAnimationFrame(animateScroll);
		}
		else {

			element.scrollTop = to;
		}
	};
	animateScroll();
}

function display_tab(a,event) {    
	event.preventDefault(); 

	// on recupère la balise a
	let _target = event.target;     
	while (_target.nodeName !== 'A') {
		_target = _target.parentNode;
	}

	var li = _target.parentNode;
	var ul = li.parentNode;
	
	// l'elem parent (li) a déjà la class active
	if(li.classList.contains('active') || li.classList.contains('disabled')) return false;    

	let _current = ul.querySelector('li.active') || ul.querySelector('li');
	let _current_tab_id = _current.querySelector('a').hash;
	_current.classList.remove('active'); 

	li.classList.add('active');

	let div = ul.nextElementSibling;

	// on retire .active sur le contenu tab_content .active
	// la div active c'est celle qui a en id l'href 
	const _current_tab = div.querySelector(_current_tab_id);    
	
	var tab_content_to_active =  div.querySelector(_target.getAttribute('href'));

	// on ajoute la class active sur le tab_content en rapport avec l'elem cliqué, sélection par son id qui correspond au href du lien _target
	// ! elem.href renvoi le lien absolu, utiliser getAttribute('href');    
	_current_tab.classList.remove('active'); 
	tab_content_to_active.classList.add('active');    
}

const i18ns =  {
	i18n: function () {
		const lang = document.documentElement.lang;
		if (_defaultLang == lang) {
	  //this.rewrite();
			return;
		}
	//const trads = document.querySelectorAll('[data-trad]');
	//console.log(trads);
	//var trads = JSON.parse(localStorage.getItem('i18n_'+lang));
		var trads = null;
	// Pas de trad associée en localStorage
		if (trads === null) {
			const uri = 'https://dev.kutvek.com/api/i18n/:lang';
			const url = uri.replace(':lang', lang);
			fetch(url, {
				method: 'GET'
			}).then((res) => {
				return res.json()
			}).then((json) => {
				if (json.success) {
					localStorage.setItem('i18n_' + lang, JSON.stringify(json.data));
					trads = json.data;
					console.log('trad fetched');
					this.translate(trads);
				}
			});
		} else {
			this.translate(trads);
		}
	},
	translate: function (trads) {
		const imgs = document.querySelectorAll('img.rw');
		trads.forEach(function (trad) {
			const type = trad.type;
			const id = trad.node;
			const content = trad.content;
			const elems = document.querySelectorAll('[data-i18n="' + id + '"]');
			
			if (content !== null) {		
				switch (type) {
				case 'placeholder':
					elems.forEach(function (el) {
						if(el.nodeName !== 'FIELDSET') {
							// Si on a un select c'est la 1ère option qui est le placeholder
							if (el.nodeName == 'SELECT') {
								var option = el.options[0].textContent = content;
							} else
							el.placeholder = content;
						}				
					});
					break;
				case 'legend':
					elems.forEach(e => {
						if(e.nodeName == 'FIELDSET') {
							e.querySelector('legend').textContent = content;
						}
					});					
					break;	
				case 'label':
					elems.forEach(
						function (el) {
							const label = document.querySelector('[for="' + el.id + '"]');
				//console.log(label);
							if (label)
								label.textContent = content;
						}
						);
					break;
				case 'html':
					elems.forEach(
						function (el) {
							el.innerHTML = content;
						}
						);
					break;
				case 'wrapper':
					elems.forEach(
						function (el) {
							el.querySelector('label').textContent = content;
						}
						);
					break;
				default:
					elems.forEach(
						function (el) {
							if(el.nodeName !== 'FIELDSET'){
								if (el.nodeName == 'INPUT' || el.nodeName == 'SELECT') {
									const label = document.querySelector('[for="' + el.id + '"]');				 
									if (label)
										label.textContent = content;
								}
								else {
									el.textContent = content;
								}
							}
							
						}
						);
					break;
				}
			}
		});
		/*if (imgs.length > 0) {
		var user = JSON.parse(localStorage.getItem('user'));
		  let lang = document.documentElement.lang;
		  if (user !== null && user.country == 16 && (lang == 'fr' || lang == 'nl')) {
			imgs.forEach((i) => {

			  i.src = i.getAttribute('data-rw').replace(':i18n', lang + '_BE');
			});
		  }
		}*/
	}
}

document.addEventListener('click', _click, false);
document.addEventListener('change', _change, false);
document.addEventListener('submit', _submit, false);
window.addEventListener("DOMContentLoaded", function(e) { 	
	
	const obflinks = document.querySelectorAll('.obflink');
	for (let i=0, n=obflinks.length; i < n; ++i){
		obflinks[i].addEventListener('click', function(e){
			// un check futur pour savoir si c'est un lien _blank
			if(obflinks[i].hasAttribute('data-ctrl'))return;
			const link = decodeURIComponent(window.atob(obflinks[i].getAttribute('data-obf')));            
			//window.open(link); 
			window.location.assign(link);
		});
	}

	var tabs = document.querySelectorAll('ul.tabs > li > a');
	if(tabs.length >  0){
		for (var i=0, n=tabs.length; i < n; ++i) {
			tabs[i].addEventListener('click', function(e) {             
				display_tab(tabs[i], e);       
			});
		}        
	}
	const orderDiv = document.getElementById('orders');
	const dashboard = document.getElementById('mockups-confirm');
	if(dashboard) {run('order.dashboard', dashboard, e);}   
	if(orderDiv)  {run('order.ordersByYear', orderDiv, e);}   
	const checkoutDiv = document.getElementById('order');
	if(checkoutDiv) {
	  // display checkout
	  //console.log('display checkout');
		run('cart.checkout', checkoutDiv , e);
		i18ns.i18n();
	}
	let cart = JSON.parse(localStorage.getItem('cart'));
	const btnCart = document.getElementById('cart-btn');
	const nbItems = document.getElementById('nbItems');
	if(cart !== null){
		if(nbItems)
			nbItems.textContent = cart.items;
		if(btnCart) btnCart.setAttribute('data-count', cart.items);
	} else {

		if(nbItems)
			nbItems.textContent = 0;
	}    
	if(window.location.hash) {        
		let a = document.querySelector('a[href="' + window.location.hash + '"]');        
		if(a !== null && !a.parentNode.classList.contains('active')) {  
			a.click(); 
		}
		//var id = hash.replace(/#/i, '');
		//var elmnt = document.getElementById(id);
		//console.log(elmnt.offsetTop);
		//scrollToE(elmnt.offsetTop - 70, 900);
	} 

	const popinInfo = document.querySelector('#promo-info');
	if(popinInfo){
		var popins = localStorage.getItem('popins');

		if(popins !== null) {
			popins = JSON.parse(popins);          
			const keys = Object.keys(popins);          
			const inArray = (element) => element === '#promo-info';
			const found = keys.findIndex(inArray);          
			if(found < 0 ) popinInfo.classList.toggle('visible');
		} else {
			popinInfo.classList.toggle('visible');
		}
	}
i18ns.i18n();

	/** Exemple import **/  
	/*import(`./modules/filter.js`).then((Module) => {
		console.debug(Module);
		Module.default.setEvent(event);
		Module.default.init(document.querySelector('.filter-js'));
	});*/  

});
export {monetary};