document.addEventListener('DOMContentLoaded', () => {

    let deleteButtons = document.querySelectorAll(".delete-company");

    deleteButtons.forEach(function (deleteButton){

        deleteButton.addEventListener('click', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let tdName = deleteButton.parentElement.parentElement;
            document.getElementById('replaceName').innerText = tdName.children[1].textContent;
            document.getElementById("del-anchor").href = `/admin/eliminar/${tdName.children[0].textContent}/desarrolladora`;
        });

    });


    let deleteGameButtons = document.querySelectorAll(".delete-game");

    deleteGameButtons.forEach(function (deleteGameButton){

        deleteGameButton.addEventListener('click', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let tdName = deleteGameButton.parentElement.parentElement;
            document.getElementById('replaceName').innerText = tdName.children[2].textContent;
            document.getElementById("del-anchor").href = `/admin/eliminar/${tdName.children[0].textContent}/juego`;
        });

    });

    let deleteWpCategoryButtons = document.querySelectorAll(".delete-wp-category");

    deleteWpCategoryButtons.forEach(function (deleteWpCatButton){

        deleteWpCatButton.addEventListener('click', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let tdName = deleteWpCatButton.parentElement.parentElement;
            document.getElementById('replaceName').innerText = tdName.children[1].textContent;
            document.getElementById("del-anchor").href = `/admin/eliminar/${tdName.children[0].textContent}/categoria-arma`;
        });

    });

    let deleteWeaponsButtons = document.querySelectorAll(".delete-weapon");

    deleteWeaponsButtons.forEach(function (deleteWeaponButton){

        deleteWeaponButton.addEventListener('click', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let tdName = deleteWeaponButton.parentElement.parentElement;
            document.getElementById('replaceName').innerText = tdName.children[3].textContent;
            document.getElementById("del-anchor").href = `/admin/eliminar/${tdName.children[0].textContent}/arma`;
        });

    });

    let deleteLoadoutsButtons = document.querySelectorAll(".delete-loadout");

    deleteLoadoutsButtons.forEach(function (deleteLoadoutButton){

        deleteLoadoutButton.addEventListener('click', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let tdName = deleteLoadoutButton.parentElement.parentElement;
            document.getElementById('replaceName').innerText = tdName.children[4].textContent;
            document.getElementById("del-anchor").href = `/admin/eliminar/${tdName.children[0].textContent}/clase`;
        });

    });


    // Functions to open and close a modal
    function openModal($el) {
        $el.classList.add('is-active');
    }

    function closeModal($el) {
        $el.classList.remove('is-active');
    }

    function closeAllModals() {
        (document.querySelectorAll('.modal') || []).forEach(($modal) => {
            closeModal($modal);
        });
    }

    // Add a click event on buttons to open a specific modal
    (document.querySelectorAll('.js-modal-trigger') || []).forEach(($trigger) => {
        const modal = $trigger.dataset.target;
        const $target = document.getElementById(modal);


        $trigger.addEventListener('click', () => {
            openModal($target);
        });
    });

    // Add a click event on various child elements to close the parent modal
    (document.querySelectorAll('.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button, .cancel') || []).forEach(($close) => {
        const $target = $close.closest('.modal');

        $close.addEventListener('click', () => {
            closeModal($target);
        });
    });

    // Add a keyboard event to close all modals
    document.addEventListener('keydown', (event) => {
        const e = event || window.event;

        if (e.keyCode === 27) { // Escape key
            closeAllModals();
        }
    });
});