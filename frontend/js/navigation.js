
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
        navigation.classList.remove('active');
    });

    //Search NAV

    toggle_search.addEventListener('click', ()=>{
        if( search.classList.contains('active') ){
            search_container.style.display = "none";
            search.classList.remove('active');
            main.style.filter = "none";
            ghost_background.style.display = "none";
            allowMainScroll = true;

        }else {
            search.classList.toggle('active');

        }

    });

    close_search.addEventListener('click', ()=>{
        search.classList.remove('active');
        search_container.style.display = "none";
        main.style.filter = "none";
        ghost_background.style.display = "none";
        document.getElementById('search-results').innerHTML = "";
        allowMainScroll = true;
    });


    //Search Button
    search_button.addEventListener('click', ()=>{
        main.style.filter = "blur(6px)";
        search_container.style.display = "block";
        ghost_background.style.display = "block";
    });
    //
    //
    //
    //
    //
    //
    if( sessionStorage.getItem('apk') === null ){
        let ak = []; ak[0] = "qKdSM4V/SKS"; ak[1] = "$2y$10$dNU6"; ak[2] = "JqQoJ.dvNTL"; ak[3] = "0DIwhg.rIxgV"; ak[4]="zlDmqmy"; ak[5]="Kf10fWS6";
        sessionStorage.setItem('apk', JSON.stringify(ak));
    }

});