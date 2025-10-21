const country = {
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
}


