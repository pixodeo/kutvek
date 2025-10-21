document.addEventListener('DOMContentLoaded', function() {
  // --- Configuration ---
  const CONFIG = {
    defaultCenter: [46.200000, 5.220000],
    defaultZoom: 15,
    mapSelector: '#map-map',
    storeListSelector: '#map-store-list',
    postalCodeSelector: '#map-postal-code',
    citySelector: '#map-city',
    searchButtonSelector: '#map-search-button',
    mapLayoutSelector: '.map-layout' // Nouveau sÃ©lecteur pour le conteneur principal
  };

  let map;
  let markers = [];
  let currentFilters = { postalCode: '', city: '' };
  let STORES = [];
  let isMapInitialized = false;
  let mapLayoutElement; // Pour stocker l'Ã©lÃ©ment du layout
  let selectedStoreId = null; // ID du magasin sÃ©lectionnÃ©

  // --- Fonction principale de recherche ---
  function performSearch() {
    currentFilters.postalCode = document.querySelector(CONFIG.postalCodeSelector).value;
    currentFilters.city = document.querySelector(CONFIG.citySelector).value;

    // Afficher la section carte/liste
    if (mapLayoutElement) {
      mapLayoutElement.classList.remove('hidden');
      // S'assurer que le conteneur de la carte a une hauteur s'il Ã©tait masquÃ© et que la carte doit s'initialiser
      const mapContainer = document.querySelector(CONFIG.mapSelector);
      if (mapContainer && !mapContainer.offsetHeight && !isMapInitialized) {
        mapContainer.style.height = '500px'; // Hauteur par dÃ©faut si nÃ©cessaire
      }
    }

    if (!isMapInitialized) {
      loadAndInitializeData();
    } else {
      updateStoresListAndMarkers();
    }
  }

  // --- Chargement des donnÃ©es et initialisation (pour la premiÃ¨re recherche) ---
  function loadAndInitializeData() {
    fetch('/js/data.json')
      .then(response => response.json())
      .then(data => {
        STORES = data.map(store => ({
          id: store.id,
          name: store.name,
          address: `${store.street}${store.house_number ? ' ' + store.house_number : ''}, ${store.postal_code} ${store.city}`,
          lat: parseFloat(store.latitude),
          lng: parseFloat(store.longitude),
          postal_code: store.postal_code,
          city: store.city,
          carrier: store.carrier,
          openingTimes: store.formatted_opening_times
        }));
        
        const validStoresData = STORES.filter(store => 
          !isNaN(store.lat) && !isNaN(store.lng) && 
          store.lat !== null && store.lng !== null
        );
        
        if (validStoresData.length === 0) {
          console.error('Aucun magasin avec des coordonnÃ©es valides trouvÃ©');
          document.querySelector(CONFIG.storeListSelector).innerHTML = 
            '<li class="map-store-item">Erreur : coordonnÃ©es de magasins invalides ou aucun magasin trouvÃ©.</li>';
          // Ne pas initialiser la carte si pas de donnÃ©es valides
          return; 
        }
        
        STORES = validStoresData;
        initMap(); // initMap n'appellera plus updateStoresListAndMarkers directement
        isMapInitialized = true;
        updateStoresListAndMarkers(); // On s'assure d'afficher les donnÃ©es aprÃ¨s initialisation complÃ¨te
      })
      .catch(error => {
        console.error('Erreur lors du chargement des donnÃ©es:', error);
        document.querySelector(CONFIG.storeListSelector).innerHTML = 
          '<li class="map-store-item">Erreur lors du chargement des donnÃ©es.</li>';
        if (mapLayoutElement) mapLayoutElement.classList.remove('hidden'); // Afficher pour montrer l'erreur
      });
  }

  // --- Initialisation de la carte ---
  function initMap() {
    const mapContainer = document.querySelector(CONFIG.mapSelector);
    // La hauteur est maintenant gÃ©rÃ©e avant l'appel Ã  performSearch ou ici si nÃ©cessaire
    if (mapContainer && !mapContainer.offsetHeight) {
      mapContainer.style.height = '500px'; 
    }
    
    const center = (CONFIG.defaultCenter && CONFIG.defaultCenter.length === 2) ? 
                  CONFIG.defaultCenter : [46.200000, 5.220000];
    const zoom = !isNaN(CONFIG.defaultZoom) ? parseInt(CONFIG.defaultZoom) : 15;
    
    console.log("Initialisation de la carte avec:", {center, zoom});
    
    map = L.map(CONFIG.mapSelector.substring(1), {
      center: center,
      zoom: zoom,
      zoomControl: true
    });
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Mettre Ã  jour la liste des magasins et les marqueurs est maintenant gÃ©rÃ©
    // par loadAndInitializeData aprÃ¨s que isMapInitialized soit true.
    // updateStoresListAndMarkers(); // CET APPEL EST SUPPRIMÃ‰ D'ICI

    // Forcer une mise Ã  jour de la taille de la carte aprÃ¨s qu'elle soit visible et initialisÃ©e
    setTimeout(function() {
      if (map) {
        map.invalidateSize(true);
        // Si aucun magasin n'est trouvÃ© par le filtre, la vue sera dÃ©jÃ  centrÃ©e par updateMarkers.
        // Sinon, fitBounds dans updateMarkers s'en chargera.
      }
    }, 200); // Un petit dÃ©lai pour s'assurer que le DOM est prÃªt
  }

  // --- Gestion des marqueurs de la carte ---
  function updateMarkers(filteredStores) {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    if (!map) { // SÃ©curitÃ© si la carte n'est pas encore initialisÃ©e
        console.error("La carte n'est pas initialisÃ©e pour mettre Ã  jour les marqueurs.");
        return;
    }

    filteredStores.forEach(store => {
      if (isNaN(store.lat) || isNaN(store.lng)) {
        console.error(`CoordonnÃ©es invalides pour ${store.name}:`, store.lat, store.lng);
        return;
      }
      
      // Contenu du Popup avec le bouton SÃ©lectionner
      const popupContent = `
        <strong>${store.name}</strong><br>
        ${store.address}<br>
        <button class="map-select-btn map-select-btn-popup" data-store-id="${store.id}" data-store-name="${store.name}">SÃ©lectionner</button>
      `;

      const marker = L.marker([store.lat, store.lng])
        .bindPopup(popupContent)
        .addTo(map);
      
      marker.storeId = store.id; // Garder l'ID du magasin sur le marqueur

      // Ã‰couteur pour quand le popup de ce marqueur s'ouvre
      marker.on('popupopen', function (e) { // e est l'Ã©vÃ©nement popup
        const currentMarkerStoreId = e.target.storeId; // AccÃ©der Ã  storeId via e.target
        
        // Mettre Ã  jour l'Ã©tat de sÃ©lection lorsque le popup s'ouvre (avant la gestion du bouton)
        selectedStoreId = currentMarkerStoreId;
        highlightSelectedStoreInList();

        const popupElem = marker.getPopup().getElement();
        if (popupElem) {
          const selectBtn = popupElem.querySelector('.map-select-btn-popup');
          if (selectBtn) {
            // Supprimer l'ancien Ã©couteur pour Ã©viter les doublons si le popup est rouvert
            // C'est une approche simple, des solutions plus robustes existent si nÃ©cessaire
            selectBtn.replaceWith(selectBtn.cloneNode(true));
            popupElem.querySelector('.map-select-btn-popup').addEventListener('click', function() {
              const storeId = parseInt(this.getAttribute('data-store-id'));
              const storeName = this.getAttribute('data-store-name');
              // Le bouton "SÃ©lectionner" du popup gÃ¨re l'alerte, la sÃ©lection et le centrage
              handleSelectAction(storeName, storeId);
            });
          }
        }
      });

      markers.push(marker);
    });

    if (markers.length > 0) {
      const group = new L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.1));
    } else {
      const center = CONFIG.defaultCenter || [46.200000, 5.220000];
      const zoom = CONFIG.defaultZoom || 15;
      map.setView(center, zoom);
    }
  }

  // --- Affichage de la liste des magasins ---
  function renderStoresList(filteredStores) {
    const storeListContainer = document.querySelector(CONFIG.storeListSelector);
    storeListContainer.innerHTML = '';

    if (filteredStores.length === 0) {
      storeListContainer.innerHTML = '<li class="map-store-item">Aucun point relais trouvÃ© pour ces critÃ¨res.</li>';
      return;
    }

    filteredStores.forEach(store => {
      const storeItem = document.createElement('li');
      storeItem.className = 'map-store-item';
      storeItem.setAttribute('data-store-id', store.id);

      // Structure interne de l'item
      storeItem.innerHTML = `
        <div class="map-store-main-content">
          <div class="map-store-info">
            <div class="map-store-title">${store.name}</div>
            <div class="map-store-address">${store.address}</div>
          </div>
          <button class="map-select-btn" data-store-id="${store.id}" data-store-name="${store.name}">SÃ©lectionner</button>
        </div>
        <div class="map-store-details-toggle-container">
            <a href="#" class="map-store-toggle-details" data-store-id="${store.id}">Voir plus</a>
        </div>
        <div class="map-store-details" style="display: none;">
          <p class="details-carrier-container"><strong>Transporteur:</strong> <span class="details-carrier"></span></p>
          <p><strong>Horaires d'ouverture:</strong></p>
          <ul class="details-opening-hours"></ul>
        </div>
      `;
      
      // Clic sur l'item ENTIER (hors bouton SÃ©lectionner et hors lien Voir plus)
      storeItem.addEventListener('click', function(event) {
        if (event.target.classList.contains('map-select-btn') || event.target.classList.contains('map-store-toggle-details')) {
            return;
        }
        const storeId = parseInt(this.getAttribute('data-store-id'));
        centerMapOnStore(storeId);
        // Mettre Ã  jour l'Ã©tat sÃ©lectionnÃ© et le visuel SANS alerte
        selectedStoreId = storeId;
        highlightSelectedStoreInList();
      });

      storeListContainer.appendChild(storeItem);
    });

    // Ã‰couteurs pour les boutons "SÃ©lectionner" (ceux dans la liste)
    document.querySelectorAll('#map-store-list .map-select-btn').forEach(button => {
      button.addEventListener('click', function() {
        const storeId = parseInt(this.getAttribute('data-store-id'));
        const storeName = this.getAttribute('data-store-name');
        // Le bouton "SÃ©lectionner" de la liste gÃ¨re l'alerte, la sÃ©lection et le centrage
        handleSelectAction(storeName, storeId);
      });
    });

    // Ã‰couteurs pour les liens "Voir plus / Masquer"
    document.querySelectorAll('.map-store-toggle-details').forEach(toggleLink => {
      toggleLink.addEventListener('click', function(event) {
        event.preventDefault(); // EmpÃªcher le comportement par dÃ©faut du lien <a>
        const storeId = parseInt(this.getAttribute('data-store-id'));
        const storeItem = this.closest('.map-store-item'); // Trouver l'Ã©lÃ©ment parent .map-store-item
        if (!storeItem) return;

        const detailsDiv = storeItem.querySelector('.map-store-details');
        if (!detailsDiv) return;

        const storeData = STORES.find(s => s.id === storeId);
        if (!storeData) return;

        if (detailsDiv.style.display === 'none') {
          // --- Si masquÃ©, on peuple et on affiche ---
          const carrierSpan = detailsDiv.querySelector('.details-carrier');
          const carrierContainer = detailsDiv.querySelector('.details-carrier-container');
          if (storeData.carrier) {
            carrierSpan.textContent = storeData.carrier;
            carrierContainer.style.display = '';
          } else {
            carrierContainer.style.display = 'none';
          }

          const hoursList = detailsDiv.querySelector('.details-opening-hours');
          const dayNames = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
          hoursList.innerHTML = ''; 
          let horariosEncontrados = false;
          if (storeData.openingTimes && typeof storeData.openingTimes === 'object') {
            for (const dayNum in storeData.openingTimes) {
              if (storeData.openingTimes[dayNum] && storeData.openingTimes[dayNum].length > 0) {
                const dayName = dayNames[dayNum] || `Jour ${dayNum}`;
                const times = storeData.openingTimes[dayNum].join(', ');
                const li = document.createElement('li');
                li.textContent = `${dayName}: ${times}`;
                hoursList.appendChild(li);
                horariosEncontrados = true;
              }
            }
          }
          if (!horariosEncontrados) {
            const li = document.createElement('li');
            li.textContent = "Non communiquÃ©s";
            hoursList.appendChild(li);
          }
          detailsDiv.style.display = 'block';
          this.textContent = 'Masquer';
        } else {
          // --- Si visible, on masque ---
          detailsDiv.style.display = 'none';
          this.textContent = 'Voir plus';
        }
      });
    });
  }

  // --- Fonctions de filtrage ---
  function filterStores() {
    // Pour le moment, nous retournons tous les magasins chargÃ©s depuis data.json
    return STORES;
  }

  // --- Mise Ã  jour de l'affichage ---
  function updateStoresListAndMarkers() {
    if (!isMapInitialized) { // Ne rien faire si la carte n'est pas prÃªte
        console.log("Mise Ã  jour demandÃ©e mais la carte n'est pas initialisÃ©e.");
        return;
    }
    const filteredStores = filterStores();
    renderStoresList(filteredStores);
    updateMarkers(filteredStores);
  }

  // --- Centrer la carte sur un magasin ---
  function centerMapOnStore(storeId) {
    if (!map) return; // SÃ©curitÃ©

    const store = STORES.find(s => s.id === storeId);
    if (store) {
      if (isNaN(store.lat) || isNaN(store.lng)) {
        console.error(`Impossible de centrer sur le magasin, coordonnÃ©es invalides:`, store);
        return;
      }
      
      map.setView([store.lat, store.lng], 16); 
      
      const marker = markers.find(m => m.storeId === storeId);
      if (marker) marker.openPopup();
    }
  }

  // --- Afficher le message de sÃ©lection et mettre Ã  jour le visuel ---
  // RenommÃ©e en handleSelectAction pour mieux reflÃ©ter son rÃ´le
  function handleSelectAction(storeName, storeId) {
    alert(`Vous avez choisi le point relais : ${storeName}`);
    selectedStoreId = storeId;
    highlightSelectedStoreInList();
    centerMapOnStore(storeId); // Centrer la carte aussi lors de la sÃ©lection via bouton
  }

  // --- Mettre en Ã©vidence l'item sÃ©lectionnÃ© dans la liste ---
  function highlightSelectedStoreInList() {
    document.querySelectorAll('#map-store-list .map-store-item').forEach(item => {
      const itemStoreId = parseInt(item.getAttribute('data-store-id'));

      if (itemStoreId === selectedStoreId) {
        item.classList.add('selected');
      } else {
        item.classList.remove('selected');
      }
    });
  }

  // --- Gestionnaires d'Ã©vÃ©nements ---
  function setupEventListeners() {
    document.querySelector(CONFIG.searchButtonSelector).addEventListener('click', performSearch);

    document.querySelector(CONFIG.postalCodeSelector).addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        performSearch();
        e.preventDefault();
      }
    });

    document.querySelector(CONFIG.citySelector).addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        performSearch();
        e.preventDefault();
      }
    });
    
    window.addEventListener('resize', function() {
      if (map && isMapInitialized) { // S'assurer que la map existe et est initialisÃ©e
        map.invalidateSize(true);
      }
    });
  }

  // --- Initialisation de l'application ---
  function initApp() {
    mapLayoutElement = document.querySelector(CONFIG.mapLayoutSelector);

    setupEventListeners();
    // Ne plus appeler loadStoreData() ou initMap() ici.
  }

  // DÃ©marrer l'application
  initApp();
});