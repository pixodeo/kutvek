let monetary = function (number, l10n, currency, maximumFractionDigits = 2) {
  // ex i18n : 'de_DE'on remplace le "_" par "-"
  // ex currency : 'EUR'
  if (currency == '€') currency = 'EUR';
  if (currency == '£') currency = 'GBP';
  if (currency == '$') currency = 'USD';
  if (currency == '$ CAN') currency = 'CAD';
  if (l10n == 'fr') l10n = 'fr_FR';
  return new Intl.NumberFormat(l10n.replace('_', '-'), { style: 'currency', currency: currency, maximumFractionDigits: maximumFractionDigits }).format(number);
};

const customPlate = {
  _elem: null,
  _ev: null,
  _items: document.querySelector('#nbItems'),
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
    const familly = option.value;
    
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

          // L'univers quad a été choisis, rendre visible la section des plaques
          let plates = document.getElementById('plates-options');
          if (familly == 2 && plates.classList.contains('hide')) {
            
            plates.classList.remove('hide');
          } else {
            plates.classList.add('hide');
          }
        }
      }
      );
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
      }
      );
  },
  yearsOptions: function () {
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

        // On veut afficher les widgets selon les options disponibles sur le véhicule
        if (json.vehicle) {
          let opts = json.vehicle.opts;

          for (let property in opts) {

            let field = document.getElementById(property);
            if (opts[property]) {
              if (field.parentNode.classList.contains('hide'))
                field.parentNode.classList.remove('hide');
              if (field.hasAttribute('data-required'))
                field.required = true;
            } else {
              if (!field.parentNode.classList.contains('hide')) {
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
  increase: function () {
    var input = this._elem.previousElementSibling;
    var qty = parseInt(input.value, 10) + 1;
    input.value = qty;
    this.update(qty);
  },
  decrease: function () {
    var input = this._elem.nextElementSibling;
    var qty = parseInt(input.value, 10);
    if (qty > 1) {
      qty = qty - 1;
      input.value = qty;
      this.update(qty);
    }
  },
  uploadSponsor: function () {
    let file = this._elem.files[0];
    let span = this._elem.parentNode.querySelector('span.fileName');
    span.textContent = file.name;
  },
  finish: function () {
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.finish-opt');
    _p.textContent = _option.value != '' ? _option.textContent : '';
    _p.setAttribute('data-opt', _option.getAttribute('data-name') || 'Fini Brillant');
    _p.setAttribute('data-id', _option.getAttribute('data-id'));
    this.update();
  },
  typeKit: function () {
    if (!this._elem.value) return;
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.type-opt');
    _p.textContent = _option.value != '' ? 'Kit ' + _option.textContent : '';
    _p.setAttribute('data-opt', _option.getAttribute('data-name'));
    _p.setAttribute('data-id', _option.getAttribute('data-id'));
    this.update();
  },
  checkboxAccessory: function () {
    const id = this._elem.getAttribute('data-id');
    const price = this._elem.getAttribute('data-price');
    const pricef = this._elem.getAttribute('data-pricef'); 
    const name = this._elem.getAttribute('data-name'); 
    let accessories = document.getElementById('selected-accessories');
    
    if (this._elem.checked) {
      // Ajout à la liste des accessoires
      let p = document.createElement('p');
      p.setAttribute('data-id', id);
      p.setAttribute('data-price', price);
  
      let span = document.createElement('span');
      span.textContent = name + ' ' + pricef;
      
      p.appendChild(span);
      accessories.appendChild(p);
    } else {
      let selectedAccessory = accessories.querySelector(`[data-id="${id}"]`);
      accessories.removeChild(selectedAccessory);
    }

    this.update();
  },
  selectAccessory: function () {
    const id = this._elem.id;
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];

    let accessories = document.getElementById('selected-accessories');
    let selectedAccessory = accessories.querySelector(`[data-id="${id}"]`);

    // On peut ajouter, remplacer ou supprimer si pas de value sélectionnée
    if (selectedAccessory !== null) {
      accessories.removeChild(selectedAccessory);
    }
    
    if (option.value.length != 0) {
      let price = option.getAttribute('data-price');
      let text = option.innerText; 
      
      let p = document.createElement('p');
      p.setAttribute('data-id', id);
      p.setAttribute('data-price', price);
  
      let span = document.createElement('span');
      span.textContent = text;
      
      p.appendChild(span);
      accessories.appendChild(p);
    }

    this.update();
  },
  update: function (qty) {
    var qty = qty || parseInt(document.getElementById('qty').value, 10);
    var price = parseFloat(document.getElementById('kit-type').value);
    var finish = document.getElementById('finish') || false;
    price = isNaN(price) ? 0.0 : price;
    let accessories = this.updateAccessoriesPrice(qty);

    var finishCost = finish != false ? parseFloat(finish.value) || 0.0 : 0.0;
    var updated = (price + finishCost + accessories) * qty;


    // price.value = updated;
    this.updateCart(updated);
  },
  updateAccessoriesPrice: function () {
    // Les plaques avec les prix à vérifier je dois aller les chercher dans selected-accessories
    const accessories = document.getElementById('selected-accessories').querySelectorAll('p[data-id]');
    
    // Pour chacun des accessoires je récupère l'attribut price, on en fait un tableau et on accumule
    const total = Array.from(accessories).map(accessory =>
      parseFloat(accessory.getAttribute('data-price'))
    ).reduce((accumulator, price) => accumulator + price, 0.0);

    document.getElementById('price-accessories').value = total;
    return total;
  },
  updateCart: function (price_item) {
    let input = document.getElementById('item-total');
    let currency = input.getAttribute('data-currency');
    let l10n = input.getAttribute('data-l10n');
    let price_opts = parseFloat(document.getElementById('price-opts').value);
    let updated = price_item + price_opts;

    input.setAttribute('data-price', updated);
    input.textContent = monetary(updated, l10n, currency);
  },
  pushToCart: async function () {
    const form = this._elem;
    const formData = new FormData(form);
    const _stored = JSON.parse(localStorage.getItem('cart'));
    const _id = _stored !== null ? _stored.id : null;

    formData.append('item[id_order]', _id);

    let _optBloc = document.getElementById('opts');
    let _opts = _optBloc.querySelectorAll('[data-checked="1"]');
    let _finish = _optBloc.querySelector('p.finish-opt');
    let _type = _optBloc.querySelector('p.type-opt');

    if (!formData.has('item[description]')) {
      formData.append(
        'item[description]',
        document.querySelector('.custom').getAttribute('data-name') + document.querySelector('.designation').textContent
      );
    }

    formData.append('item[type][name]', _type.getAttribute('data-opt'));
    formData.append('item[type][id]', _type.getAttribute('data-id'));
    formData.append('item[finish][name]', _finish.getAttribute('data-opt'));
    formData.append('item[finish][id]', _finish.getAttribute('data-id'));

    _opts.forEach(opt => {
      let name = opt.getAttribute('data-opt');
      switch (name) {
        case 'plate':
          formData.append('opts[checked][]', 'plate');
          break;
        case 'plate-sponsor':
          formData.append('opts[checked][]', 'plate');
          formData.append('opts[checked][]', 'sponsor');
          break;
        case 'sponsor':
          formData.append('opts[checked][]', 'sponsor');
          break;
        case 'custom':
          formData.append('opts[checked][]', 'custom');
          break;
        default:
          formData.append('opts[checked][]', name);
      }
    });

    let _sponsors = document.querySelectorAll('p.sponsor');
    _sponsors.forEach(e => {
      let place = e.querySelector('.place').textContent;
      let file = null;
      let text = e.querySelector('input.text').value || null;
      let inputfile = e.querySelector('input.file');

      if (inputfile.files.length > 0) {
        file = inputfile.files[0];
        formData.append('item_sponsors[' + place + ']', file);
      }

      if (text !== null || file !== null) {
        let data = {
          place: place,
          file: file !== null ? file.name : null,
          text: text
        };
        formData.append('opts[sponsor][' + place + ']', JSON.stringify(data));
      }
    });

    // Fichiers véhicule - custom
    const vehicleFiles = document.querySelector('.vehicle-preview');
    if (vehicleFiles) {
      vehicleFiles.querySelectorAll('span.obj').forEach(function (el) {
        formData.append('vehicle_files[]', el.file);
      });
    }

    // Ajouter les options selectionnées au formulaire
    const switchColor = formData.get('switch_color');
    if (switchColor > 0) {
      const switchOpts = document.querySelectorAll('input[name*="opts[switch]"]');
      switchOpts.forEach((opt) => {
        if (opt.value != "") {
          formData.append(opt.name, opt.value);
        }
      })
    }

    const plateSponsor = formData.get('plate_sponsor');
    if (plateSponsor > 0) {
      const plateOpts = document.querySelectorAll('input[name*="opts[plate]"]');
      plateOpts.forEach((opt) => {
        if (opt.value != "") {
          formData.append(opt.name, opt.value);
        }
      })
    }

    const response = await fetch(form.action, { method: 'POST', body: formData });
    const json = await response.json();

    if (response.status >= 200 && response.status <= 226) {
      if (json.cart) {
        localStorage.setItem('cart', JSON.stringify(json.cart));
        this.updateCartIcon(json.cart.items);
        document.getElementById('cart-btn').click();
      }
    }

    if (response.status >= 500 && response.status <= 527) {
      throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
    }
  },
  updateCartIcon: function (nb_items) {
    if (this._items) this._items.textContent = nb_items;
  },
}

export default customPlate;