const user = {	
	_elem: null,	
	_ev: null,	
	setEvent: function(event){
		this._ev = event;
	},
	setElem: function(elem){
	this._elem = elem;
	},
	init: function() {
		return;
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
	dashboard: function()
	{   // tjs aller sur le dashboard
		let attribute = this._elem.getAttribute("data-obf"); 
		let url = decodeURIComponent(window.atob(attribute));		
		if(this._ev.ctrlKey) {                   
			var newWindow = window.open(url, '_blank');                    
			newWindow.focus();               
		} else {                    
			document.location.href = url || '/';
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
	logout: async function(){
		// Authentification ok, enregistrement en session
		localStorage.removeItem('xsrfToken');
		localStorage.removeItem('accessTokenExpiresIn');
		localStorage.removeItem('refreshTokenExpiresIn');
		// enlever le cookie access_token et l'user du panier
		let _storage = localStorage.getItem('cart');
		let url = '/api/identity/logout';
		if(_storage){
			let _cart = JSON.parse(_storage);
			url = url + '?cart=' + _cart.id;
			_cart.user = null;
			localStorage.setItem('cart', JSON.stringify(_cart));
		}
		let res = await fetch(
			url, { method: 'DELETE', mode: 'cors', credentials: 'include'}
			);
		if(!res.ok) return false;
		else {
			console.log('success deconnexion');
			window.location.assign(this._elem.href);
		}
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
export default user;