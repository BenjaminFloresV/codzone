document.addEventListener('DOMContentLoaded', ()=>{

    let loadoutItems = document.getElementById('loadout-items');
    let loadoutNodes = loadoutItems.childNodes;

    let weaponItems = document.querySelectorAll(".weapon-category-item");

    weaponItems.forEach(function (weaponItem){

        weaponItem.addEventListener('click', () =>{
            // Con esto seleccionamos el texto dentro del td del nombre de la compañía
            let wpcatId = weaponItem.getAttribute('wpcatid');

            for(let i=0; i<loadoutNodes.length; i++){

                if( loadoutNodes[i].nodeName === 'A' ) {

                    loadoutNodes[i].setAttribute('style','display: none !important');
                    let wpcatValue = loadoutNodes[i].getAttributeNode('wpcatid').value;

                    if( wpcatValue === wpcatId ){
                        loadoutNodes[i].setAttribute('style','display: block !important');
                    }
                }
            }

        });

    });

    let openLoadoutModals = document.getElementById( 'loadouts-button' );



});