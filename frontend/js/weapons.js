document.addEventListener('DOMContentLoaded', () =>{

    let gamesSelect = document.getElementById('games');
    let gameName = document.getElementById('gameName');

    gameName.value = gamesSelect.options[gamesSelect.selectedIndex].text;

    // Con esta función detectamos si hubo un cambio en el gamesSelect
    document.addEventListener('input', function (event){
       if ( event.target.id !== 'games' ) return;

       // Actualizamos el valor del input de tipo hidden
        gameName.value = event.target.options[event.target.selectedIndex].text


    });


});