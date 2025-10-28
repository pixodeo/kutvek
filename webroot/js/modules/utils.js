const utils = {
	_elem: null,		
	setElem: function(elem){
	this._elem = elem;
	},	
	setEvent: function(event){
		this._ev = event;
	},
	_reference: document.body || document.documentElement,
	_to:null,	
	_duration: 300,
	_start: 0,
	scrollTo: function(){        
        this._startDate = new Date();
        this._start = this._reference.scrollTop;
        this._to = document.querySelector(this._elem.hash).scrollTop;   
        this.animateScroll();
    },
	animateScroll: function(){
               
        var change = this._to - this._start;        
        var _currentDate = new Date();  
        var currentTime = _currentDate - this._startDate;
        this._reference.scrollTop = parseInt(this.easeInOutQuad(currentTime, this._start, change, this._duration));
        if(currentTime < this._duration) {
            requestAnimationFrame(this.animateScroll());
        }
        else {
            this._reference// t = current time, b = start value, c = change in value,d = duration.scrollTop = this._to;
        }
    },    
    easeInOutQuad: function(t, b, c, d) {
        // t = current time, b = start value, c = change in value,d = duration
        t /= d/2;
        if (t < 1) return c/2*t*t + b;
        t--;
        return -c/2 * (t*(t-2) - 1) + b;
    },
    popin: function(){
        console.log(`data-modal: ${this._elem.getAttribute('data-modal')}`); 
        console.log(`hash: ${this._elem.hash}`); 
        console.log(`id: ${this._elem.id}`);

        let _id = this._elem.hash || this._elem.querySelector('id') ||  this._elem.getAttribute('data-modal'); 
        _id = _id.replace('#', '');
        const modal = document.getElementById(_id); 
        if(!modal) return;
        modal.classList.toggle('visible');
        return;      
    },
    popup: function()
    {       
        const popup = window.open(this._elem.getAttribute('data-uri') || this._elem.href, 'example', 'width=1024,height=768', 'location=no');  
    },
   /* autoHeight: function(){
        this._elem.parentNode.parentNode.classList.toggle('auto-height');
    },*/
    autoHeight: function(){
        document.getElementById('form-filter').classList.add('visible');
    },
    picker: function() {
        const picker = document.querySelector(this._elem.hash);
        picker.classList.toggle('visible');
    }
}
export default utils;