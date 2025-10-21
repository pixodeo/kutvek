const cart = {
  _elem: null,
  _ev: null,
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  overview: async function () {
    console.log('overview legacy !')
    const cartPreview = document.getElementById('cart-preview');
    if (!cartPreview) return;

    this._store = JSON.parse(localStorage.getItem('cart'));

    if (this._store !== null && (this._store.items > 0 || this._store.qty > 0)) {
      var attribute = this._elem.getAttribute("data-obf"); 

      var expireDate = new Date();
      expireDate.setTime(expireDate.getTime() + 1 * 3600 * 1000); // 1hour

      document.cookie = `cartId=${this._store.id}; expires=${expireDate}; SameSite=Strict`;
      let url = decodeURIComponent(window.atob(attribute));
      //console.log(url);
      const itemsList = document.getElementById('items');
      //const platform = this._store.platform;
      
      let _l10n = app._defaultLang;

      cartPreview.querySelector('#empty-cart').classList.add('invisible');
      cartPreview.querySelector('#cart-filled').classList.remove('invisible');
      let itemTotal = cartPreview.querySelector('#item-total');
      let totalAmount = cartPreview.querySelector('#total-to-pay');
      let discountInfo = cartPreview.querySelector('#discount');
      if(discountInfo) discountInfo.innerHTML = '';    
      let response = await fetch(`${url}?id=${this._store.id}`, {method: 'GET'});
      let json = await response.json();

      if (!response.ok) {
        //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);

      }

      if(response.status >= 200 && response.status <= 226)
      {       
     
        if(json.cart.items.length > 0){
          const currency = json.cart.amount.currency_code;
          itemTotal.textContent = monetary(parseFloat(json.cart.amount.breakdown.item_total.value) + parseFloat(json.cart.amount.breakdown.tax_total.value),_l10n, currency,2);
          
          const total_amount = parseFloat(json.cart.amount.breakdown.item_total.value) + parseFloat(json.cart.amount.breakdown.tax_total.value) - parseFloat(json.cart.amount.breakdown.discount.value) - parseFloat(json.cart.amount.breakdown.shipping_discount.value);
          totalAmount.textContent = monetary(total_amount,_l10n, currency,2);

          let template = document.getElementById('item-tpl');
          
          // Je boucle les items dans itemsList, j'enlève le 1er élément tant qu'il y a 1 enfant
          while (itemsList.firstChild) { itemsList.removeChild(itemsList.firstChild) }
          json.cart.items.forEach((itm) => {

          let unitValue = parseFloat(itm.tax.value) + parseFloat(itm.unit_amount.value);
          let itemTpl = document.importNode(template.content, true);
          itemTpl.querySelector('div').id = 'item-' + itm.item_id;
          let img = itemTpl.querySelector('img');
          let deleteItem = itemTpl.querySelector('a.delete-item');
          let infoItem = itemTpl.querySelector('a.item-info');          
          let quantity = itemTpl.querySelector('.item-qty');
          let price = itemTpl.querySelector('.item-price');
          let itemName = itemTpl.querySelector('.item-desc');

          img.src = itm.item_visual != null ? itm.item_visual : 'img/blank.png';
          itemName.textContent = itm.name;
          quantity.value = itm.quantity;
          quantity.id = `qty-${itm.item_id}`;

          deleteItem.href = itm.links.self;

          //deleteItem.addEventListener('click', e => {item.delete(deleteItem);});
          deleteItem.classList.add('click');
          deleteItem.setAttribute('data-ctrl', 'item.delete');
          deleteItem.setAttribute('data-item', 'item-' + itm.item_id);
          //let split = itm.links.self.split('/'); // attention actuellement y'a un index 0 à vide
          //let joinValues = ['',split[1], 'carts', split[2], split[3]];
          //let join = joinValues.join('/');         
          //infoItem.setAttribute('data-behavior', itm.behavior);
          //infoItem.href = infoItem.href.replace(':item', itm.item_id) + '?behavior=' + itm.behavior;
          infoItem.setAttribute('data-fetch', infoItem.getAttribute('data-fetch').replace(':item', itm.item_id) + '?behavior=' + itm.behavior);
          price.textContent = monetary(unitValue, _l10n, currency, 2);
          itemsList.appendChild(itemTpl);
          });

          // estimation des coûts de livraison
          if(json.cart.shipping_estimation){
            const shipping_cost = json.cart.shipping_estimation;
            const vat = shipping_cost.vat;

            //const chronoClassic = vat > 0 ? parseFloat(shipping_cost.chrono_classic * 1.20) : parseFloat(shipping_cost.chrono_classic);
            //const chronoExpress = vat > 0 ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
            //const chronoRelay = vat > 0 ? parseFloat(shipping_cost.chrono_relay * 1.20): parseFloat(shipping_cost.chrono_relay);

            const chronoClassic = (vat > 0 && shipping_cost.c_type == 'std') ? parseFloat(shipping_cost.chrono_classic * 1.20) : parseFloat(shipping_cost.chrono_classic);
            const chronoExpress = (vat > 0 && shipping_cost.c_type == 'std') ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
            const chronoRelay = (vat > 0 && shipping_cost.c_type == 'std') ? parseFloat(shipping_cost.chrono_relay * 1.20): parseFloat(shipping_cost.chrono_relay);

            cartPreview.querySelector('#shipping-amount').textContent = monetary(chronoClassic, _l10n, currency);
            totalAmount.textContent = monetary(total_amount + chronoClassic,_l10n,currency,2);
            let divCClassic = cartPreview.querySelector('.chrono-classic');
            let divCExpress = cartPreview.querySelector('.chrono-express');
            let divCRelay = cartPreview.querySelector('.chrono-relay');
            if(shipping_cost.chrono_express) 
            {
              divCExpress.classList.remove('hide');
              divCExpress.querySelector('.cost').textContent = monetary(chronoExpress, _l10n, currency);
            }
            if(shipping_cost.chrono_relay) 
            {
              divCRelay.classList.remove('hide');
              divCRelay.querySelector('.cost').textContent = monetary(chronoRelay, _l10n, currency);
            }
            if(shipping_cost.chrono_classic) 
            {
              divCClassic.classList.remove('hide');
              divCClassic.querySelector('.cost').textContent = monetary(chronoClassic, _l10n, currency);
            }
          }
          
          // Affichage de la remise le cas échénant
          if(json.cart.discount.length > 0){              
              let tp = document.getElementById('discount-tpl');
              let d = document.importNode(tp.content, true);
              json.cart.discount.forEach(e => {
                  let p = d.cloneNode(true);                  
                  p.querySelector('.discount').textContent = e.designation;
                  p.querySelector('.amount').textContent = monetary(e.value,_l10n, currency,2);
                  if(e.type == 'coupon'){
                    // code promo possibilité de supprimé 
                    let link = p.querySelector('.delete');
                    link.href = link.href.replace(':order', json.cart.id);
                    link.classList.remove('hide');
                  }
                  discountInfo.appendChild(p);
              });
              discountInfo.classList.remove('hidden');          
          }

          if(json.cart.coupon && json.cart.coupon.id){
            document.getElementById('discount-tabs').classList.add('hidden');
          }

        }
      } if(response.status >= 500 && response.status <= 527)
      {
        throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
      }      
      //const itemsQty = document.getElementById('items-qty');
      //itemsQty.textContent = this._store.items;
      
      

      //const breakdown = this._store.amount.breakdown;
      //let totalWithVat = parseFloat(breakdown.item_total.value) + parseFloat(breakdown.tax_total.value);

      //subTotal.textContent = monetary(totalWithVat, _l10n, currency, 2);
    } else {
      cartPreview.querySelector('#empty-cart').classList.remove('invisible');
      cartPreview.querySelector('#cart-filled').classList.add('invisible');
    }
    cartPreview.classList.toggle('visible');
  },
  closeOverview: function () {
    const cartPreview = document.getElementById('cart-preview');
    cartPreview.classList.toggle('visible');
  },
  promoCode: async function(){
    this._store = JSON.parse(localStorage.getItem('cart'));
    //console.log(this._store.id);
     // quel button a submit le formulaire ? pour récupérer sa valeur
    const submitter = this._ev.submitter || document.activeElement;
    submitter.disabled = true;
    //console.log(submitter);
    const url = submitter.getAttribute('formaction').replace(':order',this._store.id)
    //console.log(url);
    const formData = new FormData(this._elem);
    let response = await fetch(url, {method: 'POST', mode: 'cors', credentials: 'include', body: formData});
    if(!response.ok) {submitter.disabled = false; return};
    const err_div = this._elem.querySelector('.promocode-error');
    err_div.querySelector('.h5').textContent = '';
    err_div.querySelector('.h5 + div').textContent = '';
     const json = await response.json();
    if(json.error){            
      err_div.querySelector('.h5').innerHTML = json.designation;
      err_div.querySelector('.h5 + div').innerHTML = json.description; 
      submitter.disabled = false;
      return;    
    }
    if(response.status === 200){
      window.location.reload();
    }
  }
}

const delivery = {
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  points: function () {
    const formData = new FormData(this._elem);
    var countryCode = document.getElementById('country-code').value;
    const queries = [
      { name: "address", "value": formData.get('adresse') },
      { name: "zipcode", "value": formData.get('cp') },
      { name: "city", "value": formData.get('ville') },
      { name: "countryCode", "value": countryCode }
    ];

    var filter = queries.filter(query => query.value.length > 0);
    const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');

    // Récupérer la liste des points relais en synchrone
    fetch('https://demo.firstracing.com/cart/stores' + '?' + asString, {
      method: ('GET')
    }).then((res) => {
      return res.json()
    }).then((json) => {
      if (json.success) {
        var stores = { "type": "FeatureCollection" };
        stores.features = json.collection.features;
        // Place les points sur la carte
        if (map.getSource('places') != undefined) {
          map.removeSource('places');
        }
        map.addSource('places', {
          type: 'geojson',
          data: stores
        });
        chronoRelay.addMarkers(stores);
        // Construit la liste
        chronoRelay.buildLocationList(stores);
        map.flyTo({
          center: json.collection.features[0].geometry.coordinates,
          duration: 1200,
          zoom: 12
        });
      }
    });
  }
};

const item = {
  _elem: null,
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  qty: function () {
      
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
    _p.textContent = _option.value != '' ? _option.textContent : '';
    _p.setAttribute('data-opt', _option.getAttribute('data-name') || 'Fini Brillant');
    _p.setAttribute('data-id', _option.getAttribute('data-id'));
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
  typeKit: async function () {
    if(!this._elem.value) return;
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.type-opt');
    var discount = document.getElementById('item-discount');

    if(discount) {
        let currency = document.getElementById('item-currency').getAttribute('content');
        let l10n = document.documentElement.lang;
        let price = parseFloat(_option.value);
        let price_f = monetary(price - (price * discount.value / 100), l10n, currency);
      _p.textContent = _option.value != '' ? ' ' + _option.getAttribute('data-name') + ' ' + price_f : '';
    } else {
      _p.textContent = _option.value != '' ? '' + _option.textContent : '';
    }  
    
    const kitType = _option.getAttribute('data-id');

    const family = document.querySelector('[name="item[family]"]').value;
    const lang = document.documentElement.lang === 'en' ? '/en' : '';
    const isCustomPage = document.querySelector('[name="behavior"]').value == "CustomGraphicKitBehavior";
    
    let url = `${lang}/api/families/${family}/kit-types/${kitType}/finish`;
    if (isCustomPage) url += "&custom=1";

    const inputPlate = document.getElementById('plate');
    const inputPlateSponsor = document.getElementById('plate-sponsor');
    const inputPlateO = document.getElementById('plate-0');
    const pps = document.querySelector('p.plate-sponsors');
    //console.log(pps);
    if(kitType == 1) {      
      if(inputPlate) inputPlate.disabled = true;
      if(inputPlateSponsor) inputPlateSponsor.disabled = true;
      if(inputPlateO) inputPlateO.checked = true;

      if(pps){
        pps.setAttribute('data-checked', 0);
        pps.textContent = '';
        option.updatePrice();
      } 

    }
    else {
      if(inputPlate) inputPlate.disabled = false;
      if(inputPlateSponsor) inputPlateSponsor.disabled = false;
    }
    try {
      const response = await fetch(url, { method: 'GET', mode: 'cors', credentials: 'include' });
      if (response.ok) {
        const json = await response.json();
        const finishData = json.finish;
        const premiumData = json.premium;
        const finishSelect = document.querySelector('[name="item[price][finish]"]');
        const premiumSelect = document.querySelector('[name="item[price][premium]"]');

        this.updateSelectOptions(finishSelect, finishData);
        this.updateSelectOptions(premiumSelect, premiumData);
      }
    } catch (error) {
      console.error("Prix des options en fonction des types de kit", error);
    }

    _p.setAttribute('data-opt', _option.getAttribute('data-name'));
    _p.setAttribute('data-id', _option.getAttribute('data-id'));
    
    this.update();
  },
  updateSelectOptions: function(selectElement, newOptions) {
    if(selectElement === undefined) return;
    const currentOptions = Array.from(selectElement.options);
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    newOptions.forEach((optionData) => {
      const found = currentOptions.find(option => option.getAttribute('data-id') === optionData.id.toString());
      if (found) {
        found.value = optionData.value;
        found.text = optionData.text;
      }

      if (found && found == selectedOption) {
        const defaultName = optionData.opt_type == 'finish' ? 'Fini Brillant' : 'Aucune';
        const _p = document.querySelector(`p.${optionData.opt_type}-opt`);
        _p.textContent = found.value != '' ? found.textContent : '';
        _p.setAttribute('data-opt', found.getAttribute('data-name') || defaultName);
        _p.setAttribute('data-id', found.getAttribute('data-id'));
      }
    });
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
      _p.setAttribute('data-id', this._elem.getAttribute('data-id'));
      _p.setAttribute('data-name', this._elem.getAttribute('data-name'));
      _p.setAttribute('data-checked', 1);
        
    } else {
      _p.textContent = '';
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-checked', 0);
      _p.setAttribute('data-id', '');
      _p.setAttribute('data-name', '');
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
      _p.setAttribute('data-id', this._elem.getAttribute('data-id'));
      _p.setAttribute('data-name', this._elem.getAttribute('data-name'));
      _p.setAttribute('data-checked', 1);        
    } else {
      _p.textContent = '';
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-checked', 0);
      _p.setAttribute('data-id', '');
      _p.setAttribute('data-name', '');
    }
    this.update();
  },
  update: function (qty) {
    var qty = qty || parseInt(document.getElementById('qty').value, 10);   
    var price = parseFloat(document.getElementById('kit-type').value);
    var discount = document.getElementById('item-discount');
    if(discount) {
      price = parseFloat(price - (price * discount.value / 100));
      var old = parseFloat(document.getElementById('kit-type').value);
    }   
    price = isNaN(price) ? 0.0 : price;
    var finish = document.getElementById('finish') || false;
    var premium = document.getElementById('premium') || false;
    //console.log(premium);
    
    var seat = document.getElementById('seat-cover') || false;
    var rim = document.getElementById('rim-sticker') || false;
    var doorStickers = document.getElementById('door-stickers') || false;

    var finishCost = finish != false ? parseFloat(finish.value) || 0.0 : 0.0;
    var premiumCost = premium != false ? parseFloat(premium.value) || 0.0 : 0.0;    
    var seatCost = (seat && seat.checked) ? parseFloat(seat.value) || 0.0 : 0.0;
    var rimCost = (rim && rim.checked) ? parseFloat(rim.value) || 0.0 : 0.0;
    var doorCost = (doorStickers && doorStickers.checked) ? parseFloat(doorStickers.value) || 0.0 : 0.0;

    var updated = (price + finishCost + premiumCost +  seatCost + rimCost + doorCost) * qty;
    //console.log(premiumCost);
    this.updateCart(updated);
  },
  delete: async function() {
    let lang = document.documentElement.lang;
    let msg_confirm = lang == 'fr' ? 'Vous confirmez la suppression ? ' : 'Are you sure ?';
    if(!confirm(msg_confirm)) return;
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
        app.updateCartIcon(json.quantity);
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
  updateQuantity: async function(){
      //console.log(this._elem.value);
      let id = this._elem.id.replace('qty-', '');
      //console.log(id);
      const _stored = JSON.parse(localStorage.getItem('cart'));
      let _orderId =  _stored.id; 
      let uri = this._elem.getAttribute('data-uri');
      uri = uri.replace(':id', id).replace(':qty', this._elem.value);
      //console.log(uri); 
      // envoi cookies
    let response = await fetch(uri, {
      method: 'PUT',
      mode: 'cors',
      credentials: 'include'
    });
    
    if (response.ok) {
      //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);
      let json = await response.json();
      let storage = {id: json.cart, items: json.quantity}
      localStorage.setItem('cart', JSON.stringify(storage));
      app.updateCartIcon(json.quantity);
      const cartPreview = document.getElementById('cart-preview');
      cartPreview.classList.toggle('visible');
      document.getElementById('cart-btn').click();
    }

  },
  updateCart: function (price_item) {
    var input = document.getElementById('item-total');
    //console.log(input);
    var currency = document.getElementById('item-currency').getAttribute('content');
    let l10n = document.documentElement.lang;
    var price_opts = parseFloat(document.getElementById('price-opts').value);
    const plastics = document.getElementById('plastics-option') || false;
    var price_plastics = plastics != false ? parseFloat(plastics.value) || 0.0 : 0.0;    

    const hubs_stickers = document.getElementById('hubs-stickers') || false;
    const hubs_stickers_price = hubs_stickers != false ? parseFloat(hubs_stickers.value) || 0.0 : 0.0; 

    const mini_plates = document.getElementById('mini-plates') || false;
    const mini_plates_price = mini_plates != false ? parseFloat(mini_plates.value) || 0.0 : 0.0; 

    var price_accessories = parseFloat(document.getElementById('price-accessories').value);  
    input.value = price_item + price_opts + price_accessories + price_plastics + hubs_stickers_price + mini_plates_price; 
    input.textContent = monetary(input.value, l10n, currency);
  },
  pushToCart: async function () {
    const form = this._elem;
    const button = form.querySelector('button[type="submit"]');
    button.disabled = 'disabled';
    button.querySelector('span.load').classList.toggle('hidden');
    
    const formData = new FormData(form);

    var offer = document.getElementById('item-discount');
    if(offer) {
      let item_price = parseFloat(document.getElementById('kit-type').value);
      let rate = offer.value;
      //let type_offer = offer.getAttribute('data-type');
      //if(type_offer == 'rate'){
      let discount = parseFloat((item_price * rate / 100) * -1);
      //}
      formData.set('item[price][discount]', discount);
    }
    const _stored = JSON.parse(localStorage.getItem('cart'));
    let _id =  _stored !== null ? _stored.id  : null;    
    formData.append('item[id_order]', _id);    
    let _optBloc = document.getElementById('opts');
    let _opts = _optBloc.querySelectorAll('[data-checked="1"]');
    let _finish = _optBloc.querySelector('p.finish-opt');


    let _premium =  document.getElementById('premium') ||  _optBloc.querySelector('p.premium-opt');
    
    let _type = _optBloc.querySelector('p.type-opt');
    if(!formData.has('item[description]')) {
      formData.append(
        'item[description]',
        document.querySelector('.custom').getAttribute('data-name') + document.querySelector('.designation').textContent
      );
    }
    formData.append('item[type][name]', _type.getAttribute('data-opt'));
    formData.append('item[type][id]', _type.getAttribute('data-id'));
    if(_finish){
      formData.append('item[finish][name]', _finish.getAttribute('data-opt'));
      formData.append('item[finish][id]', _finish.getAttribute('data-id'));   
      formData.append(`item[item_custom][finish][id]`, _finish.getAttribute('data-id'));
      formData.append(`item[item_custom][finish][name]`, _finish.getAttribute('data-opt'));
    }    
    if(_premium){ 
      //console.log(_premium.tagName.toLowerCase());
      let name, id;
      if(_premium.tagName.toLowerCase() === 'select'){
          const option = _premium.options[_premium.selectedIndex];
          name = option.getAttribute('data-name') || 'Aucune';
          id = option.getAttribute('data-id') || '10';
      }else{
        id = _premium.getAttribute('data-id');
        name = _premium.getAttribute('data-opt');
      }

      //console.log(name,id);       
        if(id != 10){
          formData.append('item[premium][id]', id); 
          formData.append('item[premium][name]', name);           
          formData.append(`item[item_custom][premium][id]`,id);
          formData.append(`item[item_custom][premium][name]`, name);
        }else {
          //formData.delete('item[price][premium]');
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
        case 'hubs_stickers':
          formData.append('opts[checked][]', name);
          formData.append(`item[item_custom][${name}][id]`,opt.getAttribute('data-id') );
          formData.append(`item[item_custom][${name}][name]`,opt.getAttribute('data-name'));
          formData.append(`item[item_custom][${name}][qty]`,1);
          break;
        case 'rim-sticker':
          formData.append('opts[checked][]', name);
          formData.append(`item[item_custom][rim_stickers][id]`,opt.getAttribute('data-id') );
          formData.append(`item[item_custom][rim_stickers][name]`,opt.getAttribute('data-name'));
          //formData.append(`item[item_custom][${name}][qty]`,1);
          break;
        case 'mini_plates':
            formData.append('opts[checked][]', name);
            formData.append(`item[item_custom][${name}][id]`,opt.getAttribute('data-id'));
            formData.append(`item[item_custom][${name}][name]`,opt.getAttribute('data-name'));
            formData.append(`item[item_custom][${name}][qty]`,1);
          break;
        case 'door-stickers':
            formData.append('opts[checked][]', name);
            formData.append(`item[item_custom][door_stickers][id]`,opt.getAttribute('data-id'));
            formData.append(`item[item_custom][door_stickers][name]`,opt.getAttribute('data-name'));
            //formData.append(`item[item_custom][door_stickers][qty]`,1);
          break;
        default:
          formData.append('opts[checked][]', name);
      }
    });
    const _plastics = document.querySelector('p.plastics');
    if (_plastics && _plastics.getAttribute('data-checked') == 1) {
      formData.delete('plastics-option');
      formData.append('opts[plastics][name]', _plastics.getAttribute('data-name'));
      formData.append('opts[plastics][option]', _plastics.getAttribute('data-id'));
      formData.append('opts[plastics][qty]', 1);
    }
    let _inputSeat = document.getElementById('seat-cover');
    if(_inputSeat && _inputSeat.checked == true)
    {
      formData.append('opts[seat_cover][option]', _inputSeat.getAttribute('data-id'));
      formData.append('opts[seat_cover][vehicle]', formData.get('vehicle[version]'));
    }
    formData.delete('vehicle[version]');    
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
    // envoi cookies
    let response = await fetch(form.action, {
      method: 'POST',
      mode: 'cors',
      credentials: 'include',
      body: formData
    });
    let json = await response.json();
    if (!response.ok) {
      //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `); 
    }
    

    if(response.status >= 200 && response.status <= 226)
    {
      if(json.cart){
        localStorage.setItem('cart', JSON.stringify(json.cart));
        app.updateCartIcon(json.cart.items);
        button.disabled = false;
        button.querySelector('span.load').classList.toggle('hidden');       
        document.getElementById('cart-btn').click();
        item.pushToDataLayer();
      }
    }  
    if(response.status >= 500 && response.status <= 527)
    {
      //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
    }
  },
  pushToDataLayer: function(){    
    dataLayer.push({ ecommerce: null });
    const id = document.querySelector('article').getAttribute('data-product');
    const total_price = parseFloat(document.getElementById('item-total').value);
    const qty = document.getElementById('qty').value;
    const unit_price = parseFloat(total_price/qty);
    const data = {
      event: "add_to_cart",
      ecommerce: {
        currency: document.querySelector('main').getAttribute('data-cur'),
        value: total_price.toFixed(2),
        items: [
        {
          item_id: `I_${id}`,
          item_name: document.getElementById('designation').textContent,        
          price: unit_price.toFixed(2),
          quantity: qty
        }
        ]
      }
    };
    dataLayer.push(data);    
  },
  pushCustomToCart: async function () {
    const form = this._elem;
    const button = form.querySelector('button[type="submit"]');
    button.disabled = 'disabled';
    button.querySelector('span.icon').classList.toggle('hidden');
    button.querySelector('span.text').classList.toggle('hidden');
    button.classList.toggle('in-progress');
    const formData = new FormData(form);
    formData.delete('item[family]');

    formData.delete('item[brand]');
    var offer = document.getElementById('offer');
    if(offer) {
      let item_price = parseFloat(document.getElementById('kit-type').value);
      let discount = offer.value;
      let type_offer = offer.getAttribute('data-type');
      if(type_offer == 'rate'){
        item_price = parseFloat(item_price - (item_price * discount / 100));
      }
      formData.set('item[price][product]', item_price);

    }
    const _stored = JSON.parse(localStorage.getItem('cart'));
    let _id =  _stored !== null ? _stored.id  : null; 
    formData.append('item[id_order]', _id);
    
    let _optBloc = document.getElementById('opts');
    let _opts = _optBloc.querySelectorAll('[data-checked="1"]');

    let _finish = _optBloc.querySelector('p.finish-opt');
    let _premium = _optBloc.querySelector('p.premium-opt');
    let _type = _optBloc.querySelector('p.type-opt');

    

    if(!formData.has('item[description]')) {
      const selector = document.getElementById('vehicles');     
      const option = selector.options[selector.selectedIndex];
      const vehicle_name = option.getAttribute('data-name');
      formData.append(
        'item[description]',
        document.querySelector('.custom').getAttribute('data-name') + vehicle_name
      );
    }

    formData.append('item[type][name]', _type.getAttribute('data-opt'));
    formData.append('item[type][id]', _type.getAttribute('data-id'));


    if(_finish){
      formData.append('item[finish][name]', _finish.getAttribute('data-opt'));
      formData.append('item[finish][id]', _finish.getAttribute('data-id'));   
      formData.append(`item[item_custom][finish][id]`, _finish.getAttribute('data-id'));
      formData.append(`item[item_custom][finish][name]`, _finish.getAttribute('data-opt'));
    }
    
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
        case 'hubs_stickers':
          formData.append('opts[checked][]', name);
          formData.append(`item[item_custom][${name}][id]`,opt.getAttribute('data-id') );
          formData.append(`item[item_custom][${name}][name]`,opt.getAttribute('data-name'));
          formData.append(`item[item_custom][${name}][qty]`,1);
          break;
        case 'rim-sticker':
          formData.append('opts[checked][]', name);
          formData.append(`item[item_custom][rim_stickers][id]`,opt.getAttribute('data-id') );
          formData.append(`item[item_custom][rim_stickers][name]`,opt.getAttribute('data-name'));
          //formData.append(`item[item_custom][${name}][qty]`,1);
          break;
        case 'mini_plates':
            formData.append('opts[checked][]', name);
            formData.append(`item[item_custom][${name}][id]`,opt.getAttribute('data-id'));
            formData.append(`item[item_custom][${name}][name]`,opt.getAttribute('data-name'));
            formData.append(`item[item_custom][${name}][qty]`,1);
          break;
        case 'door-stickers':
            formData.append('opts[checked][]', name);
            formData.append(`item[item_custom][door_stickers][id]`,opt.getAttribute('data-id'));
            formData.append(`item[item_custom][door_stickers][name]`,opt.getAttribute('data-name'));
            //formData.append(`item[item_custom][door_stickers][qty]`,1);
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
    // envoi cookies
    let response = await fetch(form.action, {
      method: 'POST',
      mode: 'cors',
      credentials: 'include',
      body: formData
    });
    let json = await response.json();
    if (!response.ok) {
      //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);

    }

    if(response.status >= 200 && response.status <= 226)
    {
      if(json.cart){
        localStorage.setItem('cart', JSON.stringify(json.cart));
        button.querySelector('span.icon').classList.toggle('hidden');
        button.querySelector('span.text').classList.toggle('hidden');
        button.classList.toggle('in-progress');
        button.disabled = false;
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
            modal.querySelector('div.content').innerHTML = data;
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

const option = {
  _elem: null,
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  premium: function () {
    const form = this._elem.form;
    let cost = parseFloat(this._elem.getAttribute('data-price')) || 0;
    let input = form.querySelector('input[name="price[premium]"]');
    input.value = cost;

    //console.log(cost);		
  },
  finish: function () {
    const form = this._elem.form;
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    let cost = parseFloat(option.getAttribute('data-price')) || 0;
    let input = form.querySelector('input[name="price[finish]"]');
    input.value = cost;
    //console.log(cost);
  },
  plate: function () {
    const form = this._elem.form;
    let cost = parseFloat(this._elem.getAttribute('data-price')) || 0;
    let input = form.querySelector('input[name="price[options]"]');
    let opt_cost = parseFloat(input.value);
    let sponsor = form.querySelector('#std-s-sponsors');
    if (this._elem.checked) // Ajout
    {
      if (!sponsor.checked) // Ajout 10		
        input.value = opt_cost + cost;
    }
    else // retrait
    {
      if (!sponsor.checked) // -10		
        input.value = opt_cost - cost;
    }

  },
  switch: function () {
    const form = this._elem.form;
    let cost = parseFloat(this._elem.getAttribute('data-price')) || 0;
    let input = form.querySelector('input[name="price[options]"]');
    let opt_cost = parseFloat(input.value);
    if (this._elem.checked) // Ajout
      input.value = opt_cost + cost;
    else 			// retrait
      input.value = opt_cost - cost;
  },
  sponsor: function () {
    const form = this._elem.form;
    let cost = parseFloat(this._elem.getAttribute('data-price')) || 0;
    let input = form.querySelector('input[name="price[options]"]');
    let opt_cost = parseFloat(input.value);
    let plate = form.querySelector('#std-s-plate');
    if (this._elem.checked) // Ajout
    {
      if (plate.checked) // Ajout 20
        input.value = opt_cost + 20;
      else
        input.value = opt_cost + cost;
    }
    else // retrait
    {
      if (plate.checked) // - 20
        input.value = opt_cost - 20;
      else
        input.value = opt_cost - cost;
    }
  },
  /**
   * Lorsque qu'on sélectionne une option dans un select additional
   * enclenchement d'un click sur la checkbox correspondante
   */
  selectAdditional: function () {
    const select = this._elem;
    if (this._elem.value == "") return;

    var div = this._elem.parentNode.querySelector('div[data-checked="1"]');
    if (div !== null) // on avait déjà fait un choix
    {
      div.setAttribute('data-checked', 0);
      div.querySelector('input[type="checkbox"]').click();
    }

    var checkbox = document.getElementById(select.value);
    checkbox.click();
    checkbox.parentNode.setAttribute('data-checked', 1);
    //console.log(this._elem);
    select.options[0].selected = true;
  },
  accessories: function () {
    let accessories = document.getElementById('selected-accessories');

    if (!this._elem.checked) {
      let accessory = accessories.querySelector(`[data-id="${this._elem.value}"]`);
      accessories.removeChild(accessory);

      let parent = this._elem.parentNode;
      if (parent.hasAttribute('data-checked'))
        parent.setAttribute('data-checked', 0);
    } else {
      let p = document.createElement('p');
      p.setAttribute('data-id', this._elem.value);

      let labels = document.getElementById('accessories').querySelectorAll('label');
      labels.forEach(label => {
        if (label.htmlFor == this._elem.id) {
          let labelSpan = document.createElement('span');
          labelSpan.classList.add('label-span');
          labelSpan.textContent = this._elem.getAttribute('data-name');

          let priceSpan = document.createElement('span');
          priceSpan.classList.add('price-span');
          priceSpan.textContent = this._elem.getAttribute('data-pricef');

          p.appendChild(labelSpan);
          p.appendChild(priceSpan);
        }
      });
      accessories.appendChild(p);
    }
    this.updatePrice();
  },  
  dropSponsor: function () {
    //console.log('File(s) dropped');

    if (this._ev.dataTransfer.items) {
      // Use DataTransferItemList interface to access the file(s)
      for (var i = 0; i < this._ev.dataTransfer.items.length; i++) {
        // If dropped items aren't files, reject them
        if (this._ev.dataTransfer.items[i].kind === 'file') {
          let file = this._ev.dataTransfer.items[i].getAsFile();
          // Affichage 
          this._elem.file = file;
          this._elem.classList.add("obj");
          this._elem.title = file.name;
          if (file.type) {
            this._elem.setAttribute('data-type', file.type);
          }
          else {
            let splits = file.name.split('.')

            this._elem.setAttribute('data-type', 'application/' + splits.pop());
          }

          // Si fichier type image	
          const imageType = /^image\//;
          if (imageType.test(file.type)) {
            // si on a déjà une image 

            var img = this._elem.querySelector('img') || document.createElement("img");
            img.file = file;
            var reader = new FileReader();
            reader.onload = (function (aImg) { return function (e) { aImg.src = e.target.result; }; })(img);
            reader.readAsDataURL(file);
            this._elem.appendChild(img);
          }
          else {
            if (this._elem.querySelector('img')) this._elem.removeChild(this._elem.querySelector('img'));
          }
          //console.log('DataTransferItemList... file[' + i + '].name = ' + file.name + ' type = ' + file.type);
        }
      }
    } else {
      // Use DataTransfer interface to access the file(s)
      for (var i = 0; i < this._ev.dataTransfer.files.length; i++) {
        //console.log('DataTransfer ... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
      }
    }
  },
  uploadSponsor: function () {
    let file = this._elem.files[0];
    let place = this._elem.getAttribute('data-place');
    // console.log(`place: ${place}, file: ${file.name}`);

    
    let span = this._elem.parentNode.querySelector('span.fileName');
    span.textContent = file.name;
  },
  set2: function () {
    let _type = this._ev.type;   
    let _isChecked = this._elem.checked;
    let link = this._elem.parentNode.querySelector('a');

    //console.log(`target : ${this._ev.target.nodeName}, currentTarget: ${this._ev.target.nodeName}`)

    let _modal = this._elem.getAttribute('data-modal');
    let _p = document.querySelector('p.' + _modal);
    let _pIsChecked = _p.getAttribute('data-checked');
    //console.log(_p);
    let _cost = this._elem.value;

    //console.log(`Cout : ${_cost}`)
    const _id = this._elem.id;
    if (_isChecked) {
      const plateSponsors = document.getElementById('plate-sponsors');
      const tab = plateSponsors.querySelector("[data-input=plate-sponsor]");
      _id == 'plate' ? tab.classList.add('tab-disabled') : tab.classList.remove('tab-disabled');

      link.classList.add('on-top');
      if (this._elem.type == 'radio') {
        let radios = document.querySelectorAll(`input[name="${this._elem.name}"]`);
        //console.log(radios);
        radios.forEach(e => {
          e.classList.remove('click');
          let _a = e.parentNode.querySelector('a');
          if (_a !== link) _a.classList.remove('on-top');
        })

        // Continuer à pouvoir cliquer sur le radio
        this._elem.classList.add('click');
      }
      // _p.textContent = _cost > 0 ? this._elem.getAttribute('data-text') : '';

      if (_cost > 0) {
        _p.innerHTML = '';

        let labelSpan = document.createElement('span');
        labelSpan.classList.add('label-span');
        labelSpan.textContent = this._elem.getAttribute('data-name');

        let priceSpan = document.createElement('span');
        priceSpan.classList.add('price-span');
        priceSpan.textContent = this._elem.getAttribute('data-pricef');

        _p.appendChild(labelSpan);
        _p.appendChild(priceSpan);
      }

      _p.setAttribute('data-opt', this._elem.id);
      _p.setAttribute('data-checked', 1);

      const modal = document.getElementById(_modal);
      modal.querySelectorAll('[data-input]').forEach((e) => {
        let _tab = e.getAttribute('data-input');
        _tab == _id ? e.classList.add('active') : e.classList.remove('active');
      });
      modal.classList.toggle('visible');
    } else {
      link.classList.remove('on-top');

      _p.textContent = '';
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-checked', 0);
    }

    this.updatePrice();
  }, 
  set: function () {
    // si type = radio
    let _parent = this._elem.type == 'radio' ? this._elem.parentNode : false;
    let _isChecked = this._elem.checked;
    let _modal = this._elem.getAttribute('data-modal');
    let _p = document.querySelector('p.' + _modal);
    let _pIsChecked = _p.getAttribute('data-checked');
    let _id = this._elem.id;
    //console.log(`type: ${this._ev.type}, checked: ${_pIsChecked}, name: ${this._elem.name}, id: ${_id}`);


    if (_isChecked) {
      _p.textContent = this._elem.value > 0 ? this._elem.getAttribute('data-text') : '';
      _p.setAttribute('data-opt', this._elem.id);
      _p.setAttribute('data-checked', 1);
      _pIsChecked = 1;
      const modal = document.getElementById(_modal);
      modal.querySelectorAll('[data-input]').forEach((e) => {

        e.getAttribute('data-input') == this._elem.id ? e.classList.add('active') : e.classList.remove('active');
      });
      modal.classList.toggle('visible');
    } else {
      _p.textContent = '';
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-checked', 0);
      _pIsChecked = 0;
    }
    if (_parent) {
      let _siblings = _parent.parentNode.querySelectorAll('div.product-option.radio.checked');
      _siblings.forEach((s) => {

        s.classList.remove('checked');
      });
      _parent.classList.toggle('checked');
    }

    this.updatePrice();
  },
  unset: function () {
    let link = this._elem.parentNode.querySelector('a');

    link.classList.remove('on-top');
    let _modal = this._elem.getAttribute('data-modal');

    let setInput = link.nextElementSibling;
    setInput.classList.remove('click');

    let _p = document.querySelector('p.' + _modal);
    let _parent = this._elem.parentNode;
    _p.textContent = '';
    _p.setAttribute('data-opt', '');
    _p.setAttribute('data-checked', 0);
    _parent.classList.remove('checked');
    this.updatePrice();
  },
  plastics: function() {
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.plastics');
    let hasPrice = _option.value != '';

    _p.textContent = _option.value != '' ? 'Kit Plastiques ' + _option.textContent : '';

    if (hasPrice) {
      _p.setAttribute('data-opt', 'plastics');
      _p.setAttribute('data-id', _option.getAttribute('data-id'));
      _p.setAttribute('data-name', _option.getAttribute('data-name'));
      _p.setAttribute('data-checked', 1);
    } else {
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-id', '');
      _p.setAttribute('data-name', '');
      _p.setAttribute('data-checked', 0);
    }

    this.updatePrice();
  },
  hubStickers: function() {
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.hubs-stickers');
    let hasPrice = _option.value != '';

    _p.textContent = _option.value != '' ?  _option.textContent : '';

    if (hasPrice) {
      _p.setAttribute('data-opt', 'hubs_stickers');      
      _p.setAttribute('data-id', _option.getAttribute('data-id'));
      _p.setAttribute('data-name', _option.getAttribute('data-name'));
      _p.setAttribute('data-checked', 1);
    } else {
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-id', '');
      _p.setAttribute('data-name', '');
      _p.setAttribute('data-checked', 0);
    }
    this.updatePrice();
  },
  miniPlates: function() {
    let _index = this._elem.selectedIndex;
    let _option = this._elem.options[_index];
    let _p = document.querySelector('p.mini-plates');
    let hasPrice = _option.value != '';

    _p.textContent = _option.value != '' ?  _option.textContent : '';

    if (hasPrice) {
      _p.setAttribute('data-opt', 'mini_plates');
      _p.setAttribute('data-id', _option.getAttribute('data-id'));
      _p.setAttribute('data-name', _option.getAttribute('data-name'));
      _p.setAttribute('data-checked', 1);
    } else {
      _p.setAttribute('data-opt', '');
      _p.setAttribute('data-id', '');
      _p.setAttribute('data-name', '');
      _p.setAttribute('data-checked', 0);
    }
    this.updatePrice();
  },
  updatePrice() {
    let customOpt = document.querySelector('.options .custom');
    // Calcul du prix des options choisies
    let priceOpts = 0.00;
    let priceAccessories = 0.00;
    
    let o = document.querySelector('.options-container');    
    if(o){
      o.querySelectorAll('input:checked').forEach(el => {
        // pas la housse ! pas les rim stickers les door-stickers
        if(!el.classList.contains('standalone')) priceOpts = priceOpts + parseFloat(el.value);
        //if(el.id != 'seat-cover') priceOpts = priceOpts + parseFloat(el.value);
      });
    }
    // Accessoires choisis
    let accessories = document.querySelector('#accessories').querySelectorAll('input:checked');
    let inputOpts = document.getElementById('price-opts');
    let inputOptsValue = parseFloat(inputOpts.value);
    let inputAccessories = document.getElementById('price-accessories'); 
    accessories.forEach(el => {
      priceAccessories = priceAccessories + parseFloat(el.getAttribute('data-price'));
    });

    // Option plastiques
    let plasticsOpt = document.getElementById('plastics-option');
    if (plasticsOpt) {
      let plasticOptPrice = parseFloat(plasticsOpt[plasticsOpt.selectedIndex].value) ?? 0.0;
      plasticPrice = isNaN(plasticOptPrice) ? 0.0 : plasticOptPrice;
    }



    // Si option custom, ne pas enlever la value de item[price][opts]
    inputOpts.value = !customOpt ? priceOpts  : inputOptsValue + priceOpts;
    inputAccessories.value = priceAccessories;
    item.update();
  }
};

window.addEventListener("DOMContentLoaded", function (e) {
  // récupération du panier
  //cart.fetch();
});