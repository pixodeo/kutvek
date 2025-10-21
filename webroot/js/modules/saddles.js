const saddle = {
  _ev: null,
  _elem:null,
  lang:document.documentElement.lang,
  currency: document.getElementById('item-currency') ? document.getElementById('item-currency').getAttribute('content') || 'EUR' : 'EUR',
   _items: document.querySelector('#nbItems'),
   setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  vehicles: function(){
    if(!this._elem.value)return; 
    const vehicle = document.getElementById('vehicle');
    let placeholder = vehicle.firstElementChild;
    while (vehicle.firstChild) { vehicle.removeChild(vehicle.firstChild) }
    vehicle.add(placeholder, vehicle.options[0]);        

    const options = document.getElementById('vehicle-2');      
    for (const option of options.options) {
      if(option.hasAttribute('data-brand-id')){
        let brand_id = option.getAttribute('data-brand-id');
        if(brand_id == this._elem.value){
          const opt = document.createElement("option");
          opt.value = option.value;
          opt.text = option.text;
          opt.setAttribute('data-brand-id', brand_id);
          vehicle.add(opt,null);          
        }        
      }      
    }    
    vehicle.options[0].selected = true;
    vehicle.disabled = false;
  },
  years: async function(){
    if(!this._elem.value)return;
    const year = document.getElementById('year');    
    let placeholder = year.firstElementChild;
    while (year.firstChild) { year.removeChild(year.firstChild) }
    year.add(placeholder, year.options[0]);

    const options = document.getElementById('years');
    for (const option of options.options) {
      if(option.hasAttribute('data-id')){
        let data_id = option.getAttribute('data-id');
        let data_year_id = option.getAttribute('data-year-id');
        if(data_id == this._elem.value){
          const opt = document.createElement("option");
          opt.value = option.value;
          opt.text = option.text;
          opt.setAttribute('data-id', data_id);
           opt.setAttribute('data-year-id', data_year_id);
          year.add(opt,null);
        }        
      }      
    }
    year.disabled = false;
    year.options[0].selected = true;    
  },
  year: function() {
    const input = document.getElementById('year-id');
    const val = this._elem.options[this._elem.selectedIndex].getAttribute('data-year-id');;
    input.value = val;
    const btn = document.getElementById('button-cart');
    btn.disabled = false;
    this.updateCost();
  },
  filters:async function(){
    this.refreshFilters();
    const universes = Array.from(document.querySelectorAll('input[name="universe[]"]:checked'), (x) => x.value).join(',');
    const brands = Array.from(document.querySelectorAll('input[name="brand[]"]:checked'), (x) => x.value).join(',');
    const vehicles = Array.from(document.querySelectorAll('input[name="vehicle[]"]:checked'), (x) => x.value).join(',');
    const colors = Array.from(document.querySelectorAll('input[name="color[]"]:checked'), (x) => x.value).join(',');
    const url = new URL(this._elem.form.action);    
    const urlParams = new URLSearchParams;
    
    if(universes.length > 0 )urlParams.append('universes', universes);
    if(brands.length > 0 )urlParams.append('brands', brands);
    if(vehicles.length > 0 )urlParams.append('vehicles', vehicles);
    if(colors.length > 0 )urlParams.append('colors', colors);
    url.search = urlParams.toString();
  
    console.log(url);
    console.log(urlParams.toString());

    const request = await fetch(url.href);
    if(request.ok){
      const text = await request.text();
      let frag = document.createRange().createContextualFragment(text);
      const parent = document.getElementById('products').parentNode;
      const products = document.getElementById('products');      
      const pagination = parent.querySelector('.pagination');           
      parent.replaceChild(frag.querySelector('#products'), products);      
      parent.replaceChild( frag.querySelector('.pagination'), pagination);
      return;
    }
    const json = await request.json();
  },
  refreshFilters: async function(){
    const universes = Array.from(document.querySelectorAll('input[name="universe[]"]:checked'), (x) => x.value).join(',');
    const brands = Array.from(document.querySelectorAll('input[name="brand[]"]:checked'), (x) => x.value).join(',');
    const vehicles = Array.from(document.querySelectorAll('input[name="vehicle[]"]:checked'), (x) => x.value).join(',');
    const colors = Array.from(document.querySelectorAll('input[name="color[]"]:checked'), (x) => x.value).join(',');
    const url = new URL(this._elem.form.getAttribute('data-refresh'));    
    const urlParams = new URLSearchParams;    
    if(universes.length > 0 )urlParams.append('universes', universes);
    if(brands.length > 0 )urlParams.append('brands', brands);
    if(vehicles.length > 0 )urlParams.append('vehicles', vehicles);
    if(colors.length > 0 )urlParams.append('colors', colors);
    url.search = urlParams.toString();
    
    const request = await fetch(url.href);
    if(request.ok){
       const json = await request.json();
       const arrayB =Array.from(json.brands);
       const brands = document.querySelectorAll('input[name="brand[]"]');
       brands.forEach(b => {
          if(arrayB.indexOf(parseInt(b.value, 10)) < 0)  {
            b.disabled = true;
            b.checked = false;
          } else {
            b.disabled = false;            
          }
          /*console.log( `value : ${b.value} : exists : ${array.indexOf(parseInt(b.value, 10))}`);*/
       });
       const list = document.getElementById('vehicles-list');
       const frag = document.createRange().createContextualFragment(json.vehicles);
       list.parentNode.replaceChild(frag.querySelector('#vehicles-list'), list);
       return;
    }
    const json = await request.json();
  },
  foam: function(){
    const id = this._elem.getAttribute('data-id');
    const name = this._elem.getAttribute('data-name');
    document.getElementById('foam-id').value = id;
    document.getElementById('foam-name').value= name;
    this.updateCost();
  },
  addToCart: async function(){
    //console.log(this._elem.action);
    const form = this._elem;
    const button = form.querySelector('button[type="submit"]');
    button.disabled = 'disabled';
    button.querySelector('span.load').classList.toggle('hidden');
    button.querySelector('span.text').style.opacity = '.5';

    const formData = new FormData(form);
    const _stored = JSON.parse(localStorage.getItem('cart'));
    if(_stored !== null) formData.append('item[id_order]', _stored.id);   
    const req = await fetch(form.action, {method: 'POST', body: formData,mode: 'cors', credentials: 'include'});
    if(req.status !== 201){
      const json = await req.json();
      return;
    }
    const text = await req.text();
    let frag = document.createRange().createContextualFragment(text);
    const aside = frag.firstChild;
    const items = aside.querySelector('#items').getAttribute('data-qty');
    if(_stored !== null) {
      _stored.qty = items;
      localStorage.setItem('cart', JSON.stringify(_stored));
    } else {
      const cart = {id: aside.getAttribute('data-order'), qty: items};
      localStorage.setItem('cart', JSON.stringify(cart));
    } 
    this.updateCartIcon(items);  
    const cart = document.getElementById('cart-preview'); 
    if(cart) cart.parentNode.replaceChild(aside, cart);  
    else document.body.append(aside);
    button.disabled = false;
    button.querySelector('span.load').classList.toggle('hidden');
    button.querySelector('span.text').style.opacity = '1';
    return;
  },
  updateCost: function(){
     const prices = document.querySelectorAll('.cost');
     var cost = 0.00;
     //console.log(prices);
     prices.forEach(e => {
       if(e.nodeName == 'INPUT' && e.checked) cost = cost + Number(e.value);
       if(e.nodeName == 'SELECT') cost = cost + Number(e.value);
     });      
     document.getElementById('item-cost').textContent = this.monetary(cost);
    
  },
  updateCartIcon: function (nb_items) {
    if (this._items) this._items.textContent = nb_items;
  },
  monetary: function (number,  maximumFractionDigits = 2) {
  // ex i18n : 'de_DE'on remplace le "_" par "-"
  // ex currency : 'EUR'  
  if (this.lang == 'fr') this.lang = 'fr_FR';
  return new Intl.NumberFormat(this.lang.replace('_', '-'), { style: 'currency', currency: this.currency, maximumFractionDigits: maximumFractionDigits }).format(number);
  }
}

export default saddle;