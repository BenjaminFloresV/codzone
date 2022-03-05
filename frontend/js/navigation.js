
document.addEventListener('DOMContentLoaded', () =>{

    let main = document.getElementById('main');

    let toggle_nav = document.getElementById('open-nav');
    let close_nav = document.getElementById('close-nav');
    let navigation = document.getElementById('navigation');

    let toggle_search = document.getElementById('open-search');
    let close_search = document.getElementById('close-search');
    let search = document.getElementById('search-nav');

    let search_container = document.getElementById('search-container');
    let search_button = document.getElementById('search-button')
    let ghost_background = document.getElementById('ghost-background');


    //Main NAV
    toggle_nav.addEventListener('click', ()=>{
        navigation.classList.toggle('active');
    });

    close_nav.addEventListener('click', () =>{
        navigation.classList.remove('active')
    });

    //Search NAV

    toggle_search.addEventListener('click', ()=>{
        search.classList.toggle('active');
    });

    close_search.addEventListener('click', ()=>{
        search.classList.remove('active');
        search_container.style.display = "none";
        main.style.filter = "none";
        ghost_background.style.display = "none";
    });


    //Search Button
    search_button.addEventListener('click', ()=>{
        main.style.filter = "blur(6px)";
        search_container.style.display = "block";
        ghost_background.style.display = "block";
    });


});