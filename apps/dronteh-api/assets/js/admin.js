window.addEventListener('load', windowLoaded);

function windowLoaded() {
    rowClick();
    sendTimeAndSendNotificationClick();
    setTimeClick();
    undeleteActions();
    setTimeActions();
    changeNumberType();
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

const undeleteActions = () => {
    document.querySelectorAll('.action-undelete').forEach((actionElement) => {
        actionElement.addEventListener('click', (event) => {
            event.preventDefault();

            document.querySelector('#modal-undelete-button').addEventListener('click', () => {
                const undeleteForm = document.querySelector('#undelete-form');
                undeleteForm.setAttribute('action', actionElement.getAttribute('formaction'));
                undeleteForm.submit();
            });
        });
    });
}

const setTimeActions = () => {
    document.querySelectorAll('.action-setTime').forEach((actionElement) => {
        actionElement.addEventListener('click', (event) => {
            event.preventDefault();
            const setTimeModal = document.querySelector('#modal-set-time');
            const setTimeModalContent = setTimeModal.querySelector('.modal-body .content');
            const loading = setTimeModal.querySelector('#loading');

            loading.style.display = 'block';
            setTimeModalContent.style.display = 'none';

            const fetchUrl = actionElement.getAttribute('fetchurl');
            const csrfToken = actionElement.getAttribute('csrf-token');

            getSetTimeData(fetchUrl, csrfToken).then(data => {
                const time = data.data.time;

                setTimeModal.querySelector('input#set_time_time').setAttribute('value', time ? time.split('+')[0] : null);
                setTimeModal.querySelector('#interval-start').innerText = data.data.interval_start;
                setTimeModal.querySelector('#interval-end').innerText = data.data.interval_end;
                setTimeModal.querySelector('label[for="set_time_time"]').style.opacity = time ? 1 : 0;

                loading.style.display = 'none';
                setTimeModalContent.style.display = 'block';

                setTimeModal.querySelector('#modal-set-time-button').addEventListener('click', () => {
                    const undeleteForm = setTimeModal.querySelector('#set-time-form');
                    undeleteForm.setAttribute('action', actionElement.getAttribute('formaction'));
                    undeleteForm.submit();
                });
            });
        });
    });
}

async function getSetTimeData(fetchUrl, csrfToken) {
    const response = await fetch(fetchUrl, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'x-csrf-token': csrfToken
        },
    });

    return response.json();
}

function changeNumberType() {
    const form = document.querySelector("form#edit-Rating-form");
    if (form) {
        input = form.querySelector("input[type='text']#Rating_rating");
        if (input) {
            input.setAttribute("type", "number");
            input.setAttribute("max", "5");
            input.setAttribute("min", "1");
        }
    }
}
