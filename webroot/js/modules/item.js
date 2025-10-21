const _defaultLang = 'fr';
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
const item = {
  _elem: null,
  _ev: null,  
  _items: document.querySelector('#nbItems'),
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  updateQuantity: function () {
      console.log(this._elem.value);
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
  finish: function () {
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.finish-opt');
    if(_p){
       _p.textContent = _option.value != '' ? _option.textContent : '';
      _p.setAttribute('data-opt', _option.getAttribute('data-name') || 'Fini Brillant');
      _p.setAttribute('data-id', _option.getAttribute('data-id'));
    }
   
    this.update();
  },
  premium: function () {
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.premium-opt');
    _p.textContent = _option.value != '' ? _option.textContent : '';
    _p.setAttribute('data-opt', _option.getAttribute('data-name') || 'Aucune');
    _p.setAttribute('data-id', _option.getAttribute('data-id'));
    this.update();
  },
  typeKit: function () {
    if(!this._elem.value) return;
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.type-opt');
    _p.textContent = _option.value != '' ? 'Kit ' + _option.textContent : '';
    _p.setAttribute('data-opt', _option.getAttribute('data-name'));
    _p.setAttribute('data-id', _option.getAttribute('data-id'));
    this.update();
  },
  seat: function() {
    let _p = document.querySelector('p.seat-cover');    
    let _cost = this._elem.value;   
    const _id = this._elem.id;
    if (this._elem.checked) {
         _p.textContent = this._elem.getAttribute('data-text');
        _p.setAttribute('data-opt', this._elem.id);
        _p.setAttribute('data-checked', 1);

        // On a une modal ? 
        let _modal = document.getElementById('seats-millesims');
        if(_modal) {
          _modal.classList.toggle('visible');
        }
    } else {
       _p.textContent = '';
        _p.setAttribute('data-opt', '');
        _p.setAttribute('data-checked', 0);
    }
    this.update();
  },
  rim: function() {
    let _p = document.querySelector('p.rim-sticker');    
    let _cost = this._elem.value;   
    const _id = this._elem.id;
    if (this._elem.checked) {
         _p.textContent = this._elem.getAttribute('data-text');
        _p.setAttribute('data-opt', this._elem.id);
        _p.setAttribute('data-checked', 1);

        
    } else {
       _p.textContent = '';
        _p.setAttribute('data-opt', '');
        _p.setAttribute('data-checked', 0);
    }
    this.update();
  },
  doorStickers: function() {
    let _p = document.querySelector('p.door-stickers');    
    let _cost = this._elem.value;   
    const _id = this._elem.id;
    if (this._elem.checked) {
         _p.textContent = this._elem.getAttribute('data-text');
        _p.setAttribute('data-opt', this._elem.id);
        _p.setAttribute('data-checked', 1);

        
    } else {
       _p.textContent = '';
        _p.setAttribute('data-opt', '');
        _p.setAttribute('data-checked', 0);
    }
    this.update();
  },
  update: function (qty) {
    var qty = qty || parseInt(document.getElementById('qty').value, 10);
    var price = parseFloat(document.getElementById('kit-type').value);
    price = isNaN(price) ? 0.0 : price;
    var finish = document.getElementById('finish') || false;
    var premium = document.getElementById('premium') || false;
    var seat = document.getElementById('seat-cover') || false;

    var finishCost = finish != false ? parseFloat(finish.value) || 0.0 : 0.0;
    var premiumCost = premium != false ? parseFloat(premium.value) || 0.0 : 0.0;
    var seatCost = (seat && seat.checked) ? parseFloat(seat.value) || 0.0 : 0.0;
    var updated = (price + finishCost + premiumCost + seatCost) * qty;

    price.value = updated;
    this.updateCart(updated);
  },
  delete: async function() {
    let lang = document.documentElement.lang;
    let msg_confirm = lang == 'fr' ? 'Vous confirmez la suppression ? ' : 'Are you sure ?';
    if(!confirm(msg_confirm)) return;

    if(this._elem.hasAttribute('data-id')){
      const id = this._elem.getAttribute('data-id');
      const tr = document.getElementById(`i-${id}`);
      const url = tr.getAttribute('data-uri');
      const req = await fetch(url, {method: 'DELETE', mode: 'cors', credentials: 'include'});
      if(req.status !== 200){
        const json = await req.json();
        return;
      }      
          
      const text = await req.text();
      let frag = document.createRange().createContextualFragment(text);
      const aside = frag.firstChild;
      const items = aside.querySelector('#items').getAttribute('data-qty');
      const cart = document.getElementById('cart-preview');
      const _stored = JSON.parse(localStorage.getItem('cart')); 
      if(_stored !== null) {
        
        _stored.qty = items;
        localStorage.setItem('cart', JSON.stringify(_stored));
      }
      this.updateCartIcon(items); 
      if(cart) cart.parentNode.replaceChild(aside, cart);  
      else document.body.append(aside);
      return;
    }
    this.oldDelete();
  },
  oldDelete:async function(){
    let response = await fetch(this._elem.href, {method: 'DELETE'});   
    let json = await response.json();
    if (!response.ok) {
      throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);
    }
    if(response.status >= 200 && response.status <= 226)
    {
      if(json.cart){
        let storage = {id: json.cart.id, items: json.quantity}
        localStorage.setItem('cart', JSON.stringify(storage));
        this.updateCartIcon(json.quantity);
        const cartPreview = document.getElementById('cart-preview');
        cartPreview.classList.toggle('visible');
        document.getElementById('cart-btn').click();
        //let item = document.getElementById(this._elem.getAttribute('data-item'));
        //item.parentNode.removeChild(item);
      }      
    }  
    if(response.status >= 500 && response.status <= 527)
    {
      throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
    }
  },
  updateCartIcon: function (nb_items) {
    if (this._items) this._items.textContent = nb_items;
  },
  updateCart: function (price_item) {
    var input = document.getElementById('item-total');
    var currency = input.getAttribute('data-currency');
    let l10n = input.getAttribute('data-l10n');
    var price_opts = parseFloat(document.getElementById('price-opts').value);
    var price_accessories = parseFloat(document.getElementById('price-accessories').value);
    var updated = price_item + price_opts + price_accessories;
    // console.log(updated);
    input.setAttribute('data-price', updated);
    input.textContent = monetary(updated, l10n, currency);
  },
  pushToCart: async function () {
    const form = this._elem;
    const button = form.querySelector('button[type="submit"]');
    //button.disabled = 'disabled';
    //button.querySelector('span.load').classList.toggle('hidden');
    //button.querySelector('span.text').classList.toggle('hidden');
    const formData = new FormData(form);
    
    const _stored = JSON.parse(localStorage.getItem('cart'));
    let _id =  _stored !== null ? _stored.id  : null; 
    //console.log('cart exist : ' + _id); 
    formData.append('item[id_order]', _id);
    
    let _optBloc = document.getElementById('opts');
    let _opts = _optBloc.querySelectorAll('[data-checked="1"]');
    let _finish = _optBloc.querySelector('p.finish-opt');
    let _premium = _optBloc.querySelector('p.premium-opt');
    let _type = _optBloc.querySelector('p.type-opt');

    // pour 100% perso, vehicule et millesim pas rattaché à addToCart
		// if(!formData.has('vehicle[version]')){
		// 	let vehicle = document.getElementById('vehicle');
		// 	if(vehicle) {
		// 		formData.append('vehicle[version]', vehicle.value);
		// 	} else {
		// 		let select = document.getElementById('select-vehicle');
		// 		let _index = select.selectedIndex;
		// 		let _option = select.options[_index];
		// 		formData.append('vehicle[version]', _option.textContent);
		// 	} 
		// }

    if(!formData.has('item[description]')) {
      formData.append(
        'item[description]',
        document.querySelector('.custom').getAttribute('data-name') + document.querySelector('.designation').textContent
      );
    }

    formData.append('item[type][name]', _type.getAttribute('data-opt'));
    formData.append('item[type][id]', _type.getAttribute('data-id'));
    formData.append('item[finish][name]', _finish.getAttribute('data-opt'));
    formData.append('item[finish][id]', _finish.getAttribute('data-id'));   
    if(_premium ){
        let idPremium =  _premium.getAttribute('data-id');
        if(idPremium != 10){
          formData.append('item[premium][name]', _premium.getAttribute('data-opt'));
          formData.append('item[premium][id]', _premium.getAttribute('data-id'));  
        }else {
          formData.delete('item[price][premium]');
        }
         
    }
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
        case 'switch-color':
          formData.append('opts[checked][]', 'switch');
          break;
        case 'seat-cover':
          formData.append('opts[checked][]', 'seat_cover');
          break;
        case 'custom':
          formData.append('opts[checked][]', 'custom');
          break;
        default:
          formData.append('opts[checked][]', name);
      }
    });

    let _inputSeat = document.getElementById('seat-cover');
    if(_inputSeat && _inputSeat.checked == true)
    {
      formData.append('opts[seat_cover][option]', _inputSeat.getAttribute('data-id'));
      formData.append('opts[seat_cover][vehicle]', formData.get('vehicle[version]'));
    }
    formData.delete('vehicle[version]');
    // Comment 

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
		if(vehicleFiles) {
			vehicleFiles.querySelectorAll('span.obj').forEach(function(el){
					formData.append('vehicle_files[]', el.file);
			});
		}
		
		// Fichiers supplémentaire - custom
		const otherFiles = document.querySelector('.files-preview');
		if(otherFiles) {
			otherFiles.querySelectorAll('span.obj').forEach(function(el){
					formData.append('item_files[]', el.file);
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
    
    let response = await fetch(form.action, {method: 'POST',body: formData});
    let json = await response.json();

    if (!response.ok) {
      //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);

    }
    console.log(`statut : ${response.status}`);

    if(response.status >= 200 && response.status <= 226)
    {
      if(json.cart){
        localStorage.setItem('cart', JSON.stringify(json.cart));
        app.updateCartIcon(json.cart.items);
        document.getElementById('cart-btn').click();
      }
    }  
    if(response.status >= 500 && response.status <= 527)
    {
      throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
    }
  },
  info: function () {
    var modal = document.getElementById(this._elem.getAttribute('data-modal'));
    // Si cette modale necessite une requÃªte fetch
    if (this._elem.hasAttribute('data-fetch')) {
      fetch(this._elem.getAttribute('data-fetch'), {
        method: 'GET'
      })
        .then((res) => {
          return res.text()
        })
        .then((data) => {
          if (data) {
            modal.querySelector('div.modal-content').innerHTML = data;
            modal.classList.toggle('visible');
          }
        });
    } else {
      if (modal && modal.hasAttribute('data-location'))
            window.location.assign(modal.getAttribute('data-location'));
      modal.classList.toggle('visible');
    }
  },
  details: function () {

    return;

    const fetched = this._elem.dataset.fetched;
    if (fetched === 'true') { return; }

    const orderId = this._elem.dataset.id;
    const url = `/orders/item/${orderId}/details`;

    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        this._elem.dataset.fetched = 'true';

        const content = document.getElementById(`content-${orderId}`);
        const details = json.details;
        const custom = JSON.parse(details.item_custom);
        const price = JSON.parse(details.item_price);
        const currencyCode = app._store.amount.currency_code;
        const l10n = document.documentElement.lang;

        let template = document.getElementById('details-tpl');
        const detailTpl = template.content.cloneNode(true);
        detailTpl.querySelector('.finish').textContent = details.finish;

        if (price.finish != 0) {
          detailTpl.querySelector('.finish-price').textContent = monetary(price.finish, l10n, currencyCode);
        }
        content.appendChild(detailTpl);

        if (custom.options.plate !== undefined) {
          template = document.getElementById('race-tpl');
          const raceTpl = template.content.cloneNode(true);
          const race = custom.options.plate;

          raceTpl.querySelector('.opt-price').textContent = monetary(price.opts, l10n, currencyCode);
          raceTpl.querySelector('.name').textContent = race.name;
          raceTpl.querySelector('.name-typo').src = `/img/typo/${race.name_typo}`;
          raceTpl.querySelector('.number').textContent = race.number;
          raceTpl.querySelector('img.number-typo').src = `/img/typo/${race.number_typo}`;
          content.appendChild(raceTpl);
        }

        if (custom.options.sponsor !== undefined) {
          template = document.getElementById('sponsors-tpl');
          const sponsorsBlocTpl = template.content.cloneNode(true);
          const sponsors = custom.options.sponsor;

          sponsors.forEach(sponsor => {
            template = document.getElementById('sponsor-tpl');
            const sponsorTpl = template.content.cloneNode(true);
            sponsorTpl.querySelector('.sponsor-place').textContent = sponsor.place;
            sponsorTpl.querySelector('.sponsor-text').textContent = sponsor.text;
            if (sponsor.file !== null) { sponsorTpl.querySelector('img').src = sponsor.file; }
            sponsorsBlocTpl.appendChild(sponsorTpl);
          });
          content.appendChild(sponsorsBlocTpl);
        }
      });
  }
};
export default item;