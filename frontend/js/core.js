const stringToUri = ( string ) => {

    let newString = string.toLowerCase();



    let stringParts = newString.split(' ');



    if( stringParts.length > 1 ){

        return stringParts.join('-');

    }else {

        return  stringParts[0];

    }

}





const formatDescription = ( description ) => {

    let descriptionParts = description.split('_');
    let initialDesc = descriptionParts[0].substring(0, 30);
    return initialDesc;

}



const formatDate = ( date ) => {



    let unixDate = parseInt(date);

    let miliseconds = unixDate * 1000;



    const dateObject = new Date(miliseconds);



    return dateObject.toLocaleDateString('es-CL', { month: 'long', day: 'numeric', year: 'numeric' });



}



const findMinorByAttribute = async ( containerClass , attributeName ) => {

    return new Promise( resolve => {

        let data = document.querySelectorAll(containerClass);

        if( data.length === 0 ){

            resolve(1000000);

        }

        let idValues = [];

        data.forEach(

            function (value, index, listObj) {

                idValues.push(parseInt(listObj[index].getAttribute(attributeName)));

            }

        );

        console.log(idValues);

        resolve( Math.min(...idValues));

    });

};



const loopArticle = async ( data, container, className, categoryLeft = false ) =>{

    return new Promise(resolve =>



        data.forEach(e => {

        let article = document.createElement('article'); article.classList.add('article', className , `${e.articleType}` , 'is-flex', 'is-flex-direction-column', 'has-background-white', 'p-2', 'mb-3', 'mt-3'); article.setAttribute('id',`${e.id}`);

        let divImage = document.createElement('div');  divImage.setAttribute('id', 'post-image');  divImage.classList.add('is-flex', 'is-relative');

        let linkImage = document.createElement('a'); linkImage.href = `/${e.startUri}/${stringToUri(e.shortNameUri)}/${e.id}/${stringToUri(e.title)}`;

        let figureImage = document.createElement('figure'); figureImage.classList.add('image', 'pt-2', 'pl-2', 'pr-2');

        let Image = document.createElement('img'); Image.src = `/uploads/images/${e.startImgUri}/${e.imgDirectory}/${e.image}`;

        let linkGame = document.createElement('a'); linkGame.innerHTML = `${e.catName}`; linkGame.href = `/${e.startUri}/${stringToUri(e.shortNameUri)}`;

        if( categoryLeft ) {

            linkGame.classList.add('category')

        }else {

            linkGame.classList.add('category', 'right');

        }

        let linkContent = document.createElement('a'); linkContent.href= `/${e.startUri}/${stringToUri(e.shortNameUri)}/${e.id}/${stringToUri(e.title)}`; linkContent.classList.add('pl-2', 'pr-2');

        let titleDiv = document.createElement('div'); titleDiv.setAttribute('id', 'title'); titleDiv.classList.add('post-title');

        let title = document.createElement('h2'); title.classList.add('title', 'post-title-h2'); title.innerHTML = `${e.title}`;

        let infoDiv = document.createElement('div'); infoDiv.classList.add('post-upload-info');

        let dateSpan = document.createElement('span');

        let dateI = document.createElement('i'); dateI.classList.add('fa-solid', 'fa-clock');

        let dateText = document.createTextNode(`${formatDate(e.creationDate)}`);

        let authorSpan = document.createElement('span'); authorSpan.classList.add('ml-2');

        let authorI = document.createElement('i'); authorI.classList.add('fa-solid', 'fa-circle', 'i-font-size');

        let authorText = document.createTextNode(' SudoKiss');

        let divDesc = document.createElement('p'); divDesc.classList.add('is-block', 'posts-description');

        let desc = document.createElement('p'); desc.classList.add('is-text', 'posts-description'); desc.innerHTML = formatDescription(`${e.description}`);

        let readMoreDiv = document.createElement('div'); readMoreDiv.classList.add('read-more');

        let readMoreLink = document.createElement('a'); readMoreLink.classList.add('pt-0', 'pl-2', 'pr-2', 'pb-2'); readMoreLink.href = `/${e.startUri}/${stringToUri(e.shortNameUri)}/${e.id}/${stringToUri(e.title)}`; readMoreLink.innerHTML = "Leer más <<<";



        authorSpan.appendChild(authorI); authorSpan.appendChild(authorText);

        dateSpan.appendChild(dateI); dateSpan.appendChild(dateText);

        infoDiv.appendChild(dateSpan); infoDiv.appendChild(authorSpan);

        titleDiv.appendChild(title);



        divDesc.appendChild(desc);



        linkContent.appendChild(titleDiv); linkContent.appendChild(infoDiv); linkContent.appendChild(divDesc);

        linkImage.appendChild(figureImage);

        figureImage.appendChild(Image);

        divImage.appendChild(linkImage);divImage.appendChild(linkGame);



        readMoreDiv.appendChild(readMoreLink);



        article.appendChild(divImage); article.appendChild(linkContent); article.appendChild(readMoreDiv);

        resolve(container.appendChild(article));

    }))

}



const renderArticles = async ( data, container, className = 'post-search-id', categoryLeft = false ) => {

    return await loopArticle(data, container, className, categoryLeft );

}


const renderLoader = async (target) => {
    return new Promise(resolve => {
        let container = document.createElement('div'); container.id = 'loader-container';
        let loader = document.createElement('div'); loader.classList.add('custom-loader');
        for(let i = 1; i < 7; i++){
            let bar = document.createElement('div'); bar.classList.add(`bar${i}`);
            loader.appendChild(bar);
        }
        container.appendChild(loader);
        let targetContainer = document.getElementById(target);
        resolve(targetContainer.appendChild(container));
    });
};	

const removeLoader = () => {
    let loader = document.getElementById('loader-container');
    loader.remove();
};






let searchUrl = 'http://localhost:8001/myapi/search'
let allowSearchScroll = false;



let search = {

    init: async function (){





        let searchInput = document.getElementById('search-input');

	    let searchArticlesResult = document.getElementById('search-articles-results');

        let searchButton = document.getElementById('search-button');



        searchButton.addEventListener('click', async () =>{

            allowMainScroll = false;

            allowSearchScroll = true;

            searchButton.disabled = true;
            searchArticlesResult.style.display = 'none';
	        document.getElementById('main').scrollIntoView(true);
            await renderLoader('search-results');

            let content = searchInput.value;
            let myHeaders = new Headers();

            myHeaders.append("Content-Type", "text/plain");
            myHeaders.append("Content-Length", content.length.toString());
            myHeaders.append("Search-Input", content);


            fetch(searchUrl, {

                mode: 'cors',

                credentials: 'include',

                headers: myHeaders

            })

                .then(res =>res.json())

                .then(async data => {

                    console.log(data);

                    let searchResults = document.getElementById('search-articles-results');

                    searchResults.innerHTML = '';

                    if (data.status) {

                        allowSearchScroll = false;

                        searchButton.disabled = false;

                        console.log(data.status);

                        let noResultSpan = document.createElement('span');

                        noResultSpan.classList.add('is-size-3');

                        noResultSpan.innerHTML = `${data.status}`;

                        searchResults.appendChild(noResultSpan);
                        removeLoader();
			            searchResults.style.display = 'flex';

                    } else {

                        allowSearchScroll = true;

                        await renderArticles(data, searchResults, 'post-search-id').then( () => {

                            setTimeout(()=>{
                                searchButton.disabled = false;
                                removeLoader();
                        searchResults.style.display = 'flex';
                                    }, 1000);

                                });

                            }

                        })

                        .catch(err=>console.log(err))


        });

    }

};



window.addEventListener('DOMContentLoaded', search.init);



let SearchEndless = {



    // INITIALIZE HOME INFINTE SCROLL

    init: function (){



        window.addEventListener("scroll", async function () {

            if (allowSearchScroll) {



                if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight) {

                    console.log('final');

                }



                let articlesLastId = {};



                if ((window.innerHeight + window.pageYOffset) >= document.body.offsetHeight && allowSearchScroll) {

                    allowSearchScroll = false;

                    await findMinorByAttribute( '.loadout-article', 'id').then(response =>{

                        console.log('Last loadout Id: ', response);

                        articlesLastId.loadout = response



                    });



                    await findMinorByAttribute( '.news-article', 'id').then(response =>{

                        console.log('Last News Id: ', response);

                        articlesLastId.news = response

                    });





                    await findMinorByAttribute( '.tutorial-article', 'id').then(response =>{

                        console.log('Last Tutorial Id: ', response);

                        articlesLastId.tutorial = response

                    });



                    console.log(JSON.stringify(articlesLastId));

                    await SearchEndless.load(articlesLastId);

                }

            }

        });





    },



    load: async function ( lastId ){



        let searchResults = document.getElementById('search-articles-results');

        let content = document.getElementById('search-input').value;



        console.log("=====");

        console.log(JSON.stringify(lastId));





        await fetch(searchUrl, {

                mode: 'cors',

                credentials: 'include',

                headers: {

                    'X-Api-Key': sessionStorage.getItem('apk'),

                    'Search-Input': content,

                    'Last-Search-Id': JSON.stringify([lastId])

                }

        })

            .then(res =>res.json())

            .then(data=>{





                if( data.status ){

                    allowSearchScroll = false;

                    console.log(data);



                }else {

                    allowSearchScroll = true;

                    console.log(data);

                    data.sort(function (a, b){

                        return b.id - a.id;

                    })





                    data.reverse();



                    renderArticles(data, searchResults, 'post-search-id').then( () => {

                        allowSearchScroll = true;

                    });







                }



            })

            .catch(err=>console.log(err));

    }





};



window.addEventListener('DOMContentLoaded', SearchEndless.init );



