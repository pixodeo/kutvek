const sales = {
    _elem: null,
    _ev: null,
    _form: null,
    setEvent: function (event) {
        this._ev = event;
    },
    setElem: function (elem) {
        this._elem = elem;
    },
    filter: async function(){
      
        this._form = this._elem.form;
        const formData = new FormData(this._form);
        const url = new URL(this._form.action || window.location.href);
       

        const params = new URLSearchParams();
        formData.forEach((val, key) => {
            params.append(key, val);
        });
        params.append('uri', this._form.getAttribute('data-uri'));
        const res = await fetch(url.pathname + '?' + params.toString(),{
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if(!res.ok){
            const json = await res.json();
            
        } else {
            const text = await res.text();
            let frag = document.createRange().createContextualFragment(text);
           

            let oldChild = document.getElementById('products');
            oldChild.parentNode.replaceChild(frag.firstChild, oldChild);
            let oldPagination = document.querySelector('div.pagination');
            oldPagination.parentNode.replaceChild(frag.querySelector('div.pagination'), oldPagination);
            
             /*history.replaceState({}, '', newUrl);*/
        }
        

    }
    
}



export default sales;