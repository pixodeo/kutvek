const user = {	
	_elem: null,
	_cookies: {},
	_ev: null,
	_store: null,
	setEvent: function(event){
		this._ev = event;
	},
	setElem: function(elem){
	this._elem = elem;
	},
	init: function() {
		let u_name = document.getElementById('username');
		let user = JSON.parse(sessionStorage.getItem('user'));
		let page_parameters = document.querySelector('#account-parameters');
		if(user !== null) {
			this._store = user;
			if(u_name) u_name.textContent = user.fullname;
			let div = document.getElementById('user-name');
			if(div) div.querySelector('span.icon').classList.add('online');
			if(page_parameters) {
				document.querySelector('#email').value = user.email;
				let tpl = document.getElementById('tpl-identity');
				let _content = document.importNode(tpl.content, true);;
				const bloc_identity = document.getElementById('bloc-identity');
				
				for (const prop in user) {
					switch(prop){
						case 'firstname':										
							let _input = _content.querySelector('#firstname');
							_input.value = user.firstname;
							bloc_identity.appendChild(_input.parentNode);						
							break;
						case 'lastname':							
							let _input2 = _content.querySelector('#lastname');
							_input2.value = user.lastname;
							bloc_identity.appendChild(_input2.parentNode);
							break;
						case 'company':							
							if(user.company.length > 0)
							{
								let _input3 = _content.querySelector('#company');
								_input3.value = user.company;
								bloc_identity.appendChild(_input3.parentNode);
							}							
							break;
						case 'rebate':
							if(parseFloat(user.rebate) > 0){
								let _input4 = _content.querySelector('#rebate');
								_input4.value = user.rebate + '%';
								bloc_identity.appendChild(_input4.parentNode);
							}
					}				
				}
			}
		}
	},
	auth: function () {

		let url = this._elem.action;
		let formData = new FormData(this._elem);	
		// si on a un panier 
		let cart = JSON.parse(localStorage.getItem('cart'));
		if(cart !== null) {
			formData.append('order', cart.id);
		}
		fetch(url, { 
		method: 'POST', 
		//headers: headers,
		body: formData
		})
		.then((res) => {
		return res.json()
		})
		.then((json) => {
			if(json.success ) {
				// Authentification ok, enregistrement en session
				localStorage.setItem('xsrfToken', json.xsrfToken);
				localStorage.setItem('accessTokenExpiresIn', json.accessTokenExpiresIn);
				localStorage.setItem('refreshTokenExpiresIn', json.refreshTokenExpiresIn);
				// Choix de la redirection				
				let href = document.location.href;
				let url;
				var searchParams = new URLSearchParams(location.search);
				let referer = searchParams.get('r');
				
				if(referer !== null){
					switch(referer){
						case 'checkout':							
							url = app._currentLang == 'fr' ? '/cart/checkout' : '/' + app._currentLang + '/cart/checkout';    						
    						break;
    					case 'dashboard':
    						url = app._currentLang == 'fr' ? '/account/dashboard' : '/' + app._currentLang + '/account/dashboard';    						
    						break;
    					default:
    						url = app._currentLang == 'fr' ? '/' : '/' + app._currentLang;
    						break;
					}
				} else {
					url = app._currentLang == 'fr' ? '/' : '/' + app._currentLang;
				}
				document.location.href = url;	
			} else {
				if(json.error){

				}
				if(json.user == null)
				{
					// Utilisateur non trouvé
					const div = document.getElementById('message');
					if (div.firstChild) div.removeChild(div.firstChild);
					const message = document.createElement('p');
					console.log(app._currentLang);
					message.innerHTML = app._currentLang == 'fr' 
						? "Identifiants incorrects.<br>Si vous venez de vous inscrire, avez-vous vérifié vos e-mails ou spam ?"
						: "Wrong credentials.<br>If you have just registered, have you checked your emails or spam ?";
					div.appendChild(message);
				}
			}					
		});
	},
	auth2: function () {

		let url = this._elem.action;
		let formData = new FormData(this._elem);	
		// si on a un panier 
		let cart = JSON.parse(localStorage.getItem('cart'));
		if(cart !== null) {
			formData.append('order', cart.id);
		}
		fetch(url, { 
		method: 'POST', 
		//headers: headers,
		body: formData
		})
		.then((res) => {
		return res.json()
		})
		.then((json) => {
			if(json.success && json.user) {
				// Authentification ok, enregistrement en session
				sessionStorage.setItem('user', JSON.stringify(json.user));
				// Choix de la redirection				
				let href = document.location.href;
				let url;
				var searchParams = new URLSearchParams(location.search);
				let referer = searchParams.get('r');
				console.log(app._currentLang);
				if(referer !== null){
					switch(referer){
						case 'checkout':							
							url = app._currentLang == 'fr' ? '/cart/checkout' : '/' + app._currentLang + '/cart/checkout';    						
    						break;
    					case 'dashboard':
    						url = app._currentLang == 'fr' ? '/account/dashboard' : '/' + app._currentLang + '/account/dashboard';    						
    						break;
    					default:
    						url = app._currentLang == 'fr' ? '/' : '/' + app._currentLang;
    						break;
					}
				} else {
					url = app._currentLang == 'fr' ? '/' : '/' + app._currentLang;
				}
				document.location.href = url;			
			} else {
				if(json.error){}
				if(json.user == null)
				{
					// Utilisateur non trouvé
					const div = document.getElementById('message');
					if (div.firstChild) div.removeChild(div.firstChild);
					const message = document.createElement('p');
					console.log(app._currentLang);
					message.innerHTML = app._currentLang == 'fr' 
						? "Identifiants incorrects.<br>Si vous venez de vous inscrire, avez-vous vérifié vos e-mails ou spam ?"
						: "Wrong credentials.<br>If you have just registered, have you checked your emails or spam ?";
					div.appendChild(message);
				}
			}					
		});
	},
	dashboard: function()
	{
		let accessTokenExpiresIn = localStorage.getItem('accessTokenExpiresIn');
		let refreshTokenExpiresIn = localStorage.getItem('refreshTokenExpiresIn');
		const now = new Date;
		let tokenTimestamp = parseInt(accessTokenExpiresIn, 10) * 1000;
		let refreshTokenTimestamp = parseInt(refreshTokenExpiresIn, 10) * 1000;
		const tokenDate = new Date(tokenTimestamp);
		const refreshTokenDate = new Date(refreshTokenTimestamp);

		// tjs aller sur le dashboard
		let attribute = this._elem.getAttribute("data-obf"); 
		let url = decodeURIComponent(window.atob(attribute));
		
		/*
			if(now.getTime() > tokenTimestamp ){
				console.log('refresh connexion');
				return;
			} else {
				var attribute = this._elem.getAttribute("data-obf"); 
				url = decodeURIComponent(window.atob(attribute));
			}
			return;

			if(jwt === null) {
				var attribute = this._elem.parentNode.getAttribute("data-obf"); 
				url = decodeURIComponent(window.atob(attribute));
				url += '?r=dashboard';

				            
			} else {
				var attribute = this._elem.getAttribute("data-obf"); 
				url = decodeURIComponent(window.atob(attribute));
			}
		*/
		
		if(this._ev.ctrlKey) {                   
			var newWindow = window.open(url, '_blank');                    
			newWindow.focus();               
		} else {                    
			document.location.href = url || '/';
		}		
	},
	open: function()
	{
		var attribute = this._elem.getAttribute("data-obf"); 
		var url =   decodeURIComponent(window.atob(attribute));
		console.log(url);
		return;            
		if(this._ev.ctrlKey) {                   
			//var newWindow = window.open(decodeURIComponent(window.atob(attribute)), '_blank');                    
			//newWindow.focus();               
		} else {                    
			//document.location.href= decodeURIComponent(window.atob(attribute));
		}
	},
	update: function()
	{
		console.log('Update user infos');
		var formData = new FormData(this._elem);
		var object = {};
		formData.forEach((value, key) => {
			// si la clÃ© est un tableau
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
		var json = JSON.stringify(object);
		//console.log(json);
		fetch(this._elem.action, { 
			method: 'PUT', 
			//headers: headers,
			body: json
			})
			.then((res) => {
			return res.json()
			})
			.then((json) => {
				if(json.success) {
					// on reload la page 
					document.location.reload();
					// On change le nom					
					// On reset le formulaire
					this._elem.reset();					
				} else {
					alert(json.error);
				}					
			});
	},
	registration: function()
	{
		let url = this._elem.action;
		let formData = new FormData(this._elem);
		fetch(url, { 
		method: 'POST',	
		body: formData
		}).then((res) => {
		return res.json()
		}).then((json) => {
			if(json.success) {			
				document.getElementById('welcome').classList.remove('hidden');
				document.querySelector('a[href="#tab-signin"]').click();
				document.querySelector('a[href="#tab-signup"]').classList.add('hide');
			} else {
				if(json.error){
					if(json.duplicate){
						// Utilisateur non trouvé
						const div = document.getElementById('message-registration');
						if (div.firstChild) div.removeChild(div.firstChild);
						const message = document.createElement('p');
						message.innerHTML = app._currentLang == 'fr' 
						? "L'inscription n'est pas possible avec cette adresse mail.<br>Vous possédez déjà un compte chez Firstracing ?"
						: "Registration is not possible with this email address.<br>Do you already have an account with Firstracing ?";
					div.appendChild(message);

					}else{
						console.error(json.error);
					}					
				}			
			}					
		});
	},
	logout: function(){
		// Authentification ok, enregistrement en session
		localStorage.removeItem('xsrfToken');
		localStorage.removeItem('accessTokenExpiresIn');
		localStorage.removeItem('refreshTokenExpiresIn');
		document.location.reload();
	},
	states: function() {
		if (this._elem === null) { return; }

		const selected = this._elem[this._elem.selectedIndex];
		const state = selected.getAttribute('data-states');
		const country = selected.value;
		const states = document.getElementById('a-line4');
		const statesLabel = states.previousSibling;

		if (state === '1') {
			const url = `/signup/states/${country}`;
			fetch(url, { method: 'GET' })
			.then(res => { return res.json() })
			.then(json => {
				let placeholder = states.firstChild;
				
				while(states.firstChild) { states.removeChild(states.firstChild) }
				states.add(placeholder);
				states.removeAttribute('disabled');
				statesLabel.classList.add('required');

				json.states.forEach(state => {
					let option = document.createElement('option');
					option.value = state.value;
					option.text = state.text;
					states.add(option);
				})
			})
		} else {
			states.selectedIndex = '0';
			states.setAttribute('disabled', true);
			statesLabel.classList.remove('required');
		}
	}
};

const customer = {
	_elem: null,
	_cookies: {},
	_infos : document.querySelectorAll('a.user-icon > span')[1],
	setElem: function(elem){
	this._elem = elem;
	},	
	setEvent: function(event){
		this._ev = event;
	},

	search: function()
	{	
		var datalist = document.getElementById(this._elem.getAttribute('data-list'));
		var min_char = 3;
		if (this._elem.value.length < min_char ) { 
			return;
		} else { 
			var url = this._elem.getAttribute('data-url') + '?search=' + this._elem.value;
			console.log(url);

			fetch(url, { 
				method: 'GET'			
				})
				.then((res) => {
					return res.json();
				})
				.then((json) => {
					if(json.success) {
						// on vide la datalist
						//while(datalist.firstChild) {datalist.removeChild(datalist.firstChild)}
						datalist.innerHTML = '';	
						json.users.forEach(function(user){
							// Create a new <option> element.
							var option = document.createElement('li');
							var a = document.createElement('a');
							a.href = user.link;
							a.textContent = user.customer;
							option.appendChild(a);
							/* option.setAttribute('data-uid', user.id);
							option.setAttribute('data-appid', user.app_id);
							option.setAttribute('data-ctrl', 'customer.show');
							option.className = 'click'; */
							datalist.appendChild(option);
						});			
					}else {
						console.log(json.error);
					}		
			});
		}
	},		
	orders: function()
	{
		// client connecté ? jwt 
		let bearer = 'Bearer ' + sessionStorage.getItem('jwt_kutvek');
		let uid = app._user.uid;
		fetch('https://dev.kutvek.com/api/customers/'+ uid +'/order-items', { 
			method: 'GET',
			withCredentials: true,
        credentials: 'include',
        headers: {
            'Authorization': bearer,
            //it can be iPhone or your any other attribute'X-FP-API-KEY': 'iphone', 
            'Content-Type': 'application/json'
        }
		})
		.then((res) => {

			if (res.ok) {	
    			return res.json();
  			}
  			throw new Error(res.statusCode);
		})
		.then((json) => {
			if(json.success) {				
				if(json.toValidate.length > 0)
				{
					var container = document.getElementById('validate').querySelector('div.items');
					var template = document.querySelector("#task");	
					var l = json.toValidate.length;
					
					for(var i = 0; i < l; i++){
						(function(item) {
							var start = 1;
							let clone = document.importNode(template.content, true);

							let label = clone.querySelector('label');
							label.setAttribute('for', label.getAttribute('for').replace(':id', item.id) );
							label.setAttribute('data-item', item.id);


							let input = clone.querySelector('input[name="rolling"]');
							input.id = input.id.replace(':id', item.id);

							let mockupContainer = clone.querySelector('div.container');
							mockupContainer.id = mockupContainer.id.replace(':id', item.id);

							let mockup = clone.querySelector('.item-mockup');
							let _mockup = false;
							let links = clone.querySelector('div.links');
							links.setAttribute('data-popup-choice', links.getAttribute('data-popup-choice').replace(':id', item.id) );

							let btns = clone.querySelector('div.item-actions').querySelectorAll('button');
							btns[0].setAttribute('data-modal', btns[0].getAttribute('data-modal').replace(':id', item.id));
							btns[1].setAttribute('data-modal', btns[1].getAttribute('data-modal').replace(':id', item.id));						
							clone.querySelector('div.item-ref').textContent = item.reference;
							clone.querySelector('div.item-desc').textContent = item.id + '/' + item.description;

							let modal_1 = clone.getElementById("mockupChoice:id");
							modal_1.id = modal_1.id.replace(':id', item.id);
							modal_1.setAttribute('data-modal', modal_1.id);
							// 1er <a>
							modal_1.querySelector('a').setAttribute('data-modal', modal_1.id);

							if(item.mocks.length > 0) {
								
								let img = document.createElement('img');

								let input = document.createElement('input');
								input.type = 'radio';
								input.classList.add('mockup');

								let label = document.createElement('label');
								label.classList.add('click');
								label.setAttribute('data-ctrl',  'carrousel.select');
								
								let current = item.mocks.pop();
								let lm = item.mocks.length;
								
								//input.classList.add('hide');
								var fragment = document.createDocumentFragment();
								var linksFragment = document.createDocumentFragment();

								for (var i = 0; i < lm; i++) {
									(function(mock) {
										let _img = img.cloneNode();										
										let _input = input.cloneNode(true);
										let _label = label.cloneNode(true);

										_img.src = mock.thumb_s;
										_input.id = 'mockup-' + mock.id;																				
										_input.name = 'mockup-item-' + item.id;
										_label.setAttribute('for',  'mockup-' + mock.id);
										_label.textContent = start;
										

										fragment.appendChild(_input);
										fragment.appendChild(_img);
										linksFragment.appendChild(_label);
										if(mock.selected > 0) {mockup.src = mock.thumb_xs; _mockup = true;}
										start++;
											
									})(item.mocks[i]);    				
		  						};

		  						let _img = img.cloneNode();								
								let _input = input.cloneNode(true);
								let _label = label.cloneNode(true);

								_img.src = current.thumb_s;
								_input.id = 'mockup-' + current.id;
								_input.name = 'mockup-item-' + item.id;
								_input.checked = true;
								_label.setAttribute('for',  'mockup-' + current.id);
								_label.textContent = start;
								_label.classList.add('checked');
								if(!_mockup) {mockup.src = current.thumb_xs; _mockup = true;}

								fragment.appendChild(_input);
								fragment.appendChild(_img);
								linksFragment.appendChild(_label);

								// let c = document.getElementById('c-' + item.id);
								// console.log(c.id);								
							}
							mockupContainer.appendChild(fragment);
							links.appendChild(linksFragment);
							container.appendChild(clone);
							//console.log(fragment);			
										
						})(json.toValidate[i]); 
					};			
					// json.toValidate.forEach(function(item) {

						
					// });
					app.i18n();
				}
				
			} else alert(json.error);			
		})
		.catch(error => {
			console.log(error);
			let url = '/' + document.documentElement.lang + '/login';
			window.location.assign(url);
		});

	
	}
};
window.addEventListener("DOMContentLoaded", function(e) {
	user.init();	
});