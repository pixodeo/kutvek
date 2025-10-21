const app = {
  _elem: null,
  _ev: null,
  _colorsCounter: 0,
  setElem: function (elem) {
    this._elem = elem;
  },
  setEvent: function (event) {
    this._ev = event;
  },
  modal: function(){    
    const modal = document.getElementById(this._elem.getAttribute('data-modal'));
    console.log(modal);
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
  menu: function () {
    const menu = document.getElementById(this._elem.getAttribute('data-target'));

    document.body.classList.toggle('opened');
    this._elem.classList.toggle('opened');
    if(this._elem.classList.contains('opened'))
    {
      this._elem.querySelector('span').textContent = 'close';
    } 
    else  this._elem.querySelector('span').textContent = 'menu';
  },
  closeAllSections: function() {
    if (this._elem.checked) {
      const depth = parseInt(this._elem.getAttribute('data-depth'), 10);
      let currentDepth;
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
  widget: function () {
    const id = this._elem.getAttribute('data-widget');

    document.querySelectorAll('ul.opt-list').forEach(widget => {
      if (widget.id == id) widget.classList.toggle('widget-hide'); else widget.classList.add('widget-hide');
    })

  },
  widgetSeatColor: function () {
    let selected = this._elem;
    let parent = selected.parentNode;
    let id = parent.getAttribute('data-input');
    document.querySelector('label[for="' + id + '"]').innerHTML = selected.innerHTML;
    document.getElementById(id).value = selected.getAttribute('data-value');
    parent.classList.toggle('widget-hide');
    this._colorsCounter = this._colorsCounter + 1;
    console.log(this._colorsCounter);
    if(this._colorsCounter >= 3)
    {
      document.getElementById('seat-options').classList.remove('disabled');
    }

  },
  showMore: function(){
		let hash = this._elem.href.split('#')[1];
		const content = document.getElementById(hash);
		let scrollHeight = content.scrollHeight + 'px';
		content.style.maxHeight = scrollHeight; 
		this._elem.classList.add('hide');
	},
	showLess: function(){		
		let hash = this._elem.href.split('#')[1];
		document.getElementById(hash).style.maxHeight = '176px'; 
		document.querySelector('[data-i18n="show-more"]').classList.remove('hide');
	},
}
export default app;