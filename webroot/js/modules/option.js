const option = {
    _elem: null,
    _ev: null,
    lang:document.documentElement.lang,
    currency: document.getElementById('item-currency') ? document.getElementById('item-currency').getAttribute('content') || 'EUR' : 'EUR',
    setElem: function(elem){
        this._elem = elem;
    },  
    setEvent: function(event){
        this._ev = event;
    },
    close: function() {
        const _id = this._elem.hash || ('#' + this._elem.id) || ('#' + this._elem.getAttribute('data-modal'));  
        const modal = document.querySelector(_id); 
        if(!modal) return;
        modal.classList.toggle('visible');
        var popins = localStorage.getItem('popins');
        if(popins === null) popins = JSON.parse('{}');
        else popins = JSON.parse(popins);

        const keys = Object.keys(popins);
        
        const inArray = (element) => element === _id;
        const found = keys.findIndex(inArray);
        if(found > -1) console.log(`popin ${_id} found`);
        
        else {
            popins[_id] = 1;
            localStorage.setItem('popins', JSON.stringify(popins));
        }        
        return;      
    },
    popin: function(){  
        console.log('click label option');

        let input = document.getElementById(this._elem.getAttribute('for'));  
        console.log(input);      
        if(!input.checked) {
            input.checked = true;
        }
        console.log(`input ${input.id} : ${input.checked}`);      
        const popin = document.getElementById(this._elem.getAttribute('data-modal'));
        popin.querySelector(`a[href="#${this._elem.getAttribute('data-tab')}"]`).click();
        popin.classList.add('visible');
        this.update();       
    },   
    miniPlates: function(){        
        const selected = this._elem.options[this._elem.selectedIndex];
        document.getElementById('mini-plates-id').value = selected.getAttribute('data-id');
        document.getElementById('mini-plates-name').value = selected.getAttribute('data-name');
        this.update();
    },
    hubStickers: function(){
        const selected = this._elem.options[this._elem.selectedIndex];
        document.getElementById('hubs-stickers-id').value = selected.getAttribute('data-id');
        document.getElementById('hubs-stickers-name').value = selected.getAttribute('data-name');
        this.update();
    }, 
    doorStickers: function(){
        console.log('door stickers !');
       
        document.getElementById('door-sticker-id').value = this._elem.getAttribute('data-id');
        document.getElementById('door-sticker-name').value = this._elem.getAttribute('data-name');
       
        this.update();
    },
    rimStickers: function(){       
        document.getElementById('rim-sticker-id').value = this._elem.getAttribute('data-id');
        document.getElementById('rim-sticker-name').value = this._elem.getAttribute('data-name');       
        this.update();
    },
    update: function(){
        const prices = document.querySelectorAll('.cost');
        var cost = 0.00;
        const inputQty = document.getElementById('item-qty');
     //console.log(prices);
        for(const e of Array.from(prices)){
            if(e.nodeName == 'SPAN') continue;            
            const q = inputQty ? inputQty.value : 1;
            const qty = e.classList.contains('qty-depend') ? Number(q) : 1; 
            if(e.nodeName == 'INPUT' && e.checked) cost = cost + (Number(e.value) * qty);
            if(e.nodeName == 'SELECT') cost = cost + (Number(e.value) * qty);      
        }
        document.getElementById('item-cost').textContent = this.monetary(cost);   
    },
    picker: function(){       
        const picker = document.getElementById(this._elem.getAttribute('data-picker'));
        picker.classList.toggle('visible');
    },
    typoPicker: function(){
        const picker = document.getElementById(this._elem.getAttribute('data-picker'));
        picker.classList.toggle('visible');
    },
    pickColor: function(){
        const input = document.getElementById(this._elem.getAttribute('data-input'));
        input.value = this._elem.value;
        const label = input.parentNode.querySelector('label');
        label.textContent = this._elem.parentNode.querySelector('label').textContent;
        label.setAttribute('data-color', this._elem.parentNode.querySelector('label').getAttribute('data-color'));
        input.click();
    },
    setTypo: function(){
        const typo = this._elem.querySelector('img').src;
        const input = document.getElementById(this._elem.getAttribute('data-input'));
        input.value = typo;
        const inputParent = input.parentNode;
        inputParent.querySelector('img').src = typo;
        inputParent.click();
    },
    setLogo: function(){
        const logo = this._elem.querySelector('img').src;
        const input = document.getElementById(this._elem.getAttribute('data-input'));
        input.value = logo;
        const inputParent = input.parentNode;
        inputParent.querySelector('img').src = logo;
        inputParent.click();
    },
    uploadSponsor: function(){
        let file = this._elem.files[0];
        let place = this._elem.getAttribute('data-place');
        // console.log(`place: ${place}, file: ${file.name}`);    
        let span = this._elem.parentNode.querySelector('span.filename');
        let i = document.createElement('i');
        i.className = 'delete-file click';
        i.setAttribute('data-ctrl', 'option.deleteSponsor');

        span.textContent = file.name;
        span.appendChild(i);
    },
    deleteSponsor: function(){
        const span = this._elem.parentNode;
        const fileInput = span.parentNode.querySelector('input[type="file"]');
        fileInput.value = '';
        span.innerHTML = '';
    },
    monetary: function (number,  maximumFractionDigits = 2) {
        // ex i18n : 'de_DE'on remplace le "_" par "-"
        // ex currency : 'EUR'  
        if (this.lang == 'fr') this.lang = 'fr_FR';
        return new Intl.NumberFormat(this.lang.replace('_', '-'), { style: 'currency', currency: this.currency, maximumFractionDigits: maximumFractionDigits }).format(number);
    }   
}   
export default option;