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
const giftCard = {
  _elem: null,
  _ev: null,
  _items: document.querySelector('#nbItems'),
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  pushToCart: async function(){
  	
  	const form = this._elem;
    const button = form.querySelector('button[type="submit"]');
    //button.disabled = 'disabled';
    //button.querySelector('span.load').classList.toggle('hidden');
    //button.querySelector('span.text').classList.toggle('hidden');
    const formData = new FormData(form);
    const _stored = JSON.parse(localStorage.getItem('cart'));
    let _id =  _stored !== null ? _stored.id  : null; 
    formData.append('item[id_order]', _id);
    let response = await fetch(form.action, {method: 'POST',body: formData});
    let json = await response.json();
    if (!response.ok) {
      console.log(`statut : ${response.status}`);
      console.log('ohhhhh it failed !');
      return;
    }
    if(response.status >= 200 && response.status <= 226)
    {
      if(json.cart){         
        localStorage.setItem('cart', JSON.stringify(json.cart));
        this.updateCartIcon(json.cart.items);
        document.getElementById('cart-btn').click();
      } else {
      	console.log('ohhhhh it failed !');
      	return;
      }
    }  
    if(response.status >= 500 && response.status <= 527)
    {
      throw new Error(`Erreur HTTP ! statut : ${response.status}, msg : ${json.error}`);
    }
  },
  updateCartIcon: function (nb_items) {
    if (this._items) this._items.textContent = nb_items;
  }
};
export default giftCard;