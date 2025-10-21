const contact = {
    _elem: null,
    _ev: null, 
    _map: null,  
    _accessToken: 'pk.eyJ1Ijoic29maWFkcmlhaSIsImEiOiJjamp6a3VhMXUwNnV3M3BxbjVpeHpzNnA3In0.D9sGAvstW7PyPhK2P7d92g',
    setMap: function(map){      
        this._map = map;
    }, 
    setElem: function(elem){
    this._elem = elem;
    },  
    setEvent: function(event){
        this._ev = event;
    },
    init: function(){
             
        // MapBox
        mapboxgl.accessToken = this._accessToken;
        // This adds the map to your page
        const mapBoxGl = new mapboxgl.Map({
            // container id specified in the HTML
            container: 'map',
            // style URL
            style: 'mapbox://styles/mapbox/streets-v9',
            // initial position in [lon, lat] format lyon
            center: [4.953954958657846, 46.43814531445884],
            // initial zoom
            zoom: 5.4
              //scrollZoom: false
        });
        const marker1 = new mapboxgl.Marker({ color: 'black' })
        .setLngLat([4.953954958657846, 46.43814531445884])
        .addTo(mapBoxGl);
        //Initialisation de la carte
        mapBoxGl.on('load', function(e) {            
            this._map = mapBoxGl;
            //this._map.resize();
        });       
    },
    send: async function(){
        const formData = new FormData(this._elem);
        const response = await fetch(this._elem.action, {method: 'POST', body: formData}); 
        if(response.ok){
            const json = await response.json();
            //let info = JSON.parse(json.info);
            console.log(json);
            let modal = document.getElementById('contact-success');
            modal.querySelector('p.title').textContent = json.info.designation;
            modal.querySelector('div.content').innerHTML = json.info.description;
            modal.classList.add('visible');

        }               
    } 
    
}

export default contact;
window.addEventListener("DOMContentLoaded", function(e) { 
     contact.init();
});