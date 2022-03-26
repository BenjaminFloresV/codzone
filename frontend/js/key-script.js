const genBtn = document.querySelector('.gen-btn');
const useBtn = document.querySelector('.use-btn');
const genDiv = document.querySelector('.gen-apikey');
const useDiv = document.querySelector('.use-apikey');
const submit = document.querySelector('.submit');
const email = document.querySelector('#email');


genDiv.style.display = 'none';
useDiv.style.display = 'none';

genBtn.addEventListener('click', ()=>{

    genDiv.style.display = 'block';
    useDiv.style.display = 'none';

});


useBtn.addEventListener('click', ()=>{

    genDiv.style.display = 'none';
    useDiv.style.display = 'block';

});

submit.addEventListener('click', (e)=>{
    e.preventDefault();

    fetch('http://localhost:8001/prueba', {
      method: 'POST',
      mode: 'cors',
      body: new FormData(document.querySelector('.reg-form'))
    }).then(res => res.json())
    .then(data=>{
        //console.log(data)
        if( data.status === 'success' )
        {
            genDiv.innerHTML = `${data.status} <br> ${data.apikey}`;
            sessionStorage.setItem('apikey', data.apikey);

        }
    })
    .catch(err=>console.log(err));
});


useBtn.addEventListener('click', ()=>{
    console.log(sessionStorage.getItem('apikey'))
   fetch('http://localhost:8001/myapi/clases', {
       mode: 'cors',
       credentials: 'include',
       headers:{
           'X-Api-Key' : sessionStorage.getItem('apikey')
       }
   })
       .then(res =>res.json())
       .then(data=>{
           //console.log(data);
           useDiv.innerHTML = '';

           if( data.status ){
               useDiv.innerHTML = data.status;
           }else {
               data.forEach(e=>{
                   const p = document.createElement('p');
                   p.innerHTML = `${e.title}  ${e.description}`;
                   useDiv.appendChild(p);
               })
           }

       })
       .catch(err=>console.log(err))
});