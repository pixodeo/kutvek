const vehicle = {
    _elem: null,
    _ev: null,
    setEvent: function (event) {
        this._ev = event;
    },
    setElem: function (elem) {
        this._elem = elem;
    },
    brands: function () {
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        const url = option.getAttribute('data-uri');

        fetch(url, { method: 'GET' })
            .then((res) => res.json())
            .then((json) => {
                if (json.brands && json.brands.length > 0) {
                    let _target = document.getElementById(this._elem.getAttribute('data-target'));
                    _target.disabled = 'disabled';
                    let fragment = document.createDocumentFragment();
                    let childNode = _target.firstElementChild;

                    while (_target.firstChild) { _target.removeChild(_target.firstChild) }
                    
                    fragment.appendChild(childNode);
                    json.brands.forEach(brand => {
                        let option = document.createElement('option');
                        option.value = brand.value;
                        option.textContent = brand.text;
                        option.setAttribute('data-uri', brand.uri);
                        fragment.appendChild(option);
                    });
                    _target.appendChild(fragment);
                    //_target.value = _target.options[1].value;
                    _target.disabled = false;
                }
            });
    },
    models: function(){
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        const url = option.getAttribute('data-uri');
        fetch(url, { method: 'GET' })
        .then((res) => res.json())
        .then((json) => {
            if (json.models && json.models.length > 0) {
                let _target = document.getElementById(this._elem.getAttribute('data-target'));
                _target.disabled = 'disabled';
                let fragment = document.createDocumentFragment();
                let childNode = _target.firstElementChild;
                while (_target.firstChild) { _target.removeChild(_target.firstChild) }                    
                fragment.appendChild(childNode);

                json.models.forEach(model => {
                    let option = document.createElement('option');
                    option.value = model.value;
                    option.textContent = model.text;
                    option.setAttribute('data-uri', model.uri);
                    fragment.appendChild(option);
                });
                _target.appendChild(fragment);
                _target.disabled = false;
                //_target.value = _target.options[1].value;
            }

        });

    },
    versions : function() {
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        const url = option.getAttribute('data-uri');
        fetch(url, { method: 'GET' })
        .then((res) => res.json())
        .then((json) => {
            if (json.models && json.models.length > 0) {
                let _target = document.getElementById(this._elem.getAttribute('data-target'));
                _target.disabled = 'disabled';
                let fragment = document.createDocumentFragment();
                let childNode = _target.firstElementChild;
                while (_target.firstChild) { _target.removeChild(_target.firstChild) }                    
                fragment.appendChild(childNode);

                json.models.forEach(model => {
                    let option = document.createElement('option');
                    option.value = model.value;
                    option.textContent = model.text;
                    option.setAttribute('data-uri', model.uri);
                    option.setAttribute('data-seat', model.seat_cover);
                    fragment.appendChild(option);
                });
                _target.appendChild(fragment);
                _target.disabled = false;
                //_target.value = _target.options[1].value;
            }

        });
    },
    vehicles: function () {
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        const url = option.getAttribute('data-uri');

        fetch(url, { method: 'GET' })
            .then((res) => res.json())
            .then((json) => {
                if (json.vehicles && json.vehicles.length > 0) {
                    let _target = document.getElementById(this._elem.getAttribute('data-target'));
                    _target.disabled = 'disabled';
                    let fragment = document.createDocumentFragment();
                    let childNode = _target.firstElementChild;

                    while (_target.firstChild) { _target.removeChild(_target.firstChild) }
                    
                    fragment.appendChild(childNode);
                    json.vehicles.forEach(vehicle => {
                        let option = document.createElement('option');
                        option.value = vehicle.value;
                        option.textContent = vehicle.text;
                        option.setAttribute('data-uri', vehicle.uri);
                        fragment.appendChild(option);
                    });
                    _target.appendChild(fragment);
                    _target.disabled = false;
                }
            });
    
    },
    years: function () {
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        let url = option.getAttribute('data-uri');

        fetch(url, {
            method: 'GET'
        }).then((res) => {
            return res.json();
        }).then((json) => {
            if (json.years && json.years.length > 0) {

                let _target = document.getElementById(this._elem.getAttribute('data-target'));
                _target.disabled = 'disabled';
                let fragment = document.createDocumentFragment();
                let childNode = _target.firstElementChild;
                while (_target.firstChild) { _target.removeChild(_target.firstChild) }
                fragment.appendChild(childNode);
                json.years.forEach(year => {
                    let option = document.createElement('option');
                    option.value = year.value;
                    option.textContent = year.text;
                    option.setAttribute('data-uri', year.uri);
                    fragment.appendChild(option);
                });
                _target.appendChild(fragment);
                _target.disabled = false;
            }
        });
    },
    yearTypeKit: function() {
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index]; 
  
        let url = option.getAttribute('data-uri');     
        console.log(`${option.textContent} : ${url}`);
  
        fetch(url, {
        method: 'GET'
        }).then((res) => {
            return res.json();
        }).then((json) => {
          if(json.types && json.types.length > 0){
            let _hasSelected = false;
            const _target = document.getElementById(this._elem.getAttribute('data-target'));
            _target.disabled = 'disabled'; 
            let selected = _target.value; 
            console.log(selected);      
            let fragment = document.createDocumentFragment();
            let childNode = _target.firstElementChild;
            
            //return;
            while (_target.firstChild) { _target.removeChild(_target.firstChild) }
            if(childNode) fragment.appendChild(childNode);
            // penser à sélectionner 
            json.types.forEach(_type =>{
                let option = document.createElement('option');
                option.value = _type.value;
                option.textContent = _type.text;
                if(_type.value == selected){
                  option.selected = 'selected';
                  _hasSelected = true;
                }
                //option.setAttribute('data-uri', year.uri);
                fragment.appendChild(option);
            });
            _target.appendChild(fragment);
            if(!_hasSelected){
              // on prend la 1ere option avec une valeur et on la sélectionne
              // puis on fire l'event onchange dessus
              // 
            }
            _target.disabled = false;
          }
            
            document.getElementById('accessories').innerHTML = json.accessories;
            this.checkAccessories();
          });
    }
}

export default vehicle;