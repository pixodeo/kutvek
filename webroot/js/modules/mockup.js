const mockup = {	
	_elem: null,	
	_ev: null,	
	setEvent: function(event){
		this._ev = event;
	},
	setElem: function(elem){
	this._elem = elem;
	},
	display: function(){
		const container = document.querySelector(this._elem.hash);
		if(container.hasAttribute('data-display')) return;
		const mockups = container.querySelectorAll('img.mockup');
		mockups.forEach(mock => {
			let src = decodeURIComponent(window.atob(mock.getAttribute('data-file')));
			mock.src = src;			
		});
		container.setAttribute('data-display', 1);
	},
	translate: function() {
		const img = document.querySelector(`img[data-id="${this._elem.value}"]`);
		const parent = img.parentNode;
		const translate = this._elem.getAttribute('data-translate');
		let className = 'mockup-translate-' + translate; 
		parent.className = className;
		let select = document.querySelector(`#mockup-choosen-${this._elem.getAttribute('data-item')}`);
		if(select)select.value = this._elem.value;
		let label = this._elem.parentNode.querySelector('.checked');
		if(label) label.classList.remove('checked');		
	},
	select: function() {
		// onchange sur le select
		let id = this._elem.value;
		let radio = document.querySelector(`#mockup-${id}`);
		if(radio) {
			this._elem = radio;
			this.translate();
			radio.checked = true;
		}

	},
	modify: function() {


	},
	accept: async function() {
		// Le client valide une maquette, accepte éventuellement une housse de selle 
		let formData = new FormData(this._elem);
		// quel button a submit le formulaire ? pour modifier son état
    	const submitter = this._ev.submitter || document.activeElement;
    	submitter.classList.add('in-progress');
    	submitter.disabled = true;

    	let xsrfToken = localStorage.getItem('xsrfToken');		
		const headers = new Headers();
		headers.append('x-xsrf-token', xsrfToken);
		const res = await fetch(
		this._elem.action,
		{ method: 'POST', mode: 'cors', credentials: 'include', headers, body: formData}
		);
		if(!res.ok) return false;
		const json = await res.json();
		//return json;
		console.log(res.status);
		console.log(json);
		submitter.classList.remove('in-progress');
		submitter.disabled = true;
		window.location.reload();

	}
}
export default mockup;