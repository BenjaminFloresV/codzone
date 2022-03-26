let search = {
   init: function (){


      let searchInput = document.getElementById('search-input');

      let searchButton = document.getElementById('search-button');


      searchButton.addEventListener('click', () =>{
         allowMainScroll = false;
         searchButton.disabled = true;

         let content = searchInput.value;
         let myHeaders = new Headers();
         myHeaders.append("Content-Type", "text/plain");
         myHeaders.append("Content-Length", content.length.toString());
         myHeaders.append("X-Search-Input", content);


         fetch('http://localhost:8001/myapi/clases', {
            mode: 'cors',
            credentials: 'include',
            headers: myHeaders
         })
             .then(res =>res.json())
             .then(data=>{
                console.log(data);
                let searchResults = document.getElementById('search-results');
                searchResults.innerHTML = '';
                if( data.status ){
                   console.log(data.status);
                   let noResultSpan = document.createElement('span'); noResultSpan.classList.add('is-size-3');
                   noResultSpan.innerHTML = `${data.status}`;
                   searchResults.appendChild(noResultSpan);
                }else {
                   data.forEach(e=>{
                      const article = document.createElement('article'); article.classList.add('article','post-id', 'is-flex', 'is-flex-direction-column', 'has-background-white', 'p-2', 'mb-3', 'mt-3'); article.setAttribute('id',`${e.loadout_id}`);
                      const divImage = document.createElement('div');  divImage.setAttribute('id', 'post-image');  divImage.classList.add('is-flex', 'is-relative');
                      const linkImage = document.createElement('a'); linkImage.href = `/clases/${stringToUri(e.shortNameUri)}/${e.loadout_id}/${stringToUri(e.title)}`;
                      const figureImage = document.createElement('figure'); figureImage.classList.add('image', 'pt-2', 'pl-2', 'pr-2');
                      const Image = document.createElement('img'); Image.src = `/uploads/images/loadout/${e.gameName}/${e.image}`;
                      const linkGame = document.createElement('a'); linkGame.innerHTML = `${e.shortName}`; linkGame.href = `/clases/${stringToUri(e.shortNameUri)}`; linkGame.classList.add('category', 'right');
                      const linkContent = document.createElement('a'); linkContent.href= `/clases/${stringToUri(e.shortNameUri)}/${e.loadout_id}/${stringToUri(e.title)}`; linkContent.classList.add('pl-2', 'pr-2');
                      const titleDiv = document.createElement('div'); titleDiv.setAttribute('id', 'title'); titleDiv.classList.add('post-title');
                      const title = document.createElement('h2'); title.classList.add('title', 'post-title-h2'); title.innerHTML = `${e.title}`;
                      const infoDiv = document.createElement('div'); infoDiv.classList.add('post-upload-info');
                      const dateSpan = document.createElement('span');
                      const dateI = document.createElement('i'); dateI.classList.add('fa-solid', 'fa-clock');
                      const dateText = document.createTextNode(`${formatDate(e.creationDate)}`);
                      const authorSpan = document.createElement('span'); authorSpan.classList.add('ml-2');
                      const authorI = document.createElement('i'); authorI.classList.add('fa-solid', 'fa-circle', 'i-font-size');
                      const authorText = document.createTextNode(' SudoKiss');
                      const divDesc = document.createElement('p'); divDesc.classList.add('is-block', 'posts-description');
                      const desc = document.createElement('p'); desc.classList.add('is-text', 'posts-description'); desc.innerHTML = formatDescription(`${e.description}`);
                      const readMoreDiv = document.createElement('div'); readMoreDiv.classList.add('read-more');
                      const readMoreLink = document.createElement('a'); readMoreLink.classList.add('pt-0', 'pl-2', 'pr-2', 'pb-2'); readMoreLink.href = `/clases/${stringToUri(e.shortNameUri)}/${e.loadout_id}/${stringToUri(e.title)}`; readMoreLink.innerHTML = "Leer más <<<";

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
                      searchResults.appendChild(article)
                   })

                }


                searchButton.disabled = false;


             })
             .catch(err=>console.log(err))
      });

   }

};
