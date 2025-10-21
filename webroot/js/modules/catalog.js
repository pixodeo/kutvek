const catalog = {
    _elem: null,
    _ev: null,
    setElem: function(elem){this._elem = elem;},  
    setEvent: function(event){this._ev = event;},
    loadFilterData: async function(){
        if(this._elem.getAttribute('data-fetched') == 1) return;
        const url = new URL(this._elem.getAttribute('data-uri') || window.location.href);            
        const res = await fetch(url.href, { method: 'GET', mode: 'cors', credentials: 'include',headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        if(res.ok){
            let ul;
            const json = await res.json();
            const filter = this._elem.id;            
            if (window.innerWidth < 1024) {
                const modalName = this._elem.getAttribute('data-modal');
                const modal = document.getElementById(modalName);
                modal.classList.add('visible');
                ul = modal.querySelector('div > ul');
            } else {ul = this._elem.parentNode.querySelector('div > ul');}
            const tpl = document.getElementById(`filter-${filter}`);
            let fragment = document.importNode(tpl.content, true);
            let li = fragment.querySelector('li');
            while(ul.firstChild) {ul.removeChild(ul.firstChild)}    
            this._elem.setAttribute('data-fetched', 1);
            const _inputs = [];
            json.data.forEach(property => {                
                let _clone = li.cloneNode(true);
                let _input = _clone.querySelector('input');                     
                let _label = _clone.querySelector('label')
                _input.id = `${filter}-${property.id}`;
                _input.value = property.id;                        
                _label.htmlFor = `${filter}-${property.id}`;
                _label.textContent = `${property.name}`;
                _input.setAttribute('data-ctrl', 'catalog.filter');                                          
                ul.appendChild(_clone);
                this.checkFilters();  
            });        
        }
    },
    filterData: function(filter){
        if(filter.hasAttribute('data-fetched')) {
            // données déjà existantes 
            if (window.innerWidth < 1024) {
                const modalName = filter.getAttribute('data-modal');
                    const modal = document.getElementById(modalName);
                    modal.classList.add('visible');
            }
        } else {
            return fetch(filter.getAttribute('data-uri'), 
                {method: 'GET'}
            ).then((res) => {
                return res.json()
            }).then((data) => {         
                if(data.success){
                    let ul;
                    // Affichage des filtres dans une modal pour les versions mobiles
                    if (window.innerWidth < 1024) {
                        const modalName = filter.getAttribute('data-modal');
                        const modal = document.getElementById(modalName);
                        modal.classList.add('visible');
                        ul = modal.querySelector('div > ul'); 
                    } else {
                        ul = filter.parentNode.querySelector('div > ul');
                    }    
                    const tpl = document.getElementById(`filter-${data.filter}`);
                    let fragment = document.importNode(tpl.content, true);
                    while(ul.firstChild) {ul.removeChild(ul.firstChild)}    
                    filter.setAttribute('data-fetched', 1);
                    
                    return Promise.resolve();                       
                }
            }).catch((error) => {
                return Promise.reject(error);
            });
        }
    },
    filter: async function () {        
        const url = new URL(window.location.href);
        const formData = new FormData(this._elem.form);
        const params = new URLSearchParams();
        formData.forEach((val, key) => {
            // Réunir les valeurs sous une même clé séparées par des virgules
            if (url.searchParams.has(key)) {               
                let actualParams = url.searchParams.getAll(key);
                actualParams.push(val);
                url.searchParams.set(key, actualParams.join(','));
            } else {
                url.searchParams.append(key, val);
            }
        });
        if (url.searchParams.size === 0) {
            url.search = '';            
            document.location.reload();
            return;
        }       
        const req = await fetch(url,{ method: 'GET', mode: 'cors', credentials: 'include',headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        if(req.status === 200){
            const json = await req.json();
            let frag = document.createRange().createContextualFragment(json.cards); 
            let frag_pagination = document.createRange().createContextualFragment(json.pagination);                
            const products = document.getElementById('products'); 
            const parent = products.parentNode;
            const pagination = parent.querySelector('.pagination');           
            parent.replaceChild(frag.querySelector('#products'), products);
            parent.replaceChild(frag_pagination, pagination);
            this.obflinks();
            return;
        }
        const json = await req.json();
        console.log(json.msg);
        return; 
    },
    paginate: async function () { 
        const form = document.getElementById('form-filter');
        const url = new URL(form.action || window.location.href);
        const formData = new FormData(form);
        const current = this._elem.getAttribute('data-page');
        document.querySelector('.pagination').setAttribute('data-current', current);
        console.log('page');        
        formData.forEach((val, key) => {
            // Réunir les valeurs sous une même clé séparées par des virgules
            if (url.searchParams.has(key)) {               
                let actualParams = url.searchParams.getAll(key);
                actualParams.push(val);
                url.searchParams.set(key, actualParams.join(','));
            } else {
                url.searchParams.append(key, val);
            }
        });
        if(current > 0) url.searchParams.append('page', current);
        if (url.searchParams.size === 0) {
            url.search = '';            
            document.location.reload();
            return;
        }       
        const req = await fetch(url,{ method: 'GET', mode: 'cors', credentials: 'include',headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        if(req.status === 200){
            const json = await req.json();
            let frag = document.createRange().createContextualFragment(json.cards); 
            let frag_pagination = document.createRange().createContextualFragment(json.pagination);                
            const products = document.getElementById('products'); 
            const parent = products.parentNode;
            const pagination = parent.querySelector('.pagination');           
            parent.replaceChild(frag.querySelector('#products'), products);
            parent.replaceChild(frag_pagination, pagination);
            //history.replaceState({}, '', url);
            this.obflinks();
            return;
        }
        const json = await req.json();
        console.log(json.msg);
        return; 
    },
    /**
     * Coche les filtres récupérés via loadFilData et existants dans l'url
     */
    checkFilters: function(){
        const url = new URL(window.location.href); 
        const inputs = [];
        for (var p of url.searchParams) {
            //console.log(p)
            const ids = p[1].split(',');
            const map = ids.map((x) => `#${p[0]}-${x}`);
            //const iterator = map.values();
            inputs.push(...map);            
        }
        if(inputs.length > 0){
            inputs.forEach((id)=> {
                const input = document.querySelector(id);
                if(input && input.type == 'checkbox') input.checked = true;
            });
        }
    },
    obflinks: function(){
        const obflinks = document.querySelectorAll('.pagination a.obflink');
        //console.log(obflinks);
        for (let i=0, n=obflinks.length; i < n; ++i){
            const link = decodeURIComponent(window.atob(obflinks[i].getAttribute('data-obf')));           
            obflinks[i].setAttribute('data-ctrl', 'catalog.paginate');
            obflinks[i].classList.add('click');
        }
    } 
};
export default catalog;