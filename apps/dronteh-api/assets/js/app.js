require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

window.addEventListener('load', () => {
  dismissAlert();
  loading();
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

function loading() {
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('[type="submit"]');
      button.disabled = true;
      let buttonInnerHTML = button.innerHTML;
      buttonInnerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + buttonInnerHTML;
    });
  });
}
