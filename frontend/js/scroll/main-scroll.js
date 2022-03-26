
let allowMainScroll = true;

let endless = {

    // INITIALIZE HOME INFINTE SCROLL
    init: async function ( url, container ){

        // Listen to end of page scroll

        window.addEventListener("scroll", async function () {

            if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight && allowMainScroll) {

                await findMinorByAttribute('.post-id', 'id').then( response =>{
                    console.log( response )
                    endless.load( response, url, container );

                });

                // Load more contents
            }

        });


    },

    load: function ( lastId, url, container ){
        let postContainer = document.getElementById(container);

        fetch(url, {
            method: 'GET',
            mode: 'cors',
            credentials: 'include',
            headers:{
                'X-Api-Key' : sessionStorage.getItem('apk'),
                'X-Last-Loadout-Id': lastId

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

                        await renderArticles(data, postContainer, 'post-id', true);

                    }
                }

            })
            .catch(err=>console.log(err));
    }


};
window.addEventListener('DOMContentLoaded', () => {
    let LoadoutContainer = document.getElementById('l-posts-container');
    let NewsContainer = document.getElementById('n-posts-container');
    if( LoadoutContainer !== null ) {
        let url = '/myapi/search/loadout';
        endless.init( url, 'l-posts-container');
    }else if( NewsContainer !== null ) {
        let url = '/myapi/search/news';
        endless.init( url, 'n-posts-container');
    }
} );
