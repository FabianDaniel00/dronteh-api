import 'leaflet';

window.addEventListener('load', () => {
    loadMap();
    setTimeClick();
    sendTimeAndSendNotificationClick();
    setTimeActions();
    selectFirstTab();
});

function loadMap() {
    const mapElement = document.querySelector('#map');

    if (mapElement) {
        var latlng = mapElement.getAttribute('data-latlng').split(';');
        var map = L.map(mapElement).setView(latlng, 13);

        var myIcon = L.icon({
            iconUrl: '../../assets/images/marker-icon-2x.png',
            iconSize: [38, 60],
            iconAnchor: [22, 60],
            popupAnchor: [-3, -65],
            shadowUrl: '../../assets/images/marker-shadow.png',
            shadowSize: [68, 60],
            shadowAnchor: [22, 60]
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        L.marker(latlng, { icon: myIcon }).addTo(map);
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
