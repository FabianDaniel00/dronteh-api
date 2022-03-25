window.addEventListener('load', windowLoaded);

function windowLoaded() {
    rowClick();
    sendTimeAndSendNotificationClick();
    setTimeClick();
    undeleteActions();
    setTimeActions();
}

function rowClick() {
    const rows = document.querySelectorAll(".table-container .table.datagrid tbody tr");

    if (rows.length) {
        for (const row of rows) {
            row.addEventListener("click", (event) => event.currentTarget.querySelector("input[type='checkbox']").click());

            const links = row.querySelectorAll("a, input[type='checkbox'].form-check-input");
            for (const link of links) {
                link.addEventListener("click", (event) => event.stopPropagation());
            }

            row.addEventListener("mouseleave", (event) => {
                const dropdownDiv = event.currentTarget.querySelector(".dropdown-menu.dropdown-menu-right.show");
                const dropdownA = event.currentTarget.querySelector(".dropdown-toggle.show");

                if (dropdownDiv && dropdownA) {
                    dropdownDiv.classList.remove("show");
                    dropdownDiv.removeAttribute("style");
                    dropdownDiv.removeAttribute("data-popper-placement");

                    dropdownA.classList.remove("show");
                    dropdownA.setAttribute("aria-expanded", false);
                }
            });
        }
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
    const setTimeForm = document.querySelector("#set-time-form");
    if (setTimeForm) setTimeForm.addEventListener("submit", () => {
        document.addEventListener("click", (event) => {
            event.stopPropagation();
            event.preventDefault();
        }, true);
        const submitButton = document.querySelector("button.btn[type='submit'][form='set-time-form']");
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        submitButton.style.width = '70px';
        submitButton.style.textAling = 'center';
        submitButton.disabled = true;
        document.querySelector("div#modal-set-time button[type='button'][data-bs-dismiss='modal']").disabled = true;
    });
}

const undeleteActions = () => {
    document.querySelectorAll('.action-undelete').forEach((actionElement) => {
        actionElement.addEventListener('click', (event) => {
            event.preventDefault();

            document.querySelector('#modal-undelete-button').addEventListener('click', () => {
                const undeleteFormAction = new URL(actionElement.getAttribute('href'));
                undeleteFormAction.search = undeleteFormAction.search.split('&').filter(param => param.substring(0, 8) !== 'referrer').join('&');
                const undeleteForm = document.querySelector('#undelete-form');
                undeleteForm.setAttribute('action', undeleteFormAction);
                undeleteForm.submit();
            });
        });
    });
}

const setTimeActions = () => {
    function isNumeric(str) {
        if (typeof str != "string") return false // we only process strings!
        return !isNaN(str) && // use type coercion to parse the _entirety_ of the string (`parseFloat` alone does not do this)...
            !isNaN(parseFloat(str)) // ...and ensure strings of whitespace fail
    }

    const bodyId = document.querySelector("body").id.split('-');
    const entityId = bodyId[bodyId.length - 1];

    if (isNumeric(entityId)) {
        document.querySelector("a[data-bs-target='#modal-set-time']").setAttribute('data-bs-target', '#modal-set-time-' + entityId);
    } else if (bodyId[1] === 'index') {
        document.querySelectorAll(".table-wrapper table tbody tr").forEach(row => {
            const entityId = row.getAttribute("data-id");
            row.querySelector("td.actions a[data-bs-target='#modal-set-time']").setAttribute('data-bs-target', '#modal-set-time-' + entityId);
        })
    }

    document.querySelectorAll("[id^='modal-set-time-']").forEach(modalSetTime => {
        modalSetTime.querySelector("form").addEventListener("submit", () => {
            document.addEventListener("click", (event) => {
                event.stopPropagation();
                event.preventDefault();
            }, true);
            const submitButton = modalSetTime.querySelector("button.btn[type='submit'][form^='set-time-form-']");
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            submitButton.style.width = '70px';
            submitButton.style.textAling = 'center';
            submitButton.disabled = true;
            modalSetTime.querySelector("div button[type='button'][data-bs-dismiss='modal']").disabled = true;
        });
    });
}
