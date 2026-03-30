import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
// import './styles/app.css';
import './styles/eniwhere2.css';
import './styles/nav2.css';
import './styles/style_formulaire.css'

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

window.onload = init;

let selectVille=   document.getElementById("sortie_villes") ;
let selectLieu=document.getElementById("sortie_lieu");
let villes = [];

async function callAPI(url) {
    let val = await fetch(url)
    if (val.status === 200 && val.ok) {
        return val.json();
    }
}
function init(){
    displayLieu();
}

async function displayLieu(){

    selectVille.addEventListener("change", async (event) =>{
        selectLieu.innerHTML = "";
        selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';
      let idVille = parseInt(event.target.value);

      let lieux = await callAPI(`http://localhost:8081/projet_sortir/public/api/villes/${idVille}/lieux`)

      for (const l of lieux){
          const opt = document.createElement("option");
          opt.value = l.id;
          opt.textContent = `${l.nom}`;
          document.getElementById("sortie_lieu").appendChild(opt);
      }
  } )
}

















