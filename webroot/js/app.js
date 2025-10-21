const app = {
  _elem: null,
  _defaultLang: 'fr',
	_currentLang: 'fr',
  _cookies: {},
  _store: null,
  _items: document.querySelector('#nbItems'),
  setElem: function (elem) {
    this._elem = elem;
  },
  getIndex: function (arr, key, val, strict) {
    // !!! comparaison sur Ã©galitÃ© stricte ===
    if (strict !== undefined || strict === true) {
      val = String(val);
    }
    val = String(val);
    return arr.map(function (e) { return e[key]; }).indexOf(val);
  },
  _findIndex: function (array, key, val) {
    let index = (e) => e[key] == val;
    return array.findIndex(index);
  },
  currency: function () {
    // change de monnaie		
    let parent = this._elem.parentNode;
    let next = this._elem.getAttribute('data-currency');

    // Modifier le cookie
    let expires = new Date();
    expires.setFullYear(expires.getFullYear() + 1);
    expires = expires.toUTCString();
    document.cookie = 'currency=' + next + '; expires="' + expires + '; path=/; domain=dev.kutvek.com; secure';
    // On indique la currency dans le panier
    cart.setCurrency(next);
    // Pour le moment un simple reload de la page
    document.location.reload();
  },
  thumbsSlide: function () {
    var container = document.querySelector('.p-gallery > p');
    var translate = this._elem.getAttribute('data-translate');
    var thumb_active = document.querySelector('img.visual_thumb.active');
    thumb_active.classList.remove('active');
    this._elem.classList.add('active');
    container.style.transform = 'translateX(' + translate + '%)';
  },
  getParameterByName: function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  },
  getUrlParams: function (prop, url) {
    if (!url) url = window.location.href;
    let params = {},
      search = decodeURIComponent(url.slice(url.indexOf('?') + 1)),
      definitions = search.split('&');

    definitions.forEach(function (val) {
      let parts = val.split('=', 2);
      params[parts[0]] = parts[1];
    });

    return (prop && prop in params) ? params[prop] : params;
  },
  triggerEvent: function (el, type) {
    if ('createEvent' in document) {
      // modern browsers, IE9+
      var ev = document.createEvent('Event');
      ev.initEvent(type, false, true);
      el.dispatchEvent(ev);
    } else {
      // IE 8
      var e = document.createEventObject();
      e.eventType = type;
      el.fireEvent('on' + e.eventType, e);
    }
  },
  handleFile: function () {
    //console.log(this._elem);
  },
  preUpload: function () {
    var preview = this._elem.parentNode.parentNode.querySelector('.preload');
    var file = this._elem.files[0];
    /* var progress = document.createElement('progress');
    progress.setAttribute('min', 0);
    progress.setAttribute('max', 100);
    progress.value = 0;
    progress.textContent = '0% complete';
    preview.appendChild(progress); */

    // div englobant visuel et actions
    var div = document.createElement('div');
    // les actions
    var actions = document.createElement('div');
    actions.className = 'actions';
    // action delete
    var del = document.createElement('a');
    del.setAttribute('data-ctrl', 'app.clearPreload');
    del.setAttribute('data-input', this._elem.id);
    del.className = 'click';
    del.href = '#';

    var span = document.createElement('span');
    span.className = 'material-icons';
    span.textContent = 'delete';

    var see = del.cloneNode(true);
    see.setAttribute('data-ctrl', 'app.imgZoom');
    see.setAttribute('data-modal', 'imgZoom');
    var spanSee = span.cloneNode(true);
    spanSee.textContent = 'visibility';


    del.appendChild(span);
    see.appendChild(spanSee);


    actions.appendChild(see);
    actions.appendChild(del);

    // visuel
    var img = document.createElement("img");
    img.classList.add("obj");
    img.file = file;

    div.appendChild(img);
    div.appendChild(actions);
    preview.appendChild(div);

    var reader = new FileReader();
    reader.onload = (function (aImg) { return function (e) { aImg.src = e.target.result; }; })(img);
    reader.readAsDataURL(file);
    //console.log(file);
    // on disable l'input file
    this._elem.disabled = true;
  },
  clearPreload: function () {
    //console.log('clear thumbnail')
    var id = this._elem.getAttribute('data-input');
    var input = document.getElementById(id);
    var parent = this._elem.parentNode.parentNode;
    input.value = '';
    parent.parentNode.removeChild(parent);
    // On rÃ©ctive l'input
    input.disabled = false;

  },
  imgZoom: function () {

    var id = this._elem.getAttribute('data-input');
    var input = document.getElementById(id);
    var parent = this._elem.parentNode.parentNode;
    var img = parent.querySelector('img');
    var clone = img.cloneNode();
    // On ouvre la modale
    var modal = document.getElementById(this._elem.getAttribute('data-modal'));
    modal.querySelector('div.modal-content').appendChild(clone);
    //console.log(modal);
    modal.classList.toggle('visible');
  },
  thumbnail: function () {
    //var preview = files.parentNode.querySelector('.preview');

    var file = elem.files[0];
    if (elem.hasAttribute('data-preview'))
      var preview = document.getElementById(elem.getAttribute('data-preview'));
    else
      var preview = elem.parentNode.querySelector('.preview');
    var imageType = /^image\//;

    if (!imageType.test(file.type)) {
      return;
    }
    // On vÃ©rifie qu'il n'y ai pas dÃ©jÃ  un visuel;
    var exist = preview.querySelector('img[data-file="' + elem.id + '"]');
    if (exist)
      var img = exist;
    else
      var img = document.createElement("img");
    img.classList.add("obj");
    img.setAttribute('data-file', elem.id);
    img.file = file;
    preview.appendChild(img); // En admettant que "preview" est l'Ã©lÃ©ment div qui contiendra le contenu affichÃ©.
    var reader = new FileReader();
    reader.onload = (function (aImg) { return function (e) { aImg.src = e.target.result; }; })(img);
    reader.readAsDataURL(file);
  },
  modal: function () {
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
  appendFile: function () {
    var file = this._elem.files[0];
    // Affichage 
    // la preview 
    let preview = document.getElementById(this._elem.getAttribute('data-preview'));
    thumb(file, preview);
  },
  sidebar: function () {
    if (!this._elem.classList.contains('container'))
      document.body.classList.toggle('with-sidebar');
    else
      if (document.body.classList.contains('with-sidebar'))
        document.body.classList.remove('with-sidebar');
  },
  cloneTo: function () {
    var container = document.getElementById(this._elem.getAttribute('data-container'));
    var tpl = document.getElementById(this._elem.getAttribute('data-tpl'));
    var clone = document.importNode(tpl.content, true);
    container.appendChild(clone);
  },
  slide: function () {
    const container = this._elem.parentNode.querySelector('.slide-container');
    let translated = parseFloat(container.getAttribute('data-translate'));
    let counter = parseInt(container.getAttribute('data-counter'), 10);
    let nbItems = parseInt(container.getAttribute('data-item'), 10);
    if (this._elem.classList.contains("left")) {
      if (counter == nbItems) return;
      let translate = translated - 36;
      container.setAttribute('data-translate', translate);
      container.setAttribute('data-counter', counter + 1);
      container.style.transform = 'translateX(-' + translate + 'rem)';
    }
    else {
      if (counter == 0) return;
      let translate = translated + 36;
      container.setAttribute('data-translate', translate);
      container.setAttribute('data-counter', counter - 1);
      container.style.transform = 'translateX(-' + translate + 'rem)';
    }
  },
  menu: function () {
    const menu = document.getElementById(this._elem.getAttribute('data-target'));

    document.body.classList.toggle('opened');
		this._elem.classList.toggle('opened');
		if(this._elem.classList.contains('opened'))
		{
			this._elem.querySelector('span').textContent = 'close';
		}	
		else 	this._elem.querySelector('span').textContent = 'menu';
  },
  i18n: function () {
    const lang = document.documentElement.lang;
    if (this._defaultLang == lang) {
      this.rewrite();
      return;
    }
    //const trads = document.querySelectorAll('[data-trad]');
    ////console.log(trads);
    //var trads = JSON.parse(localStorage.getItem('i18n_'+lang));
    var trads = null;
    // Pas de trad associée en localStorage
    if (trads === null) {
      const uri = 'https://dev.kutvek.com/api/i18n/:lang';
      const url = uri.replace(':lang', lang);
      fetch(url, {
        method: 'GET'
      }).then((res) => {
        return res.json()
      }).then((json) => {
        if (json.success) {
          localStorage.setItem('i18n_' + lang, JSON.stringify(json.data));
          trads = json.data;
          //console.log('trad fetched');
          this.translate(trads);
        }
      });
    } else {
      this.translate(trads);
    }
  },
  translate: function (trads) {
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
                ////console.log(label);
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
                  ////console.log(label);
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
      var user = JSON.parse(localStorage.getItem('user'));
      let lang = document.documentElement.lang;
      if (user !== null && user.country == 16 && (lang == 'fr' || lang == 'nl')) {
        imgs.forEach((i) => {

          i.src = i.getAttribute('data-rw').replace(':i18n', lang + '_BE');
        });
      }
    }
  },
  widget: function () {
    const id = this._elem.getAttribute('data-widget');

    document.querySelectorAll('ul.opt-list').forEach(widget => {
      if (widget.id == id) widget.classList.toggle('widget-hide'); else widget.classList.add('widget-hide');
    })

  },
  widgetOpt: function () {
    let selected = this._elem;
    let parent = selected.parentNode;
    let id = parent.getAttribute('data-input');
    document.querySelector('label[for="' + id + '"]').innerHTML = selected.innerHTML;
    document.getElementById(id).value = selected.getAttribute('data-value');
    parent.classList.toggle('widget-hide');
  },
  rewrite: function () {
    const imgs = document.querySelectorAll('img.rw');
    ////console.log(imgs);
    if (imgs.length > 0) {
      var user = JSON.parse(localStorage.getItem('user'));
      let lang = document.documentElement.lang;
      if (user !== null && user.country == 16 && (lang == 'fr' || lang == 'nl')) {
        imgs.forEach((i) => {
          i.src = i.getAttribute('data-rw').replace(':i18n', lang + '_BE');
        });
      }
    }
  },
  formdataToArray: function (formData) {
    let array = [];
    formData.forEach((value, key) => {
      array.push(value);
    })
    return array;
  },
  formdataToJson: function (formData) {
    var object = {};
    formData.forEach((value, key) => {
      // si la clé est un tableau
      const words = key.split('[');
      // si words a plus d'un index
      if (words.length > 1) {
        // suprimer le ] de l'index 1
        if (!Reflect.has(object, words[0])) {
          object[words[0]] = {};
          object[words[0]][words[1].slice(0, -1)] = value;
          return;
        } else {
          object[words[0]][words[1].slice(0, -1)] = value;
          return;
        }
      } else {
        // Reflect.has in favor of: object.hasOwnProperty(key)
        if (!Reflect.has(object, key)) {
          object[key] = value;
          return;
        }
        if (!Array.isArray(object[key])) {
          object[key] = [object[key]];
        }
        object[key].push(value);
      }
    });
    var json = JSON.stringify(object);
    return json;
  },
  updateCartIcon: function (nb_items) {
    if (this._items) this._items.textContent = nb_items;
  },
  
  init: async function(){
		let cart = JSON.parse(localStorage.getItem('cart'));
    let currency = localStorage.getItem('currency');
    if(currency === null) {
        // var searchParams = new URLSearchParams(window.location.search);
        // let debug = searchParams.get('debug');
        // if(debug !== null) {
          //  document.querySelector('div[data-modal="countries"]').click();
        // }
        const formData = new FormData();
        formData.append('country', 'FR');
        formData.append('currency', 'EUR');
        const response = await fetch('https://www.kutvek-kitgraphik.com/api/customers/country-currency', {
            method: 'POST',
            body: formData,
            credentials: 'include'
          }
        );
        if (response.ok && response.status == 200) {
          localStorage.setItem('currency', '1');
        } else {
          document.querySelector('div[data-modal="countries"]').click();
        } 
    } 

		////console.log('Cart init...');
   	if(cart === null) {			
			this.updateCartIcon(0);
			// this.create();
		} else {		
			this.updateCartIcon(cart.items);							
		}	
		if(this._user === null) {
			return;
		} else {	
			let div = document.querySelector('div.signin');
			if(div){div.classList.add('hide');}				
		}
	},
  closeAllSections: function() {
		if (this._elem.checked) {
			const depth = parseInt(this._elem.getAttribute('data-depth'), 10);

			const mainNav = document.getElementById('main-nav');
			const allCheckbox = mainNav.querySelectorAll('input[type="checkbox"]');

			allCheckbox.forEach(checkbox => {
				currentDepth = parseInt(checkbox.getAttribute('data-depth'), 10);
				if (currentDepth >= depth) {
					checkbox.checked = false;
				}
			})
			this._elem.checked = true;
		}
	},
};

const gallery = {
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  slide: function () {
    var container = document.querySelector('.gallery > .slider');
    //console.log(container);

    var translate = this._elem.getAttribute('data-translate');
    var thumb_active = document.querySelector('img.visual_thumb.active');
    thumb_active.classList.remove('active');
    this._elem.classList.add('active');
    container.style.transform = 'translateX(' + translate + '%)';
  },
  thumbnail: function () {
    const container_id = this._elem.parentNode.getAttribute('data-gallery');
    const container = document.getElementById(container_id).querySelector('.slider');
       
    var translate = this._elem.getAttribute('data-translate');
    var thumb_active = this._elem.parentNode.querySelector('img.active');
    if(thumb_active)
      thumb_active.classList.remove('active');
    this._elem.classList.add('active');
    container.style.transform = 'translateX(' + translate + '%)';
  },
  attach: function () {
    let link = this._elem;
    let unlink = false;
    let icon = link.querySelector('.icon');

    if (icon.classList.contains('fa-unlink')) {
      icon.classList.replace('fa-unlink', 'fa-link');
    }
    else {
      icon.classList.replace('fa-link', 'fa-unlink');
      unlink = true;

    }

    fetch(link.getAttribute('href'), {
      method: ('PUT'),
      body: JSON.stringify({ unlink: unlink })
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        //if(json.success) this._elem.classList.toggle('associate');         
      });
  }
  
};

const carousel = {
  _ev: null,
  _elem: null,
  _carousel:null,
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  slide: function() {
    this.slideDiv();
    this.stopAnimation();
  },
  slideDiv: function()
  {
    var container = document.querySelector('.gallery > .slider');
    var translate = this._elem.getAttribute('data-translate');
    var thumb_active = document.querySelector('div.visual_thumb.active');
    thumb_active.classList.remove('active');
    this._elem.classList.add('active'); 
    container.style.transform = 'translateX(' + translate + '%)';   
  },
  playAnimation: function(c) {
  this._carousel = c;
  const mainSlider = this._carousel.querySelector('div.slider');
  if (!mainSlider) return;
  
  const images = mainSlider.querySelectorAll('img');
  const cursors = this._carousel.querySelectorAll('div.cursor');
  ////console.log(cursors);
  cursors.forEach(cur => {
    cur.addEventListener('click', e => {
        this.slide();
      });
  });
  const max = images.length - 1;
  let slide = 0;
    
  _interval = setInterval(()=>{
    slide++;
    if (slide > max) { 
      slide = 0; 
      mainSlider.style.transition = "0s"
    } else { 
      mainSlider.style.transition = ".7s" 
    }
    
    this.setElem(cursors[slide]);
    this.slideDiv();
  }, 5000);
  
  },
  stopAnimation: function() {
  if (_interval != null) { clearInterval(_interval); } 
  }

}

/**
 * { constant_description }
 *
 * @type       {<type>}
 * _cards : nombre total de cartes
 * 
 * _cardsOnLeft|Right : cartes invisibles sur les côtés
 */
const slider = {
  _elem: null,
  _ev: null,
  _slider: null,
  _container: null,
  _translated: 0,
  _cards:0,
  _cardsOnLeft: 0,
  _cardsOnRight:0,
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  slide: function(){
    let side = this._elem.getAttribute('data-side');
    this._slider = this._elem.parentNode;
    this._container = this._slider.querySelector('.container');

    // calculer les cartes cachées   
    this._cardsOnRight = parseInt(this._container.getAttribute('data-rcards'),10);    
    this._cardsOnLeft = parseInt(this._container.getAttribute('data-lcards'),10);

    // memoriser le translate
    this._translated = parseInt(this._container.getAttribute('data-translate'),10); 

    this[side]();    
  },
  left: function()
  {

    if(parseInt(this._container.getAttribute('data-lcards'),10) < 1) return;
    this._cardsOnLeft = this._cardsOnLeft - 1;
    this._cardsOnRight = this._cardsOnRight + 1;
    this._container.setAttribute('data-lcards', this._cardsOnLeft);
    this._container.setAttribute('data-rcards', this._cardsOnRight);
    
    this._translated = this._translated + 352;
    this._container.setAttribute('data-translate', this._translated);

    this._container.style.transform = `translateX(${this._translated}px)`;
    
  },
  right: function(){

    if( parseInt(this._container.getAttribute('data-rcards'),10) < 1) return;
    this._cardsOnLeft = this._cardsOnLeft + 1;
    this._cardsOnRight = this._cardsOnRight - 1;
    this._container.setAttribute('data-lcards', this._cardsOnLeft);
    this._container.setAttribute('data-rcards', this._cardsOnRight);

    // ici ça coince
    this._translated = this._translated - 352;
    this._container.setAttribute('data-translate', this._translated);
    
    this._container.style.transform = `translateX(${this._translated}px)`;
    
  }
}

// polyfills 
// This will let you use the .remove() function later on
if (!('remove' in Element.prototype)) {
  Element.prototype.remove = function () {
    if (this.parentNode) {
      this.parentNode.removeChild(this);
    }
  };
}

if (window.NodeList && !NodeList.prototype.forEach) {
  NodeList.prototype.forEach = function (callback, thisArg) {
    thisArg = thisArg || window;
    for (var i = 0; i < this.length; i++) {
      callback.call(thisArg, this[i], i, this);
    }
  };
}

function array_sum(array) {
  // eslint-disable-line camelcase
  // discuss at: https://locutus.io/php/array_sum/
  // original by: Kevin van Zonneveld (https://kvz.io)
  // bugfixed by: Nate
  // bugfixed by: Gilbert
  // improved by: David Pilia (https://www.beteck.it/)
  // improved by: Brett Zamir (https://brett-zamir.me)
  //   example 1: array_sum([4, 9, 182.6])
  //   returns 1: 195.6
  //   example 2: var $total = []
  //   example 2: var $index = 0.1
  //   example 2: for (var $y = 0; $y < 12; $y++){ $total[$y] = $y + $index }
  //   example 2: array_sum($total)
  //   returns 2: 67.2
  let key
  let sum = 0
  // input sanitation
  if (typeof array !== 'object') {
    return null
  }
  for (key in array) {
    if (!isNaN(parseFloat(array[key]))) {
      sum += parseFloat(array[key])
    }
  }
  return sum
};

function monetary(number, l10n, currency, maximumFractionDigits = 2) {
  // ex i18n : 'de_DE'on remplace le "_" par "-"
  // ex currency : 'EUR'
  if (currency == '€') currency = 'EUR';
  if (currency == '£') currency = 'GBP';
  if (currency == '$') currency = 'USD';
  if (currency == '$ CAN') currency = 'CAD';
  if (l10n == 'fr') l10n = 'fr_FR';
  return new Intl.NumberFormat(l10n.replace('_', '-'), { style: 'currency', currency: currency, maximumFractionDigits: maximumFractionDigits }).format(number);
}

function percent(number, l10n, currency) {
  if (currency == '€') currency = 'EUR';
  if (currency == '£') currency = 'GBP';
  if (currency == '$') currency = 'USD';
  if (currency == '$ CAN') currency = 'CAD';
  return new Intl.NumberFormat(l10n.replace('_', '-'), {
    style: 'percent', currency: currency, minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(number);
}

function resizeIframe(obj) {
  obj.style.height = 0;
  obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
}

function deactivateSelect(select) {
  if (!select.classList.contains('active')) return;

  var optList = select.querySelector('.optList');

  optList.classList.add('hidden');
  select.classList.remove('active');
}

function activeSelect(select, selectList) {
  if (select.classList.contains('active')) return;
  selectList.forEach(deactivateSelect);
  select.classList.add('active');
};

function toggleOptList(select, show) {
  var optList = select.querySelector('.optList');
  optList.classList.toggle('hidden');
}

function highlightOption(select, option) {
  var optionList = select.querySelectorAll('.option');
  optionList.forEach(function (other) {
    other.classList.remove('highlight');
  });
  option.classList.add('highlight');
};

function updateValue(select, index) {
  var nativeWidget = select.nextElementSibling;
  var value = select.querySelector('.value');
  var optionList = select.querySelectorAll('.option');
  nativeWidget.selectedIndex = index;
  value.innerHTML = optionList[index].innerHTML;
  value.style.cssText = optionList[index].style.cssText;
  highlightOption(select, optionList[index]);
};

function getIndex(select) {
  var nativeWidget = select.nextElementSibling;
  return nativeWidget.selectedIndex;
};

const runModule = async function(params, elem, event) 
{ 
    let p = params.split(".");
    let _module = p[0];
    let _action = p[1];
    let _asset;
    // test fetch asset
    let response = await fetch(`/asset/js/modules/${_module}.js`, {method: 'GET'});
    if(response.ok){
        _asset = await response.text();

    }  
    else {_asset = `/js/modules/${_module}.js`;} 
    
    const Module = await import(`..${_asset}`);    
    Module.default.setEvent(event);
    if(elem !== null && elem !== undefined)
    Module.default.setElem(elem);
    Module.default[_action]();
       
};

function run(params, elem, event) {
  const is_module = elem.hasAttribute('data-module');

  if(is_module) return runModule(params, elem, event);
  
  let p = params.split(".");
  let ctrl = p[0];
  let action = p[1];
  switch (ctrl) {
    case 'cart':
      cart.setEvent(event);
      if (elem !== null && elem !== undefined)
        cart.setElem(elem);
      cart[action]();
      break;
    case 'chronoRelay':
      chronoRelay.setEvent(event);
      if(elem !== null && elem !== undefined)
        chronoRelay.setElem(elem); 
      chronoRelay[action]();    	
      break;
    case 'itemCart':

      if (elem !== null && elem !== undefined)
        itemCart.setElem(elem);
      itemCart[action]();
      break;
    case 'item':
      item.setEvent(event);
      if (elem !== null && elem !== undefined)
        item.setElem(elem);
      item[action]();
      break;
    case 'slider':
      slider.setEvent(event);
      if (elem !== null && elem !== undefined)
        slider.setElem(elem);
      slider[action]();
      break;
    case 'app':
      if (elem !== null && elem !== undefined)
        app.setElem(elem);
      app[action]();
      break;
    case 'order':
      if (elem !== null && elem !== undefined)
        order.setElem(elem);
      order[action]();
      break;
    case 'orderItem':
      if (elem !== null && elem !== undefined)
        orderItem.setElem(elem);
      orderItem[action]();
      break;
    case 'task':
      if (elem !== null && elem !== undefined)
        task.setElem(elem);
      task[action]();
      break;
    case 'purchase':
      if (elem !== null && elem !== undefined)
        purchase.setElem(elem);
      purchase[action]();
      break;
    case 'shipping':
      if (elem !== null && elem !== undefined)
        shipping.setElem(elem);
      shipping[action]();
      break;
    case 'user':
      user.setEvent(event);
      if(elem !== null && elem !== undefined)
        user.setElem(elem); 
      user[action]();    	
    break;
    case 'carrousel':
      carrousel.setEvent(event)
      if (elem !== null && elem !== undefined)
        carrousel.setElem(elem);
      carrousel[action]();
      break;
    case 'gallery':
      gallery.setEvent(event)
      if (elem !== null && elem !== undefined)
        gallery.setElem(elem);
      gallery[action]();
      break;
    case 'delivery':
      delivery.setEvent(event)
      if (elem !== null && elem !== undefined)
        delivery.setElem(elem);
      delivery[action]();
      break;
    case 'customer':
      customer.setEvent(event)
      if (elem !== null && elem !== undefined)
        customer.setElem(elem);
      customer[action]();
      break;
    case 'vehicles':
      vehicle.setEvent(event)
      if (elem !== null && elem !== undefined)
        vehicle.setElem(elem);
      vehicle[action]();
      break;
    case 'model':
      if (elem !== null && elem !== undefined)
        model.setElem(elem);
      model[action]();
      break;
    case 'vehicle':
      vehicle.setEvent(event)
      if (elem !== null && elem !== undefined)
        vehicle.setElem(elem);
      vehicle[action]();
      break;
    case 'category':
      if (elem !== null && elem !== undefined)
        category.setElem(elem);
      category[action]();
      break;
    case 'accessory':
      if (elem !== null && elem !== undefined)
        accessory.setElem(elem);
      accessory[action]();
      break;
    case 'menu':
      if (elem !== null && elem !== undefined)
        menu.setElem(elem);
      menu[action]();
      break;
    case 'universe':
      if (elem !== null && elem !== undefined)
        universe.setElem(elem);
      universe[action]();
      break;
    case 'brand':
      if (elem !== null && elem !== undefined)
        brand.setElem(elem);
      brand[action]();
      break;
    case 'option':
      option.setEvent(event)
      if (elem !== null && elem !== undefined)
        option.setElem(elem);
      option[action]();
      break;
    case 'product':
      product.setEvent(event)
      if (elem !== null && elem !== undefined)
        product.setElem(elem);
      product[action]();
      break;
    case 'seatCover':
      seatCover.setEvent(event)
      if (elem !== null && elem !== undefined)
        seatCover.setElem(elem);
      seatCover[action]();
      break;
    case 'locale':
      locale.setEvent(event)
      if (elem !== null && elem !== undefined)
        locale.setElem(elem);
      locale[action]();
      break;
    default:
      //console.log('Sorry, we are out of ' + ctrl + '.');
  }
};

function _submit(event) {

  var target = event.target;

  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;

  // on ne soumet que des formulaires avec un data-ctrl via js
  if (!el || el.nodeName.toLowerCase() !== 'form') return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);

}

function _click(event) {
  var target = event.target;

  // doofindr
  if (target.classList.contains('df-card__main')) {
    event.preventDefault();
    //console.log('dfidr');
  }
  // aria-expanded
  if (target.hasAttribute('aria-expanded')) {
    var expanded = target.getAttribute('aria-expanded')
    if (expanded === 'false') {
      target.setAttribute('aria-expanded', 'true')
    } else {
      target.setAttribute('aria-expanded', 'false')
    }
    var cid = target.getAttribute('aria-controls');
    var content = document.getElementById(cid);

    if (content) {
      content.getAttribute('hidden')
      if (typeof content.getAttribute('hidden') === 'string') {
        content.removeAttribute('hidden')
      } else {
        content.setAttribute('hidden', 'hidden')
      }
    }
    return;
  }

  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;

  const click = el.classList.contains('click');
  if (!click) return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

function _change(event) {
  const target = event.target;

  //let acces = target.hasAttribute('data-event') &&  target.getAttribute('data-event') === 'change' ? true : target.parentNode.hasAttribute('data-event') &&  target.parentNode.getAttribute('data-event') === 'change'? true : false;

  //if(!acces) return;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;

  const onchange = el.classList.contains('onchange');
  if (!onchange) return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

function _keyup(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;

  const onkeyup = el.classList.contains('onkeyup');
  if (!onkeyup) return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

function _focus(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;

  const onfocus = el.classList.contains('onfocus');
  if (!onfocus) return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

function _focusOut(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;

  const focusout = el.classList.contains('focusout');
  if (!focusout) return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

function _drop(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;
  const ondrop = el.classList.contains('ondrop');
  if (!ondrop) return;
  event.stopPropagation();
  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

function _dragenter(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;
  const dragenter = el.classList.contains('dragenter');
  if (!dragenter) return;
  event.stopPropagation();
  event.preventDefault();
}

function _dragover(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;
  const dragover = el.classList.contains('dragover');
  if (!dragover) return;
  event.stopPropagation();
  event.preventDefault();
}

function _mouseover(event) {
  const target = event.target;
  const el = target.hasAttribute('data-ctrl') ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if (!el) return;

  const onmouseover = el.classList.contains('onmouseover');
  if (!onmouseover) return;

  event.preventDefault();
  var params = el.getAttribute('data-ctrl');
  run(params, el, event);
}

var updateSize = function (file) {
  var counterBytes = document.getElementById("fileSize");
  var counterFiles = document.getElementById("fileNum");

  var nBytes = parseInt(counterBytes.getAttribute('data-bytes'), 10);
  var nFiles = parseInt(counterFiles.getAttribute('data-files'), 10);
  nBytes += file.size;
  nFiles++;
  var sOutput = nBytes + " bytes";

  // partie de code facultative pour l'approximation des multiples
  /* for (var aMultiples = ["KiB", "MiB", "GiB", "TiB", "PiB", "EiB", "ZiB", "YiB"], nMultiple = 0, nApprox = nBytes / 1024; nApprox > 1; nApprox /= 1024, nMultiple++) {
   sOutput = nApprox.toFixed(3) + " " + aMultiples[nMultiple] + " (" + nBytes + " bytes)";
  } */
  for (var aMultiples = ["Ko", "Mo", "Go"], nMultiple = 0, nApprox = nBytes / 1024; nApprox > 1; nApprox /= 1024, nMultiple++) {
    sOutput = nApprox.toFixed(3) + " " + aMultiples[nMultiple] + " / ";
  }
  counterFiles.setAttribute('data-files', nFiles);
  counterFiles.textContent = nFiles + 'Fichier(s)';
  counterBytes.setAttribute('data-bytes', nBytes);
  counterBytes.textContent = sOutput;
}

var uploadBlob = function (params, success, error) {
  var xhr = new XMLHttpRequest();
  var formData = new FormData();
  formData.append('myFile', params.blobOrFile);
  formData.append('action', 'upload');
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status >= 200 && xhr.status < 304) {
        var resp = xhr.response;
        success(resp);
      } else if (xhr.status === 0) {
        error(xhr.status + ' [Network failed]')
      } else { error(xhr.statusText) }
    }
  }
  //req.onerror = function() { error( 'err') }
  xhr.ontimeout = function () {
    console.error("The request for upload.php timed out.");
  };
  xhr.open('POST', params.url, true);
  //req.responseType = params.type || 'json';  		
  xhr.onload = function (e) { };
  xhr.upload.onprogress = function (e) {
    if (e.lengthComputable) {
      params.progressBar.value = (e.loaded / e.total) * 100;
      params.progressBar.textContent = params.progressBar.value; // Fallback for unsupported browsers.
    }
  };
  //xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
  xhr.send(formData);
}

var handleFiles = function (files, dropbox) {
  //console.log(dropbox) // le form.dropbox
  var fileList = files;
  var preview = dropbox.parentNode.parentNode.querySelector('.preview');
  var url = dropbox.action;
  for (var i = 0; i < files.length; i++) {
    (function (item) {
      var progress = document.createElement('progress');
      progress.setAttribute('min', 0);
      progress.setAttribute('max', 100);
      progress.value = 0;
      progress.textContent = '0% complete';
      var file = item;
      var imageType = /^image\//;
      if (imageType.test(file.type)) {
        preview.appendChild(progress);
        uploadBlob({ blobOrFile: file, progressBar: progress, url: url }, function (resp) {
          //updateSize(file);
          var img = document.createElement("img");
          img.classList.add("obj");
          img.file = file;

          var reader = new FileReader();
          reader.onloadend = function () {
            if (reader.readyState == 2) {
              preview.replaceChild(img, progress);
              var frameId = 'iframe-mockups';
              var height = document.body.scrollHeight + 'px';
              var msg = { action: 'setIframeHeight', id: frameId, h: height };
              window.parent.postMessage(msg, 'https://www.kutvek.com');
            }

          }
          reader.onload = (
            function (aImg) { return function (e) { aImg.src = e.target.result; }; }
          )(img);
          reader.readAsDataURL(file);
        }, function (e) { }
        );
      }

    })(files[i]);
  };

  //return;
}

var dragenter = function (e) {
  e.stopPropagation();
  e.preventDefault();
};

var dragover = function (e) {
  e.stopPropagation();
  e.preventDefault();
};

var thumb = function (file, preview) {
  var span = document.createElement('span');
  span.file = file;
  span.classList.add("obj");

  // Si fichier type image	
  const imageType = /^image\//;
  if (imageType.test(file.type)) {
    var img = document.createElement("img");
    img.file = file;
    var reader = new FileReader();
    reader.onload = (function (aImg) { return function (e) { aImg.src = e.target.result; }; })(img);
    reader.readAsDataURL(file);
    span.appendChild(img);
  } else {
    const fileParts = file.type.split('/');
    span.classList.add(fileParts[1]);
  }
  preview.appendChild(span);
}

var drop = function (e) {
  var target = e.currentTarget;
  e.stopPropagation();
  e.preventDefault();
  var dt = e.dataTransfer;
  var files = dt.files;
  handleFiles(files, target);
}

document.addEventListener('submit', _submit, false);
document.addEventListener('click', _click, false);
document.addEventListener('change', _change, false);
document.addEventListener('keyup', _keyup, false);
document.addEventListener('focus', _focus, false);
document.addEventListener('focusout', _focusOut, false);
document.addEventListener("mouseover", _mouseover, false);
document.addEventListener('dragenter', _dragenter, false);
document.addEventListener('dragover', _dragover, false);
document.addEventListener('drop', _drop, false);

window.addEventListener('message', event => {
  // IMPORTANT: check the origin of the data! 
  if (event.origin.startsWith('https://www.kutvek.com')) {
    // The data was sent from your site.
    // Data sent with postMessage is stored in event.data:
    var frameId = event.data.id;
    var height = document.body.scrollHeight + 'px';
    var msg = { action: 'setIframeHeight', id: frameId, h: height };
    event.source.postMessage(msg, event.origin);
  } else {
    return;
  }
});

window.addEventListener("DOMContentLoaded", function (e) {
  app.i18n();
  const obflinks = document.querySelectorAll('.obflink');
    for (let i=0, n=obflinks.length; i < n; ++i){
        obflinks[i].addEventListener('click', function(e){
            if(obflinks[i].hasAttribute('data-ctrl'))return;
            // un check futur pour savoir si c'est un lien _blank
            let link = decodeURIComponent(window.atob(obflinks[i].getAttribute('data-obf')));            
            if (obflinks[i].hasAttribute('id') && obflinks[i].id == 'cart-uri') {
              const cart = JSON.parse(localStorage.getItem('cart')).id;
              const url = new URL(link);
              url.searchParams.set('cart', cart);
              link = url.href;
            }        
            //window.open(link); 
            window.location.assign(link);
        });
    }
    const c = document.getElementById('carousel');
    if(c)carousel.playAnimation(c);
    if (document.querySelector('#dropbox')) {
    var dropbox = document.querySelectorAll('.dropbox');
    dropbox.forEach(function (box) {
      box.addEventListener("dragenter", dragenter, false);
      box.addEventListener("dragover", dragover, false);
      box.addEventListener("drop", drop, false);
    });
    }
  app.init();
  const popinInfo = document.querySelector('#promo-info');
    if(popinInfo){
        var popins = localStorage.getItem('popins');

        if(popins !== null) {
          popins = JSON.parse(popins);          
          const keys = Object.keys(popins);          
          const inArray = (element) => element === '#promo-info';
          const found = keys.findIndex(inArray);          
          if(found < 0 ) popinInfo.classList.toggle('visible');
        } else {
          popinInfo.classList.toggle('visible');
        }
    }

  let slides = document.querySelectorAll('.slides .container');
  if(slides.length > 0) {
    slides.forEach(slide => {
        let containerWidth = 0;
        let cards = slide.querySelectorAll('.card'); 
             
        cards.forEach(card => {
          containerWidth = containerWidth + (320 + parseInt(window.getComputedStyle(card).marginRight,10));          
        });
        slide.style.width = containerWidth + 'px';
        let hiddenCards = Math.round((containerWidth - slide.parentNode.offsetWidth) / 352);
        slide.setAttribute('data-rcards', hiddenCards);  

    });
  }
});