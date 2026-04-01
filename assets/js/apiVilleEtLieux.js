window.onload = init;

let selectVille = document.getElementById("sortie_villes");
let selectLieu = document.getElementById("sortie_lieu");
let selectLieu2 = document.getElementById("sortie_lieu2");
let ville = selectVille.closest('.form-group');
let lieu = selectLieu.closest('.form-group');
let lieu2 = selectLieu2.closest('.form-group');


async function callAPI(url) {
    let val = await fetch(url)
    if (val.status === 200 && val.ok) {
        return val.json();
    }
}

function init() {
    showHide();
    modifLieux();
    displayLieu();
}

async function displayLieu() {

    selectVille.addEventListener("change", async (event) => {
        selectLieu.innerHTML = "";
        selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';
        let idVille = parseInt(event.target.value);

        let lieux = await callAPI(`api/villes/${idVille}/lieux`)

        for (const l of lieux) {
            const opt = document.createElement("option");
            opt.value = l.id;
            opt.textContent = `${l.nom}`;
            document.getElementById("sortie_lieu").appendChild(opt);
        }
    });
}

function showHide() {
    ville.classList.add('show-me');
    lieu.classList.add('show-me');
    lieu2.classList.add('hide-me');
}

function inverse() {
    ville.classList.toggle('show-me');
    ville.classList.toggle('hide-me');

    lieu.classList.toggle('show-me');
    lieu.classList.toggle('hide-me');

    lieu2.classList.toggle('show-me');
    lieu2.classList.toggle('hide-me');
}

function modifLieux(){
    document.getElementById('mask').addEventListener("click", (e) => {
        e.preventDefault();
        inverse()
        selectLieu.innerHTML = "";
        selectVille.value = "";
        selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';

        if (document.getElementById('mask').textContent.trim() === "Ajouter un lieu") {
            document.getElementById('mask').innerHTML = "Revenir au choix par défaut"
        } else {
            document.getElementById('mask').innerHTML = "Ajouter un lieu"
        }
    })



}
