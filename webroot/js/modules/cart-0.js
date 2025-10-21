const _defaultLang = 'fr';
const monetary = function (number, l10n, currency, maximumFractionDigits = 2) {
  // ex i18n : 'de_DE'on remplace le "_" par "-"
  // ex currency : 'EUR'
  if (currency == '€') currency = 'EUR';
  if (currency == '£') currency = 'GBP';
  if (currency == '$') currency = 'USD';
  if (currency == '$ CAN') currency = 'CAD';
  if (l10n == 'fr') l10n = 'fr_FR';
  return new Intl.NumberFormat(l10n.replace('_', '-'), { style: 'currency', currency: currency, maximumFractionDigits: maximumFractionDigits }).format(number);
};
const cart = {
  _elem: null,
  _ev: null,
  _userInfo: false,
  _customer: false,
  _connected: false,
  _items: document.querySelector('#nbItems'),
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  read: async function(){  
    const decode = decodeURIComponent(window.atob(this._elem.getAttribute("data-obf")));
    const _store = JSON.parse(localStorage.getItem('cart'));
    const url = _store !== null ? decode.replace(':order', _store.id): decode.replace(':order', 0);      
    const req = await fetch(url, {method: 'GET', mode: 'cors', credentials: 'include'});
    if(req.status !== 200){
      const json = await req.json();
      return;
    }
    const text = await req.text();
    let frag = document.createRange().createContextualFragment(text);
    const aside = frag.firstChild;    
    const items = aside.querySelector('#items').getAttribute('data-qty');
    const cart = document.getElementById('cart-preview'); 
    if(cart) cart.parentNode.replaceChild(aside, cart);  
    else document.body.append(aside);

    this.updateCartIcon(items); 
    return;
  },
  updateCartIcon: function (nb_items) {
    if (this._items) this._items.textContent = nb_items;
  },
  overview: async function () {
    console.log('module overview !');   
    const cartPreview = document.getElementById('cart-preview');
    if (!cartPreview) return;

    this._store = JSON.parse(localStorage.getItem('cart'));

    if (this._store !== null && (this._store.items > 0 || this._store.qty > 0)) {
      var attribute = this._elem.getAttribute("data-obf"); 
      let url = decodeURIComponent(window.atob(attribute));
      console.log(url);
      const itemsList = document.getElementById('items');
      //const platform = this._store.platform;
      
      let _l10n = _defaultLang;
      cartPreview.querySelector('#empty-cart').classList.add('invisible');
      cartPreview.querySelector('#cart-filled').classList.remove('invisible');
      let itemTotal = cartPreview.querySelector('#item-total');
      let totalAmount = cartPreview.querySelector('#total-to-pay');
      let discountInfo = cartPreview.querySelector('#discount');
      discountInfo.innerHTML = '';    
      let response = await fetch(`${url}?id=${this._store.id}`, {method: 'GET'});
      let json = await response.json();
      if (!response.ok) {
        //throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);
      }
      if(response.status >= 200 && response.status <= 226)
      {    
        if(json.cart.items.length > 0)
        {
          const currency = json.cart.amount.currency_code;
          itemTotal.textContent = monetary(parseFloat(json.cart.amount.breakdown.item_total.value) + parseFloat(json.cart.amount.breakdown.tax_total.value),_l10n, currency,2);
          
          const total_amount = parseFloat(json.cart.amount.breakdown.item_total.value) + parseFloat(json.cart.amount.breakdown.tax_total.value) - parseFloat(json.cart.amount.breakdown.discount.value) - parseFloat(json.cart.amount.breakdown.shipping_discount.value);
          totalAmount.textContent = monetary(total_amount,_l10n, currency,2);

          let template = document.getElementById('item-tpl');
          
          // Je boucle les items dans itemsList, j'enlève le 1er élément tant qu'il y a 1 enfant
          while (itemsList.firstChild) { itemsList.removeChild(itemsList.firstChild); }
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
              let split = itm.links.self.split('/'); // attention actuellement y'a un index 0 à vide
              let joinValues = ['',split[1], 'carts', split[2], split[3]];
              let join = joinValues.join('/');         
              infoItem.setAttribute('data-behavior', itm.behavior);
              infoItem.href = join + '?behavior=' +itm.behavior;
              price.textContent = monetary(unitValue, _l10n, currency, 2);
              itemsList.appendChild(itemTpl);
          });

          // estimation des coûts de livraison
          if(json.cart.shipping_estimation){
              const shipping_cost = json.cart.shipping_estimation;
              const vat = shipping_cost.vat;
              console.log(shipping_cost.c_type);
              const chronoClassic = vat > 0 ? parseFloat(shipping_cost.chrono_classic * 1.20) : parseFloat(shipping_cost.chrono_classic);
              const chronoExpress = vat > 0 ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
              const chronoRelay = vat > 0 ? parseFloat(shipping_cost.chrono_relay * 1.20): parseFloat(shipping_cost.chrono_relay);

              // si pays avec tva et pro
              //const chronoClassic = (vat > 0 && shipping_cost.c_type == 'std') ? parseFloat(shipping_cost.chrono_classic * 1.20) : parseFloat(shipping_cost.chrono_classic);
              //const chronoExpress = (vat > 0 && shipping_cost.c_type == 'std') ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
              //const chronoRelay = (vat > 0 && shipping_cost.c_type == 'std') ? parseFloat(shipping_cost.chrono_relay * 1.20): parseFloat(shipping_cost.chrono_relay);

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
  setUserInfo: function(info){
    this._userInfo = info;
  },  
  checkout: async function(){   
    console.log('module checkout');
    this._store = JSON.parse(localStorage.getItem('cart'));
    if (this._store !== null && (this._store.items > 0 || this._store.qty > 0)) {       
        // on va chercher le panier sur l'api           
        let _l10n = _defaultLang;  
        const cartPreview = document.getElementById('order'); 
        const itemsList = document.getElementById('items'); 
        let attribute = cartPreview.getAttribute("data-obf"); 
        let url = decodeURIComponent(window.atob(attribute));       
        let itemTotal = cartPreview.querySelector('#item-total');
        let totalAmount = cartPreview.querySelector('#total-to-pay');
        let discountInfo = cartPreview.querySelector('#discount');
        const formUserAddr = document.getElementById('user-address');             
        const formCustomer = document.getElementById('form-customer');            
        const divPickup = document.getElementById('pickup');
        const formPickup = divPickup.querySelector('#form-pickup'); 
        // ici ajouter les cors et crédentials
        let response = await fetch(`${url}?id=${this._store.id}`, {method: 'GET',mode: 'cors', credentials: 'include'});
        let json = await response.json();
        if (!response.ok) { throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : `);}
        if(response.status >= 200 && response.status <= 226) {
          this._customer = json.cart.customer;       
          if(json.cart.items.length > 0) {
            const currency = json.cart.amount.currency_code;
            itemTotal.textContent = monetary(parseFloat(json.cart.amount.breakdown.item_total.value) + parseFloat(json.cart.amount.breakdown.tax_total.value),_l10n, currency,2);            
            const total_amount = parseFloat(json.cart.amount.value);
            totalAmount.textContent = monetary(total_amount,_l10n, currency,2);

            // Affichage de les  remises et autres cartes cadeau le cas échénant
            if(json.cart.discount.length > 0){              
              let tp = document.getElementById('discount-tpl');
              let d = document.importNode(tp.content, true);
              json.cart.discount.forEach(e => {
                  let p = d.cloneNode(true);                  
                  p.querySelector('.discount').textContent = e.designation;
                  p.querySelector('.amount').textContent = monetary(e.value,_l10n, currency,2);
                  discountInfo.appendChild(p);
              });
              discountInfo.classList.remove('hidden');             
            }

            let template = document.getElementById('item-tpl');
            let currentShippingCost = 0;
            
            // Je boucle les items dans itemsList, j'enlève le 1er élément tant qu'il y a 1 enfant
            while (itemsList.firstChild) { itemsList.removeChild(itemsList.firstChild) }
            json.cart.items.forEach((itm) => {
              let unitValue = parseFloat(itm.tax.value) + parseFloat(itm.unit_amount.value);
              let itemTpl = document.importNode(template.content, true);
              itemTpl.querySelector('div').id = 'item-' + itm.item_id;
              let img = itemTpl.querySelector('img');
                      
              let quantity = itemTpl.querySelector('.item-qty');
              let price = itemTpl.querySelector('.item-price');
              let itemName = itemTpl.querySelector('.item-desc');

              img.src = itm.item_visual != null ? itm.item_visual : 'img/blank.png';
              itemName.textContent = itm.name;
              quantity.value = itm.quantity;
              quantity.id = `qty-${itm.item_id}`;          
              price.textContent = monetary(unitValue, _l10n, currency, 2);
              itemsList.appendChild(itemTpl);
            });

            let shippingValue = cartPreview.querySelector('#shipping-amount');
            let _hasShipping = false;
            if(json.cart.amount.breakdown.shipping.value){
              shippingValue.textContent = monetary(parseFloat(json.cart.amount.breakdown.shipping.value), _l10n, currency);
              currentShippingCost = parseInt(json.cart.amount.breakdown.shipping.value,10);
              _hasShipping = true;
            }             
            // estimation des coûts de livraison
            if(json.cart.shipping.estimation){
              const address_user = json.cart.customer.address !== null ? json.cart.customer.address : 0;
              document.getElementById('fees-address').value = address_user;
              const shipping_cost = json.cart.shipping.estimation;
              const shipping_country = shipping_cost.id;
              const vat = shipping_cost.vat;

              // si pays avec tva et part sauf france
              //const chronoClassic = (vat > 0 && (shipping_cost.c_type == 'std' || shipping_country == 62)) ? parseFloat(shipping_cost.chrono_classic * 1.20) : parseFloat(shipping_cost.chrono_classic);
              //const chronoExpress = (vat > 0 && (shipping_cost.c_type == 'std' || shipping_country == 62)) ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
              //const chronoRelay = (vat > 0 && (shipping_cost.c_type == 'std' || shipping_country == 62)) ? parseFloat(shipping_cost.chrono_relay * 1.20): parseFloat(shipping_cost.chrono_relay);
              
              const chronoClassic = (vat > 0 &&  shipping_country == 62) ? parseFloat(shipping_cost.chrono_classic * 1.20) : parseFloat(shipping_cost.chrono_classic);
              const chrono13 = vat > 0 && shipping_country == 62 ? parseFloat(shipping_cost.chrono_13 * 1.20) : parseFloat(shipping_cost.chrono_13);
              const chronoExpress = vat > 0 && shipping_country == 62 ? parseFloat(shipping_cost.chrono_express * 1.20) : parseFloat(shipping_cost.chrono_express);
              const chronoRelay = vat > 0  && shipping_country == 62 ? parseFloat(shipping_cost.chrono_relay * 1.20): parseFloat(shipping_cost.chrono_relay);

              if(!_hasShipping) {
                shippingValue.textContent = monetary(chronoClassic, _l10n, currency);
                totalAmount.textContent = monetary(total_amount + chronoClassic,_l10n,currency,2); 
              } 

              let divCClassic = cartPreview.querySelector('.chrono-classic');
              let divC13 = cartPreview.querySelector('.chrono-13');
              let divCExpress = cartPreview.querySelector('.chrono-express');
              let divCIntl = cartPreview.querySelector('.chrono-express-intl');
              let divCRelay = cartPreview.querySelector('.chrono-relay');

              console.log(json.cart.delivery_address);
              if(shipping_cost.chrono_relay) 
                {
                  divCRelay.classList.remove('hide');
                  divCRelay.querySelector('.cost').textContent = monetary(chronoRelay, _l10n, currency);
                  divCRelay.querySelector('#chrono-relay').value = chronoRelay;
                } else {
                  // Pas de chrono relay !
                  document.getElementById('link-to-relay').classList.add('hide');
                }
              // si on a une addresse de livraison déjà choisie ou qu'on a une adresse par défaut sur le compte
              if(json.cart.delivery_address !== null || address_user > 0){
                               
                if(shipping_cost.chrono_13 && parseInt(shipping_cost.classic,10) == 0) 
                {
                  divC13.classList.remove('hide');
                  divCExpress.classList.add('hide');
                  divC13.querySelector('.cost').textContent = monetary(chrono13, _l10n, currency);
                  divC13.querySelector('#std-13').value = chrono13;
                } else {
                  // on a un express pas de classic
                  if(shipping_cost.chrono_express && parseInt(shipping_cost.classic,10) == 0) 
                  {
                    divCIntl.classList.remove('hide');
                    divCIntl.querySelector('.cost').textContent = monetary(chronoExpress, _l10n, currency);
                    divCIntl.querySelector('#std-intl').value = chronoExpress;
                  }
                  // On a un express et un classic
                  if(shipping_cost.chrono_express && parseInt(shipping_cost.classic,10) > 0) 
                  {
                    divCExpress.classList.remove('hide');
                    divCExpress.querySelector('.cost').textContent = monetary(chronoExpress, _l10n, currency);
                    divCExpress.querySelector('#std-express').value = chronoExpress;
                  }

                  if(shipping_cost.chrono_classic && parseInt(shipping_cost.classic,10) > 0) 
                  {
                    divCClassic.classList.remove('hide');
                    let textDiv = divCClassic.querySelector('.cost');
                    divCClassic.querySelector('#std-classic').value = chronoClassic;
                    if(chronoClassic == 0){                      
                      divCClassic.querySelector('.free').classList.remove('hide');
                    }else{
                      textDiv.textContent = monetary(chronoClassic, _l10n, currency);
                    }
                    if(chronoClassic == 0 && shipping_cost.c_type === 'pro'){
                      divCClassic.classList.add('hide');
                    }
                                      
                  }
                }
              }           
            }

            /**
             * Adresse de livraison
             */
            const shippingAddress = json.cart.shipping.address;            
            const pickup = json.cart.shipping.pickup;
            const currentAddr = json.cart.shipping.method; 
            const defaultAddr = json.cart.shipping.default; 
            const customerInfo = json.cart.shipping.name;
            const contactInfo = json.cart.shipping.contact;
            const deliveryType = parseInt(json.cart.shipping.type_id,10);
            const email_delivery = json.cart.shipping.email_delivery;
            
            let checkAddress = document.getElementById('shipping-method-input');
            let checkPayment = document.getElementById('pay-input');


            // Adresse point retrait
            if(deliveryType === 2 && shippingAddress.id !== null) {
              document.querySelector('a[href="#pickup"]').click();              
              divPickup.querySelector('.address_line_1').textContent = shippingAddress.address_line_1;
              divPickup.querySelector('.address_line_2').textContent = shippingAddress.address_line_2;
              divPickup.querySelector('.postal_code').textContent = shippingAddress.postal_code;
              divPickup.querySelector('.admin_area_2').textContent = shippingAddress.admin_area_2;              
              formPickup.querySelector('input[name="address[delivery_address]"]').value = shippingAddress.id;
              
              for (const property in customerInfo) {                  
                  let input = formCustomer.querySelector(`#${property}`);
                if(input) { 
                  if(customerInfo[property] !== null)
                    input.value = customerInfo[property];                                  
                }    
              }
              for (const property in contactInfo) {                  
                  let input = formCustomer.querySelector(`#${property}`);
                if(input) { 
                  if(contactInfo[property] !== null)
                    input.value = contactInfo[property];                             
                }    
              }
                checkAddress.previousElementSibling.querySelector('span.step').classList.add('check');
                checkAddress.disabled = false;
                checkPayment.checked = true;
                checkPayment.disabled = false;  
            }
            // Relais colis
            if(deliveryType === 1 && shippingAddress.id !== null){
              
               for (const property in customerInfo) {                  
                let input = formCustomer.querySelector(`#${property}`);
              if(input) { 
                if(customerInfo[property] !== null)
                  input.value = customerInfo[property];
                               
              }    
              }
              for (const property in contactInfo) {                  
                  let input = formCustomer.querySelector(`#${property}`);
                if(input) { 
                  if(contactInfo[property] !== null)
                    input.value = contactInfo[property];                               
                }    
              }
              divPickup.querySelector('.address_line_1').textContent = pickup.address_line_1;
              divPickup.querySelector('.address_line_2').textContent = pickup.address_line_2;
              divPickup.querySelector('.postal_code').textContent = pickup.postal_code;
              divPickup.querySelector('.admin_area_2').textContent = pickup.admin_area_2;              
              formPickup.querySelector('input[name="address[delivery_address]"]').value = pickup.id;
              if(json.cart.shipping.cost !== null) {
                checkAddress.previousElementSibling.querySelector('span.step').classList.add('check');
                checkAddress.disabled = false;
                checkPayment.checked = true;
                checkPayment.disabled = false;  
              }
            }
            // Livraison standard
            if(deliveryType === 4 && shippingAddress.id !== null){      
              for (const property in customerInfo) {                  
                let input = formCustomer.querySelector(`#${property}`);
              if(input) { 
                if(customerInfo[property] !== null)
                  input.value = customerInfo[property];                             
              }    
              }
              for (const property in contactInfo) {                  
                  let input = formCustomer.querySelector(`#${property}`);
                if(input) { 
                  if(contactInfo[property] !== null)
                    input.value = contactInfo[property];                                  
                }    
              }
              document.getElementById('fees-address').value = shippingAddress.id;
              divPickup.querySelector('.address_line_1').textContent = pickup.address_line_1;
              divPickup.querySelector('.address_line_2').textContent = pickup.address_line_2;
              divPickup.querySelector('.postal_code').textContent = pickup.postal_code;
              divPickup.querySelector('.admin_area_2').textContent = pickup.admin_area_2;              
              formPickup.querySelector('input[name="address[delivery_address]"]').value = pickup.id;     
              checkAddress.disabled = false;
              if(json.cart.shipping.cost !== null) {
                checkAddress.previousElementSibling.querySelector('span.step').classList.add('check');                
                checkPayment.checked = true;
                checkPayment.disabled = false;  
              } else {
                console.log('no shipping fees');
                checkAddress.checked = true;
              }
            }
            if(defaultAddr){
              document.querySelectorAll('.shipping-default').forEach(e => {e.innerHTML = defaultAddr;});              
            } else {
              const btn = document.getElementById('change-address');
              btn.setAttribute('data-i18n', 'add-address');
              btn.textContent = 'Ajouter une adresse';
            }            
            if(currentAddr){
              if(deliveryType === 1 || deliveryType === 2 ){
                // point relais 
                document.querySelectorAll('.shipping-default').forEach(e => {e.innerHTML = defaultAddr;});              
              }else{
                document.querySelectorAll('.shipping-default').forEach(e => {e.innerHTML = currentAddr;});   
              }
                    
              document.querySelectorAll('.shipping-method').forEach(e => {e.innerHTML = currentAddr;});
            }
            if(json.cart.delivery_address == null){
              checkAddress.checked = true;
              checkAddress.disabled = true;
              checkPayment.disabled = true; 
              checkPayment.checked = false;
            }
            if(email_delivery > 0){
              checkAddress.checked = false;
              checkAddress.disabled = true;
              checkPayment.disabled = false; 
              checkPayment.checked = true;
            }
          }
        }  
        if(response.status >= 500 && response.status <= 527) {throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`); } 
        // on check l'user
        //let userInfo = await checkoutInfo();
        let checkInput = document.getElementById('email-input');
        checkInput.disabled = false;
        
        // check customer info


        
        //console.debug(this._userInfo);
        // pas de client, étape 1 à compléter
        if(this._connected === false){ 
          document.querySelector('#form-customer').classList.add('hide');
          document.querySelector('.already-custommer').classList.remove('hide');
          checkInput.checked = true; 

          // l'adresse pour une livraison standard à domicile sera associée à la commande

        } else { 
          if(formCustomer.checkValidity() == false){
            //checkInput.checked = true;
            //document.querySelector('#form-customer').classList.remove('hide'); ; 
          }

          // si on a un user on a forcément un email        
          //console.log(userInfo)
          // on va mettre id user dans le panier
          
          let spanStep = checkInput.previousElementSibling.querySelector('span.step');
          spanStep.classList.add('check');          
          //document.getElementById('email').value = this._userInfo.email;          
          if(this._customer && this._customer.payLater > 0){
            document.getElementById('pro-payment').classList.remove('hide');
            document.querySelector('#pro-payment + div').classList.remove('hide');      
          } else {
            document.getElementById('gift-card-payment').classList.remove('hide');
            document.querySelector('#gift-card-payment + div').classList.remove('hide');
          }        
          // si on a une adresse de livraison, un type de livraison et le coût on passe direct au paiement
          /*if(json.cart.step === 'payment'){
            checkAddress.previousElementSibling.querySelector('span.step').classList.add('check');
            checkAddress.disabled = false;
            checkPayment.checked = true;
            checkPayment.disabled = false;

          } else {
            checkAddress.disabled = false;
            checkAddress.checked = true;
          }*/
          // On renseigne l'id user 
          let userInput = document.getElementById('user-input');
          userInput.value = this._customer.id ?? null;
        }        
    }
    this.translate();
  },
  address: async function() {
      const formData = new FormData(this._elem);
      let storage = JSON.parse(localStorage.getItem('cart'));
      formData.append('order', storage.id); 
      let response = await fetch(this._elem.action, {method: 'POST', mode: 'cors', credentials: 'include', body: formData});
      if(response.ok) {  
        window.location.reload();
        return;         
      }
  },  
  stdDelivery: async function() {
    // quel button a submit le formulaire ? pour récupérer sa valeur
    const submitter = this._ev.submitter || document.activeElement;  
    const _type = submitter.getAttribute('data-type');
    let cost; 
    const formData = new FormData(this._elem);
    let storage = JSON.parse(localStorage.getItem('cart'));
    let uri = this._elem.action.replace(':order', storage.id);  
    if(submitter.value > 0) cost = submitter.value; 
    else  cost = '0.00';  
    formData.append('cost', cost);
    if(_type) formData.append('shipping_type', _type);
    let response = await fetch(uri, {method: 'POST', body: formData});
    if(response.ok) {  
      window.location.reload();
      return;      
    }   
  },
  relayAddress: async function(currentFeature, e){
    // On envoie l'adresse du point relai au panier avec les infos du client
    const el = e.target;
    /*console.log(e);       
    console.log(currentFeature);   */
    const form = el.form;
    const formCustomer = document.getElementById('form-customer');
    console.log(formCustomer.checkValidity());
    
    if(!formCustomer.checkValidity()) {
      el.checked = false;
      let checkInput = document.getElementById('email-input');
      checkInput.checked = true;
      formCustomer.querySelector('input[type="submit"]').click();
      return;
    } 
    const formData = new FormData(form);
    const customerData = new FormData(formCustomer); 
    let storage = JSON.parse(localStorage.getItem('cart'));

    formData.append('order', storage.id); 

    formData.append('customer[firstname]', customerData.get('firstname'));
    formData.append('customer[lastname]', customerData.get('lastname'));  
    formData.append('customer[company]', customerData.get('company'));       
    formData.append('customer[phone]', customerData.get('phone'));
    formData.append('customer[cellphone]', customerData.get('cellphone')); 

    formData.append('chronoRelay[id]', currentFeature.properties.id);
    formData.append('chronoRelay[name]', currentFeature.properties.name);        
    formData.append('chronoRelay[type]', currentFeature.properties.type); 

    formData.append('address[line1]', currentFeature.properties.address_line_1);  
    if(currentFeature.properties.address_line_2 != null)
      formData.append('address[line2]', currentFeature.properties.address_line_2);       
    formData.append('address[city]', currentFeature.properties.city);
    formData.append('address[zipcode]', currentFeature.properties.postalCode);
    const indexOfFirst = currentFeature.properties.postalCode.indexOf('20');
    if(currentFeature.properties.countryCode == 'FR' && indexOfFirst === 0) {
      alert('Une surtaxe de 25,60 € H.T est appliquée pour les livraisons vers la Corse ');
    }
    
    if(currentFeature.properties.admin_area_2 != null)
      formData.append('address[line4]', currentFeature.properties.admin_area_2);

    formData.append('countryCode', currentFeature.properties.countryCode);    

    // Penser à ajouter les points geometry, latitude / longitude
    let response = await fetch(form.action, {method: 'POST', body: formData});
    if(response.ok) {
      window.location.reload();
      return;
    }
  },
  pickupAddress: async function(){
    const formCustomer = document.getElementById('form-customer');    
    console.log(formCustomer.checkValidity());
    if(!formCustomer.checkValidity()) {
      let checkInput = document.getElementById('email-input');
      checkInput.checked = true;
      formCustomer.querySelector('input[type="submit"]').click();
      return;
    } 
    const customerData = new FormData(formCustomer); 
    const formData = new FormData(this._elem);
    let storage = JSON.parse(localStorage.getItem('cart'));
    formData.append('order', storage.id); 

    formData.append('customer[firstname]', customerData.get('firstname'));
    formData.append('customer[lastname]', customerData.get('lastname'));  
    formData.append('customer[company]', customerData.get('company'));       
    formData.append('customer[phone]', customerData.get('phone'));
    formData.append('customer[cellphone]', customerData.get('cellphone')); 
    formData.append('customer[id]', customerData.get('uid'));
    let response = await fetch(this._elem.action, {method: 'POST', body: formData});
    if(response.ok) { 
      window.location.reload();
      return;     
    }
  },
  updateAmount: function(amount){
    // On met à jour les totaux du panier, fras de livraison, total...
    let itemTotal = document.getElementById('item-total');
    itemTotal.textContent = monetary(parseFloat(amount.breakdown.item_total.value) + parseFloat(amount.breakdown.tax_total.value), _defaultLang, amount.breakdown.tax_total.currency_code);
    let totalAmount = document.getElementById('total-to-pay');
    totalAmount.textContent = monetary(amount.value, _defaultLang, amount.currency_code);
    let shippingAmount = document.getElementById('shipping-amount');
    shippingAmount.textContent = monetary(amount.breakdown.shipping.value, _defaultLang, amount.breakdown.shipping.currency_code);
  },
  translate: function () {
    let lang = document.documentElement.lang;
    if (_defaultLang == lang) return;
    let trads = JSON.parse(localStorage.getItem('i18n_' + lang));
    const imgs = document.querySelectorAll('img.rw');
    trads.forEach(function (trad) {
      const type = trad.type;
      const id = trad.node;
      const content = trad.content;
      const elems = document.querySelectorAll('[data-i18n="' + id + '"]');
      if (content !== null) {
        // on a une traduction
        switch (type) {
          case 'placeholder':
            elems.forEach(
              function (el) {
                // Si on a un select c'est la 1ère option qui est le placeholder
                if (el.nodeName == 'SELECT') {
                  var option = el.options[0].textContent = content;
                } else
                  el.placeholder = content;
              }
            );
            break;
          case 'label':
            elems.forEach(
              function (el) {
                const label = document.querySelector('[for="' + el.id + '"]');
                //console.log(label);
                if (label)
                  label.textContent = content;
              }
            );
            break;
          case 'html':
            elems.forEach(
              function (el) {
                el.innerHTML = content;
              }
            );
            break;
          case 'wrapper':
            elems.forEach(
              function (el) {
                el.querySelector('label').textContent = content;
              }
            );
            break;
          default:
            elems.forEach(
              function (el) {
                if (el.nodeName == 'INPUT' || el.nodeName == 'SELECT') {

                  // rien dans colonne type 
                  const label = document.querySelector('[for="' + el.id + '"]');
                  //console.log(label);
                  if (label)
                    label.textContent = content;

                }
                else {
                  el.textContent = content;
                }
              }
            );
            break;
        }
      }
    });
    if (imgs.length > 0) {
      /*var user = JSON.parse(localStorage.getItem('user'));
      let lang = document.documentElement.lang;
      if (user !== null && user.country == 16 && (lang == 'fr' || lang == 'nl')) {
        imgs.forEach((i) => {

          i.src = i.getAttribute('data-rw').replace(':i18n', lang + '_BE');
        });
      }*/
    }
  },
  payLater: async function(){
    // paiement à l'expédition
    const _id = JSON.parse(localStorage.getItem('cart')).id;
    const thanksUrl = document.getElementById('thanks').value;
    const xsrfToken = localStorage.getItem('xsrfToken');
    let url = this._elem.getAttribute('data-url');   
    url = url.replace(':order', _id);
    console.log(url);
    // inclure token csrf
    let headers = new Headers();
    headers.append('x-xsrf-token', xsrfToken);
    let response = await fetch(url, {
      method: 'POST',      
      headers
    });

    if(response.status === 200) {
        window.location.assign(thanksUrl);
    }},
  payWithGiftCard: async function(){
    // paiement avec carte cadeau    
    const _id = JSON.parse(localStorage.getItem('cart')).id;
    const xsrfToken = localStorage.getItem('xsrfToken');
    let url = this._elem.action;   
    url = url.replace(':order', _id);
    const err_div = document.getElementById('gift-card-error');
    err_div.querySelector('.h5').textContent = '';
    err_div.querySelector('.h5 + div').textContent = '';
    const formData = new FormData(this._elem);    
    let headers = new Headers();
    headers.append('x-xsrf-token', xsrfToken);
    let response = await fetch(url, {
      method: 'POST',     
      headers,
      body: formData
    });
    if(!response.ok) return;
    const json = await response.json();
    if(json.error){      
      err_div.querySelector('.h5').innerHTML = json.designation;
      err_div.querySelector('.h5 + div').innerHTML = json.description; 
      return;    
    }
    if(json.paid){
      // paid est à 1 on redirige vers la page de remerciements
      const thanksUrl = document.getElementById('thanks').value;
      console.log(thanksUrl);
      console.log(json);
      window.location.assign(thanksUrl);
    } else {
      window.location.reload();
    }
    // Pour tous les autres cas on refresh sans cache
    //
  },
  promoCode: async function(){
    this._store = JSON.parse(localStorage.getItem('cart'));
    console.log(this._store.id);
     // quel button a submit le formulaire ? pour récupérer sa valeur
    const submitter = this._ev.submitter || document.activeElement;
    submitter.disabled = true;
    console.log(submitter);
    const url = submitter.getAttribute('formaction').replace(':order',this._store.id)
    console.log(url);
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
export default cart;