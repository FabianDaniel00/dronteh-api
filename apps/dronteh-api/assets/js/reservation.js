import L from 'leaflet';
import 'leaflet-routing-machine';
import 'leaflet-control-geocoder';
import 'leaflet-loading';

window.addEventListener('load', () => {
    setTimeClick();
    sendTimeAndSendNotificationClick();
    setTimeActions();
    selectFirstTab();
    collapseMap();
});

function loadMap() {
    const mapElement = document.querySelector('#map');

    if (mapElement) {
        var latLng = mapElement.getAttribute('data-latlng').split(';');
        var map = L.map(mapElement, {
            loadingControl: true
        }).setView(latLng, 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const routingControl = L.Routing.control({
            plan: new L.Routing.Plan([
                L.latLng(45.972544730285605, 19.830133099999998),
                L.latLng(latLng[0], latLng[1]),
            ], {
                geocoder: L.Control.Geocoder.nominatim(),
                reverseWaypoints: true,
                createMarker: (i, start, n) => {
                    const markerIconOptions = {
                        iconSize: [38, 60],
                        iconAnchor: [20, 60],
                        shadowUrl: '../../assets/images/marker-shadow.png',
                        shadowSize: [68, 60],
                        shadowAnchor: [20, 60]
                    };

                    let markerIcon = L.icon({
                        iconUrl: '../../assets/images/marker-icon.png',
                        ...markerIconOptions
                    });

                    if (i == 0) {
                        markerIcon = L.icon({
                            iconUrl: '../../assets/images/start-marker-icon.png',
                            ...markerIconOptions
                        });
                    } else if (i == n - 1) {
                        markerIcon = L.icon({
                            iconUrl: '../../assets/images/destination-marker-icon.png',
                            ...markerIconOptions
                        });
                    }

                    return L.marker(start.latLng, {
                        icon: markerIcon,
                        draggable: true
                    });
                }
            }),
            router: L.Routing.mapbox('pk.eyJ1IjoiZmFiaWFuZGFuaWVsMDAiLCJhIjoiY2wxbTF5Z3I4MGdqajNka3FiZDMxNHdvbCJ9.pURJ4GNmtHbERjXSCXc9iw'),
            showAlternatives: true,
            fitSelectedRoutes: true,
            lineOptions: {
                styles: [
                    { color: 'black', opacity: 0.15, weight: 9 },
                    { color: 'white', opacity: 0.8, weight: 6 },
                    { color: 'blue', opacity: 1, weight: 2 },
                ]
            },
            altLineOptions: {
                styles: [
                    { color: 'black', opacity: 0.15, weight: 8 },
                    { color: 'white', opacity: 0.8, weight: 5 },
                    { color: '#808080', opacity: 1, weight: 2 },
                ]
            },
            collapsible: true
        }).addTo(map);

        L.Routing.errorControl(routingControl).addTo(map);
    }
}

function collapseMap() {
    const collapseButton = document.querySelector(".collapse-button");

    if (collapseButton) {
        let mapLoaded = false;
        collapseButton.addEventListener("click", () => {
            const map = document.querySelector('.map-container');
            if (map.style.height) {
                map.style.height = null;
            } else {
                if (!mapLoaded) {
                    loadMap();
                    mapLoaded = true;
                }
                map.style.height = "549px";
            }
        });
    }
}

function setTimeClick() {
    const field = document.querySelector(".form-control.set-time-reservation");
    if (field) field.addEventListener("click", () => {
        const button = document.querySelector("a.action-setTime[data-bs-target='#modal-set-time']");
        if (button) button.click();
    })
}

function sendTimeAndSendNotificationClick() {
    const setTimeModal = document.querySelector("#modal-set-time");
    if (setTimeModal) setTimeModal.querySelector("#set-time-form").addEventListener("submit", () => {
        document.addEventListener("click", (event) => {
            event.stopPropagation();
            event.preventDefault();
        }, true);

        const submitButton = setTimeModal.querySelector("button.btn[type='submit'][form='set-time-form']");
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        submitButton.style.width = '70px';
        submitButton.style.textAling = 'center';
        submitButton.disabled = true;
        setTimeModal.querySelector("div#modal-set-time button[type='button'][data-bs-dismiss='modal']").disabled = true;
    });
}

const setTimeActions = () => {
    document.querySelectorAll('.action-setTime').forEach((actionElement) => {
        actionElement.addEventListener('click', (event) => {
            event.preventDefault();
            const setTimeModal = document.querySelector('#modal-set-time');

            const time = actionElement.getAttribute('data-time');
            setTimeModal.querySelector('input#set_time_time').setAttribute('value', time ? time : null);
            setTimeModal.querySelector('#interval-start').innerText = actionElement.getAttribute('data-interval-start');
            setTimeModal.querySelector('#interval-end').innerText = actionElement.getAttribute('data-interval-end');
            setTimeModal.querySelector('label[for="set_time_time"]').style.opacity = time ? 1 : 0;

            setTimeModal.querySelector('#modal-set-time-button').addEventListener('click', () => {
                const setTimeForm = setTimeModal.querySelector('#set-time-form');
                setTimeForm.onsubmit = () => {
                    setTimeForm.setAttribute('action', actionElement.getAttribute('formaction'));
                    setTimeForm.submit();
                };
            });
        });
    });
}

function selectFirstTab() {
    const editForm = document.querySelector('#edit-Reservation-form');
    if (editForm) {
        const tab = editForm.querySelector('.nav-item .nav-link:first-of-type');
        if (tab) tab.classList.add('active');
        const tabContent = editForm.querySelector('.tab-content .tab-pane:first-of-type');
        if (tabContent) tabContent.classList.add('active');
    }
}
