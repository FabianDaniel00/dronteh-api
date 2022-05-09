require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

window.addEventListener('load', () => {
  dismissAlert();
  dropdownToggle();
});

function dismissAlert() {
  document.querySelectorAll('[role="alert"]').forEach(alert => {
    alert.querySelector('button').addEventListener('click', () => {
      alert.addEventListener('transitionend', () => {
        alert.removeEventListener('transitionend', this);
        alert.remove();
      });
      alert.classList.remove('show');
    });
  });
}

function dropdownToggle() {
  document.querySelectorAll('[data-dropdown-toggle]').forEach(button =>
    button.onclick = () =>
      document.getElementById(button.getAttribute('data-dropdown-toggle')).classList.toggle('hidden')
  );
}
