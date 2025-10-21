const custom = {
    _elem: null,
    _ev: null,
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
                
                return;  
                app.i18n();
                
                 
                // modification des infos du panier
                const itmCart = document.querySelector('aside.p-cart');
                const itms = itmCart.querySelector('div.items');
                const itm = itms.querySelectorAll('p')[0];
                itm.querySelector('span.designation').textContent = data.designation;               
                itm.querySelector('input[name="item[webshop_price]"]').value = data.price;
                itmCart.querySelector('#item-total').setAttribute('data-price', data.price);
                itmCart.querySelector('#item-total').setAttribute('data-currency', data.currency_lib);
                itmCart.querySelector('#item-total').textContent = data.priceCart;              

            } else {
                alert(json.error);      
            }                   
        }); 

    }
}

const seatCover = {
    _elem: null,
    _ev: null,
    _cookies: {},
    setElem: function(elem){
    this._elem = elem;
    },  
    setEvent: function(event){
        this._ev = event;
    },
    custom : function()
    {
        // Option sélectionée
        const index = this._elem.selectedIndex;
        const option = this._elem.options[index];
        const millesim = option.value;
        const seat = option.getAttribute('data-seat')
        
        // Récupérer les infos de la housse
        var url = this._elem.getAttribute('data-uri').replace(':vid', millesim).replace(':scid', seat);
        fetch(url, { 
        method: 'GET'
        })
        .then((res) => {
         return res.json()
        })
        .then((json) => {
            if(json.success) {  
                const data = json['seat-cover'];
                console.log(`Récupération des infos de la housse: - ${millesim}`);      
                var container = document.getElementById('seat-cover');  
                var tpl = document.getElementById('tpl-seatcover'); 
                var clone = document.importNode(tpl.content, true);
                // Ajouter les différentes options
                var seatopts = clone.querySelector('.seat-opts');
                if(data.comfort_foam != null)
                {                   
                    seatopts.querySelector('.opt-foam').classList.remove('hide');
                }
                if(data.installation != null)
                {                   
                    seatopts.querySelector('.opt-install').classList.remove('hide');

                }

                clone.querySelector('img').src = data.visual;               
                while (container.firstChild) {container.removeChild(container.firstChild)}
                container.appendChild(clone);
                app.i18n(); 
                // modification des infos du panier
                const itmCart = document.querySelector('aside.p-cart');
                const itms = itmCart.querySelector('div.items');
                const itm = itms.querySelectorAll('p')[0];
                itm.querySelector('span.designation').textContent = data.designation;               
                itm.querySelector('input[name="item[webshop_price]"]').value = data.price;
                itmCart.querySelector('#item-total').setAttribute('data-price', data.price);
                itmCart.querySelector('#item-total').setAttribute('data-currency', data.currency_lib);
                itmCart.querySelector('#item-total').textContent = data.priceCart;              

            } else {
                alert(json.error);      
            }                   
        }); 

    },
    customColor: function(){
        const radio = this._elem;
        const sibling = radio.nextElementSibling;
        if(sibling.classList.contains('color-unselect')) sibling.classList.remove('color-unselect');

        //console.log(this._elem.value);
        const parent = radio.parentNode;
        parent.querySelectorAll('label').forEach(function(label){
             if(label.getAttribute('for') != radio.id)
                label.classList.add('color-unselect');
        });
    }
};

export default custom;