document.addEventListener('DOMContentLoaded', () => {

    let checkBoxes = document.querySelectorAll(".imgCheckBox");

    checkBoxes.forEach(function (checkBox){

        checkBox.addEventListener('change', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let target = checkBox.getAttribute('target');
            let input = document.getElementById(target);
            if ( checkBox.checked ) {
                input.value = "1";
            }else {
                input.value = "0";
            }
        });

    });

});