
let allowMainScroll = true;

let endless = {

    // INITIALIZE HOME INFINTE SCROLL
    init: async function ( url, container, categoryLeft = false ){

        // Listen to end of page scroll
        let expresion = /http:\/\/localhost:8001\/[A-Za-z]+\/[A-Za-z\-\d+]+$/i;
        let subcategory = null;
        if( window.location.href.match(expresion) !== null ){
            let explodedUri = window.location.href.split('/');
            subcategory = explodedUri[4];
            console.log(subcategory);
            console.log('match');
        }else {
            console.log('not match');
        }

        window.addEventListener("scroll", async function () {

            if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight && allowMainScroll) {

                await findMinorByAttribute('.post-id', 'id').then( response =>{
                    console.log( response )
                    endless.load( response, url, container, categoryLeft, subcategory );

                });

                // Load more contents
            }

        });


    },

    load: function ( lastId, url, container, categoryLeft = false, subcategory ){
        let postContainer = document.getElementById(container);

        fetch(url, {
            method: 'GET',
            mode: 'cors',
            credentials: 'include',
            headers:{
                'X-Api-Key' : sessionStorage.getItem('apk'),
                'X-Last-Loadout-Id': lastId,
                'Subcategory-Name': subcategory

            },

        })
            .then(res =>res.json())
            .then(async data => {
                console.log(data);
                if (data.length === 0) {
                    console.log('is empty');
                } else {
                    if (data.status) {
                        allowMainScroll = false;
                    } else {

                        data.sort(function (a, b) {
                            return a.id - b.id;
                        })

                        data.reverse();

                        await renderArticles(data, postContainer, 'post-id', categoryLeft);

                    }
                }

            })
            .catch(err=>console.log(err));
    }


};
window.addEventListener('DOMContentLoaded', () => {
    console.log(window.location.href);
    let LoadoutContainer = document.getElementById('l-posts-container');
    let NewsContainer = document.getElementById('n-posts-container');
    let TutorialContainer = document.getElementById('t-posts-container');
    if( LoadoutContainer !== null ) {
        let url = '/myapi/search/loadout';
        endless.init( url, 'l-posts-container', false);
    }else if( NewsContainer !== null ) {
        let url = '/myapi/search/news';
        endless.init( url, 'n-posts-container', true);
    }else if ( TutorialContainer !== null) {
        let url = '/myapi/search/tutorial';
        endless.init( url, 't-posts-container', true);
    }
} );
