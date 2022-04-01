window.addEventListener('load', () => {
    rowClick();
    undeleteActions();
    changeNumberType();
});

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

function changeNumberType() {
    document.querySelectorAll('.field-number').forEach(element => {
        const numberInput = element.querySelector('input[type="text"]');
        if (numberInput) numberInput.setAttribute('type', 'number');
    });

    const form = document.querySelector("form#edit-Rating-form");
    if (form) {
        input = form.querySelector("input[type='text']#Rating_rating");
        if (input) {
            input.setAttribute("max", "5");
            input.setAttribute("min", "1");
        }
    }
}
