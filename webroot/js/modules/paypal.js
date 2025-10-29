const paypal = {
	_ev: null,
  	_elem:null,
  	lang:document.documentElement.lang,
  	_store: JSON.parse(localStorage.getItem('cart')),  
   	setElem: function (elem) {this._elem = elem;},
  	setEvent: function (event) {this._ev = event;},
  	getStoreId: function(){
  		console.debug(this._store.id);
  	}

}
export default paypal;