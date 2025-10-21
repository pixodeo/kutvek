if (window.NodeList && !NodeList.prototype.forEach) {
  NodeList.prototype.forEach = function (callback, thisArg) {
    thisArg = thisArg || window;
    for (var i = 0; i < this.length; i++) {
      callback.call(thisArg, this[i], i, this);
    }
  };
}

const carrousel = {
  _elem: null,
  _ev: null,
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  translateX: function () {
    const slider = document.getElementById(this._elem.getAttribute('data-carrousel')).querySelector('div.slide-container');
    const parent = this._elem.parentNode;
    let translate = this._elem.getAttribute('data-translate');
    console.log(translate);
    slider.style.transform = 'translateX(' + translate + '%)';


    // Affichage de la maquette sélectionné dans la popup
    // const figure =document.getElementById(parent.getAttribute('data-popup-choice')).querySelector('figure');
    // figure.querySelector('img').src = this._elem.getAttribute('data-mockup');
    // figure.querySelector('figcaption > span.selected-mockup').textContent = this._elem.value;
    //console.log(this._elem.form);

    //this._elem.form.querySelector('input[name="mockup"]').value = this._elem.value;
    this._elem.checked = true;


  },
  select: function () {
    const parent = this._elem.parentNode;
    parent.querySelectorAll('label').forEach(label => { label.classList.remove('checked') });
    this._elem.classList.add('checked');
    let _id = this._elem.getAttribute('for');
    document.getElementById(_id).checked = true;
  }
};

const category = {
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  create: function () {
    const form = this._elem;
    fetch(form.action, {
      method: form.method,
      //headers: headers,
      body: new FormData(form)
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          const flash = form.parentNode.querySelector('.flash-container');
          flash.innerHTML = json.msg;
          //const fragment = document.createDocumentFragment();

        }
      });
  }
};

const itemCart = {
  _elem: null,
  _ev: null,
  _cookies: {},
  _items: document.querySelector('#nbItems'),
  _store: null,
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  finish: function () {
    // Ajouter supprimer l'option finition dans le panier
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    console.log(option.value, option.textContent);
    var totalCart = document.getElementById('cartTotal');
    var currency = totalCart.getAttribute('data-currency');
    const pCart = document.getElementById('p-cart');
    const finishLine = pCart.querySelector('p.finish');
    const spans = finishLine.querySelectorAll('span');
    spans[0].textContent = option.textContent;
    spans[1].textContent = monetary(option.value, 'fr', currency);
    if (finishLine.hasAttribute('data-option')) {
      var idx = parseInt(finishLine.getAttribute('data-option'), 10);
      if (idx == index) {
        console.log('même index on ne fait rien');
      }
      else {

      }
    }
  },
  increase: function () {
    var input = this._elem.previousElementSibling;
    var int = parseInt(input.value, 10);
    var price = parseFloat(input.getAttribute('data-uprice'));

    var totalCart = document.getElementById('cartTotal');
    var total = parseFloat(totalCart.getAttribute('data-price'));
    var currency = totalCart.getAttribute('data-currency');
    input.value = int + 1;
    input.setAttribute('data-price', price * (int + 1));
    totalCart.setAttribute('data-price', total + price);
    totalCart.textContent = monetary((total + price), 'fr', currency);
  },
  decrease: function () {
    var input = this._elem.nextElementSibling;
    var int = parseInt(input.value, 10);
    var price = parseFloat(input.getAttribute('data-uprice'));
    var totalCart = document.getElementById('cartTotal');
    var total = parseFloat(totalCart.getAttribute('data-price'));
    var currency = totalCart.getAttribute('data-currency');
    //input.value = int > 1 ? int -1 : 1;
    if (int > 1) {
      input.value = int - 1;
      input.setAttribute('data-price', price * (int - 1));
      totalCart.setAttribute('data-price', total - price);
      totalCart.textContent = monetary((total - price), 'fr', currency);
    }
  },
  getStore: function () {
    var cart = JSON.parse(localStorage.getItem('cart'));
    // Pas encore de panier
    if (cart === null) {
    } else {
      document.body.append(localStorage.getItem('cart'));
    }
  },
  comfortFoam: function () {
    // Ajouter supprimer l'option finition dans le panier
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    const optLine = option.getAttribute('data-optline'); // A ajouter au panier
    if (optLine !== null)
      console.log(optLine);
  },
  addOption: function () {
    // Ajouter supprimer l'option finition dans le panier
    const items = document.querySelector('div.items');
    const options = items.querySelector('div.options');
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    const optLine = option.getAttribute('data-optline'); // A ajouter au panier
    if (optLine !== null) {

      var template = document.createElement('template');
      var html = optLine.trim(); // Never return a text node of whitespace as the result
      template.innerHTML = html;
      var opt = template.content.firstChild;
      opt.setAttribute('data-price', option.getAttribute('data-price'));
      var id = option.getAttribute('data-opt');
      opt.id = id;
      // On recherche  cette option dans le panier
      var exist = document.getElementById(id);
      if (exist) options.replaceWith(opt, exist);
      else options.appendChild(opt);

      //console.log(opt);
    } else {
      // On n'ajoute rien donc on vire
      var id = this._elem.getAttribute('data-opt');
      var exist = document.getElementById(id);
      if (exist) options.removeChild(exist);

    }
    const prices = items.querySelectorAll('[data-price]');
    var values = Array.prototype.map.call(prices, function (obj) {
      return parseFloat(obj.getAttribute('data-price'));
    });
    // Une mise à jour du montant 
    var sum = array_sum(values);
    var totalCart = document.getElementById('cartTotal');
    var currency = totalCart.getAttribute('data-currency');
    totalCart.setAttribute('data-price', sum);
    totalCart.textContent = monetary(sum, 'fr', currency);
    console.log(sum);
  }
};

const order = {

  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  details: function () {
    let oid = this._elem.getAttribute('data-order-id');
    let trId = 'details-' + oid;
    let tr = document.getElementById(trId);
    !tr.classList.contains('hide') ? tr.classList.add('hide') : tr.classList.remove('hide');
    // On vÃ©rifie que les lignes sont chargÃ©es.
    if (!tr.hasAttribute('data-loaded')) {
      let url = 'https://dev.kutvek.com/orders/' + oid + '/items';
      fetch(url, {
        method: 'GET'
      })
        .then((res) => {
          return res.json()
        })
        .then((json) => {
          if (json.success) {
            var template = document.querySelector("#productrow");
            // On clone la ligne et on l'insÃ¨re dans le tableau
            var tbody = tr.querySelector("#list-items-body");
            let totalHT = 0;
            let currency = 'EUR';
            let shipping = 0;
            let vat = 0;

            json.items.forEach(function (item) {
              let opts = document.createElement('p');
              opts.textContent = '';
              let optsArray = [];
              opts.className = 'only-print';
              let options = JSON.parse(item.options);
              for (var prop in options) {
                if (prop == 'custom_colors') optsArray.push('100%PERSO');
                if (prop == 'switch') optsArray.push('Switch Couleur');
                if (prop == 'plate') optsArray.push('Nom+NumÃ©ro');
                if (prop == 'sponsor' || prop == 'sponsors') optsArray.push('Sponsors');

                console.log(`obj.${prop} = ${options[prop]}`);
              }
              if (item.premium !== null && item.premium !== 'Aucune')
                optsArray.push(item.premium);

              var clone = document.importNode(template.content, true);
              var trs = clone.querySelectorAll("tr");
              var trComment = trs[1].querySelector('div');
              if (optsArray.length > 0) {
                opts.innerHTML = '<b>Options : </b>' + opts.textContent + optsArray.join(', ');
                trComment.appendChild(opts);
                console.log(opts);
              }

              var trItem = trs[0];
              var divCom = document.createElement('div');
              var pCom = document.createElement('p');

              if (item.item_comment !== null || item.order_comment !== null) {
                pCom.innerHTML = '<b>Commentaire : </b>';
                divCom.appendChild(pCom);
              }
              if (item.item_comment !== null) {
                let n = pCom.cloneNode(false);
                n.innerHTML = item.item_comment;
                divCom.appendChild(n);
              }
              if (item.order_comment !== null) {
                let n = pCom.cloneNode(false);
                n.innerHTML = item.order_comment;
                divCom.appendChild(n);
              }
              trComment.appendChild(divCom);
              // il y a des accessoires
              if (item.accessories && item.accessories.length > 0) {
                let tpl = document.createDocumentFragment();
                let div = document.createElement('div');
                let p = document.createElement('p');
                p.innerHTML = '<b>Accessoires :</b>';
                div.appendChild(p)

                item.accessories.forEach(function (accessory) {
                  let l = p.cloneNode(false);
                  l.textContent = accessory.name;
                  div.appendChild(l);
                });
                tpl.appendChild(div);

                trComment.appendChild(tpl);
              }
              var trDetail = trs[2];
              trItem.id = 'item-' + item.id;
              trDetail.id = 'item-details-' + item.id;
              var td = trItem.querySelectorAll("td");
              // td[0] le libÃ©llÃ© de l'article
              var link = td[0].querySelector('a');
              link.textContent = item.id;
              link.href = item.uri;
              td[1].textContent = item.name + ' - Ref. ' + item.reference;

              td[2].textContent = item.app_com_id;
              td[3].textContent = item.qty;
              td[4].textContent = monetary(item.priceHT, 'fr_FR', item.webshop_currency);
              td[5].textContent = percent(item.pro_rebate / 100, 'fr_FR', item.webshop_currency);
              td[6].textContent = item.vat;
              td[7].textContent = monetary(item.pr * item.qty, 'fr_FR', item.webshop_currency);
              td[8].textContent = '';
              td[10].querySelector('a').setAttribute('data-item-id', item.id);
              if (item.item_version > 2)
                td[10].querySelector('a').setAttribute('data-item-version', item.item_version);
              totalHT += item.pr * item.qty;
              if (item.tva > 0) vat = 1.20;
              if (item.com_shipping > 0) shipping = item.com_shipping;
              currency = item.webshop_currency;
              // On modifie les ids des formulaires process
              // RÃ©cupÃ©ration du formulaire
              if (item.app_com_id == null) {
                td[9].classList.remove('hide');
                var form = td[9].querySelector('form');
                // tous les inputs hidden du form
                var hiddens = form.querySelectorAll('input[type="hidden"]');
                var checkbox = form.querySelector('input[type="checkbox"]');
                // si on a une bonne affaire state = 14
                if (item.reference == 'BONNEAFFAIRE') hiddens[0].value = 14;
                else hiddens[0].value = item.state;
                hiddens[1].value = item.com_numero;
                hiddens[2].value = item.platform;
                hiddens[3].value = item.client_id;
                hiddens[4].value = item.id;
                hiddens[5].value = item.old;
                form.id = 'process-' + item.id;
                checkbox.id = 'process-step' + item.id;
                // mise en planche ou prise en charge ?
                if (item.state > 1 || item.reference == 'BONNEAFFAIRE') {
                  checkbox.checked = true;
                }
                form.querySelector('label').setAttribute('for', 'process-step' + item.id);
              }


              tbody.append(clone);
            });
            // calcul taxes Ã  ajouter;
            let rates = (totalHT + shipping) * vat - (totalHT + shipping);
            console.log(monetary((totalHT + shipping), 'fr_FR', currency));
            console.log(monetary((rates), 'fr_FR', currency));
            console.log('Total ttc : ', monetary((totalHT + shipping + rates), 'fr_FR', currency));
            tr.setAttribute('data-loaded', 1);
          }
        });
    }
    let divs = document.querySelectorAll('.orders-details'), i, l = divs.length;
    for (i = 0; i < l; ++i) {
      if (divs[i].id !== trId && !divs[i].classList.contains('hide')) divs[i].classList.add('hide');
    }

  },
  items: function () {

    let oid = this._elem.getAttribute('data-order-id');
    let trId = 'details-' + oid;
    let tr = document.getElementById(trId);
    console.log(tr.getAttribute('data-nocache'));
    if (!tr.hasAttribute('data-nocache')) {
      console.log('pas de hide');
      !tr.classList.contains('hide') ? tr.classList.add('hide') : tr.classList.remove('hide');
    }

    // On vÃ©rifie que les lignes sont chargÃ©es.
    if (!tr.hasAttribute('data-loaded') || tr.hasAttribute('data-nocache')) {
      let url = 'https://dev.kutvek.com/orders/' + oid + '/items';
      fetch(url, {
        method: 'GET'
      })
        .then((res) => {
          return res.json()
        })
        .then((json) => {
          if (json.success) {
            var template = document.querySelector("#productrow");
            // On clone la ligne et on l'insÃ¨re dans le tableau
            var tbody = tr.querySelector("#list-items-body");
            // vider tbody pour Ã©viter les doublons 
            while (tbody.firstChild) { tbody.removeChild(tbody.firstChild) }
            let totalHT = 0;
            let currency = 'EUR';
            let shipping = 0;
            let vat = 0;

            json.items.forEach(function (item) {
              console.log('crÃ©ation ligne de commande ...');
              let opts = document.createElement('p');
              opts.textContent = '';
              let optsArray = [];
              opts.className = 'only-print';
              let options = JSON.parse(item.options);
              for (var prop in options) {
                if (prop == 'custom_colors') optsArray.push('100%PERSO');
                if (prop == 'switch') optsArray.push('Switch Couleur');
                if (prop == 'plate') optsArray.push('Nom+NumÃ©ro');
                if (prop == 'sponsor' || prop == 'sponsors') optsArray.push('Sponsors');

                console.log(`obj.${prop} = ${options[prop]}`);
              }
              var clone = document.importNode(template.content, true);
              var trs = clone.querySelectorAll("tr");
              var trComment = trs[1].querySelector('div');
              if (optsArray.length > 0) {
                opts.innerHTML = '<b>Options : </b>' + opts.textContent + optsArray.join(', ');
                trComment.appendChild(opts);
                console.log(opts);
              }


              var trItem = trs[0];

              var divCom = document.createElement('div');
              var pCom = document.createElement('p');
              if (item.item_comment !== null || item.order_comment !== null) {
                pCom.innerHTML = '<b>Commentaire : </b>';
                divCom.appendChild(pCom);
              }
              if (item.item_comment !== null) {
                let n = pCom.cloneNode(false);
                n.innerHTML = item.item_comment;
                divCom.appendChild(n);
              }
              if (item.order_comment !== null) {
                let n = pCom.cloneNode(false);
                n.innerHTML = item.order_comment;
                divCom.appendChild(n);
              }
              trComment.appendChild(divCom);
              // il y a des accessoires
              if (item.accessories && item.accessories.length > 0) {
                let tpl = document.createDocumentFragment();
                let div = document.createElement('div');
                let p = document.createElement('p');
                p.innerHTML = '<b>Accessoires :</b>';
                div.appendChild(p)

                item.accessories.forEach(function (accessory) {
                  let l = p.cloneNode(false);
                  l.textContent = accessory.name;
                  div.appendChild(l);

                });
                tpl.appendChild(div);

                trComment.appendChild(tpl);
              }
              var trDetail = trs[2];
              trItem.id = 'item-' + item.id;
              trDetail.id = 'item-details-' + item.id;
              var td = trItem.querySelectorAll("td");
              // td[0] le libÃ©llÃ© de l'article
              //td[0].textContent = item.id;
              var link = td[0].querySelector('a');
              link.textContent = item.id;
              link.href = item.uri;
              td[1].textContent = item.name + ' - Ref. ' + item.reference;
              td[2].textContent = item.app_com_id;
              td[3].textContent = item.qty;
              td[4].textContent = monetary(item.priceHT, 'fr_FR', item.webshop_currency);
              td[5].textContent = percent(item.pro_rebate / 100, 'fr_FR', item.webshop_currency);
              td[6].textContent = item.vat;
              td[7].textContent = monetary(item.pr * item.qty, 'fr_FR', item.webshop_currency);
              td[8].textContent = '';
              td[10].querySelector('a').setAttribute('data-item-id', item.id);
              if (item.item_version > 2)
                td[10].querySelector('a').setAttribute('data-item-version', item.item_version);
              totalHT += item.pr * item.qty;
              if (item.tva > 0) vat = 1.20;
              if (item.com_shipping > 0) shipping = item.com_shipping;
              currency = item.webshop_currency;
              // On modifie les ids des formulaires process
              // RÃ©cupÃ©ration du formulaire
              if (item.app_com_id == null) {
                td[9].classList.remove('hide');
                var form = td[9].querySelector('form');
                // tous les inputs hidden du form
                var hiddens = form.querySelectorAll('input[type="hidden"]');
                var checkbox = form.querySelector('input[type="checkbox"]');
                // si on a une bonne affaire state = 14
                if (item.reference == 'BONNEAFFAIRE') hiddens[0].value = 14;
                else hiddens[0].value = item.state;
                hiddens[1].value = item.com_numero;
                hiddens[2].value = item.platform;
                hiddens[3].value = item.client_id;
                hiddens[4].value = item.id;
                hiddens[5].value = item.old;
                form.id = 'process-' + item.id;
                checkbox.id = 'process-step' + item.id;
                // mise en planche ou prise en charge ?
                if (item.state > 1 || item.reference == 'BONNEAFFAIRE') {
                  checkbox.checked = true;
                }
                form.querySelector('label').setAttribute('for', 'process-step' + item.id);
              }


              tbody.append(clone);
            });
            // calcul taxes Ã  ajouter;
            let rates = (totalHT + shipping) * vat - (totalHT + shipping);
            console.log(monetary((totalHT + shipping), 'fr_FR', currency));
            console.log(monetary((rates), 'fr_FR', currency));
            console.log('Total ttc : ', monetary((totalHT + shipping + rates), 'fr_FR', currency));
            if (!tr.hasAttribute('data-nocache'))
              tr.setAttribute('data-loaded', 1);
          }
        });
    }
    let divs = document.querySelectorAll('.orders-details'), i, l = divs.length;
    for (i = 0; i < l; ++i) {
      if (divs[i].id !== trId && !divs[i].classList.contains('hide')) divs[i].classList.add('hide');
    }

  },
  setPlatform: function () {
    let order = this._elem.getAttribute('data-order');
    let url = 'https://dev.kutvek.com/orders/' + order + '/platform';
    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");

    fetch(url, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ platform: this._elem.value })
    })
      .then((res) => {
        return res.text();
      })
      .then((data) => {
        document.location.reload();
        // on injecte le html 
        //let container = document.getElementById('visual_container').innerHTML = data;				
      });

  },
  carrier: function () {
    let order = this._elem.getAttribute('data-order');
    let url = 'https://dev.kutvek.com/orders/' + order + '/carrier';
    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");
    fetch(url, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ carrier: this._elem.value })
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        }
      });
  },
  setWebshopId: function () {
    let order = this._elem.getAttribute('data-order');
    let url = 'https://dev.kutvek.com/orders/' + order + '/webshop-id';
    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");

    fetch(url, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ webshopId: this._elem.value })
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        }
      });
  },
  shippingCost: function () {
    const cost = this._elem.value;
    console.log(cost);

    let order = this._elem.getAttribute('data-order');
    let url = 'https://dev.kutvek.com/orders/' + order + '/shipping-cost';
    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");
    fetch(url, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ shippingCost: cost })
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        }
      });
  },
  type: function () {
    let order = this._elem.getAttribute('data-order');
    let url = 'https://dev.kutvek.com/orders/' + order + '/type';

    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");

    fetch(url, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ type: this._elem.value })
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {


      });
  },
  platform: function () {
    let order = this._elem.getAttribute('data-order');
    let url = 'https://dev.kutvek.com/orders/' + order + '/platform';

    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");

    fetch(url, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ platform: this._elem.value })
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {


      });
  },
  setCustomer: function () {
    // Au click sur l'Ã©lÃ©ment de la liste
    var datalist = this._elem.parentNode;

    // 1 - on rÃ©cupÃ¨re les id user pour les mettre dans les input associÃ©s
    var user_id = this._elem.getAttribute('data-uid');
    var billing_addr = this._elem.getAttribute('data-billing') === 'null' ? null : this._elem.getAttribute('data-billing');
    var delivery_addr = this._elem.getAttribute('data-delivery') === 'null' ? null : this._elem.getAttribute('data-delivery');
    // l'input cible
    var input = document.getElementById(datalist.getAttribute('data-target'));
    input.value = user_id;

    // 2- on rÃ©cupÃ¨re le nom du client pour le mettre dans l'input associÃ©
    var user_name = this._elem.textContent;
    var input_name = document.querySelector('input[data-list="' + datalist.id + '"]');
    input_name.value = user_name;

    // 3 - on cache la liste
    while (datalist.firstChild) { datalist.removeChild(datalist.firstChild) }

    // 4 - besoin des adresses, email coordonnÃ©es etc
    let url = `https://dev.kutvek.com/cockpit/users/${user_id}/order-infos`;
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.text();
      })
      .then((html) => {

        if (html) {

          let div = document.querySelector('div.order-header');
          div.innerHTML = html;
        } else {

        }
      });
    // 5 - On update la commande
    let order = input.getAttribute('data-order');
    let url2 = 'https://dev.kutvek.com/orders/' + order + '/user';
    var headers = new Headers();
    headers.append("Content-Type", "application/json; charset=utf-8");

    fetch(url2, {
      method: 'PUT',
      headers: headers,
      body: JSON.stringify({ user: user_id, billing_addr: billing_addr, delivery_addr: delivery_addr })
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {

      });

  }
};

const orderItem =
{
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  infos: function () {
    let url = this._elem.getAttribute('data-uri');
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.text();
      })
      .then((html) => {

        if (html) {

          let div = document.querySelector(`div[data-item="${this._elem.id}"]`);
          div.innerHTML = html;
        } else {

        }
      });
  },
  details: function () {
    let oid = this._elem.getAttribute('data-item-id');
    let trId = 'item-details-' + oid;
    let tr = document.getElementById(trId);
    !tr.classList.contains('hide') ? tr.classList.add('hide') : tr.classList.remove('hide');
    // On vÃ©rifie que les lignes sont chargÃ©es.
    if (!tr.hasAttribute('data-loaded')) {
      let url = 'https://dev.kutvek.com/orderItems/' + oid;

      if (this._elem.hasAttribute('data-item-version') && this._elem.getAttribute('data-item-version') == 3) {
        url += '?version=' + this._elem.getAttribute('data-item-version');
      }

      fetch(url, {
        method: 'GET'
      })
        .then((res) => {
          return res.json()
        })
        .then((json) => {
          if (json.success) {

            let sponsors = tr.querySelector('.sponsors');
            let plate = tr.querySelector('.plate');
            const infos = JSON.parse(json.item.item_custom);
            const plateOpt = json.plate;
            // Est-ce qu'on a option plaque
            if (json.plate) {
              //const plateInfos = infos.options.plate;
              console.log(plateOpt);
              var places = plate.querySelectorAll('.places');

              if (plateOpt.color != undefined)
                places[0].innerHTML = 'Couleur plaque: ' + plateOpt.color;
              if (plateOpt.name != undefined)
                places[1].innerHTML = 'Nom plaque: ' + plateOpt.name;
              if (plateOpt.name_typo != undefined)
                places[2].innerHTML = `Typo nom: ${plateOpt.name_typo}`;
              if (plateOpt.number_color != undefined)
                places[3].innerHTML = 'Couleur numÃ©ro: ' + plateOpt.number_color;
              if (plateOpt.number != undefined)
                places[4].innerHTML = 'NumÃ©ro: ' + plateOpt.number;
              if (plateOpt.number_typo != undefined)
                places[5].innerHTML = `Typo numÃ©ro: ${plateOpt.number_typo}`;
              if (plateOpt.logo != undefined)
                places[6].innerHTML = 'Logo: ' + plateOpt.logo || '';

              plate.classList.remove('hide');
              json.item.state = 1;
            }
            if (json.item.item_comment) {
              let div = tr.querySelector('.comment');
              let tpl = document.createElement('template');
              tpl.innerHTML = json.item.item_comment;
              div.appendChild(tpl.content);
              div.classList.remove('hide');
              json.item.state = 1;

            }
            if (json.sponsors) {
              var tpl = document.createElement('template');
              tpl.innerHTML = json.sponsors;
              sponsors.appendChild(tpl.content);
              sponsors.classList.remove('hide');
              json.item.state = 1;
            }
            if (json.switch) {
              let div = tr.querySelector('.switch');

              json.switch.forEach(function (el) {
                let p = document.createElement('p')
                p.innerHTML = el;
                div.appendChild(p);
              });
              div.classList.remove('hide');
              json.item.state = 1;
            }
            if (json.accessories) {
              let div = tr.querySelector('.accessories');
              let tpl = document.createElement('template');
              tpl.innerHTML = json.accessories;
              div.appendChild(tpl.content);
              div.classList.remove('hide');
            }
            if (json.attached) {
              let div = tr.querySelector('.attached-files');
              let tpl = document.createElement('template');
              tpl.innerHTML = json.attached;
              div.appendChild(tpl.content);
              div.classList.remove('hide');
              json.item.state = 1;
            }
            if (json.visuals) {
              let div = tr.querySelector('.vehicle-files');
              let tpl = document.createElement('template');
              tpl.innerHTML = json.visuals;
              div.appendChild(tpl.content);
              div.classList.remove('hide');
              json.item.state = 1;
            }
            if (json.premium) {
              let div = tr.querySelector('.premium');
              let tpl = document.createElement('template');
              let price = monetary(json.premium.price, 'fr', 'EUR');

              tpl.innerHTML = json.premium.name + ' ' + price;
              div.appendChild(tpl.content);
              div.classList.remove('hide');
              json.item.state = 1;
            }
            tr.setAttribute('data-loaded', 1);
          }
        });
    }
    let divs = document.querySelectorAll('.item-details'), i, l = divs.length;
    for (i = 0; i < l; ++i) {
      if (divs[i].id !== trId && !divs[i].classList.contains('hide')) divs[i].classList.add('hide');
    }

  },
  process: function () {
    const form = this._elem;
    if (form.id == 'process-15541') {
      form.parentNode.removeChild(form);
      return;
    }
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          alert(data.msg);
          form.parentNode.removeChild(form);

        } else {
          alert(data.error);
        }
      });
  },
  state: function () {
    let checked = this._elem.checked;
    let state;
    let input = this._elem.parentNode.parentNode.querySelector('input[name="state"]');
    //console.log(this._elem.checked);
    // modif du state de la commande 
    if (checked) state = 11
    else state = 1;
    input.value = state;
  },
  setFinish: function () {
    // Modifie la finition cotÃ© appli et cÃ´tÃ© dev
    let item = this._elem.getAttribute('data-item');
    let url = '/orderItems/' + item + '/finish';
    let data = { finish: this._elem.value };
    fetch(url, {
      method: 'PUT',
      body: JSON.stringify(data)
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        if (data.success) {


        } else {

        }
      });

  },
  crossSelling: function () {
    let checked = this._elem.checked ? 1 : 0;
    let item = this._elem.getAttribute('data-item');
    let url = '/orderItems/' + item + '/crossSelling';
    fetch(url, {
      method: 'PUT',
      body: JSON.stringify({ seatCover: checked })
    })
      .then((res) => {
        return res.text();
      })
      .then((data) => {
        if (data.success) {


        } else {

        }
      });
    console.log(checked);

  },
  work: function () {
    let url = this._elem.getAttribute('data-uri');
    fetch(url, {
      method: 'GET'
    }).then((res) => {
      return res.text()
    }).then((text) => {
      if (text) {
        let container = this._elem.parentNode.querySelector('div.fetched');
        container.innerHTML = text;
        app.i18n();
        this._elem.classList.remove('onchange');
      }
    });
  },
  accept: function () {
    let url = this._elem.action;
    let formData = new FormData(this._elem);
    fetch(url, {
      method: 'POST',
      body: formData
    }).then((res) => {
      return res.json()
    }).then((json) => {
      if (json.success) {
        document.location.reload();
      }
    });
  },
  decline: function () {
    let url = this._elem.action;
    let btn = this._elem.querySelector('button[type="submit"]');
    btn.classList.add('hide');
    btn.disabled = true;
    let formData = new FormData(this._elem);
    fetch(url, {
      method: 'POST',
      body: formData
    }).then((res) => {
      return res.json()
    }).then((json) => {
      if (json.success) {
        document.location.reload();
      } else {
        btn.disabled = false;
        btn.classList.remove('hide');
      }
    });
  },
  filterByYear: function () {

    let year = this._elem.value;
    let url = this._elem.getAttribute('data-uri') + '/' + year;
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          const container = document.getElementById(this._elem.getAttribute('data-container'));
          let template = document.createElement('template');
          document.getElementById('current-year').textContent = json.currentYear;
          while (container.firstChild) { container.removeChild(container.firstChild) }
          json.orders.forEach((el) => {
            el = el.trim();
            template.innerHTML = el;
            container.append(template.content.firstChild);
          });

        } else {
          alert(json.error);
        }
      });
  },
  searchById: function () {

    var min_char = 5;
    if (this._elem.value.length < min_char) return;

    let id = this._elem.value;
    let url = this._elem.getAttribute('data-uri') + '/' + id;

    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          if (json.orders.length > 0) {
            if (json.orders.length > 0) {
              const container = document.getElementById(this._elem.getAttribute('data-container'));
              let template = document.createElement('template');
              document.getElementById('current-year').textContent = json.item;
              while (container.firstChild) { container.removeChild(container.firstChild) }
              let html = json.orders[0].trim();
              template.innerHTML = html;
              container.append(template.content.firstChild);
            }
          }
        } else {
          console.log(json.error);
        }
      });
  },

  update: function () {
    let url = this._elem.action;
    var formData = new FormData(this._elem);
    var data = {};
    formData.forEach((value, key) => {
      // Reflect.has in favor of: data.hasOwnProperty(key)
      if (!Reflect.has(data, key)) {
        data[key] = value;
        return;
      }
      if (!Array.isArray(data[key])) {
        data[key] = [data[key]];
      }
      data[key].push(value);
    });
    fetch(url, {
      method: 'PUT',
      body: JSON.stringify(data)
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          var checkbox = document.getElementById('check-seat-cover');
          checkbox.disabled = false;
          checkbox.checked = true;
          this._elem.querySelector('button[type="submit"]').disabled = true;

        } else {

        }
      });
  },
  create: function () {
    const form = this._elem;

    var formData = new FormData(form);
    var otherFiles = form.querySelector('.files-preview');
    otherFiles.querySelectorAll('span.obj').forEach(function (el) {
      formData.append('item_files[]', el.file);
    });

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          form.reset();
          document.getElementById('draft-order').click();
          document.querySelector('a[href="#add-item"]').click();

        } else {
          alert(json.error);
        }
      });

  },
  addFiles: function () {
    const form = this._elem;

    var formData = new FormData(form);
    var otherFiles = form.querySelector('.files-preview');
    otherFiles.querySelectorAll('span.obj').forEach(function (el) {
      formData.append('item_files[]', el.file);
    });

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          // on reload la page 
          document.location.reload();


        } else {
          alert(json.error);
        }
      });
  },
  newMessage: function () {
    const form = this._elem;
    var formData = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          // on reload la page 
          document.location.reload();
        } else {
          alert(json.error);
        }
      });

  },
  navigate: function () {
    let typeItem = this._elem.getAttribute('data-type');
    // on renvoie vers la bonne mÃ©thode
    this[typeItem]();
  },
  /**
   * CrÃ©er un article type kit dÃ©co std/ dispo en boutique
   */
  home: function () {
    const slider = document.getElementById(this._elem.getAttribute('data-slider'));
    slider.classList.remove('slide-standard');
    slider.classList.remove('slide-custom');
    slider.classList.add('slide-home');
  },
  standard: function () {
    // On slide vers la gauche	
    const slider = document.getElementById(this._elem.getAttribute('data-slider'));
    slider.classList.remove('slide-home');
    slider.classList.remove('slide-custom');
    slider.classList.add('slide-standard');
  },
  custom: function () {
    const slider = document.getElementById(this._elem.getAttribute('data-slider'));
    slider.classList.remove('slide-home');
    slider.classList.remove('slide-standard');
    slider.classList.add('slide-custom');
  },
  setPrice: function () {
    const form = this._elem.form;
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    let cost = parseFloat(option.getAttribute('data-price')) || 0;
    let input = form.querySelector('input[name="price[item]"]');
    input.value = cost;
  }
};

const accessory = {
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  create: function () {
    const form = this._elem;
    fetch(form.action, {
      method: form.method,
      //headers: headers,
      body: new FormData(form)
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          var name = json.item.name;
          var ul = form.parentNode.querySelector('ul');
          var template = document.querySelector('template.list-item');
          var clone = document.importNode(template.content, true);
          var li = clone.querySelector('li');
          li.textContent = name;
          ul.append(clone);
          // si on est sur la page vÃ©hicle il faut rajouter une ligne au tableau
          form.reset();
          // const flash = form.parentNode.querySelector('.flash-container');
          // flash.innerHTML = json.msg;
        }
      });
  },
  brands: function () {
    let id = this._elem.value;
    fetch('/accessories/categories/' + id + '/brands', {
      method: 'GET'
    }).then((res) => {
      return res.json()
    }).then((json) => {
      let container_id = this._elem.getAttribute('data-target');
      let select = document.getElementById(container_id); // cible				
      let fragment = document.createDocumentFragment();
      let childNode = select.firstElementChild;
      while (select.firstChild) { select.removeChild(select.firstChild) }
      fragment.appendChild(childNode);
      select.appendChild(fragment);
      if (json.brands.length > 0) {

        json.brands.forEach((brand, index) => {
          let option = document.createElement('option');
          option.textContent = brand.text;
          option.value = brand.value;
          fragment.appendChild(option);
        });
        select.appendChild(fragment);
        select.disabled = null;

      }
    });
  },
  categories: function () { },
  filter: function () {
    const queries = [
      { name: "brand", "value": this._elem.querySelector('#brand').value },
      { name: "category", "value": this._elem.querySelector('#category').value }
    ];

    var filter = queries.filter(query => query.value.length > 0);

    const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');

    /*  
      console.log(queries);
      console.log(filter);
      console.log(asString);
      return; 
    */

    let url = this._elem.action + '?' + asString + '&xhr=1';
    fetch(url, {
      method: this._elem.method
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        var table = document.getElementById(this._elem.getAttribute('data-list'));
        var tbody = table.querySelector('tbody');
        while (tbody.firstChild) { tbody.removeChild(tbody.firstChild) }
        if (json.accessories.length > 0) {
          var template = document.querySelector("#accessory-row");
          json.accessories.forEach(function (accessory) {
            var clone = document.importNode(template.content, true);
            var tds = clone.querySelectorAll("td");
            tds[0].querySelector('span').textContent = accessory.id;
            tds[0].querySelector('input').value = accessory.id;
            tds[1].textContent = accessory.cat_name;
            tds[2].textContent = accessory.name;
            tds[3].innerHTML = accessory.cost;
            tds[4].innerHTML = accessory.link;
            tbody.append(clone);
          });
        }
        // fil d'ariane
        if (json.breadcrumb) {
          document.querySelector('.breadcrumb-container').innerHTML = json.breadcrumb;
        }
        if (json.category && json.category !== null) {
          document.getElementById('category').value = json.category
        }
        if (json.brand && json.brand !== null) {
          document.getElementById('brand').value = json.brand
        }
      });
  }
};

const task = {
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  assignment: function () {

    var formData = new FormData(this._elem);
    var object = {};
    formData.forEach((value, key) => {
      // Reflect.has in favor of: object.hasOwnProperty(key)
      if (!Reflect.has(object, key)) {
        object[key] = value;
        return;
      }
      if (!Array.isArray(object[key])) {
        object[key] = [object[key]];
      }
      object[key].push(value);
    });
    var json = JSON.stringify(object);
    console.log(json);
    fetch(this._elem.action, {
      method: 'PUT',
      //headers: headers,
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On rÃ©actualise la page avec un postMessage
          var msg = { action: 'reload' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');

        } else {
          alert(json.error);
        }

      });
  },
  create: function () {
    const form = this._elem;

    // Penser Ã  enlever disabled sur input file
    let input = form.querySelector('input[type="file"]');
    let submit = form.querySelector('button[type="submit"]');
    submit.disabled = true;
    input.disabled = false;
    //console.log(input);return;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        if (data.msg) {
          console.log('Nouveau message');
          // le chat
          var chat = document.getElementById('chat-container');
          var fragment = document.createDocumentFragment();
          var p = document.createElement('div');
          p.innerHTML = data.msg;
          fragment.appendChild(p);
          chat.appendChild(fragment);
          chat.scrollTop = chat.scrollHeight;
          var preload = form.querySelector('.preload');
          while (preload.firstChild) { preload.removeChild(preload.firstChild) }
          form.reset();
        }
        if (data.mockup) {
          // Ajouter la maquette
          var mockups = document.getElementById('widget-mockups').querySelector('.preview');
          let tpl = document.createElement('template');
          tpl.innerHTML = data.mockup;
          mockups.appendChild(tpl.content);
        }
        if (data.task) {
          // la tÃ ache est crÃ©e 
          // on ferme le tchat 
          form.parentNode.classList.add('closed');
          // On active le bouton valider du formulaire de validation de la tache
          let btn = document.querySelector('form[data-ctrl="task.validate"]').querySelector('button[type="submit"]');
          btn.disabled = false;
        }

      });
  },
  printing: function () {
    const form = this._elem;
    const required = document.querySelectorAll('.required').length;
    const formData = new FormData(form);
    formData.append('required', required);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        } else {
          //alert(json.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');
        }
      });
  },
  cutout: function () {
    const form = this._elem;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        } else {
          //alert(json.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');
        }
      });
  },
  billing: function () {
    const form = this._elem;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        } else {
          //alert(json.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');
        }
      });
  },
  shipping: function () {
    const form = this._elem;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        } else {
          //alert(json.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');
        }
      });

  },
  shipment: function () {
    const form = this._elem;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (!json.success) {
          alert(json.error);
        } else {
          //alert(json.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');
        }
      });
  },
  validate: function () {
    // CrÃ©tion d'une nouvelle Ã©tape/ tÃ¢che avec envoi d'un email 
    // informant le client d'une maquette dispo ou d'un message graphiste
    const form = this._elem;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          //alert(data.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');

        }
        else {
          alert(data.error);
        }

      });
  },
  next: function () {
    const form = this._elem;
    let method = form.getAttribute('data-method') || form.method || 'GET';

    const formData = new FormData(form);

    const preview = form.querySelector('div.preview');
    // Si on a une div preview de fichiers et au moins un fichier
    if (preview) {
      const _files = preview.querySelectorAll('span.obj');
      if (_files.length > 0) {
        _files.forEach(function (el) {
          formData.append('files[]', el.file);
        });
      }
    }
    fetch(form.action, {
      method: method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {

        /* if(data.success){
          //alert(data.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = {action: 'home'};
          window.parent.postMessage(msg, 'https://www.kutvek.com');

        }					
        else {
          alert(data.error);	
        } */

      });
  },
  modify: function () {
    const form = this._elem;
    let method = form.getAttribute('data-method') || form.method || 'GET';

    const formData = new FormData(form);

    const preview = form.querySelector('div.preview');
    // Si on a une div preview de fichiers et au moins un fichier
    if (preview) {
      const _files = preview.querySelectorAll('span.obj');
      if (_files.length > 0) {
        _files.forEach(function (el) {
          formData.append('files[]', el.file);
        });
      }
    }
    fetch(form.action, {
      method: method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {

        /* if(data.success){
          //alert(data.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = {action: 'home'};
          window.parent.postMessage(msg, 'https://www.kutvek.com');

        }					
        else {
          alert(data.error);	
        } */

      });
  },
  manufacturing: function () {
    const form = this._elem;
    const formData = new FormData(form);
    fetch(form.action, {
      method: form.method,
      body: formData
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (data.success) {
          //alert(data.msg);
          // Renvoi accueil de l'application via postMessage
          var msg = { action: 'home' };
          window.parent.postMessage(msg, 'https://www.kutvek.com');
        }
        else {
          alert(data.error);
        }
      });
  },
  backward: function () {
    var link = this._elem;
    fetch(link.href, {
      method: 'GET'
    }).then((res) => {
      return res.json();
    }).then((json) => {
      /* if(data.success){
        //alert(data.msg);
        // Renvoi accueil de l'application via postMessage
        var msg = {action: 'home'};
        window.parent.postMessage(msg, 'https://www.kutvek.com');

      }					
      else {
        alert(data.error);	
      } */
    });
  },
  forward: function () {
    var link = this._elem;
    fetch(link.href, {
      method: 'GET'
    }).then((res) => {
      return res.json();
    }).then((json) => {
      /* if(data.success){
        //alert(data.msg);
        // Renvoi accueil de l'application via postMessage
        var msg = {action: 'home'};
        window.parent.postMessage(msg, 'https://www.kutvek.com');

      }					
      else {
        alert(data.error);	
      } */
    });
  }
};

const universe = {
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  brands: function () {
    var id = this._elem.value;
    var url = this._elem.getAttribute('data-uri').replace(':uid', id);

    // le formulaire
    //const form =  document.getElementById()
    const model = this._elem.parentNode.querySelector('.model');
    const vehicle = this._elem.parentNode.querySelector('.vehicle');
    if (model !== null) {
      let childNode = model.firstElementChild;
      while (model.firstChild) { model.removeChild(model.firstChild) }
      model.appendChild(childNode);
    }
    if (vehicle !== null) {
      let childNode = vehicle.firstElementChild;
      while (vehicle.firstChild) { vehicle.removeChild(vehicle.firstChild) }
      vehicle.appendChild(childNode);
    }
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.brands.length > 0) {
          var container_id = this._elem.getAttribute('data-target');
          var select = document.getElementById(container_id); // cible				
          var fragment = document.createDocumentFragment();
          var childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);
          json.brands.forEach((brand, index) => {
            var option = document.createElement('option');
            option.textContent = brand.text;
            option.value = brand.value;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
        }
      });

  },

};

const brand = {
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  create: function () {
    const form = this._elem;
    const data = new FormData(form)
    fetch(form.action, {
      method: form.method,
      body: data
    })
      .then((response) => {
        if (response.ok) {
          return response.json()
        } else {
          console.log('Mauvaise rÃ©ponse du rÃ©seau');
          return response.status
        }
      })
      .then((json) => {
        if (json.success) {
          if (json.success) {
            var ul = document.getElementById('brands-added');
            var template = document.querySelector("#brand-item");
            var clone = document.importNode(template.content, true);
            var li = clone.querySelector('li');
            li.textContent = json.item;
            ul.append(clone);
            form.reset();
          } else {

          }


        } else {

        }

      })
      .catch(function (error) {
        console.log('Il y a eu un problÃ¨me avec l\'opÃ©ration fetch: ' + error.message);
      });
  },
  list: function () {
    let id = this._elem.value;
    // le formulaire
    const form = document.getElementById(this._elem.getAttribute('form'));
    const vehicles = form.querySelector('select.vehicle');
    // on vire les prÃ©cÃ©dents vÃ©hicules
    if (vehicles !== null) {
      let childNode = vehicles.firstElementChild;
      while (vehicles.firstChild) { vehicles.removeChild(vehicles.firstChild) }
      vehicles.appendChild(childNode);
    }
    // Les marques
    fetch('/universes/' + id + '/brands?list=1', {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.brands.length > 0) {

          let select = form.querySelector(this._elem.getAttribute('data-target')); // cible				
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);
          json.brands.forEach((brand, index) => {
            let option = document.createElement('option');
            option.textContent = brand.text;
            option.value = brand.value;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
        }
      });
    // Les sponsors
    fetch('/widgets/widget-sponsors?fid=' + id, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.widget) {
          const area = form.querySelector('.sponsor-area');
          const input = form.querySelector('input[name="kit[weight]"]');
          input.value = json.weight;
          area.innerHTML = json.widget;
        }
      });

  },
  models: function () {
    var id = this._elem.value;
    var uid = this._elem.parentNode.querySelector('.universe').value;
    var url = this._elem.getAttribute('data-uri').replace(':uid', uid).replace(':bid', id);

    // le formulaire
    //const form =  document.getElementById()
    const model = this._elem.parentNode.querySelector('.model');
    const vehicle = this._elem.parentNode.querySelector('.vehicle');
    if (model !== null) {
      let childNode = model.firstElementChild;
      while (model.firstChild) { model.removeChild(model.firstChild) }
      model.appendChild(childNode);
    }
    if (vehicle !== null) {
      let childNode = vehicle.firstElementChild;
      while (vehicle.firstChild) { vehicle.removeChild(vehicle.firstChild) }
      vehicle.appendChild(childNode);
    }
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.models.length > 0) {
          var container_id = this._elem.getAttribute('data-target');
          var select = document.getElementById(container_id); // cible				
          var fragment = document.createDocumentFragment();
          var childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);
          json.models.forEach((model, index) => {
            var option = document.createElement('option');
            option.textContent = model.text;
            option.value = model.value;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
        }
      });

  },
  vehicles: function () {
    var brand = this._elem.value;

    var universe = document.getElementById('universe').value;
    let url = 'https://dev.kutvek.com/vehicles.getVehicle?universe=' + universe + '&brand=' + brand;
    fetch(url, {
      method: 'GET'

    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.vehicles.length > 0) {
          let container_id = this._elem.getAttribute('data-target');
          let select = document.getElementById(container_id); // cible				
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;

          while (select.firstChild) { select.removeChild(select.firstChild) }

          fragment.appendChild(childNode);
          select.appendChild(fragment);

          json.vehicles.forEach((model, index) => {
            let option = document.createElement('option');
            option.textContent = model.name;
            option.value = model.id;
            fragment.appendChild(option);
          });

          select.appendChild(fragment);
          select.disabled = null;
        }
        if (this._elem.id == 'brand') {
          var popupSelect = document.querySelector('#addBrand');
          if (popupSelect) {
            popupSelect.value = this._elem.value;
            popupSelect.addEventListener('change', _change, false);
            popupSelect.dispatchEvent(new Event("change"));
          }
          //popupSelect.options[this._elem.options.selectedIndex].selected = true;

        }

      });
  }
};

const model = {
  _elem: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  list: function () {
    let id = this._elem.value;
    let uid = this._elem.parentNode.querySelector('.universe').value;
    fetch('/cockpit/models?universe=' + uid + '&brand=' + id, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          const models = json.models;
          if (models.length > 0) {
            let id = this._elem.getAttribute('data-target');
            let select = document.getElementById(id); // cible				
            let fragment = document.createDocumentFragment();
            let childNode = select.firstElementChild;
            while (select.firstChild) { select.removeChild(select.firstChild) }
            fragment.appendChild(childNode);
            select.appendChild(fragment);
            models.forEach((model) => {
              let option = document.createElement('option');
              option.textContent = model.text;
              option.value = model.value;
              fragment.appendChild(option);
            });
            select.appendChild(fragment);
            select.disabled = null;
          }

        } else {
          console.log(json.error);
        }
      });
  },
  filter: function () {
    const queries = [
      { name: "universe", "value": this._elem.querySelector('#universe').value },
      { name: "brand", "value": this._elem.querySelector('#brand').value }
    ];
    var filter = queries.filter(query => query.value.length > 0);
    const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');
    let url = this._elem.action + '?' + asString;

    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          const models = json.models;
          // On vide tjs le tableau
          var tbody = document.querySelector("#list-models-body");
          while (tbody.firstChild) { tbody.removeChild(tbody.firstChild) }

          // On remplit le tableau le cas Ã©chÃ©ant
          if (models.length > 0) {
            var template = document.querySelector("#model-row");
            models.forEach(function (model) {
              var clone = document.importNode(template.content, true);
              var tds = clone.querySelectorAll("td");
              tds[0].querySelector('span').textContent = model.model_id;
              tds[0].querySelector('input').value = model.model_id;
              tds[1].textContent = model.u_name;
              tds[2].textContent = model.b_name;
              tds[3].textContent = model.name;
              tds[4].innerHTML = model.link;
              tbody.append(clone);
            });
          }
        } else {
          alert(json.error);
        }

      });
  },
  create: function () {
    const form = this._elem;
    const data = new FormData(form)
    fetch(form.action, {
      method: form.method,
      body: data
    })
      .then((response) => {
        if (response.ok) {
          return response.json()
        } else {
          console.log('Mauvaise rÃ©ponse du rÃ©seau');
          return response.status
        }
      })
      .then((json) => {
        if (json.success) {
          if (json.success) {
            var ul = document.getElementById('models-added');
            var template = document.querySelector("#model-item");
            var clone = document.importNode(template.content, true);
            var li = clone.querySelector('li');
            li.textContent = json.item;
            ul.append(clone);
            form.reset();
          } else {

          }


        } else {

        }

      })
      .catch(function (error) {
        console.log('Il y a eu un problÃ¨me avec l\'opÃ©ration fetch: ' + error.message);
      });
  },
  read: function () { },
  update: function () {
    var formData = new FormData(this._elem);
    var object = {};
    formData.forEach((value, key) => {
      // Reflect.has in favor of: object.hasOwnProperty(key)
      if (!Reflect.has(object, key)) {
        object[key] = value;
        return;
      }
      if (!Array.isArray(object[key])) {
        object[key] = [object[key]];
      }
      object[key].push(value);
    });
    var json = JSON.stringify(object);
    console.log(json);
    fetch(this._elem.action, {
      method: 'PUT',
      //headers: headers,
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On change le nom
          document.getElementById('title').textContent = json.fullname;
          // On reset le formulaire
          this._elem.reset();

        } else {
          alert(json.error);
        }

      });

  },
  vehicles: function () {
    var id = this._elem.value;
    var uid = this._elem.parentNode.querySelector('.universe').value;
    var bid = this._elem.parentNode.querySelector('.brand').value;
    var url = this._elem.getAttribute('data-uri').replace(':uid', uid).replace(':bid', bid).replace(':mid', id);
    // le formulaire

    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.vehicles.length > 0) {
          var container_id = this._elem.getAttribute('data-target');
          var select = document.getElementById(container_id); // cible				
          var fragment = document.createDocumentFragment();
          var childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);
          json.vehicles.forEach((vehicle, index) => {
            var option = document.createElement('option');
            option.textContent = vehicle.text;
            option.value = vehicle.value;
            option.setAttribute('data-seat', vehicle.seat_cover)
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
        }
      });
  }

};
const vehicle = {
  _elem: null,
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  create: function () {
    const form = this._elem;
    fetch(form.action, {
      method: form.method,
      body: new FormData(form)
    })
      .then((res) => {
        return res.json();
      })
      .then((data) => {
        if (data.success) {
          var ul = document.getElementById('vehicles-added');
          var template = document.querySelector("#vehicle-item");

          var clone = document.importNode(template.content, true);
          var li = clone.querySelector('li');
          li.textContent = data.item;
          ul.append(clone);
          // si on est sur la page vÃ©hicle il faut rajouter une ligne au tableau
          form.reset();
        } else {

        }
      });
  },
  read: function () {
    let url = this._elem.href;
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.text()
      })
      .then((data) => {
        if (data) {
          window.location.hash = 'licence';
          document.querySelector('#licence').innerHTML = data;
        }
      });
  },
  update: function () {
    var formData = new FormData(this._elem);
    var object = {};
    formData.forEach((value, key) => {
      // Reflect.has in favor of: object.hasOwnProperty(key)
      if (!Reflect.has(object, key)) {
        object[key] = value;
        return;
      }
      if (!Array.isArray(object[key])) {
        object[key] = [object[key]];
      }
      object[key].push(value);
    });
    var json = JSON.stringify(object);
    console.log(json);
    //return;
    fetch(this._elem.action, {
      method: 'PUT',
      //headers: headers,
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On change le nom
          document.getElementById('title').textContent = json.fullname;
          // On reset le formulaire
          this._elem.reset();

        } else {
          alert(json.error);

        }

      });

  },
  list2: function () {
    let id = this._elem.value;
    let uid = this._elem.parentNode.querySelector('.universe').value;
    fetch('/cockpit/vehicles/list?universe=' + uid + '&brand=' + id, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success) {
          const vehicles = json.vehicles;
          let id = this._elem.getAttribute('data-target');
          let select = document.getElementById(id); // cible		
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);
          if (vehicles.length > 0) {
            vehicles.forEach((vehicle) => {
              let option = document.createElement('option');
              option.textContent = vehicle.text;
              option.value = vehicle.value;
              fragment.appendChild(option);
            });
            select.appendChild(fragment);
            select.disabled = null;
          }

        } else {
          console.log(json.error);
        }
      });
  },
  list: function () {
    const form = document.getElementById(this._elem.getAttribute('form'));
    var brand = this._elem.value;
    var universe = form.querySelector('select.universe').value;
    let url = 'https://dev.kutvek.com/vehicles.getVehicle?universe=' + universe + '&brand=' + brand;
    fetch(url, {
      method: 'GET'
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.vehicles.length > 0) {
          let select = form.querySelector(this._elem.getAttribute('data-target')); // cible				
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;

          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          select.appendChild(fragment);

          json.vehicles.forEach((model, index) => {
            let option = document.createElement('option');
            option.textContent = model.name;
            option.value = model.id;
            option.setAttribute('data-price', model.price);
            fragment.appendChild(option);
          });

          select.appendChild(fragment);
          select.disabled = null;
        }
      });
  },
  filter: function () {
    const queries = [
      { name: "universe", "value": this._elem.querySelector('#universe').value },
      { name: "brand", "value": this._elem.querySelector('#brand').value },
      { name: "model", "value": this._elem.querySelector('#model').value }
    ];

    var filter = queries.filter(query => query.value.length > 0);

    const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');

    /* console.log(queries);
    console.log(filter);
    console.log(asString);
    return; */
    let url = this._elem.action + '?' + asString + '&xhr=1';
    fetch(url, {
      method: this._elem.method
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.vehicles.length > 0) {
          var tbody = document.querySelector("#list-vehicles-body");
          while (tbody.firstChild) { tbody.removeChild(tbody.firstChild) }
          var template = document.querySelector("#vehicle-row");
          json.vehicles.forEach(function (vehicle) {
            var clone = document.importNode(template.content, true);
            var tds = clone.querySelectorAll("td");
            tds[0].querySelector('span').textContent = vehicle.v_id;
            tds[0].querySelector('input').value = vehicle.v_id;
            tds[1].textContent = vehicle.u_name;
            tds[2].textContent = vehicle.b_name;
            tds[3].textContent = vehicle.v_name;
            tds[5].innerHTML = vehicle.link;
            tbody.append(clone);
          });
        }
      });
  },
  bindAccessory: function () {
    const form = this._elem;
    const formData = new FormData(form);
    const id = formData.get('vehicle');
    const url = form.action.replace(':id', id);
    formData.delete('universe');
    formData.delete('brand');
    formData.delete('vehicle');
    fetch(url, {
      method: form.method,
      body: formData
    }).then((res) => {
      return res.json();
    }).then((json) => {
      if (json.success) {
        var ul = form.parentNode.querySelector('ul');
        var template = document.querySelector(".list-item");

        var clone = document.importNode(template.content, true);
        var li = clone.querySelector('li');
        li.textContent = json.vehicle.v_fullname;
        ul.append(clone);
        // si on est sur la page vÃ©hicle il faut rajouter une ligne au tableau
        form.reset();
      } else {
        alert(json.error);
      }
    });
  },
  deleteAccessory: function () {
    console.log(this._elem.href);
    fetch(this._elem.href, {
      method: 'DELETE',
      //headers: headers,

    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On change le nom


        } else {

        }

      });
  },
  match: function () {
    const select = this._elem;
    const parent = document.getElementById(this._elem.getAttribute('form'));

    if (this._ev.type == 'change') {
      const method = parent.getAttribute('data-onchange');
      this[method]();

    } else {
      if (select.classList.contains('hydrated')) return;
      else {
        // On va chercher la liste des vÃ©hicules correspondant Ã  l'univers et Ã  la marque
        const formData = new FormData(parent);
        const queries = [
          { name: "universe", "value": formData.get('universe') },
          { name: "brand", "value": formData.get('brand') }
        ];
        var filter = queries.filter(query => query.value.length > 0);
        if (filter.length < 2) return;
        const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');
        let url = parent.action + '?' + asString;

        fetch(url, {
          method: 'GET'
        })
          .then((res) => {
            return res.json()
          })
          .then((json) => {
            if (json.success) {
              const vehicles = json.list;
              let fragment = document.createDocumentFragment();
              let childNode = select.firstElementChild;
              while (select.firstChild) { select.removeChild(select.firstChild) }
              fragment.appendChild(childNode);
              select.appendChild(fragment);
              vehicles.forEach((vehicle) => {
                let option = document.createElement('option');
                option.textContent = vehicle.text;
                option.value = vehicle.value;
                fragment.appendChild(option);
              });
              select.appendChild(fragment);
              select.disabled = null;
              select.classList.add('hydrated');
            } else {
              alert(json.error);
            }
          });
      }
    }

  },
  matchUpdate: function () {
    const select = this._elem;
    const parent = document.getElementById(this._elem.getAttribute('form'));
    const formData = new FormData(parent);
    const object = {
      universe: formData.get('universe'),
      brand: formData.get('brand'),
      model: formData.get('model'),
      vehicle: select.value
    }
    const json = JSON.stringify(object);
    let url = parent.getAttribute('data-update');

    fetch(url, {
      method: 'PUT',
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {

        } else {
          alert(json.error);
        }
      });
  },
  matchUniverse: function () {
    const parent = document.getElementById(this._elem.getAttribute('form'));
    var formData = new FormData(parent);
    const object = {
      universe: formData.get('universe'),
      brand: formData.get('brand'),
      model: formData.get('model')
    }
    const json = JSON.stringify(object);
    let url = parent.getAttribute('data-universe-update');
    fetch(url, {
      method: 'PUT',
      body: json
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          // On autorise le select des vÃ©hicules Ã  Ãªtre de nouveau hydratÃ©
          parent.querySelector('select[name="vehicle"]').classList.remove('hydrated');
        } else {
          alert(json.error);
        }
      });
  },
  matchMillesim: function () {
    const select = this._elem;
    const parent = this._elem.parentNode;
    let id = select.value;
    let formData = new FormData(parent);
    formData.delete('universe');
    formData.delete('brand');
    let url = parent.getAttribute('data-create');

    fetch(url, {
      method: 'POST',
      body: formData
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.success) {
          console.log(`Ajout de millesims pour le vehicule: - ${id}`);

        } else {
          alert(json.error);
        }
      });
  },
  millesims: function () {
    let id = this._elem.value;
    //let uri = this._elem.getAttribute('data-uri');
    let url = new URL(this._elem.getAttribute('data-uri'));
    let params = new URLSearchParams(url.search);
    console.log(url)
    // option selectionnée 
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    let price = option.getAttribute('data-price');
    // recalculer le prix 
    // il faut modifier kit_type


    //Add a second foo parameter.
    params.append('vehicle', id);
    url.search = params;
    fetch(url.href, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {


        if (json.millesims.length > 0) {

          let select = document.getElementById('millesim'); // cible	
          select.disabled = 'disabled';
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          json.millesims.forEach((millesim) => {
            let option = document.createElement('option');
            option.textContent = millesim.year;
            option.value = millesim.id;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
          item.price(id);
        }
      });
  },
  products: function () {
    let id = this._elem.value;
    let uri = this._elem.getAttribute('data-uri').replace(':vehicle', id);
    let url = new URL(uri);
    let params = new URLSearchParams(url.search);

    // Le design et le coloris			
    if (this._elem.hasAttribute('data-design')) params.append('design', this._elem.getAttribute('data-design'));
    if (this._elem.hasAttribute('data-color')) params.append('color', this._elem.getAttribute('data-color'));

    url.search = params;


    fetch(url.href, {
      method: 'GET'
    })
      .then((res) => {
        return res.json()
      })
      .then((json) => {
        if (json.products.length > 0) {
          let kitType = document.getElementById('kit-type');
          let fragment = document.createDocumentFragment();
          let value = kitType.value;

          //let childNode = kitType.firstElementChild;
          while (kitType.firstChild) { kitType.removeChild(kitType.firstChild) }
          //fragment.appendChild(childNode);								

          // let index = kitType.selectedIndex;
          // let opt = kitType.options[index];
          kitType.disabled = 'disabled';

          json.products.forEach((product) => {
            let option = document.createElement('option');
            option.textContent = product.text;
            option.value = product.price;
            option.setAttribute('data-product', product.product);
            option.setAttribute('data-id', product.kit_type);
            option.setAttribute('data-name', product.type_name);
            if (product.price == value) option.selected = true;
            fragment.appendChild(option);
          });

          kitType.appendChild(fragment);
          kitType.disabled = null;

        }
        if (json.millesims.length > 0) {
          let select = document.getElementById('millesim'); // cible	
          select.disabled = 'disabled';
          let fragment = document.createDocumentFragment();
          let childNode = select.firstElementChild;
          while (select.firstChild) { select.removeChild(select.firstChild) }
          fragment.appendChild(childNode);
          json.millesims.forEach((millesim) => {
            let option = document.createElement('option');
            option.textContent = millesim.year;
            option.value = millesim.id;
            fragment.appendChild(option);
          });
          select.appendChild(fragment);
          select.disabled = null;
          //item.price(id);			
        }
      });
  },
  years: function() {
    const index = this._elem.selectedIndex;
    const option = this._elem.options[index];
    console.log(option.getAttribute('data-uri'));
  }
};

const seatCover = {
  _elem: null,
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  custom: function () {
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
        if (json.success) {
          const data = json['seat-cover'];
          console.log(`Récupération des infos de la housse: - ${millesim}`);
          var container = document.getElementById('seat-cover');
          var tpl = document.getElementById('tpl-seatcover');
          var clone = document.importNode(tpl.content, true);
          // Ajouter les différentes options
          var seatopts = clone.querySelector('.seat-opts');
          if (data.comfort_foam != null) {
            seatopts.querySelector('.opt-foam').classList.remove('hide');
          }
          if (data.installation != null) {
            seatopts.querySelector('.opt-install').classList.remove('hide');

          }

          clone.querySelector('img').src = data.visual;
          while (container.firstChild) { container.removeChild(container.firstChild) }
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
  customColor: function () {
    const radio = this._elem;
    const sibling = radio.nextElementSibling;
    if (sibling.classList.contains('color-unselect')) sibling.classList.remove('color-unselect');

    //console.log(this._elem.value);
    const parent = radio.parentNode;
    parent.querySelectorAll('label').forEach(function (label) {
      if (label.getAttribute('for') != radio.id)
        label.classList.add('color-unselect');
    });
  }
};