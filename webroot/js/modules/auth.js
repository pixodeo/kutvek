const auth = {
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
	}
}
export default auth;