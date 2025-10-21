// on vérifie si on est log
//import {display_tab} from '../main.js';
const order = {
	_elem: null,		
	setElem: function(elem){
	this._elem = elem;
	},	
	setEvent: function(event){
		this._ev = event;
	},
	_mockupsToConfirm: [],
	_ordersInProgress: [],
	_display_tab: false,
	dashboard: async function(){		
		let success = await this._init();
				
		if(success){
			

			let i = await getCurrentOrders(document.getElementById('in-progress').getAttribute('data-uri'));
			this._ordersInProgress = i.orders;	
			let m = await getOrdersToValidate(document.getElementById('mockups-confirm').getAttribute('data-uri'));
			this._mockupsToConfirm = m.orders;
			this.mockupsToValidate();
			this.currentOrders();	
				
		} else {
			let urlLogin = decodeURIComponent(window.atob(document.documentElement.getAttribute('data-obf')));
			document.location.href = urlLogin || '/';
		}		
	},
	mockupsToValidate: function() {		
		let div = document.getElementById('mockups-confirm');
		let url = div.getAttribute('data-uri');
		let link = document.querySelector('a[href="#mockups-confirm"]');
		link.querySelector('span.counter > small').textContent = this._mockupsToConfirm.length;
		if(this._mockupsToConfirm.length > 0){					
			div.setAttribute('data-fetched', 1);			
			let ul = div.querySelector('ul.tasks');
			let tpl = document.getElementById('task');
			var clone = document.importNode(tpl.content, true);

			this._mockupsToConfirm.forEach(task => {

			let li = clone.cloneNode(true);
			let reference = li.querySelector('span.reference');
			let label = li.querySelector('label.action.dropdown');
			label.htmlFor = `dropdown-${task.id}`;
			let input = li.querySelector('input.action.dropdown');
			input.id = `dropdown-${task.id}`;
			
			if(task.invoice && task.invoice.length > 10) {
				
				let linkToBill = li.querySelector('a.bill');
				linkToBill.classList.remove('hidden');
				linkToBill.href	= task.invoice;
			}
			reference.textContent = task.app_com_num;
			li.querySelector('.designation').textContent = task.designation;

			// tous les ids et les hrefs
			let links = li.querySelectorAll('ul.tabs > li > a');
			let divs = li.querySelectorAll('div.tab_content');
			divs.forEach(div => {
				div.id = `${div.id}-${task.id}`
			});
			links.forEach(link => {							
				link.href = `${link.hash}-${task.id}`;
				link.addEventListener('click', function(e) {
					order._tabs.default(this, e);		
			});
			});
			// posts / chat
			if(task.posts.length > 0){
				let postTpl = document.getElementById('post-tpl');
				let content = document.importNode(postTpl.content, true);
				let postsDiv = li.querySelector('div.posts');						
				task.posts.forEach(post => {
					let clone = content.cloneNode(true);
					clone.querySelector('div.post').id = post.id;
					clone.querySelector('span.created').textContent = post.created;
					clone.querySelector('span.user').textContent = post.type_user;
					clone.querySelector('div.body').innerHTML = post.body;
					clone.querySelector('div.post').classList.add(post.class);
					postsDiv.appendChild(clone);
				});
			}
			// Maquettes
			if(task.mockups.files && task.mockups.files.length > 0)
			{

				//console.log(task.mockups.files);
				let mockupTpl = document.getElementById('mockup-tpl');
				let content = document.importNode(mockupTpl.content, true);
				let mockupDiv = li.querySelector('div.mockups > div');
				let mockupLabels = li.querySelector('div.mockups-labels');
				let links = mockupLabels.querySelector('div.links');
				//let select = li.querySelector(`#mockups-${task.id} select`);
				let select = li.querySelector('#mockup-choosen');
				select.id = `mockup-choosen-${task.id}`;
				task.mockups.files.forEach((mock, index) => {
					let clone = content.cloneNode(true);
					let img = clone.querySelector('img')
					img.setAttribute('data-file', mock.base64);	
					img.src = decodeURIComponent(window.atob(mock.base64));				
					img.setAttribute('data-id', mock.id);
					img.addEventListener('click', e => {
						let t = e.currentTarget ||e.target;
						window.open(t.src, '_blank');						
					})

					if(parseInt(mock.selected,10) == 1) {
						img.classList.add('selected');
					}
					// ajout d'un label + input pour chaque image dans div.mockups-labels
					let label = document.createElement('label');
					label.htmlFor = `mockup-${mock.id}`;
					label.textContent = index + 1;					

					let radio = document.createElement('input');
					radio.type = 'radio';
					radio.id = `mockup-${mock.id}`;
					radio.value = mock.id;
					radio.name = 'mockupChoice';
					radio.setAttribute('data-ctrl', 'mockup.translate');
					radio.setAttribute('data-translate',  index * 100);
					radio.setAttribute('data-item',  task.id);
					radio.classList.add('onchange');
					radio.hidden = 'hidden';

					let option = document.createElement('option');
					option.value = mock.id;
					option.textContent = `Maquette ${index + 1}`;
					select.appendChild(option);
					// si on est sur la dernière maquette 
					if(index + 1  == task.mockups.files.length) {
						radio.checked = true;
						let className = 'mockup-translate-' + (index * 100); 
						mockupDiv.className = className;
						select.value = mock.id;
					}
					mockupDiv.appendChild(clone);
					links.appendChild(radio);
					links.appendChild(label);					
				});		
			}
			if(task.seat_cover_suggest){
				let divSuggest = li.querySelector('div.suggest');
				divSuggest.classList.remove('hide');
			}
			let modify = li.querySelector('aside.modify');
			modify.id = `modify-${task.id}`;
			modify.setAttribute('data-modal', `modify-${task.id}`);
			modify.querySelector('header > a').href =  `#modify-${task.id}`;
			modify.querySelector('header > a').setAttribute('data-modal', `modify-${task.id}`);
			let mtextarea = modify.querySelector('textarea');
			mtextarea.id = `body-${task.id}`;
			mtextarea.parentNode.querySelector('label').htmlFor = `body-${task.id}`;

			let forms =  li.querySelectorAll('form');
			forms.forEach(form => {
				form.action = form.action.replace(':item', task.id);
				let label = form.querySelector('label.files-label');
				if(label) label.htmlFor = `files-${task.id}`;
				let input = form.querySelector('input.files-input');
				if(input) input.id = `files-${task.id}`;
			});
			let button = li.querySelector('button.modify');
			button.setAttribute('data-modal', `modify-${task.id}`);

			let cgv = li.querySelector('#cgv');			
			cgv.id = `cgv-${task.id}`;
			cgv.parentNode.querySelector('label').setAttribute('for', cgv.id);

			let mockupc =  cgv.parentNode.parentNode.querySelector('select[data-ctrl="mockup.select"]');
			mockupc.id = `mockup-choosen-${task.id}`;
			mockupc.parentNode.querySelector('label').setAttribute('for', mockupc.id);		

			li.querySelector(`#info-${task.id}`).innerHTML = task.info;	
			ul.appendChild(li);
			});					
			order.pell();
			i18ns.i18n();	
		} else 	console.log(this._mockupsToConfirm);		
	},
	currentOrders: function(){		
		let div = document.getElementById('in-progress');
		let url = div.getAttribute('data-uri');
		let link = document.querySelector('a[href="#in-progress"]');
		link.querySelector('span.counter > small').textContent = this._ordersInProgress.length;
		if(this._ordersInProgress.length > 0){
			div.setAttribute('data-fetched', 1);

			let ul = div.querySelector('ul.tasks');
			let tpl = document.getElementById('task');
			var clone = document.importNode(tpl.content, true);

			this._ordersInProgress.forEach(task => {

			let li = clone.cloneNode(true);
			let reference = li.querySelector('span.reference');
			let label = li.querySelector('label.action.dropdown');
			label.htmlFor = `dropdown-${task.id}`;
			let input = li.querySelector('input.action.dropdown');
			input.id = `dropdown-${task.id}`;
			
			if(task.invoice && task.invoice.length > 10) {
				
				let linkToBill = li.querySelector('a.bill');
				linkToBill.classList.remove('hidden');
				linkToBill.href	= task.invoice;
			}
			reference.textContent = task.app_com_num;
			li.querySelector('.designation').textContent = task.designation;

			// tous les ids et les hrefs
			let links = li.querySelectorAll('ul.tabs > li > a');
			let divs = li.querySelectorAll('div.tab_content');
			divs.forEach(div => {
				div.id = `${div.id}-${task.id}`
			});
			links.forEach(link => {							
				link.href = `${link.hash}-${task.id}`;
				link.addEventListener('click', function(e) {
					order._tabs.default(this, e);		
			});
			});
			// posts / chat
			if(task.posts.length > 0){
				let postTpl = document.getElementById('post-tpl');
				let content = document.importNode(postTpl.content, true);
				let postsDiv = li.querySelector('div.posts');						
				task.posts.forEach(post => {
					let clone = content.cloneNode(true);
					clone.querySelector('div.post').id = post.id;
					clone.querySelector('span.created').textContent = post.created;
					clone.querySelector('span.user').textContent = post.type_user;
					clone.querySelector('div.body').innerHTML = post.body;
					clone.querySelector('div.post').classList.add(post.class);
					postsDiv.appendChild(clone);
				});
			}
			// Maquettes
			if(task.mockups.files && task.mockups.files.length > 0)
			{
				this.setMockups(li,task.mockups.files);
					
			}
			if(task.seat_cover_suggest){
				let divSuggest = li.querySelector('div.suggest');
				divSuggest.classList.remove('hide');
			}
			let modify = li.querySelector('aside.modify');
			modify.id = `modify-${task.id}`;
			modify.setAttribute('data-modal', `modify-${task.id}`);
			modify.querySelector('header > a').href =  `#modify-${task.id}`;
			modify.querySelector('header > a').setAttribute('data-modal', `modify-${task.id}`);
			let forms =  li.querySelectorAll('form');
			forms.forEach(form => {
				form.action = form.action.replace(':item', task.id);
				let label = form.querySelector('label.files-label');
				if(label) label.htmlFor = `files-${task.id}`;
				let input = form.querySelector('input.files-input');
				if(input) input.id = `files-${task.id}`;
				form.classList.add('hide');
			});
			let button = li.querySelector('button.modify');
			button.setAttribute('data-modal', `modify-${task.id}`);
			li.querySelector(`#info-${task.id}`).innerHTML = task.info;	
			ul.appendChild(li);
			});	
			i18ns.i18n();					
			order.pell();
		}else 	console.log(this._ordersInProgress);
	},
	setMockups: function(li,files){
		let selected = [];
		let lgt = files.length - 1;
				console.log(lgt);
				let mockupTpl = document.getElementById('mockup-tpl');
				let content = document.importNode(mockupTpl.content, true);
				let mockupDiv = li.querySelector('div.mockups > div');
				let mockupLabels = li.querySelector('div.mockups-labels');
				let links = mockupLabels.querySelector('div.links');
        for (let i = 0; i < files.length; i++) {
        	let clone = content.cloneNode(true);
        	let img = clone.querySelector('img');
        	img.setAttribute('data-file', files[i].base64);	
        	img.src = decodeURIComponent(window.atob(files[i].base64));		
        	img.setAttribute('data-id', files[i].id);img.setAttribute('data-id', files[i].id);
        	img.addEventListener('click', e => {let t = e.currentTarget ||e.target;	window.open(t.src, '_blank');						
					});
        	let label = document.createElement('label');
			label.htmlFor = `mockup-${files[i].id}`;					
			label.textContent = i + 1;
			let radio = document.createElement('input');
			radio.type = 'radio';
			radio.id = `mockup-${files[i].id}`;
			radio.value = files[i].id;
			radio.name = 'mockupChoice';
			radio.setAttribute('data-ctrl', 'mockup.translate');
			radio.setAttribute('data-translate',  i * 100);
			radio.setAttribute('data-item',  task.id);
			radio.classList.add('onchange');
			radio.hidden = 'hidden';

			if(lgt == i){
				
				radio.setAttribute('data-idx', i);
				label.classList.add('checked');
				let className = 'mockup-translate-' + (i * 100); 
				mockupDiv.className = className;
				selected.push(radio);
			} 			
  			mockupDiv.appendChild(clone);
			links.appendChild(radio);
			links.appendChild(label);	
			i18ns.i18n();	
		}
		console.log(selected);
		for(let a = 0; a < selected.length; a++){
			selected[a].checked = true;
		}
				/*files.forEach((mock, index) => {
					
					let clone = content.cloneNode(true);
					let img = clone.querySelector('img')
					img.setAttribute('data-file', mock.base64);					
					img.setAttribute('data-id', mock.id);
					img.addEventListener('click', e => {
						let t = e.currentTarget ||e.target;
						window.open(t.src, '_blank');						
					})
					if(parseInt(mock.selected,10) == 1) {
						img.classList.add('selected');
					}
					// ajout d'un label + input pour chaque image dans div.mockups-labels
					let label = document.createElement('label');
					label.htmlFor = `mockup-${mock.id}`;					
					label.textContent = index + 1;
					let radio = document.createElement('input');
					radio.type = 'radio';
					radio.id = `mockup-${mock.id}`;
					radio.value = mock.id;
					radio.name = 'mockupChoice';
					radio.setAttribute('data-ctrl', 'mockup.translate');
					radio.setAttribute('data-translate',  index * 100);
					radio.setAttribute('data-item',  task.id);
					radio.classList.add('onchange');
					radio.hidden = 'hidden';
					// si on est sur la dernière maquette 

					if(index   === lgt) {
						radio.checked = 1;
											
					}			
					mockupDiv.appendChild(clone);
					links.appendChild(radio);
					links.appendChild(label);					
				});	*/
	},
	ordersByYear: function() {		
		let div = document.getElementById('orders');
		let url;
		if(this._elem.id === 'filter-by-year') {			
			url = this._elem.getAttribute('data-uri').replace(':year', this._elem.value);		
		} else {			
			url = div.getAttribute('data-uri');
		}		
		this._init().then(success => {			
			if(success){
				getOrdersByYear(url)
				.then( json => {
				let ul = div.querySelector('ul.tasks');
				while (ul.firstChild) { ul.removeChild(ul.firstChild) }
				if(json.orders && json.orders.length > 0){
					//console.log(json.orders);
						div.setAttribute('data-fetched', 1);
						//let link = document.querySelector('a[href="#mockups"]');
						//link.querySelector('span.counter > small').textContent = json.orders.length;						
						let tpl = document.getElementById('task');
						var clone = document.importNode(tpl.content, true);
						json.orders.forEach(task => {
							let li = clone.cloneNode(true);
							let reference = li.querySelector('span.reference');
							let label = li.querySelector('label.action.dropdown');
							label.htmlFor = `dropdown-${task.id}`;
							let input = li.querySelector('input.action.dropdown');
							input.id = `dropdown-${task.id}`;							
							if(task.invoice && task.invoice.length > 10) {								
								let linkToBill = li.querySelector('a.bill');
								linkToBill.classList.remove('hidden');
								linkToBill.href	= task.invoice;
							}
							reference.textContent = task.app_com_num;
							li.querySelector('.designation').textContent = task.designation;
							// tous les ids et les hrefs
							let links = li.querySelectorAll('ul.tabs > li > a');
							let divs = li.querySelectorAll('div.tab_content');
							links.forEach(link => {							
								link.href = `${link.hash}-${task.id}`;
								link.addEventListener('click', function(e) {		
									display_tab(this, e);		
								});
							});
							// posts / chat
							if(task.posts && task.posts.length > 0){
								let postTpl = document.getElementById('post-tpl');
								let content = document.importNode(postTpl.content, true);
								let postsDiv = li.querySelector('div.posts');						
								task.posts.forEach(post => {
									let clone = content.cloneNode(true);
									clone.querySelector('div.post').id = post.id;
									clone.querySelector('span.created').textContent = post.created;
									clone.querySelector('span.user').textContent = post.type_user;
									clone.querySelector('div.body').innerHTML = post.body;
									clone.querySelector('div.post').classList.add(post.class);
									postsDiv.appendChild(clone);
								});
							}
							// Maquettes
							if(task.mockup && task.mockup.file)
							{
								console.log(task.mockup.file);

								let mockupTpl = document.getElementById('mockup-tpl');
								let content = document.importNode(mockupTpl.content, true);
								let mockup = li.querySelector('a.mockup');
								mockup.href = task.mockup.file.url;
								let clone = content.cloneNode(true);
								let img = clone.querySelector('img');
								img.src =task.mockup.file.url;
								img.classList.add('selected');

								mockup.appendChild(img);

							}
							divs.forEach(div => {
								div.id = `${div.id}-${task.id}`
							});
							li.querySelector(`#info-${task.id}`).innerHTML = task.info;	
							ul.appendChild(li);
						});									
				} else 	console.log(json);							
				}).catch(error => {console.debug(error)});
			} else {
				let urlLogin = decodeURIComponent(window.atob(document.documentElement.getAttribute('data-obf')));
				document.location.href = urlLogin || '/';
			}			
		}).catch(error => {console.debug(error)});		
	},
	pell: function(){				  
  		const editors = document.querySelectorAll('.pell-editor');
		editors.forEach(function(el,idx)
		{  
		  let editor = pell.init({
		    element: el,
		    onChange: html => {
		        el.parentNode.querySelector('.pell-editor-output').innerHTML = html
		    },
		    defaultParagraphSeparator: 'p',
		    styleWithCSS: true,
		    actions: [
		     
		    ],
		    classes: {
		      actionbar: 'pell-actionbar-custom-name',
		      button: 'pell-button-custom-name',
		      content: 'writing-area',
		      selected: 'pell-button-selected-custom-name'
		    }
		  });		  
		    editor.content.innerHTML = el.parentNode.querySelector('.pell-editor-output').innerHTML;		    
		});
	},
	_init: async function() {	
		let response = await fetch(`/asset/js/modules/tabs.js`, {method: 'GET'});
		let _asset = response.ok ? await response.text() : '/js/modules/tabs.js'; 
    	_asset = _asset.replace('/js', '');
    	let  _module = await import(`..${_asset}`);
    	this._tabs = _module		
		response = await fetch(`/asset/js/modules/auth.js`, {method: 'GET'});
	    _asset = response.ok ? await response.text() : '/js/modules/auth.js';   	
    	_asset = _asset.replace('/js', '');
    	let  _module2 = await import(`..${_asset}`);
    	let success = await _module2.default.check();
    	return success;	
	},
	decline: async function(){
		let formData = new FormData(this._elem);
		// quel button a submit le formulaire ? pour récupérer sa valeur
    	const submitter = this._ev.submitter || document.activeElement;
    	submitter.classList.add('in-progress');
    	submitter.disabled = true;
    	
		let pjs = this._elem.querySelectorAll('.obj');
		pjs.forEach(pj =>{
			formData.append('pjs[]', pj.file);
		});
		/*for (const file of fileInput.files) {
    		formData.append('files[]', file, file.name);
  		}*/
		let xsrfToken = localStorage.getItem('xsrfToken');		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
		this._elem.action,
		{ method: 'POST', mode: 'cors', credentials: 'include', headers, body: formData}
		);
		if(!res.ok) return false;
		//const json = await res.json();
		//return json;
		console.log(res.status);
		window.location.reload();
	},
	deletePromoCode: async function(){
		console.log('d p c');
		let xsrfToken = localStorage.getItem('xsrfToken');			
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
		this._elem.href,
		{ method: 'DELETE', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		window.location.reload();
	}		
}
async function getCurrentOrders(url) {
		console.log('Fetch orders in progress...');
		// il faut envoyer xsrf token

		let xsrfToken = localStorage.getItem('xsrfToken');
		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
			url,
			{ method: 'GET', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
}

async function getOrdersByYear(url) {
		let xsrfToken = localStorage.getItem('xsrfToken');		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
			url,
			{ method: 'GET', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
}
async function getOrdersToValidate(url) {
		console.log('Fetch orders to validate...');
		// il faut envoyer xsrf token

		let xsrfToken = localStorage.getItem('xsrfToken');
		
		// inclure token csrf
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
			url,
			{ method: 'GET', mode: 'cors', credentials: 'include', headers}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
}
const i18ns =  {
	_defaultLang : 'fr',
	i18n: function () {
		const lang = document.documentElement.lang;
		if (this._defaultLang == lang) {
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
export default order;