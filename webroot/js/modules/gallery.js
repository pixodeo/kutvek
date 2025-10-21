const gallery = {
  _ev: null,
  _elem:null,
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
export default gallery;