const filter = {
	_elem: null,
	_cards: null,
	_form: null,
	_pagination: null,
	_filters: null,
	init: function (elem) {
		if (elem === null) return;
		this._elem = elem;
		// console.log('loading filtering functions on this pages');
		this._cards = document.querySelector('#products');
		this._form = this._elem.querySelector('.filter-form');
		this._pagination = document.querySelector('.pagination');
		this.bindEvents();
		this.fetchDataAndLinks();
		this.enableFilters();
	},
	bindEvents: async function () {
		if (this._form === null) return;
		this._form.querySelectorAll('li > input[type="checkbox"].onchange').forEach(input => {
			input.addEventListener('change', e => {
				this.fetchData();
			});
		});
		this._filters = this._form.querySelectorAll('.filter');		
		this._filters.forEach(async filter => {
			if(filter.hasAttribute('data-onload')) {
				let uri = filter.getAttribute('data-uri');
				const res = await fetch(uri);
				const json = await res.json();				
				if(json.success){				
				let ul;
				// Affichage des filtres dans une modal pour les versions mobiles
				if (window.innerWidth < 1024) {
					const modalName = filter.getAttribute('data-modal');
					const modal = document.getElementById(modalName);
					//modal.classList.add('visible');
					ul = modal.querySelector('div > ul'); 
				} else {
					ul = filter.parentNode.querySelector('div > ul');
				}
				
				const tpl = document.getElementById(`filter-${json.filter}`);
				let fragment = document.importNode(tpl.content, true);
				let li = fragment.querySelector('li');
				while(ul.firstChild) {ul.removeChild(ul.firstChild)}	
							
				json.data.forEach(property => {					
					let _clone = li.cloneNode(true);
					let _input = _clone.querySelector('input');						
					let _label = _clone.querySelector('label')
					_input.id = `${json.filter}-${property[0]}`;
					_input.value = property[0];
					_input.setAttribute('form', this._form.id);
					_label.htmlFor = `${json.filter}-${property[0]}`;
					_label.textContent = `${property[1]}`;
					_input.addEventListener('change', e => {this.fetchData();});
					ul.appendChild(_clone);
				});							
			}
			filter.setAttribute('data-fetched', 1);	
			filter.checked = true;	
			}
			filter.addEventListener('change', e => {
				this.filterData(filter);})
		});
	},
	/**
	 * Récupère touts les designs /  couleurs / gammes de la page
	 */
	filterData: function(filter){
		if(filter.hasAttribute('data-fetched')) {
			// données déjà existantes 
			if (window.innerWidth < 1024) {
				const modalName = filter.getAttribute('data-modal');
					const modal = document.getElementById(modalName);
					modal.classList.add('visible');
			}
		} else {
			return fetch(filter.getAttribute('data-uri'), 
				{method: 'GET'}
			).then((res) => {
				return res.json()
			}).then((data) => {			
				if(data.success){
					let ul;
					// Affichage des filtres dans une modal pour les versions mobiles
					if (window.innerWidth < 1024) {
						const modalName = filter.getAttribute('data-modal');
						const modal = document.getElementById(modalName);
						modal.classList.add('visible');
						ul = modal.querySelector('div > ul'); 
					} else {
						ul = filter.parentNode.querySelector('div > ul');
					}
	
					const tpl = document.getElementById(`filter-${data.filter}`);
					let fragment = document.importNode(tpl.content, true);
					let li = fragment.querySelector('li');
					while(ul.firstChild) {ul.removeChild(ul.firstChild)}	
					filter.setAttribute('data-fetched', 1);
					
					data.data.forEach(property => {
						//console.log(property);
						let _clone = li.cloneNode(true);
						let _input = _clone.querySelector('input');						
						let _label = _clone.querySelector('label')
						_input.id = `${data.filter}-${property[0]}`;
						_input.value = property[0];
						_input.setAttribute('form', this._form.id);
						_label.htmlFor = `${data.filter}-${property[0]}`;
						_label.textContent = `${property[1]}`;
						_input.addEventListener('change', e => {this.fetchData();});
						ul.appendChild(_clone);
					});
					return Promise.resolve();						
				}
			}).catch((error) => {
				return Promise.reject(error);
			});
		}
	},
	/**
     * Pour chaques liens de la pagination, ajoute un clic event pour récupérer
     * les produits correspondants en générant l'url avec les bons params des filtres
     */
	fetchDataAndLinks: async function () {
		if (this._pagination !== null) {
			this._pagination.querySelectorAll('a').forEach(link => {
				link.addEventListener('click', e => {
					e.preventDefault();
					const url = new URL(window.location.href);
					const params = new URLSearchParams();
					const formData = new FormData(this._form);
					
					formData.forEach((val, key) => {
						params.append(key, val);
					});
					params.append('page', link.getAttribute('data-page'));
					
					this.loadUrl(url.pathname + '?' + params.toString());
					const title = document.querySelector('h1.section-title');
					title.scrollIntoView(true);
				});
			});
		}
	},
	/**
     * Rajoute les filtres à l'url courante ou celle de soumission du formulaire si elle existe 
     * en tant que search params
     */
	fetchData: async function (input) {
		const formData = new FormData(this._form);
		const url = new URL(this._form.action || window.location.href);
		const params = new URLSearchParams();
		formData.forEach((val, key) => {
			params.append(key, val);
		});
		return this.loadUrl(url.pathname + '?' + params.toString());
	},
	/**
     * Fetch l'url, récupère les produits et les remplace par les nouveaux
     */
	loadUrl: async function (url) {
		const response = await fetch(url, {
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		});
		if (response.status >= 200 && response.status < 301) {
			const products = document.getElementById('products')
			const data = await response.json();
			if(data.cards.length === 0) return;
			// Je boucle sur les cards déjà présentes, j'enlève le 1er élément tant qu'il y a 1 enfant
			while (this._cards.firstChild) { this._cards.removeChild(this._cards.firstChild) }
			data.cards.forEach(card => {
				const newCard = this.createCard(card);
				
				products.appendChild(newCard);
			});
			history.replaceState({}, '', url);
			this._pagination.innerHTML = data.pagination;
			this.fetchDataAndLinks();
		} else {
			console.error(response);
		}
	},
	createCard: function (card) {
		const template = document.getElementById('card-tpl');
		const clone = document.importNode(template.content, true);
		const figure = clone.querySelector('figure');
		const img = clone.querySelector('img.visual');
		const title = clone.querySelector('h3.item');
		const price = clone.querySelector('span.price');
		const url = clone.querySelector('a');

		figure.id = card.id;
		figure.setAttribute('data-brand', card.brand)
		figure.setAttribute('data-design', card.design)
		figure.setAttribute('data-color', card.color)
		img.src = card.visual;
		title.textContent = card.designation;
		price.textContent = card.price_0;
		//card.url != null ? url.href = '/' + card.url : url.href = '/img/blank.png';
		card.url != null ? url.href =  card.url : url.href = '/img/blank.png';
		if(card.promo){
			figure.querySelector('span.offer > img').src = card.promo.img;
		}

		return clone;
	},
	/**
     * Pré-coche les checkbox des filtres présents dans l'url
     */
	enableFilters: function() {
		const url = new URL(window.location.href);
		const params = new URLSearchParams(url.search);
		
		this._filters.forEach(filter => {
			if (params.has(`${filter.id}[]`)) {
				this.filterData(filter)
				.then(() => {
					for (const [key, value] of params) {  // key = color[], value = 50
						if (key === `${filter.id}[]`) {
							document.getElementById(`${filter.id}-${value}`).checked = true
						}
					} 
				}).catch((error) => {
					console.error(error);
				});
			}
		});
	}
};