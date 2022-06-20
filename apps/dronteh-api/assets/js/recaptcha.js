/**
* The callback function executed
* once all the Google dependencies have loaded
*/
function onGoogleReCaptchaApiLoad() {
    const widgets = document.querySelectorAll('[data-toggle="recaptcha"]');
    for (const widget of widgets) {
        renderReCaptcha(widget);
    }
}

/**
* Render the given widget as a reCAPTCHA
* from the data-type attribute
*/
function renderReCaptcha(widget) {
    const form = widget.closest('form');
    const widgetType = widget.getAttribute('data-type');
    const widgetParameters = {
        'sitekey': document.querySelector('[src="../assets/js/recaptcha.js"]').getAttribute('data-sitekey')
    };

    if (widgetType == 'invisible') {
        widgetParameters['callback'] = function () {
            form.submit()
        };
        widgetParameters['size'] = "invisible";
    }

    const widgetId = grecaptcha.render(widget, widgetParameters);

    if (widgetType == 'invisible') {
        bindChallengeToSubmitButtons(form, widgetId);
    }
}

/**
* Prevent the submit buttons from submitting a form
* and invoke the challenge for the given captcha id
*/
function bindChallengeToSubmitButtons(form, reCaptchaId) {
    getSubmitButtons(form).forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            grecaptcha.execute(reCaptchaId);
        });
    });
}

/**
* Get the submit buttons from the given form
*/
function getSubmitButtons(form) {
    var buttons = form.querySelectorAll('button, input');
    var submitButtons = [];

    for (const button of buttons) {
        if (button.getAttribute('type') == 'submit') {
            submitButtons.push(button);
        }
    }

    return submitButtons;
}
