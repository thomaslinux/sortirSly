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

let selectVille ;
let selectLieu;
let villes = [];
async function callAPI(url){
    let val= await fetch(url)
    if (val.status === 200 && val.ok){
        return val.json();
    }
}

async function init(){
    selectVille = document.getElementById("sortie_villes");
    selectLieu = document.getElementById("sortie_lieu");
    selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';
    villes = await callAPI("http://localhost:8081/projet_sortir/public/api/villes");

    displayLieu();
}

async function displayLieu(){

  selectVille.addEventListener("change", async (event) => {
        selectLieu.innerHTML = "";
        selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';
        let idVille = parseInt(event.target.value);
        let ville = villes.find(v=>v.id===idVille);

for (const l of ville.lieux){
    const opt = document.createElement("option");
    opt.value = l.nom;
    opt.textContent = `${l.nom}`;
    selectLieu.appendChild(opt);
}
})}


























