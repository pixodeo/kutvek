const customer = {	
	_elem: null,	
	_ev: null,	
	setEvent: function(event){
		this._ev = event;
	},
	setElem: function(elem){
	this._elem = elem;
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
	},
	unsetAddress: async function(){
        
        const res = await fetch(this._elem.href, {method: 'DELETE', mode: 'cors', credentials: 'include'});
        if(res.redirected){
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();

        }

    },
    setAddress: async function(){       
        const res = await fetch(this._elem.href, {method: 'PATCH', mode: 'cors', credentials: 'include'});
        if(res.redirected){
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();
        }
    },
    updateAddress: async function(){
        let formData = new FormData(this._elem);
        const jsonData = {};
        for (var pair of formData.entries()) {
            jsonData[pair[0]] = pair[1];
        }
        const res = await fetch(this._elem.action, {method: 'PUT', mode: 'cors', credentials: 'include', body: JSON.stringify(jsonData)});
        if(res.redirected){
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();
        }
        
         
    },
    acceptPub: async function(){
        let url = this._elem.getAttribute('data-action');
        let json = {
            accept_pub: this._elem.checked ? 1 : 0
        }
        const res = await fetch(url, {method: 'PATCH', mode: 'cors', credentials: 'include', body: JSON.stringify(json)});
        if(res.redirected) {
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();
        }

    },
    checkEmail: async function(){
        let url = this._elem.action;
        let formData = new FormData(this._elem);
        const msgInfo = document.querySelector('.msg-info');
        msgInfo.innerHTML = '';
        msgInfo.classList.remove('warning');
        const res = await fetch(url, {method: 'POST', mode: 'cors', credentials: 'include', body: formData}); 
        const json = await res.json();       
        if(res.ok ) {            
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
    updateEmail: async function(){ 
        let formData = new FormData(this._elem);
        let json = {email: formData.get('email')};      
        const res = await fetch(this._elem.action, {method: 'PATCH', mode: 'cors', credentials: 'include', body: JSON.stringify(json)});
        if(res.redirected){
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();
        }
    },
    updatePwd: async function(){
        let formData = new FormData(this._elem);
        const jsonData = {};
        for (var pair of formData.entries()) {
            jsonData[pair[0]] = pair[1];
            //console.log(pair[0] + ", " + pair[1]);
        }  
        //console.log(json);
        //return;
        const res = await fetch(this._elem.action, {method: 'PATCH', mode: 'cors', credentials: 'include', body: JSON.stringify(jsonData)});
        if(res.redirected){
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();
        }
    },
    resetPwd: async function(){
        let formData = new FormData(this._elem);
        const jsonData = {};
        for (var pair of formData.entries()) {
            jsonData[pair[0]] = pair[1];
        }  
        const res = await fetch(this._elem.action, {method: 'PATCH', mode: 'cors', credentials: 'include', body: JSON.stringify(jsonData)});
        if(res.redirected){
            window.location.assign(res.url);
        }
        if(!res.ok){
            const json = await res.json();
        }
    }

};

const checkoutInfo = async function() {		
	let _hasUser = await checkUser();
	if(! _hasUser) {
		return false;
	}
	let xsrfToken = localStorage.getItem('xsrfToken');		
	// inclure token csrf
	const headers = new Headers();
	headers.append('x-xsrf-token', xsrfToken);
	const res = await fetch(
		'/api/customers/checkout-info',
		{ method: 'GET', mode: 'cors', credentials: 'include', headers}
	);
	if(!res.ok) return false;
	const json = await res.json();
	return json;
};

const checkUser = async function(){
	let response = await fetch(`/asset/js/modules/auth.js`, {method: 'GET'});
    let _asset = response.ok ? await response.text() : '/js/modules/auth.js';     
    _asset = _asset.replace('/js', '');
    let  _module = await import(`..${_asset}`);
    let success = await _module.default.check();
    return success;  
};
export {checkoutInfo};
export default customer;