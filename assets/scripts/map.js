import "leaflet/dist/leaflet.css";
import L from "leaflet";
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

export function initMap() {
    let mapContainer = document.querySelector("div[data-address]");
    if (mapContainer) {
        const coordinates = JSON.parse(mapContainer.dataset.address).address;

        mapContainer = L.map('map').setView([coordinates.latitude, coordinates.longitude], 16);
        L.tileLayer('https://api.maptiler.com/maps/outdoor-v2/{z}/{x}/{y}.png?key=wqbAsBHR5fA8xfe9qeYC', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
        }).addTo(mapContainer);

        L.marker([coordinates.latitude, coordinates.longitude]).addTo(mapContainer);
    }
}