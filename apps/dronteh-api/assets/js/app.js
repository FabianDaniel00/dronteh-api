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
