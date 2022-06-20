require('@fortawesome/fontawesome-free/js/all.js');
import 'flowbite-datepicker';
import 'leaflet-control-geocoder';
import L from 'leaflet';
import { GestureHandling } from 'leaflet-gesture-handling';
import 'leaflet-loading';
import DateRangePicker from 'flowbite-datepicker/DateRangePicker';
import Datepicker from 'flowbite-datepicker/Datepicker';

Datepicker.locales.hu = {
  days: ["vasárnap", "hétfő", "kedd", "szerda", "csütörtök", "péntek", "szombat"],
  daysShort: ["vas", "hét", "ked", "sze", "csü", "pén", "szo"],
  daysMin: ["V", "H", "K", "Sze", "Cs", "P", "Szo"],
  months: ["január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december"],
  monthsShort: ["jan", "feb", "már", "ápr", "máj", "jún", "júl", "aug", "sze", "okt", "nov", "dec"],
  today: "Ma",
  weekStart: 1,
  clear: "Töröl",
  titleFormat: "yyyy. MM",
  format: "yyyy.mm.dd"
};

Datepicker.locales.sr_Latn = {
  days: ["Nedelja","Ponedeljak", "Utorak", "Sreda", "Četvrtak", "Petak", "Subota"],
  daysShort: ["Ned", "Pon", "Uto", "Sre", "Čet", "Pet", "Sub"],
  daysMin: ["N", "Po", "U", "Sr", "Č", "Pe", "Su"],
  months: ["Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"],
  monthsShort: ["Jan", "Feb", "Mar", "Apr", "Maj", "Jun", "Jul", "Avg", "Sep", "Okt", "Nov", "Dec"],
  today: "Danas",
  weekStart: 1,
  clear: "Izbrisati",
  format: "dd.mm.yyyy"
};

let Locales = {};

Locales.hu = {
  loading: 'Feldolgozás',
  search: 'Keresés',
  nothingFound: 'Nincs találat',
  leafletGestureHandling: {
    touch: "K\u00e9t ujjal mozgassa a t\u00e9rk\u00e9pet",
    scroll: "A t\u00e9rk\u00e9p a ctrl + g\u00f6rget\u00e9s haszn\u00e1lat\u00e1val nagy\u00edthat\u00f3",
    scrollMac: "A t\u00e9rk\u00e9p a \u2318 + g\u00f6rget\u00e9s haszn\u00e1lat\u00e1val nagy\u00edthat\u00f3"
  }
};

Locales.sr_Latn = {
  loading: 'Obrada',
  search: 'Pretraga',
  nothingFound: 'Ništa nije pronađeno',
  leafletGestureHandling: {
    touch: "Koristite dva prsta da pomerite mapu",
    scroll: "Koristite ctrl + skrol da biste zumirali mapu",
    scrollMac: "Koristite \u2318 + skrol da biste zumirali mapu"
  }
};

Locales.en = {
  loading: 'Processing',
  search: 'Search',
  nothingFound: 'Nothing found',
  leafletGestureHandling: {
    touch: "Use two fingers to move the map",
    scroll: "Use ctrl + scroll to zoom the map",
    scrollMac: "Use \u2318 + scroll to zoom the map"
  }
};

window.addEventListener('load', () => {
  dismissAlert();
  dropdownToggle();
  loadingForm();
  dateRangePicker();
  loadMap();
  submitReservationForm();
});

const locale = document.documentElement.lang;

function dismissAlert() {
  document.querySelectorAll('[role="alert"]').forEach(alert => {
    const timer = setTimeout(() => alert.classList.remove('show-alert'), 10000);

    alert.querySelector('button').addEventListener('click', () => {
      alert.addEventListener('transitionend', () => {
        alert.removeEventListener('transitionend', this);
        alert.remove();
      });
      alert.classList.remove('show-alert');
      clearTimeout(timer);
    });
  });
}

function loadingForm() {
  document.querySelectorAll('form').forEach(form =>
    form.onsubmit = () =>
      form.querySelector('[type="submit"]').innerHTML =
        `<i class="fa-solid fa-circle-notch fa-spin !text-inherit"></i> ${ Locales[locale].loading }`
  );
}

function dropdownToggle() {
  document.querySelectorAll('[data-vs-toggle]').forEach(button => {
    const toggleElement = document.getElementById(button.getAttribute('data-vs-toggle'));
    const toggleClass = toggleElement.getAttribute('data-vs-toggle-class');

    button.onclick = (event) => {
      event.stopPropagation();

      toggleElement.classList.toggle(toggleClass);
    }

    toggleElement.onclick = (event) => event.stopPropagation();

    window.addEventListener('click', () => toggleElement.classList.add(toggleClass));
  });
}

function dateRangePicker() {
  document.querySelectorAll('[date-rangepicker]').forEach(dateRangePicker => {
    new DateRangePicker(dateRangePicker, {
      language: locale,
      clearBtn: true,
      todayBtn: true,
      todayHighlight: true,
      updateOnBlur: false,
      minDate: new Date(),
      todayBtnMode: 1
    });
  });
}

function loadMap() {
  const mapElement = document.querySelector('#map');

  if (mapElement) {
    const latInput = document.querySelector('#reservation_form_gps_coordinates_0');
    const lngInput = document.querySelector('#reservation_form_gps_coordinates_1');
    const latInputValue = latInput.value;
    const lngInputValue = lngInput.value;

    L.Map.addInitHook("addHandler", "gestureHandling", GestureHandling);

    var map = L.map(mapElement, {
      loadingControl: true,
      gestureHandling: true,
      gestureHandlingOptions: {
        text: Locales[locale].leafletGestureHandling
      }
    });

    if (latInputValue !== '' && lngInputValue !== '') {
      map.setView([latInputValue, lngInputValue], 13);
    } else {
      map.locate({setView: true, maxZoom: 13});
    }

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    L.Control.geocoder({
      defaultMarkGeocode: false,
      collapsed: false,
      placeholder: `${ Locales[locale].search }...`,
      showResultIcons: false
    })
      .on('markgeocode', function(e) {
        map.fitBounds(e.geocode.bbox, {maxZoom: 15});
      })
      .addTo(map);

    let markerIcon = L.icon({
      iconSize: [38, 60],
      iconAnchor: [20, 60],
      shadowUrl: '../../assets/images/marker-shadow.png',
      shadowSize: [68, 60],
      shadowAnchor: [20, 60],
      iconUrl: '../../assets/images/marker-icon.png'
    });

    let marker = null;

    marker = L.marker([latInputValue, lngInputValue], {
      icon: markerIcon
    }).addTo(map);

    map.on('click', (event) => {
      const { lat, lng } = event.latlng;

      if (marker) {
        map.removeLayer(marker)
      }

      marker = L.marker([lat, lng], {
        icon: markerIcon
      }).addTo(map);

      latInput.value = lat;
      lngInput.value = lng;
    });

    mapElement.querySelector('.leaflet-control-geocoder-icon').innerHTML = '<i class="fa-solid fa-magnifying-glass fa-xl text-gray-500"></i>';
    mapElement.querySelector('.leaflet-control-geocoder-form-no-error').innerText = `${ Locales[locale].nothingFound }.`;
  }
}

function submitReservationForm() {
  const form = document.querySelector('form[name="reservation_form"]');

  if (form) {
    form.querySelector('[type="submit"]').addEventListener('click', (event) => {
      if (form.querySelector('#reservation_form_gps_coordinates_0').value === '' && form.querySelector('#reservation_form_gps_coordinates_1').value === '') {
        event.preventDefault();

        const map = document.querySelector('#map');
        const mapHelp = document.querySelector('#map-help');

        map.addEventListener('click', () => {
          map.classList.remove('animate-shine');
          mapHelp.style.color = 'white';
        });

        map.classList.add('animate-shine');
        mapHelp.style.color = 'rgb(248 180 180)';
      } else {
        map.classList.remove('animate-shine');
        mapHelp.style.color = 'white';
      }
    });
  }
}
