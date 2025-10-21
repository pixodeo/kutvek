const product = {
  _elem: null,
  _ev: null,
  _cookies: {},
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  filterByBrands: function () {
    console.log('by brands');

    window.onpopstate = function (event) {
      console.log("location: " + document.location + ", state: " + JSON.stringify(event.state));
      console.log(window.location)
    };

    let filterForm = document.getElementById('filter');
    let formData = new FormData(filterForm);
    //let b = app.formdataToArray(formData);

    // Juste une marque
    let b = [this._elem.value];
    let url = filterForm.action + '?brands=' + JSON.stringify(b);
    let bli = this._elem.parentNode;
    let bul = bli.parentNode;
    if (bli.classList.contains('reset')) {
      // window.history.back(-1);

      bul.querySelectorAll('li').forEach((e) => {
        e.classList.remove('hide');
        //e.querySelector('ul')
      });
      return;
    }
    fetch(url, {
      method: 'GET',
    })
      .then((res) => {
        return res.json();
      })
      .then((json) => {
        if (json.success && json.products.length > 0) {

          // Modification de l'historique
          const pathName = window.location.pathname;
          const brand = json.products[0].b_name.toLowerCase();
          window.history.pushState({ filtering: 'brand' }, 'brand filtering', pathName + '/' + brand);

          // On est dans une liste ul , on met une classe a la li qui contient l'input
          bul.querySelectorAll('li').forEach((e) => {
            if (e === bli || e.classList.contains('reset')) return;
            e.classList.add('hide');
          });

          // On réactualise les sous liste
          bli.querySelectorAll('ul > li').forEach((l) => {
            l.classList.remove('hide');
          });

          let container = document.getElementById('cards-container');
          while (container.firstChild) { container.removeChild(container.firstChild) }

          let tpl = document.getElementById('card-tpl');

          // Création d'une carte pour chaque produit
          json.products.forEach((card) => {
            let clone = document.importNode(tpl.content, true);

            let figure = clone.querySelector('figure');
            let img = clone.querySelector('img');
            let title = clone.querySelector('h3');
            let price = clone.querySelector('figcaption > p > span');
            let link = clone.querySelector('a');

            figure.id = card.id;
            figure.setAttribute('data-brand', card.brand);
            figure.setAttribute('data-design', card.design);
            figure.setAttribute('data-color', card.color);

            img.src = card.visual;
            title.textContent = card.title;
            price.textContent = card.price_0;
            link.href = '/' + card.url;

            container.appendChild(clone);
          });
          if (json.vehicles.length > 0) {
            let tpl2 = document.getElementById('vehicle-checkbox-tpl');
            json.vehicles.forEach((vehicle => {
              let ul = document.querySelector('ul[data-brand="' + vehicle.brand + '"');
              //while(ul.firstChild) {ul.removeChild(ul.firstChild)}
              let _id = 'v-' + vehicle.value;
              if (ul.querySelector('#' + _id)) return;
              let clone2 = document.importNode(tpl2.content, true);
              let li = clone2.querySelector('li');
              li.setAttribute('data-brand', vehicle.brand);
              li.setAttribute('data-vehicle', vehicle.value);
              let checkbox = clone2.querySelector('input');
              let label = clone2.querySelector('label');
              checkbox.id = _id;
              checkbox.setAttribute('data-brand', vehicle.brand);
              checkbox.value = vehicle.value;
              label.textContent = vehicle.text;
              label.setAttribute('for', _id);
              ul.appendChild(clone2);

            }));
          }
        }
      });
  },
  filterByVehicle: function () {
    console.log('by vehicle');
    window.onpopstate = function (event) {
      console.log("location: " + document.location + ", state: " + JSON.stringify(event.state));
      console.log(window.location)
    };

    let form = document.getElementById('filter');
    if (this._elem.checked) {
      let url = this._elem.getAttribute('data-uri').replace(':vehicle', this._elem.value);
      fetch(url, {
        method: 'GET',
      }).then((res) => {
        return res.json();
      }).then((json) => {
        if (json.success && json.products.length > 0) {

          // Modification de l'historique
          const pathName = window.location.pathname;
          const vehicle = json.products[0].v_name.toLowerCase().replaceAll(' ', '-');
          window.history.pushState({ filtering: 'vehicle' }, 'vehicle filtering', pathName + '/' + vehicle);

          let container = document.getElementById('cards-container');
          while (container.firstChild) { container.removeChild(container.firstChild) }

          let tpl = document.getElementById('card-tpl');

          // Création d'une carte pour chaque produit
          json.products.forEach((card) => {
            let clone = document.importNode(tpl.content, true);

            let figure = clone.querySelector('figure');
            let img = clone.querySelector('img');
            let title = clone.querySelector('h3');
            let price = clone.querySelector('figcaption > p > span');
            let link = clone.querySelector('a');

            figure.id = card.id;
            figure.setAttribute('data-brand', card.brand);
            figure.setAttribute('data-design', card.design);
            figure.setAttribute('data-color', card.color);

            img.src = card.visual;
            title.textContent = card.title;
            price.textContent = card.price_0;
            link.href = '/' + card.url;

            container.appendChild(clone);
          });
        }
      });

      const lis = document.querySelectorAll('li[data-brand="' + this._elem.getAttribute('data-brand') + '"]');
      //const lis = document.querySelectorAll('li[data-brand]');
      lis.forEach((li) => {
        if (li.getAttribute('data-vehicle') == this._elem.value) return;
        li.classList.add('hide');
      });

    } else {
      const lis = document.querySelectorAll('li[data-brand="' + this._elem.getAttribute('data-brand') + '"]');

      lis.forEach((li) => {
        if (li.getAttribute('data-vehicle') == this._elem.value) return;
        li.classList.remove('hide');
      });

    }
    //let vehicles = form.querySelectorAll('input[name="vehicles[]"]');
  }
};