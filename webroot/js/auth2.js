	let accessTokenExpiresIn = localStorage.getItem('accessTokenExpiresIn');
	let refreshTokenExpiresIn = localStorage.getItem('refreshTokenExpiresIn');
	let urlLogin = decodeURIComponent(window.atob(document.documentElement.getAttribute('data-obf')));
	
	if(!accessTokenExpiresIn) {
		// Il n'y a pas d'infos de connexion	
		document.location.href = urlLogin || '/';
	}
	const now = new Date;
	let tokenTimestamp = parseInt(accessTokenExpiresIn, 10) * 1000;
	let refreshTokenTimestamp = parseInt(refreshTokenExpiresIn, 10) * 1000;

	//console.log('Date actuelle :');
	//console.log(now.toString());

	const tokenDate = new Date(tokenTimestamp);
	//console.log('Date Access token :');
	//console.log(tokenDate.toString());

	const refreshTokenDate = new Date(refreshTokenTimestamp);
	//console.log('Date Refresh token :');
	//console.log(refreshTokenDate.toString());

	//console.log('Timestamps :');
	//console.log(`Actuel : ${now.getTime()}, Token : ${accessTokenExpiresIn},  Token ajusté : ${tokenTimestamp}`);				

	if(now.getTime() > tokenTimestamp ){					
		getNewAccessToken()
		.then( json => {				
				// Authentification ok, enregistrement en session
				if(json.success){
					localStorage.setItem('xsrfToken', json.xsrfToken);
					localStorage.setItem('accessTokenExpiresIn', json.accessTokenExpiresIn);
					localStorage.setItem('refreshTokenExpiresIn', json.refreshTokenExpiresIn);
					document.location.reload();
				}								
			}
		).catch(error => {console.debug(error)});			
	}	

	async function getNewAccessToken() {
		console.log('Get new accessToken...');
		
		const res = await fetch(
			'/api/token',
			{ method: 'GET', mode: 'cors', credentials: 'include'}
		);
		if(!res.ok) return false;
		const json = await res.json();
		return json;			
	}