document.addEventListener('DOMContentLoaded', ()=>{
    let weaponcatSelect = document.getElementById('wcat-select');
    let weaponSelect = document.getElementById('w-select');
    let gameSelect = document.getElementById('g-select');

    let gameName = document.getElementById('gameName'); // The hidden input for subdirctory

    gameName.value = gameSelect.options[gameSelect.selectedIndex].text;

    let gameId = gameSelect.value;
    let weaponCatId = weaponcatSelect.value;

    let weaponNodes = weaponSelect.childNodes;
    let lastWeapon;
    for(let i=0; i<weaponNodes.length; i++){

        if( weaponNodes[i].nodeName.toLowerCase() === 'option' ) {
            let valueOne = weaponNodes[i].getAttributeNode('gameId').value;
            let valueTwo = weaponNodes[i].getAttributeNode('weaponcatid').value;

            if( valueOne !== gameId ||  valueTwo !== weaponCatId ){

               weaponNodes[i].style.display = 'none';

            }else{
                lastWeapon = weaponNodes[i].value;
            }
        }
    }
    



    document.addEventListener('input', function (event){

        if(event.target.id !== 'wcat-select' && event.target.id !== 'g-select' ) return;

        if(event.target.id === 'wcat-select'){

            gameId = gameSelect.value;
            weaponCatId = weaponcatSelect.value;

            weaponNodes = weaponSelect.childNodes;

            for(let i=0; i<weaponNodes.length; i++){

                if( weaponNodes[i].nodeName.toLowerCase() == 'option' ) {
                    weaponNodes[i].style.display = 'none';
                    valueOne = weaponNodes[i].getAttributeNode('gameId').value;
                    valueTwo = weaponNodes[i].getAttributeNode('weaponcatid').value;

                    if( valueOne == gameId &&  valueTwo == weaponCatId ){

                        weaponNodes[i].style.display = 'block';

                    }
                }
            }
        }


        if(event.target.id === 'g-select'){


            gameId = gameSelect.value;
            weaponCatId = weaponcatSelect.value;

            weaponNodes = weaponSelect.childNodes;

            for(let i=0; i<weaponNodes.length; i++){

                if( weaponNodes[i].nodeName.toLowerCase() == 'option' ) {
                    weaponNodes[i].style.display = 'none';
                    valueOne = weaponNodes[i].getAttributeNode('gameId').value;
                    valueTwo = weaponNodes[i].getAttributeNode('weaponcatid').value;

                    if( valueOne == gameId &&  valueTwo == weaponCatId ){

                        weaponNodes[i].style.display = 'block';

                    }
                }
            }

            gameName.value = event.target.options[event.target.selectedIndex].text


        }


    });


    //console.log(weaponSelect.options.item(0).getAttribute('gameId'));
    //console.log( gameSelect.options[gameSelect.selectedIndex].value );

});



