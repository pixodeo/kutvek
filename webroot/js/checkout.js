/**
 * Import dynamiques des modules (objets) js
 * Ici on part du principe qu'on a un export default dans chaque module
 * @param      {<type>}  params  object.action : category.test
 * @param      {<type>}  elem    The element
 * @param      {<type>}  event   The event
 */
const _defaultLang = 'fr';
const run = async function(params, elem, event) 
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
/**
 * Customer Infos
 *
 * @return     {(json|boolean)}
 */
const checkoutInfo = async function() {   
  let _hasUser = await checkUser();
  if(! _hasUser) {
    console.log('no user');
      return false;
  }
  let xsrfToken = localStorage.getItem('xsrfToken');    
  // inclure token csrf
  const headers = new Headers();
  headers.append('x-xsrf-token', xsrfToken);
  const res = await fetch(
    '/api/customers/checkout-info',
    { method: 'GET', mode: 'cors', credentials: 'include', headers}
  );
  
  if(!res.ok) return false;
  const json = await res.json();
  return json;
};

const checkUser = async function(){
  let response = await fetch(`/asset/js/modules/auth.js`, {method: 'GET'});
    let _asset = response.ok ? await response.text() : '/js/modules/auth.js';     
    //_asset = _asset.replace('/js', '');
    let  _module = await import(`..${_asset}`);
    let success = await _module.default.check();
    return success;  
};

const checkOut = async function(elem, event, logged) 
{   
  let _asset;
  // test fetch asset
  let response = await fetch(`/asset/js/modules/cart.js`, {method: 'GET'});
  if(response.ok){
      _asset = await response.text();
  }  
  else {_asset = `/js/modules/cart.js`;}     
  let Module = await import(`..${_asset}`);    
  Module.default.setEvent(event);
  if(elem !== null && elem !== undefined)
    Module.default.setElem(elem);
  Module.default._connected = logged;
  Module.default.checkout();       
};

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

function input_gift_card(input)
{
    var format_and_pos = function(char, backspace)
    {
        var start = 0;
        var end = 0;
        var pos = 0;
        var separator = " ";
        var value = input.value;

        if (char !== false)
        {
            start = input.selectionStart;
            end = input.selectionEnd;

            if (backspace && start > 0) // handle backspace onkeydown
            {
                start--;

                if (value[start] == separator)
                { start--; }
            }
            // To be able to replace the selection if there is one
            value = value.substring(0, start) + char + value.substring(end);

            pos = start + char.length; // caret position
        }

        var d = 0; // digit count
        var dd = 0; // total
        var gi = 0; // group index
        var newV = "";
        var groups = [3, 3, 3];

        for (var i = 0; i < value.length; i++)
        {
            if (/\D/.test(value[i]))
            {
                if (start > i)
                { pos--; }
            }
            else
            {
                if (d === groups[gi])
                {
                    newV += separator;
                    d = 0;
                    gi++;

                    if (start >= i)
                    { pos++; }
                }
                newV += value[i];
                d++;
                dd++;
            }
            if (d === groups[gi] && groups.length === gi + 1) // max length
            { break; }
        }
        input.value = newV;

        if (char !== false)
        { input.setSelectionRange(pos, pos); }
    };

    input.addEventListener('keypress', function(e)
    {
        var code = e.charCode || e.keyCode || e.which;

        // Check for tab and arrow keys (needed in Firefox)
        if (code !== 9 && (code < 37 || code > 40) &&
        // and CTRL+C / CTRL+V
        !(e.ctrlKey && (code === 99 || code === 118)))
        {
            e.preventDefault();

            var char = String.fromCharCode(code);

            // if the character is non-digit
            // OR
            // if the value already contains 15/16 digits and there is no selection
            // -> return false (the character is not inserted)

            if (/\D/.test(char) || (this.selectionStart === this.selectionEnd &&
            this.value.replace(/\D/g, '').length >=
            (/^\D*3[47]/.test(this.value) ? 9 : 9))) // 15 digits if Amex
            {
                return false;
            }
            format_and_pos(char);
        }
    });
    
    // backspace doesn't fire the keypress event
    input.addEventListener('keydown', function(e)
    {
        if (e.keyCode === 8 || e.keyCode === 46) // backspace or delete
        {
            e.preventDefault();
            format_and_pos('', this.selectionStart === this.selectionEnd);
        }
    });
    
    input.addEventListener('paste', function()
    {
        // A timeout is needed to get the new value pasted
        setTimeout(function(){ format_and_pos(''); }, 50);
    });
    
    input.addEventListener('blur', function()
    {
      // reformat onblur just in case (optional)
        format_and_pos(this, false);
    });
};

function input_credit_card(input)
{
    var format_and_pos = function(char, backspace)
    {
        var start = 0;
        var end = 0;
        var pos = 0;
        var separator = " ";
        var value = input.value;

        if (char !== false)
        {
            start = input.selectionStart;
            end = input.selectionEnd;

            if (backspace && start > 0) // handle backspace onkeydown
            {
                start--;

                if (value[start] == separator)
                { start--; }
            }
            // To be able to replace the selection if there is one
            value = value.substring(0, start) + char + value.substring(end);

            pos = start + char.length; // caret position
        }

        var d = 0; // digit count
        var dd = 0; // total
        var gi = 0; // group index
        var newV = "";
        var groups = /^\D*3[47]/.test(value) ? // check for American Express
        [4, 6, 5] : [4, 4, 4, 4];

        for (var i = 0; i < value.length; i++)
        {
            if (/\D/.test(value[i]))
            {
                if (start > i)
                { pos--; }
            }
            else
            {
                if (d === groups[gi])
                {
                    newV += separator;
                    d = 0;
                    gi++;

                    if (start >= i)
                    { pos++; }
                }
                newV += value[i];
                d++;
                dd++;
            }
            if (d === groups[gi] && groups.length === gi + 1) // max length
            { break; }
        }
        input.value = newV;

        if (char !== false)
        { input.setSelectionRange(pos, pos); }
    };

    input.addEventListener('keypress', function(e)
    {
        var code = e.charCode || e.keyCode || e.which;

        // Check for tab and arrow keys (needed in Firefox)
        if (code !== 9 && (code < 37 || code > 40) &&
        // and CTRL+C / CTRL+V
        !(e.ctrlKey && (code === 99 || code === 118)))
        {
            e.preventDefault();

            var char = String.fromCharCode(code);

            // if the character is non-digit
            // OR
            // if the value already contains 15/16 digits and there is no selection
            // -> return false (the character is not inserted)

            if (/\D/.test(char) || (this.selectionStart === this.selectionEnd &&
            this.value.replace(/\D/g, '').length >=
            (/^\D*3[47]/.test(this.value) ? 15 : 16))) // 15 digits if Amex
            {
                return false;
            }
            format_and_pos(char);
        }
    });
    
    // backspace doesn't fire the keypress event
    input.addEventListener('keydown', function(e)
    {
        if (e.keyCode === 8 || e.keyCode === 46) // backspace or delete
        {
            e.preventDefault();
            format_and_pos('', this.selectionStart === this.selectionEnd);
        }
    });
    
    input.addEventListener('paste', function()
    {
        // A timeout is needed to get the new value pasted
        setTimeout(function(){ format_and_pos(''); }, 50);
    });
    
    input.addEventListener('blur', function()
    {
      // reformat onblur just in case (optional)
        format_and_pos(this, false);
    });
};

function _click(event)
{   
    var target = event.target; 

  // doofindr
  if(target.classList.contains('df-card__main'))
  {
    event.preventDefault();
    console.log('dfidr');
  }
  // aria-expanded
  if(target.hasAttribute('aria-expanded'))
  {
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

  const el = target.hasAttribute('data-ctrl')  ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if(!el) return;

  const click = el.classList.contains('click');
  if(!click) return;  
  event.preventDefault();
  var params = el.getAttribute('data-ctrl');  
  run(params, el, event);   
}

function _change(event)
{
  const target = event.target;  
  const el = target.hasAttribute('data-ctrl')  ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;
  if(!el) return; 
  const onchange = el.classList.contains('onchange');
  if(!onchange) return;
  event.preventDefault();
  var params = el.getAttribute('data-ctrl');    
  run(params, el, event);   
}
/**
 * Event submit listener
 * Only when submit an html form without .unsubmit class
 * @param      {<type>}  event   The event
 */
function _submit(event) {
    var target = event.target; 
    const el = target.hasAttribute('data-ctrl')  ? target : target.parentNode.hasAttribute('data-ctrl') ? target.parentNode : false;    
    if(!el || el.nodeName.toLowerCase() !== 'form' || el.classList.contains('unsubmit')) return;  
    event.preventDefault();    
    var params = el.getAttribute('data-ctrl');  
    run(params, el, event);   
}

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

function display_tab(a,event) {    
    event.preventDefault();
    //console.log(event);
    
    // on recupère la balise a
    let _target = event.target; 
    while (_target.nodeName !== 'A') {
        _target = _target.parentNode;
    }

    var li = _target.parentNode;
    var ul = li.parentNode;
    
    // l'elem parent (li) a déjà la class active
    if(li.classList.contains('active') || li.classList.contains('disabled')) return false;    

    let _current = ul.querySelector('li.active');
    let _current_tab_id = _current.querySelector('a').hash;
    _current.classList.remove('active'); 

    li.classList.add('active');

    let div = ul.nextElementSibling;

    // on retire .active sur le contenu tab_content .active
    // la div active c'est celle qui a en id l'href 
    const _current_tab = div.querySelector(_current_tab_id);    
    
    var tab_content_to_active =  div.querySelector(_target.getAttribute('href'));

    // on ajoute la class active sur le tab_content en rapport avec l'elem cliqué, sélection par son id qui correspond au href du lien _target
    // ! elem.href renvoi le lien absolu, utiliser getAttribute('href');    
    _current_tab.classList.remove('active'); 
    tab_content_to_active.classList.add('active');    
}

const i18ns =  {
    i18n: function () {
    const lang = document.documentElement.lang;
    if (_defaultLang == lang) {
      //this.rewrite();
      return;
    }
    //const trads = document.querySelectorAll('[data-trad]');
    //console.log(trads);
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
          console.log('trad fetched');
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
  }
}

document.addEventListener('click', _click, false);
document.addEventListener('change', _change, false);
document.addEventListener('submit', _submit, false);
document.addEventListener('keyup', _keyup, false);

window.addEventListener("DOMContentLoaded", async function(e) {    
    i18ns.i18n();
    const obflinks = document.querySelectorAll('.obflink');
    for (let i=0, n=obflinks.length; i < n; ++i){
        obflinks[i].addEventListener('click', function(e){
            // un check futur pour savoir si c'est un lien _blank
            const link = decodeURIComponent(window.atob(obflinks[i].getAttribute('data-obf')));            
            //window.open(link); 
            window.location.assign(link);
        });
    }
    var tabs = document.querySelectorAll('ul.tabs > li > a');
    if(tabs.length >  0){
        for (var i=0, n=tabs.length; i < n; ++i) {
            tabs[i].addEventListener('click', function(e) {             
                display_tab(tabs[i], e);       
            });
        }        
    }     
    const checkoutDiv = document.getElementById('order');
    if(checkoutDiv) {
      let _logged = await checkUser();
      if(! _logged) {
        console.log('no user');
        document.getElementById('email-input').checked = true;
        document.querySelector('.already-customer').classList.remove('hide');
        document.querySelector('#form-customer').classList.add('hide');
      } else {
        input_gift_card(document.getElementById('gift-card-serial'));
        checkOut(checkoutDiv , e, _logged);
      }     
               
    }
});
export {monetary};