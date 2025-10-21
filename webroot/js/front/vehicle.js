const vehicle = {
  _elem: null,
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  create: function () {
    const form = this._elem;
    fetch(form.action, {
      method: form.method,
      body: new FormData(form)
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          var ul = document.getElementById('vehicles-added');
          var template = document.querySelector("#vehicle-item");

          var clone = document.importNode(template.content, true);
          var li = clone.querySelector('li');
          li.textContent = data.item;
          ul.append(clone);
          // si on est sur la page vÃ©hicle il faut rajouter une ligne au tableau
          form.reset();
        } else {

        }
      });
  },
  read: function () {
    let url = this._elem.href;
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.text()
      })
      .then((data) => {
        if (data) {
          window.location.hash = 'licence';
          document.querySelector('#licence').innerHTML = data;
        }
      });
  },
  update: function () {
    var formData = new FormData(this._elem);
    var object = {};
    formData.forEach((value, key) => {
      // Reflect.has in favor of: object.hasOwnProperty(key)
      if (!Reflect.has(object, key)) {
        object[key] = value;
        return;
      }
      if (!Array.isArray(object[key])) {
        object[key] = [object[key]];
      }
      object[key].push(value);
    });
    var json = JSON.stringify(object);
    console.log(json);
    //return;
    fetch(this._elem.action, {
      method: 'PUT',
      //headers: headers,
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On change le nom
          document.getElementById('title').textContent = json.fullname;
          // On reset le formulaire
          this._elem.reset();

        } else {
          alert(json.error);

        }

      });

  },
  list2: function () {
    let id = this._elem.value;
    let uid = this._elem.parentNode.querySelector('.universe').value;
    fetch('/cockpit/vehicles/list?universe=' + uid + '&brand=' + id, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          const vehicles = json.vehicles;
          let id = this._elem.getAttribute('data-target');
          let select = document.getElementById(id); // cible		
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);
          if (vehicles.length > 0) {
            vehicles.forEach((vehicle) => {
              let option = document.createElement('option');
              option.textContent = vehicle.text;
              option.value = vehicle.value;
              fragment.appendChild(option);
            });
            select.appendChild(fragment);
            select.disabled = null;
          }

        } else {
          console.log(json.error);
        }
      });
  },
  list: function () {
    const form = document.getElementById(this._elem.getAttribute('form'));
    var brand = this._elem.value;
    var universe = form.querySelector('select.universe').value;
    let url = 'https://dev.kutvek.com/vehicles.getVehicle?universe=' + universe + '&brand=' + brand;
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.vehicles.length > 0) {
          let select = form.querySelector(this._elem.getAttribute('data-target')); // cible				
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;

          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);

          json.vehicles.forEach((model, index) => {
            let option = document.createElement('option');
            option.textContent = model.name;
            option.value = model.id;
            option.setAttribute('data-price', model.price);
            fragment.appendChild(option);
          });

          select.appendChild(fragment);
          select.disabled = null;
        }
      });
  },
  filter: function () {
    const queries = [
      { name: "universe", "value": this._elem.querySelector('#universe').value },
      { name: "brand", "value": this._elem.querySelector('#brand').value },
      { name: "model", "value": this._elem.querySelector('#model').value }
    ];

    var filter = queries.filter(query => query.value.length > 0);

    const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');

    /* console.log(queries);
    console.log(filter);
    console.log(asString);
    return; */
    let url = this._elem.action + '?' + asString + '&xhr=1';
    fetch(url, {
      method: this._elem.method
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.vehicles.length > 0) {
          var tbody = document.querySelector("#list-vehicles-body");
          while (tbody.firstChild) { tbody.removeChild(tbody.firstChild) }
          var template = document.querySelector("#vehicle-row");
          json.vehicles.forEach(function (vehicle) {
            var clone = document.importNode(template.content, true);
            var tds = clone.querySelectorAll("td");
            tds[0].querySelector('span').textContent = vehicle.v_id;
            tds[0].querySelector('input').value = vehicle.v_id;
            tds[1].textContent = vehicle.u_name;
            tds[2].textContent = vehicle.b_name;
            tds[3].textContent = vehicle.v_name;
            tds[5].innerHTML = vehicle.link;
            tbody.append(clone);
          });
        }
      });
  },
  bindAccessory: function () {
    const form = this._elem;
    const formData = new FormData(form);
    const id = formData.get('vehicle');
    const url = form.action.replace(':id', id);
    formData.delete('universe');
    formData.delete('brand');
    formData.delete('vehicle');
    fetch(url, {
      method: form.method,
      body: formData
    }).then((res) => {
      return res.json();
    }).then((json) => {
      if (json.success) {
        var ul = form.parentNode.querySelector('ul');
        var template = document.querySelector(".list-item");

        var clone = document.importNode(template.content, true);
        var li = clone.querySelector('li');
        li.textContent = json.vehicle.v_fullname;
        ul.append(clone);
        // si on est sur la page vÃ©hicle il faut rajouter une ligne au tableau
        form.reset();
      } else {
        alert(json.error);
      }
    });
  },
  deleteAccessory: function () {
    console.log(this._elem.href);
    fetch(this._elem.href, {
      method: 'DELETE',
      //headers: headers,

    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On change le nom


        } else {

        }

      });
  },
  match: function () {
    const select = this._elem;
    const parent = document.getElementById(this._elem.getAttribute('form'));

    if (this._ev.type == 'change') {
      const method = parent.getAttribute('data-onchange');
      this[method]();

    } else {
      if (select.classList.contains('hydrated')) return;
      else {
        // On va chercher la liste des vÃ©hicules correspondant Ã  l'univers et Ã  la marque
        const formData = new FormData(parent);
        const queries = [
          { name: "universe", "value": formData.get('universe') },
          { name: "brand", "value": formData.get('brand') }
        ];
        var filter = queries.filter(query => query.value.length > 0);
        if (filter.length < 2) return;
        const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');
        let url = parent.action + '?' + asString;

        fetch(url, {
          method: 'GET'
        })
          .then((res) => {
            return res.json()
          })
          .then((json) => {
            if (json.success) {
              const vehicles = json.list;
              let fragment = document.createDocumentFragment();
              let childNode = select.firstElementChild;
              while (select.firstChild) { select.removeChild(select.firstChild) }
              fragment.appendChild(childNode);
              select.appendChild(fragment);
              vehicles.forEach((vehicle) => {
                let option = document.createElement('option');
                option.textContent = vehicle.text;
                option.value = vehicle.value;
                fragment.appendChild(option);
              });
              select.appendChild(fragment);
              select.disabled = null;
              select.classList.add('hydrated');
            } else {
              alert(json.error);
            }
          });
      }
    }

  },
  matchUpdate: function () {
    const select = this._elem;
    const parent = document.getElementById(this._elem.getAttribute('form'));
    const formData = new FormData(parent);
    const object = {
      universe: formData.get('universe'),
      brand: formData.get('brand'),
      model: formData.get('model'),
      vehicle: select.value
    }
    const json = JSON.stringify(object);
    let url = parent.getAttribute('data-update');

    fetch(url, {
      method: 'PUT',
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {

        } else {
          alert(json.error);
        }
      });
  },
  matchUniverse: function () {
    const parent = document.getElementById(this._elem.getAttribute('form'));
    var formData = new FormData(parent);
    const object = {
      universe: formData.get('universe'),
      brand: formData.get('brand'),
      model: formData.get('model')
    }
    const json = JSON.stringify(object);
    let url = parent.getAttribute('data-universe-update');
    fetch(url, {
      method: 'PUT',
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On autorise le select des vÃ©hicules Ã  Ãªtre de nouveau hydratÃ©
          parent.querySelector('select[name="vehicle"]').classList.remove('hydrated');
        } else {
          alert(json.error);
        }
      });
  },
  matchMillesim: function () {
    const select = this._elem;
    const parent = this._elem.parentNode;
    let id = select.value;
    let formData = new FormData(parent);
    formData.delete('universe');
    formData.delete('brand');
    let url = parent.getAttribute('data-create');

    fetch(url, {
      method: 'POST',
      body: formData
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          console.log(`Ajout de millesims pour le vehicule: - ${id}`);

        } else {
          alert(json.error);
        }
      });
  },
  millesims: function () {
    let id = this._elem.value;
    //let uri = this._elem.getAttribute('data-uri');
    let url = new URL(this._elem.getAttribute('data-uri'));
    let params = new URLSearchParams(url.search);
    console.log(url)
    // option selectionnée 
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    let price = option.getAttribute('data-price');
    // recalculer le prix 
    // il faut modifier kit_type


    //Add a second foo parameter.
    params.append('vehicle', id);
    url.search = params;
    fetch(url.href, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {


        if (json.millesims.length > 0) {

          let select = document.getElementById('millesim'); // cible	
          select.disabled = 'disabled';
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          json.millesims.forEach((millesim) => {
            let option = document.createElement('option');
            option.textContent = millesim.year;
            option.value = millesim.id;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
          item.price(id);
        }
      });
  },
  products: function () {
    let id = this._elem.value;
    let uri = this._elem.getAttribute('data-uri').replace(':vehicle', id);
    let url = new URL(uri);
    let params = new URLSearchParams(url.search);

    // Le design et le coloris			
    if (this._elem.hasAttribute('data-design')) params.append('design', this._elem.getAttribute('data-design'));
    if (this._elem.hasAttribute('data-color')) params.append('color', this._elem.getAttribute('data-color'));

    url.search = params;


    fetch(url.href, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.products.length > 0) {
          let kitType = document.getElementById('kit-type');
          let fragment = document.createDocumentFragment();
          let value = kitType.value;

          //let childNode = kitType.firstElementChild;
          while (kitType.firstChild) { kitType.removeChild(kitType.firstChild) }
          //fragment.appendChild(childNode);								

          // let index = kitType.selectedIndex;
          // let opt = kitType.options[index];
          kitType.disabled = 'disabled';

          json.products.forEach((product) => {
            let option = document.createElement('option');
            option.textContent = product.text;
            option.value = product.price;
            option.setAttribute('data-product', product.product);
            option.setAttribute('data-id', product.kit_type);
            option.setAttribute('data-name', product.type_name);
            if (product.price == value) option.selected = true;
            fragment.appendChild(option);
          });

          kitType.appendChild(fragment);
          kitType.disabled = null;

        }
        if (json.millesims.length > 0) {
          let select = document.getElementById('millesim'); // cible	
          select.disabled = 'disabled';
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          json.millesims.forEach((millesim) => {
            let option = document.createElement('option');
            option.textContent = millesim.year;
            option.value = millesim.id;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
          //item.price(id);			
        }
      });
  },
  years: async function() {
    const index = this._elem.selectedIndex;

    // option sélectionée
    const option = this._elem.options[index];
    let url = option.getAttribute('data-uri');
   
    const fetch_1 = await fetch(url, {method: 'GET', mode: 'cors', credentials: 'include'});
    
    if(!fetch_1.ok){
      return;
    }
    const json = await fetch_1.json();
    if(json.years && json.years.length > 0){
      let _target = document.getElementById(this._elem.getAttribute('data-target'));
      _target.disabled = 'disabled';        
      let fragment = document.createDocumentFragment();
      let childNode = _target.firstElementChild;
      while (_target.firstChild) { _target.removeChild(_target.firstChild) }
      fragment.appendChild(childNode);
      json.years.forEach(year =>{
        let option = document.createElement('option');
        option.value = year.value;
        option.textContent = year.text;
        option.setAttribute('data-uri', year.uri);
        fragment.appendChild(option);
      });
      _target.appendChild(fragment);
      _target.disabled = false;
    }
    // On reçoit les gabarits des housses de selle
      // Utilisé si le produit propose l'option selle 
      const _inputSeat = document.getElementById('seat-cover');          
      if(_inputSeat) {
        // la modal
        const modal = document.getElementById('seats-millesims');
        let _select = modal.querySelector('#seat-millesim');
        let fragment = document.createDocumentFragment();
        let childNode = _select.firstChild;
        while (_select.firstChild) { _select.removeChild(_select.firstChild) }
        if(childNode) fragment.appendChild(childNode);
        if(json.seats && json.seats.length > 0) {    
          json.seats.forEach(year =>{
            let option = document.createElement('option');
            option.value = year.value;
            option.textContent = year.text;            
            fragment.appendChild(option);
          });
          _select.appendChild(fragment);
          _select.disabled = false;
        }
        if(_inputSeat.checked == true) {
          modal.classList.toggle('visible');
        } 
      } 
    
    // Si on a l'id d'un produit, on doir avoir des images , designation et description différente
    let item = option.getAttribute('data-item');
    
    if(item) {
      const fetch_2 = await fetch('/products/sibling/' + item, {method: 'GET', mode: 'cors', credentials: 'include'});    
      if(!fetch_2.ok){
        return;
      }
      
      
      let _catched = false;
      const json_2 = await fetch_2.json();
      if(json_2.product){
        // Attributs, options etc
        const attributes = json_2.product.attributes;        
        for (const prop in attributes) {
          //console.log(`${prop}: ${attributes[prop]}`);
          switch (prop) {
            case 'door_stickers':
              const input = document.getElementById('door-stickers');
              _catched = true;
              console.log('Vérifier si modif sur option sticker de portes');
              if(attributes[prop] === null){
                // si existe en option sélectionnée on la vire
                const p = document.querySelector('p.door-stickers');
                if(p && p.getAttribute('data-checked') == 1){
                  input.parentNode.querySelector('label.label-primary').click();
                  input.parentNode.parentNode.removeChild(input.parentNode);
                }
                break;
              }
              let currency_code = json_2.product.currency.designation;
              let l10n = json_2.product.l10n.replace('_', '-');
              let price = attributes[prop].price;
              if(price == null) _catched = false;
              let designation = attributes[prop].name;            
              let pricef = new Intl.NumberFormat(l10n, { style: 'currency', currency: currency_code, maximumFractionDigits: 2 }).format(price);
              
              if(input){
                if(attributes[prop].id == input.getAttribute('data-id')){
                  console.log('option sticker de portes identique');  
                  return;
                }
                else{
                  console.log('option sticker de portes différente');
                  
                  input.setAttribute('data-name', designation);
                  input.setAttribute('data-id', attributes[prop].id);
                  input.setAttribute('data-pricef', pricef);
                  input.value = attributes[prop].price;
                  input.setAttribute('data-text', `${designation} + ${pricef}`);
                  let label = input.parentNode.querySelector('label.label-primary');
                  label.querySelector('span').textContent = designation;
                  label.querySelector('.picto').src = '/img/pictos/options/doors-option.png';
                  input.parentNode.querySelector('span.price').textContent = pricef;
                  if(input.checked){
                    input.checked = false;
                    label.click();
                  }
                }
            } else {
              // on avait pas de doors stickers
              // il faudrait le rajouter
              let optionContainer = document.querySelector('div.options-container');
              let tpl = document.getElementById('door-sticker-tpl');
              let option = document.importNode(tpl.content, true);
              let input = option.querySelector('input');
              input.id = 'door-stickers';
              input.setAttribute('data-name', designation);
              input.setAttribute('data-id', attributes[prop].id);
              input.setAttribute('data-pricef', pricef);
              input.value = attributes[prop].price;
              input.setAttribute('data-text', `${designation} + ${pricef}`);
              let label = input.parentNode.querySelector('label.label-primary');
              label.querySelector('span').textContent = designation;
              label.querySelector('.picto').src = '/img/pictos/options/doors-option.png';
              input.parentNode.querySelector('span.price').textContent = pricef;
              optionContainer.appendChild(input.parentNode);              
            }
            break;         
          }
        }
       
        // on met d'autres images dans la gallerie
        let files = json_2.product.files;
        let _count = files.length;
        const _width = _count > 1 ? _count * 100 : 100;
        const _imgWidth = _count > 0 ? 100 / _count : 100;
        const _slider = document.getElementById('g-1').querySelector('div.slider');
        const _thumbnails = document.querySelector('div[data-gallery="g-1"]');
        _slider.style.width = `${_width}%`;
        while (_slider.firstChild) { _slider.removeChild(_slider.firstChild) }
        while (_thumbnails.firstChild) { _thumbnails.removeChild(_thumbnails.firstChild) }
        // parcours de la liste des files
        let template = document.getElementById('slider-file');
        let _imgf = document.importNode(template.content, true);
        // thumbnails
        let templateThumbnails = document.getElementById('thumbnail-tpl');
        let _thumb = document.importNode(templateThumbnails.content, true);
        files.forEach((file, idx) => {
          if(file.type == 'image'){
            let _clone = _imgf.cloneNode(true);
            let _cloneThumb = _thumb.cloneNode(true);
            let _p =  _clone.querySelector('p');
            let _img = _clone.querySelector('img');
            let _imgThumb = _cloneThumb.querySelector('img');
            _p.id = `file-${file.id}`;
            _p.style.width = `${_imgWidth}%`;
            _img.src = file.url;
            _img.setAttribute('srcset', `${file.w360.url} 360w, ${file.w800.url} 800w`);
            _imgThumb.src = file.url;
            _imgThumb.setAttribute('srcset', `${file.w64.url} 64w`);
            if(idx == 0) {
              _imgThumb.setAttribute('data-direction', 'right');
              _imgThumb.setAttribute('data-translate', 0);
            } else {
              _imgThumb.setAttribute('data-direction', 'left');
              _imgThumb.setAttribute('data-translate', -(_imgWidth * idx));
            }            
            _slider.appendChild(_p);
            _thumbnails.appendChild(_imgThumb);
          }
        });            
        document.getElementById('designation').textContent = json_2.product.trads.designation;
        document.getElementById('short-description').innerHTML = json_2.product.trads.short_desc;
        document.getElementById('product-description').innerHTML = json_2.product.trads.description;
        document.getElementById('product-features').innerHTML = json_2.product.trads.features;
        const install_notice = document.getElementById('install-notice');
        if(install_notice) install_notice.innerHTML = json_2.product.trads.howto_install;
          }      
    }
  },
  yearsOptions: function() {
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index]; 
    let url = option.getAttribute('data-uri');

    fetch(url, {
      method: 'GET'
    }).then((res) => {
      return res.json();
    }).then((json) => {
      console.log(json);
      if (json.years && json.years.length > 0) {
        let _target = document.getElementById(this._elem.getAttribute('data-target'));
        _target.disabled = 'disabled';        
        let fragment = document.createDocumentFragment();
        let childNode = _target.firstElementChild;
        while (_target.firstChild) { _target.removeChild(_target.firstChild) }
        fragment.appendChild(childNode);
        json.years.forEach(year =>{
            let option = document.createElement('option');
            option.value = year.value;
            option.textContent = year.text;
            option.setAttribute('data-uri', year.uri);
            fragment.appendChild(option);
        });
        _target.appendChild(fragment);
        _target.disabled = false;

        // On veut afficher les widgets selon les options disponibles sur le véhicule
        if (json.vehicle) {
          let opts = json.vehicle.opts;
          
          for (let property in opts) {
  					
  					let field = document.getElementById(property);
  					//console.log(field);
  					if (opts[property]) {
  						
  						if (field.parentNode.classList.contains('hide')) 
  							field.parentNode.classList.remove('hide');
  						if (field.hasAttribute('data-required'))
  							field.required = true;

  					} else {
  						if(!field.parentNode.classList.contains('hide')) {
  							field.parentNode.classList.add('hide');
  						}
  						// On enlève required
  						field.required = false;
  					}
				  }
        }

        const designation = document.querySelector('.designation');
				designation.textContent = json.vehicle.fullname;
      }
    });
  },
  yearTypeKit: function() {
      const index = this._elem.selectedIndex;
      const option = this._elem.options[index]; 
      const behavior = document.querySelector('input[name="behavior"]').value ?? 'DefaultBehavior';
      const isCustomGraphikKit = behavior === 'CustomGraphicKitBehavior';
     
      // Besoin d'envoyer l'id du produit pour savoir si impression sur support fluo, et donc prix des gabarits différents / majorés
      const _article = document.querySelector('article.product-sheet');
      let url = option.getAttribute('data-uri');
      if(_article){
        const _id = _article.getAttribute('data-product');
         url = url + '?product=' + _id;
      }
      
     
     
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
            
          let fragment = document.createDocumentFragment();
          let childNode = _target.firstElementChild;
          
          //return;
          while (_target.firstChild) { _target.removeChild(_target.firstChild) }
          if(childNode) fragment.appendChild(childNode);
          // penser à sélectionner 
          json.types.forEach(_type =>{
              isPriceNull = _type.price === null;
              // On n'affiche pas les plaques sur un 100% perso
              if (isCustomGraphikKit && _type.id != 7 || !isCustomGraphikKit && !isPriceNull) {
                let option = document.createElement('option');
                option.value = _type.value;
                option.textContent = _type.text;
                option.setAttribute('data-name', _type.title);
                option.setAttribute('data-id', _type.id);

                if(_type.value == selected){
                  option.selected = 'selected';
                  _hasSelected = true;
                }
                //option.setAttribute('data-uri', year.uri);
                fragment.appendChild(option);
              } 
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
  },
  checkAccessories: function(){
    let cart_accessories = document.getElementById('selected-accessories');
    let input_accessories = document.getElementById('accessories');
    cart_accessories.querySelectorAll(`p`).forEach(accessory => {
      let value = accessory.getAttribute('data-id');
      let input = input_accessories.querySelector('input[value="' + value +'"]');
      if(!input) {
        cart_accessories.removeChild(accessory)
      } else {
        input.checked = true;
      }
    });
    option.updatePrice();    
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
            option.setAttribute('data-name', vehicle.name);
            fragment.appendChild(option);
          });
          _target.appendChild(fragment);
          _target.disabled = false;
        }
      }
    );
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
          _target.disabled = false;
        }

        if (json.item) {
          this.updateItemCustomInfos(json.item);
        }
      }
    );
  },
  // Je veux mettre à jours les différents éléments qui compose l'item choisi
  updateItemCustomInfos: function (item) {
    console.log(item);
    // L'id de l'item custom
    const id = document.querySelector("input[name='item[product]']");
    id.value = item.id;
    
    // Le poids
    const weight = document.querySelector("input[name='item[weight]']");
    weight.value = item.weight;

    // L'images des emplacements de sponsors
    const templateSponsor = document.querySelector('#template-sponsors > img');
    templateSponsor.src = item.template_sponsors
    
    // Le nombre de sponsors - item.nb_sponsor
    const gridSponsors = document.querySelector('.grid-sponsors');
    const template = document.getElementById('sponsor-input-tpl');
    
    // Supprimer les sponsors existants
    while(gridSponsors.firstChild) { gridSponsors.removeChild(gridSponsors.firstChild); }
    
    for (let index = 1; index <= parseInt(item.nb_sponsor); index++) {
      const clone = document.importNode(template.content, true);
      clone.querySelector('span.place').textContent = index;
      clone.querySelector("input[name='opts[sponsor]'").name = `opts[sponsor][${index}]`;
      clone.querySelector('label').setAttribute('for', 'sp-' + index);

      const file = clone.querySelector("input[type='file']");
      file.id = 'sp-' + index;
      file.setAttribute('data-place', index);
      
      gridSponsors.appendChild(clone);
    }

    //     <p class="sponsor col-s-12 col-l-4">
//             <span class="place"><?= $i; ?></span>
    //         <input class="field-input text" type="text" name="opts[sponsor]" data-i18n="sponsor-placeholder" placeholder="Nom du sponsor" />
    //         <input class="file onchange" type="file" data-ctrl="option.uploadSponsor" />
    //         <label>
    //             <span class="icon material-symbols-rounded">download</span>
    //         </label>
    //         <span class="fileName"></span>
    //     </p>

//     <?php for ($i = 1; $i <= $product->nb_sponsor; $i++) : ?>
//         <p class="sponsor col-s-12 col-l-4">
//             <span class="place"><?= $i; ?></span>
//             <input class="field-input text" type="text" name="opts[sponsor][<?= $i; ?>]" data-i18n="sponsor-placeholder" placeholder="Nom du sponsor" />
//             <input class="file onchange" type="file" id="sp-<?= $i; ?>" data-place="<?= $i; ?>" data-ctrl="option.uploadSponsor" />
//             <label for="sp-<?= $i; ?>">
//                 <span class="icon material-symbols-rounded">download</span>
//             </label>
//             <span class="fileName"></span>
//         </p>
//     <?php endfor; ?>
  }
};