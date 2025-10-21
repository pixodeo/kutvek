const modal = {
    _elem: null,
    _ev: null,
    setElem: function(elem){
        this._elem = elem;
    },  
    setEvent: function(event){
        this._ev = event;
    },
    popin: function(){
        const _id = this._elem.hash || ('#' + this._elem.id) || ('#' + this._elem.getAttribute('data-modal'));  
        const modal = document.querySelector(_id); 
        if(!modal) return;
        modal.classList.toggle('visible');
        return;      
    },
    popup: function()
  	{    	
        const popup = window.open(this._elem.getAttribute('data-uri') || this._elem.href, 'example', 'width=1024,height=768', 'location=no');  
  	},
    fetch: async function(){
        const _id =('#' + this._elem.getAttribute('data-modal'));  
        const modal = document.querySelector(_id); 
        if(!modal) return;
        const content = modal.querySelector('.content');
        if(modal.classList.contains('visible')){
            content.innerHTML = '';
        }else{
            const res = await fetch(this._elem.href, {headers: { 'X-Requested-With': 'XMLHttpRequest' },method: 'GET'});
            const text = await res.text();
            modal.classList.add('visible');
            content.innerHTML = text;
        }
        
       
        return;      
    }
}
export default modal;