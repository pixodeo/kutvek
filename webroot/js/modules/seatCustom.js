
const monetary = function (number, l10n, currency, maximumFractionDigits = 2) {
  // ex i18n : 'de_DE'on remplace le "_" par "-"
  // ex currency : 'EUR'
  if (l10n == 'fr') l10n = 'fr_FR';
  return new Intl.NumberFormat(l10n.replace('_', '-'), { style: 'currency', currency: currency, maximumFractionDigits: maximumFractionDigits }).format(number);
};
const seatCustom = {
    _elem: null,
    _ev: null,
    _total: document.getElementById('price_t'),
    _l10n: document.getElementById('l10n').value,
    _currency: document.getElementById('currency').value,
    setEvent: function (event) {
        this._ev = event;
    },
    setElem: function (elem) {
        this._elem = elem;
    },
    seat: function() {        
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        const millesim = option.value;
        const install = document.getElementById('install');
        const total = document.getElementById('price_t');

        if(!millesim) return;
        
        const seat = option.getAttribute('data-seat');
        // Récupérer les infos de la housse
        var url = option.getAttribute('data-uri');
        
        fetch(url, { 
        method: 'GET'
        })
        .then((res) => {
         return res.json()
        })
        .then((json) => {
            if(json.success) {  
                const data = json.seat;
                console.log(`Récupération des infos de la housse: - ${millesim}`);  
                
                //var container = document.getElementById('seat-cover');  
                //var tpl = document.getElementById('tpl-seatcover'); 
                //var clone = document.importNode(tpl.content, true);
                var container = document.getElementById('seat-cover'); 
                container.classList.add('disabled');
                // -1- afficher le visuel si existe
                const img = document.getElementById('seat-img');
                if(data.visual) img.src = data.visual;
                else img.src = "/img/blank.png";
                
                // couleurs à changer
                
                for (const property in data.color_change) {
                      let color = document.getElementById(property);
                      if(data.color_change[property] == 'on')
                        color.classList.remove('hide');
                      else 
                        color.classList.add('hide');              
                }

                if(data.opt_install == 1)  install.classList.remove('hidden');
                else install.classList.add('hidden');
                container.classList.remove('disabled');
                
                  
                // Ajouter les différentes options
                const seatopts = document.querySelector('#seat-options');
                if(data.foam != null || data.opt_install == 1)
                {                   
                    seatopts.classList.remove('hide');
                }else {
                    seatopts.classList.add('hide');
                }
                const desc = document.getElementById('description');
                const des = document.getElementById('designation');
                const price_f = document.getElementById('price_f');
                const price = document.getElementById('price');
                this._total.textContent = monetary(data.price, this._l10n, this._currency, 2 ); 

                des.textContent = data.designation;
                desc.value = data.designation;
                price_f.textContent = data.priceCart;
                price.value = data.price;
                document.getElementById('btn-cart').disabled = false;
                return;  
                app.i18n();
            } else {
                alert(json.error);      
            }                   
        }); 

    },
    optFoam: function(){
        this.updatePriceOpts();
    },
    optInstall: function(){
        this.updatePriceOpts();
    },
    updatePriceOpts() {
      // Calcul du prix des options choisies
      let priceOpts = 0.00;   
      let qty = parseInt(document.getElementById('qty').value, 10);
      let opts = document.querySelectorAll('.opts');
      
      if(opts){
        opts.forEach(o => {            
          if (o.value) {
            priceOpts = priceOpts + parseFloat(o.value);            
          } 
          else priceOpts = parseFloat(priceOpts + 0.00);
        });
        // Mettre à jour le prix de l'option dans le formulaire
        document.getElementById('price-opts').value = parseFloat(priceOpts);
        //console.log(priceOpts);
      }

      let l10n = document.getElementById('l10n').value;
      let currency = document.getElementById('currency').value;
      let optPrice = document.getElementById('price_o');

      optPrice.textContent = monetary(priceOpts, this._l10n, this._currency, 2 ); 

      let totalPrice = (priceOpts + parseFloat(document.getElementById('price').value)) * qty;
      
      this._total.textContent = monetary(totalPrice, this._l10n, this._currency, 2 ); 
    },

    pushToCart: async function () {
        const form = this._elem;
        const button = form.querySelector('button[type="submit"]');
        //button.disabled = 'disabled';
        //button.querySelector('span.load').classList.toggle('hidden');
        //button.querySelector('span.text').classList.toggle('hidden');
        const formData = new FormData(form);
        formData.append('behavior', 'CustomSeatBehavior');
        formData.append('item[img]', document.getElementById('seat-img').src);
        // envoie des options type install et mousse confort
        const inputFoam = document.getElementById('opt-foam');
        let _index1 = inputFoam.selectedIndex;
        let _option1 = inputFoam.options[_index1];
        formData.append('item[cover][foam][id]', _option1.getAttribute('data-id'));
        formData.append('item[cover][foam][name]', _option1.getAttribute('data-name'));

        const inputInstall = document.getElementById('opt-install');
        let _index2 = inputInstall.selectedIndex;
        let _option2 = inputInstall.options[_index2];
        formData.append('item[cover][install][id]', _option2.getAttribute('data-id'));
        formData.append('item[cover][install][name]', _option2.getAttribute('data-name'));

        const _stored = JSON.parse(localStorage.getItem('cart'));
        
        let _id =  _stored !== null ? _stored.id  : null; 
        //console.log('cart exist : ' + _id); 
        formData.append('item[id_order]', _id);    
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
           // app.updateCartIcon(json.cart.items);
            document.getElementById('cart-btn').click();
          }
        }  
        if(response.status >= 500 && response.status <= 527)
        {
          throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
        }
  },
}
export default seatCustom;