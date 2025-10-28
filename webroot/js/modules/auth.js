const auth = {
	_ev: null,
  	_elem:null,
  	lang:document.documentElement.lang,   
   	setElem: function (elem) {
    this._elem = elem;
  	},
  	setEvent: function (event) {
    this._ev = event;
  	},
	check: async function() {
		let accessTokenExpiresIn = localStorage.getItem('accessTokenExpiresIn');
	    let refreshTokenExpiresIn = localStorage.getItem('refreshTokenExpiresIn');

	    // pas d' accesTokenExpireIn, pas d'utilisateur, il y aura  reconnexion
	    if(!accessTokenExpiresIn) return false;
		
		// Vérification de la validité des token
		let now = new Date;
		let tokenTimestamp = parseInt(accessTokenExpiresIn, 10) * 1000;
		let refreshTokenTimestamp = parseInt(refreshTokenExpiresIn, 10) * 1000;
		if(now.getTime() > tokenTimestamp ){
			// Nouveau token d'accès
			let res = await fetch(
			'/api/token', { method: 'GET', mode: 'cors', credentials: 'include'}
			);
			if(!res.ok) return false;
			let json = await res.json();
			if(json.success){
				localStorage.setItem('xsrfToken', json.xsrfToken);
				localStorage.setItem('accessTokenExpiresIn', json.accessTokenExpiresIn);
				localStorage.setItem('refreshTokenExpiresIn', json.refreshTokenExpiresIn);				
			}
			return json.success;		
		} else {
			return true;
		}
	},
	login: async function() {

		console.log("Form submit event.");
		  if (this._elem.checkValidity() === false) {
		    console.log("Form values not valid");		   
		  } else {
		    const signinButton = document.querySelector("button#signin");
		    signinButton.disabled = true;
		    const body = new FormData(this._elem);
		    const req = await fetch(this._elem.action, {method: 'POST', body: body, mode: 'cors', credentials: 'include'})
		    if(req.ok){
		    	const json = await req.json();
		    	localStorage.setItem('xsrfToken', json.xsrfToken);
				localStorage.setItem('accessTokenExpiresIn', json.accessTokenExpiresIn);
				localStorage.setItem('refreshTokenExpiresIn', json.refreshTokenExpiresIn);
				signinButton.disabled = false;
				document.location.reload();
				return;
		    }
		    const json = await req.json();
		    //form.submit();
		    // document.body.innerHTML = "<p style='font-size: 24px'>You are now signed in.</p>"
		  }
	}
}
export default auth;