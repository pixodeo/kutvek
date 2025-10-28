const delivery = {
    _elem: null,
    _ev: null, 
    _map: null, 
    _lang:document.documentElement.lang, 
    _currency : document.documentElement.getAttribute('data-currency')||'EUR',
    _accessToken: 'pk.eyJ1Ijoic2ViYXN0aWVuLWt1dHZlayIsImEiOiJjbWdvdmk3MGsxeTExMmtxcWJwN2VhZWFuIn0.w2lWVXJIqQkSfYdD4zqFaQ',
    setMap: function(map){    	
    	this._map = map;
    }, 
    setElem: function(elem){
    this._elem = elem;
    },  
    setEvent: function(event){
        this._ev = event;
    },
    initMap: function(){
        if(this._elem && this._elem.hasAttribute('data-map')) return;        
        // MapBox
        mapboxgl.accessToken = this._accessToken;
        // This adds the map to your page
        const mapBoxGl = new mapboxgl.Map({
            // container id specified in the HTML
            container: 'map',
            // style URL
            style: 'mapbox://styles/mapbox/streets-v9',
            // initial position in [lon, lat] format lyon
            center: [4.835658999999964, 45.764043],
            // initial zoom
            zoom: 4
              //scrollZoom: false
        });
        //Initialisation de la carte
        mapBoxGl.on('load', function(e) {
        	console.log('Mapbox is loaded');
            this._map = mapBoxGl;
        });
    },
    points: async function(){
  		const formData = new FormData(this._elem);   	
      	const queries = [   	         
            {name: "zipcode", "value": formData.get('postal_code')},
            {name: "city", "value": formData.get('admin_area_2')}, 
            {name: "countryCode", "value": formData.get('country_code')}           
        ];
        var filter = queries.filter(query=> query.value.length > 0);            
        const asString = filter.map(x => `${encodeURIComponent(x.name)}=${encodeURIComponent(x.value)}`).join('&');  
        let response = await fetch('https://demo.kutvek-kitgraphik.com/cart/stores' + '?' + asString);
        if(response.ok){
            const json = await response.json();
            var stores = {"type": "FeatureCollection"};
            stores.features = json.features;  
            if(delivery._map !== null) {            
                if(delivery._map.getSource('places') != undefined){
                        delivery._map.removeSource('places');
                    }
                    delivery._map.addSource('places', {
                        type: 'geojson',
                        data: stores
                    });               
                    delivery.addMarkers(stores);
                    // Construit la liste
                    delivery.buildLocationList(stores);                
                    delivery._map.flyTo({
                        center: json.features[0].geometry.coordinates,
                        duration: 1200,
                        zoom: 12
                    });
                return;
            } else {       
                // MapBox
                mapboxgl.accessToken = this._accessToken;
                // This adds the map to your page
                const mapBoxGl = new mapboxgl.Map({
                    // container id specified in the HTML
                    container: 'map',
                    // style URL
                    style: 'mapbox://styles/mapbox/streets-v9',
                    // initial position in [lon, lat] format lyon
                    center: [4.835658999999964, 45.764043],
                    // initial zoom
                    zoom: 4
                      //scrollZoom: false
                });       
                //Initialisation de la carte
                mapBoxGl.on('load', function(e) {           
                    if(mapBoxGl.getSource('places') != undefined){
                            mapBoxGl.removeSource('places');
                    }
                    mapBoxGl.addSource('places', {
                            type: 'geojson',
                            data: stores
                    });
                    delivery.setMap(mapBoxGl);
                    delivery.addMarkers(stores);
                    // Construit la liste
                    delivery.buildLocationList(stores);                
                    mapBoxGl.flyTo({
                        center: json.features[0].geometry.coordinates,
                        duration: 1200,
                        zoom: 12
                    });             
                }); 
            }
        }       
  	},    
    addMarkers: function(stores)
    {
    	const map = this._map;
    	
    	// Gestion des markers customisés, des events au clic sur la carte
        stores.features.forEach(function(marker, i) {
            // Create a div element for the marker
            var el = document.createElement('div');
            /* Assign a unique `id` to the marker. */
            el.id = "marker-" + i;
            // Add a class called 'marker' to each div
            el.className = 'marker-chrono-relais';

            // By default the image for your custom marker will be anchored
            // by its center. Adjust the position accordingly
            // Create the custom markers, set their position, and add to map
            new mapboxgl.Marker(el, { offset: [0, -23] })
            .setLngLat(marker.geometry.coordinates)
            .addTo(map);
            el.addEventListener('click', function(e) {
               var activeItem = document.querySelectorAll('div.item.active');
                // 1. Fly to the point
                delivery.flyToStore(marker);
                // 2. Close all other popups and display popup for clicked store
                delivery.createPopUp(marker);
                // 3. Highlight listing in sidebar (and remove highlight for all other listings)
                e.stopPropagation();
                if (activeItem[0]) {
                   activeItem[0].classList.remove('active');
                }
               var listing = document.getElementById('listing-' + i);
               listing.classList.add('active');
            });
        });
    },    
    buildLocationList: async function(data) {
        // Les points de vente sous forme de liste dans la div #listings 
        var listings = document.getElementById('listings');
        var template = document.getElementById('listing-tpl');
        const lght = data.features.length;
        while (listings.firstChild) {listings.removeChild(listings.firstChild)}  
        /**
         * <div class="item" id="listing-6">
            <div id=""  data-id="{id point relai}">
                <a href="#" class="title"></a>
                <p class="line-1"> </p>
                <p class="city"></p>
            </div>          
            <div class="opening">            
            </div>
            <div class="details">
                <input type="radio" name="delivery[cost]" data-ctrl="delivery.setAddress" value="0" id="" class="onchange" data-address="" required /> - <b>Gratuit</b>
            </div>
        </div>
         */
        for (let i = 0; i < lght; i++) 
        {
            var clone = document.importNode(template.content, true);
            var currentFeature = data.features[i];
            // Shorten data.feature.properties to just `prop` so we're not
            // writing this long form over and over again.
            var prop = currentFeature.properties;
            // Select the listing container in the HTML and append a div
            // with the class 'item' for each store
            var listing = clone.querySelector('div.item');           
            listing.id = 'listing-' + i;
            const div = listing.querySelector('div');
            div.id = `relay-${i}`;
            div.setAttribute('data-id', currentFeature.properties.id);
            
            // Create a new link with the class 'title' for each store
            // and fill it with the store address
            const link = div.querySelector('a');                    
            link.dataPosition = i;
            link.textContent = prop.name;

            // Ville + cp du pr
            div.querySelector('p.city').innerHTML = prop.postalCode + ' &#183; ' + prop.city;
            div.querySelector('p.line-1').innerHTML = prop.address_line_1;

            // Horaires d'ouverture
            const opening  = listing.querySelector('div.opening'); 
            opening.innerHTML = currentFeature.opening;

            const details = listing.querySelector('div.details');

            const radio = details.querySelector('input'); 
            const label = details.querySelector('label'); 

            radio.dataPosition = i;  
            radio.id = 'carrier-' + i;
            label.htmlFor = 'carrier-' + i;
            radio.setAttribute('data-address', currentFeature.properties.id);            
            radio.setAttribute('data-relay', '#' + currentFeature.properties.type + currentFeature.properties.id);     
            radio.setAttribute('data-type',  1);   
            radio.setAttribute('data-id', currentFeature.properties.id);                   
            radio.value = 0;
            /*if (prop.phone) {
              details.innerHTML += ' &middot; ' + prop.phoneFormatted;
            }*/
            //var label = details.querySelector('label.carrier');
            //label.htmlFor = 'carrier-' + i;
            
            // Au clic sur liens du listing
            link.addEventListener('click', function(e) {
              e.preventDefault();
                // Update the currentFeature to the store associated with the clicked link
              var clickedListing = data.features[this.dataPosition];
                // 1. Fly to the point associated with the clicked link
              delivery.flyToStore(clickedListing);
                // 2. Close all other popups and display popup for clicked store
              delivery.createPopUp(clickedListing);
                // 3. Highlight listing in sidebar (and remove highlight for all other listings)
              var activeItem = document.querySelectorAll('div.item.active');
              //console.log(activeItem);
              if (activeItem[0]) {
                  activeItem[0].classList.remove('active');
              }
              this.parentNode.classList.add('active');
            });

            /*selected.addEventListener('change', async function(e) {
              e.preventDefault();
               // Update the currentFeature to the store associated with the clicked link
              var clickedListing = data.features[this.dataPosition]; 
              let _module = await getModule();
              
                       
              // 1. Select the clicked relay
              _module.relayAddress(clickedListing, e);              
            });*/

            listings.appendChild(clone);
        }
    },
    flyToStore: function(currentFeature) {
    	let map = this._map;
        //  Recentre la carte sur le point de vente sélectionné
        map.flyTo({
        center: currentFeature.geometry.coordinates,
        duration: 1200,
        zoom: 17
        });
    },
    createPopUp: function(currentFeature) {
        // Affiche un popup sur le point de la carte sélectionné
        var popUps = document.getElementsByClassName('mapboxgl-popup');
        // Check if there is already a popup on the map and if so, remove it
        if (popUps[0]) popUps[0].remove();

        var popup = new mapboxgl.Popup({ closeOnClick: false })
        .setLngLat(currentFeature.geometry.coordinates)
        .setHTML('<h4>' + currentFeature.properties.name+ '</h4>' +
          '<p>' + currentFeature.properties.address_line_1 + '<br/>' +currentFeature.properties.postalCode + ' &middot; ' + currentFeature.properties.city + '</p>')
        .addTo(this._map);
    },
    addShippingAddress: async function(){
        const req = await fetch(this._elem.href, {method: 'GET', mode: 'cors', credentials: 'include'});
        if(req.ok){
            const text = await req.text();
            const old = document.querySelector('#new-address');
            let modal = document.createRange().createContextualFragment(text);
            if(old) old.parentNode.replaceChild(modal, old);
            else document.body.appendChild(modal);
            document.querySelector('#new-address').classList.add('visible');
            return;
        }
        const json = await req.json();
        // console.debug(json);
    },
    saveShippingAddress: async function(){
        const body = new FormData(this._elem);
        const req = await fetch(this._elem.action, {method: 'POST', mode: 'cors', credentials: 'include', body: body});
        if(req.ok && req.status === 201){
            //document.location.reload();
        }
    },
    countryWithStates: function(){
        let msg;
        const selected = this._elem.options[this._elem.selectedIndex];
        const field = document.getElementById('admin-area-1');
        if(selected.getAttribute('data-with-states') > 0){
            field.disabled = false;
            field.parentNode.parentNode.classList.remove('hide');
            msg = 'Country with states';
        } 
        else {
            field.disabled = true;
            field.parentNode.parentNode.classList.add('hide');
            msg = 'Country without states';
        }
        document.getElementById('country-id').value = selected.getAttribute('data-country-id');
        console.info(msg);
    },
    setAddress: function(){
        const _id = this._elem.getAttribute('data-address');
        const address = document.querySelector(`[data-id="${_id}"]`);
        document.getElementById('address-id').value = _id;
        document.getElementById('type-id').value = this._elem.getAttribute('data-type');
        document.querySelector('div.shipping-address').innerHTML = address.innerHTML;
        document.querySelector('span.shipping-cost').textContent = this.monetary(this._elem.value, 2);
        document.querySelector('input[name="amount[shipping]"] ').value = this._elem.value;
        const inputs = document.querySelectorAll('.input-amount');
        const totalAmount = document.getElementById('total-amount');
        let total = 0.00;
        inputs.forEach(i => {
            const value = parseFloat(i.value);
            total = total + value;
        });
        totalAmount.textContent = this.monetary(total, 2);
    },
    monetary: function (number, maximumFractionDigits = 2) {
        let l10n = this._lang == 'fr' ? 'fr-FR' : this._lang.replace('_', '-');   
        return new Intl.NumberFormat(l10n, { style: 'currency', currency: this._currency, maximumFractionDigits: maximumFractionDigits }).format(number);
    }
}
export default delivery;

document.addEventListener("DOMContentLoaded", function(e) {    
    delivery.setElem(document.getElementById('search-place'));
    delivery.points();
    //document.getElementById('relay-method').checked = true;
});
