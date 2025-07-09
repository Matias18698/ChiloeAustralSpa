<template>
    <div :id="mapId" class="map-container"></div>
  </template>
  
  <script setup>
  import { onMounted, watch, onBeforeUnmount } from 'vue';
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  const props = defineProps({
    lat: Number,
    lon: Number,
    mapId: String,
    zoom: {
      type: Number,
      default: 13
    },
    markers: {
      type: Array,
      default: () => []
    }
  });
  
  let map;
  let markersGroup;
  
  // Icono personalizado
  const defaultIcon = new L.Icon({
    iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
    shadowSize: [41, 41]
  });
  


  onMounted(() => {
    map = L.map(props.mapId, {
      zoomControl: true,
      scrollWheelZoom: true
    }).setView([props.lat, props.lon], props.zoom);
  
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
      maxZoom: 19,
    }).addTo(map);
  
    markersGroup = L.featureGroup().addTo(map);
    addMarkers();
  });
  
  function addMarkers() {
    markersGroup.clearLayers();
  
    // Marcador principal
    const mainMarker = L.marker([props.lat, props.lon], { icon: defaultIcon })
      .addTo(markersGroup)
      .bindPopup("📍 Ubicación Actual")
      .openPopup();
  
    // Marcadores adicionales
    props.markers.forEach(marker => {
      const m = L.marker([marker.lat, marker.lon], { icon: defaultIcon }).addTo(markersGroup);
      if (marker.popupText) {
        m.bindPopup(marker.popupText);
      }
    });
  
    // Ajusta la vista para mostrar todos los marcadores si hay más de uno
    if (props.markers.length > 0) {
      map.fitBounds(markersGroup.getBounds().pad(0.2));
    }
  }
  
  // Observa cambios en los marcadores para actualizar el mapa
  watch(() => props.markers, addMarkers, { deep: true });
  
  onBeforeUnmount(() => {
    if (map) {
      map.remove();
    }
  });
  </script>
  
  <style scoped>
.map-container {
  width: 100%;           /* Ocupa todo el ancho posible */
  height: 160px;         /* Altura fija, igual que h-40 (10rem) */
  margin: auto;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #d1d5db;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  background-color: #f9fafb;
  transition: transform 0.2s ease, box-shadow 0.3s ease;
}
@media (max-width: 640px) {
  .map-container {
    height: 280px;    /* altura mayor en móviles */
  }
}
@media screen and (max-width: 768px) {
  .map-container {
    height: 240px;    /* altura intermedia para tablets */
  }

  
}
@media screen and (max-width: 1024px) {
  .map-container {
    height: 200px;    /* altura menor para pantallas más grandes */
  }
  
}


.map-container:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@media (max-width: 640px) {
  .map-container {
    height: 280px;
    width: 280px;
  }
}

  </style>
  