const identity = {	
	_elem: null,	
	_ev: null,	
	setEvent: function(event){
		this._ev = event;
	},
	setElem: function(elem){
	this._elem = elem;
	},
	signin: async function(elem) {
		let url = this._elem.action;
		let _cart = false;
		let formData = new FormData(this._elem);		
		let _storage = localStorage.getItem('cart');
		if(_storage){
			_cart = JSON.parse(_storage);
			formData.append('cart', _cart.id);
		} 
		let response = await fetch(url, {method: 'POST',body: formData});     	
      	if(response.ok && response.status === 200) {
      		let json = await response.json();
      		// Authentification ok, enregistrement en session
			localStorage.setItem('xsrfToken', json.xsrfToken);
			localStorage.setItem('accessTokenExpiresIn', json.accessTokenExpiresIn);
			localStorage.setItem('refreshTokenExpiresIn', json.refreshTokenExpiresIn);
			// json.sub 			
			if(_cart){				
				_cart.user = json.sub;
				localStorage.setItem('cart', JSON.stringify(_cart));			
			}
			window.location.assign(json.redirect);
      	} else {
      		const div = document.getElementById('message');
			if (div.firstChild) div.removeChild(div.firstChild);
			const message = document.createElement('p');			
			message.innerHTML = document.documentElement.lang == 'fr' 
				? "Identifiants incorrects.<br>Si vous venez de vous inscrire, avez-vous vérifié vos e-mails ou spams ?"
				: "Wrong credentials.<br>If you have just registered, have you checked your emails or spams ?";
			div.appendChild(message);
      	}
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
			const div = document.getElementById('message-registration');
			if (div.firstChild) div.removeChild(div.firstChild);
			if(json.duplicate) {			
				// Utilisateur non trouvé				
				let message = document.createElement('p');
				message.innerHTML = document.documentElement.lang == 'fr' 
				? "L'inscription n'est pas possible avec cette adresse mail.<br>Vous possédez déjà un compte chez Kutvek ?"
				: "Registration is not possible with this email address.<br>Do you already have an account with Kutvek ?";
				div.appendChild(message);
				return;
			}
			if(json.checkInfo){
				let message = document.createElement('p');
				message.innerHTML = document.documentElement.lang == 'fr' 
				? "Merci de bien vouloir vérifier les informations que vous avez saisies."
				: "Please check the information you have entered.";
				div.appendChild(message);
				return;
			}				
			if(json.error){					
				console.error(json.error);
				return;										
			}
			document.getElementById('welcome').removeAttribute('hidden');
			document.querySelector('a[href="#tab-signin"]').click();
			document.querySelector('a[href="#tab-signup"]').classList.add('hide');
			document.getElementById('email').value = json.user.email;
		});
	},
	register: function(){
		if(sessionStorage.getItem('register') !== null) {
			alert('Dumber');
			document.getElementById('register').checked = false;
			document.getElementById('type-account').checked = true;		
			return;
		}
		let url = this._elem.action;
		let formData = new FormData(this._elem);
		fetch(url, { 
		method: 'POST',	
		body: formData
		}).then((res) => {
		return res.json()
		}).then((json) => {
			const div = document.getElementById('message-registration');
			if (div.firstChild) div.removeChild(div.firstChild);
			if(json.duplicate) {			
				// Utilisateur non trouvé				
				let message = document.createElement('p');
				message.innerHTML = document.documentElement.lang == 'fr' 
				? "L'inscription n'est pas possible avec cette adresse mail.<br>Vous possédez déjà un compte chez Kutvek ?"
				: "Registration is not possible with this email address.<br>Do you already have an account with Kutvek ?";
				div.appendChild(message);
				return;
			}
			if(json.checkInfo){
				let message = document.createElement('p');
				message.innerHTML = document.documentElement.lang == 'fr' 
				? "Merci de bien vouloir vérifier les informations que vous avez saisies."
				: "Please check the information you have entered.";
				div.appendChild(message);
				return;
			}				
			if(json.error){					
				console.error(json.error);
				return;										
			}
			let user = json.uid;
			sessionStorage.setItem('register', user);				
			document.getElementById('register').checked = false;
			document.getElementById('type-account').checked = true;										
		});
	},
	typeAccount: function(){
		//console.log(this._elem.value);
		//return;
		document.getElementById('type-account').checked = false;
		if(this._elem.value == 'pro'){
			sessionStorage.setItem('typeAccount', 'pro');
			document.getElementById('pro-account').checked = true;
		}
		else{
			sessionStorage.setItem('typeAccount', 'std');
			document.getElementById('step-address').checked = true;
		}
	},
	
	businessDocuments: async function() {
        let kbis = document.getElementById('kbis');
        if (kbis.files && kbis.files.length === 0) {
            alert("Le fichier KBIS est manquant."); return;
        }

        let url = this._elem.action;
        let formData = new FormData(this._elem);
        formData.append('kbis', kbis.files[0]);
        formData.append('user', sessionStorage.getItem('register'));

        let response = await fetch(url, { method: 'POST', body: formData });
        if (response.ok && response.status === 200) {
            let json = await response.json();

        } else {
        
        }
        document.getElementById('pro-account').checked = false;
        document.getElementById('step-address').checked = true;
	},
	address: async function() {
		let url = this._elem.action;
		let formData = new FormData(this._elem);
		formData.append('user', sessionStorage.getItem('register'));
		let response = await fetch(url, {method: 'POST',body: formData}); 
		if(response.ok && response.status === 201) {
      		let json = await response.json();

      		document.getElementById('step-address').checked = false;
			let typeAccount = sessionStorage.getItem('typeAccount');
			sessionStorage.removeItem('typeAccount');
			sessionStorage.removeItem('register');

			if(typeAccount == 'pro')
				document.getElementById('pro-welcome').checked = true;
			else
				document.getElementById('std-welcome').checked = true; 	
      		
      	} else {
      		
      	}  
		
	},
	kbis: function(){
		// Afficher le nom du ficher téléversé
		var file = this._elem.files[0];
		console.log(file);
		let span = this._elem.nextElementSibling;
		span.textContent = file.name;
	},
	back: function(){
		// Revenir à l'étape précédente
		let _current = this._elem.getAttribute('data-current');
		let _prev = this._elem.hash;
		// si on revient sur sélection de type de compte
		if(_prev === '#type-account'){
			let inputId = sessionStorage.getItem('typeAccount');

			document.getElementById(inputId).checked = false;
		}
		document.getElementById(_current).checked = false;
		document.querySelector(_prev).checked = true;

	},
	checkEmail: async function() {
		let url = this._elem.action;
		let formData = new FormData(this._elem);
		const msgInfo = document.querySelector('.msg-info');
		msgInfo.innerHTML = '';
		msgInfo.classList.remove('warning');
		let response = await fetch(url, {method: 'POST',body: formData}); 
		if(response.ok ) {
      		let json = await response.json();
      		if(!json.send){
      			msgInfo.classList.add('warning');
      		} 
      		msgInfo.innerHTML = json.msg.designation + json.msg.description;
      		if(json.send){
      			msgInfo.classList.add('success');
      			document.getElementById('reset').classList.add('hidden');
      			document.getElementById('send').disabled = true;
      		}
      		
      	} else {
      		
      	}  		
	},
	resetPassword: async function(){
		let url = this._elem.action;
		let formData = new FormData(this._elem);
		
		const msgInfo = document.querySelector('.msg-info');
		msgInfo.innerHTML = '';
		msgInfo.classList.remove('warning');
		let response = await fetch(url, {method: 'POST',body: formData}); 
		if(response.ok ) {
      		let json = await response.json();
      		if(!json.modified){
      			msgInfo.classList.add('warning');
      		} 	
      		msgInfo.innerHTML = json.msg.designation + json.msg.description;
      		// lien vers la connexion 
      		if(json.modified){
      			msgInfo.classList.add('success');
      			document.getElementById('signin-link').classList.remove('hidden');
      			document.getElementById('reset').classList.add('hidden');
      		}

      		
      	} else {
      		
      	} 
	}



}
export default identity;