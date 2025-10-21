const popin = {
    _elem: null,
    _ev: null,
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
    }
    
}
export default popin;