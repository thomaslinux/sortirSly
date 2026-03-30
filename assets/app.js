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

let selectVille;
let selectLieu;
let villes = [];

async function callAPI(url) {
    let val = await fetch(url)
    if (val.status === 200 && val.ok) {
        return val.json();
    }
}

async function init() {

    selectVille = document.getElementById("sortie_villes");
    const opt = document.createElement("option");
    opt.value = '';
    opt.selected = 'hidden';
    opt.textContent = `- Choisir une ville -`;
    selectVille.appendChild(opt);
    selectLieu = document.getElementById("sortie_lieu");
    selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';
    villes = await callAPI("http://localhost:8081/projet_sortir/public/api/villes");

    displayLieu();
}

async function displayLieu() {

    selectVille.addEventListener("change", async (event) => {
        selectLieu.innerHTML = "";
        selectLieu.innerHTML = '<option value="" selected hidden>- Lieu de sortie -</option>';
        let idVille = parseInt(event.target.value);
        let ville = villes.find(v => v.id === idVille);

        for (const l of ville.lieux) {
            const opt = document.createElement("option");
            opt.value = l.nom;
            opt.textContent = `${l.nom}`;
            selectLieu.appendChild(opt);
        }
    })
}


(function trierLesTablesParTh() {
    document.querySelectorAll('th').forEach((element) => { // Table headers
        element.addEventListener('click', function () {
            let table = this.closest('table');

            // If the column is sortable
            if (this.querySelector('span')) {
                let order_icon = this.querySelector('span');
                let order = encodeURI(order_icon.innerHTML).includes('%E2%86%91') ? 'desc' : 'asc';
                let separator = '-----'; // Separate the value of it's index, so data keeps intact

                let value_list = {}; // <tr> Object
                let obj_key = []; // Values of selected column

                let string_count = 0;
                let number_count = 0;

                // <tbody> rows
                table.querySelectorAll('tbody tr').forEach((line, index_line) => {
                    // Value of each field
                    let key = line.children[element.cellIndex].textContent.toUpperCase();

                    // Check if value is date, numeric or string
                    if (line.children[element.cellIndex].hasAttribute('data-timestamp')) {
                        // if value is date, we store it's timestamp, so we can sort like a number
                        key = line.children[element.cellIndex].getAttribute('data-timestamp');
                    } else if (key.replace('-', '').match(/^[0-9,.]*$/g)) {
                        number_count++;
                    } else {
                        string_count++;
                    }

                    value_list[key + separator + index_line] = line.outerHTML.replace(/(\t)|(\n)/g, ''); // Adding <tr> to object
                    obj_key.push(key + separator + index_line);
                });
                if (string_count === 0) { // If all values are numeric
                    obj_key.sort(function (a, b) {
                        return a.split(separator)[0] - b.split(separator)[0];
                    });
                } else {
                    obj_key.sort();
                }

                if (order === 'desc') {
                    obj_key.reverse();
                    order_icon.innerHTML = '&darr;';
                } else {
                    order_icon.innerHTML = '&uarr;';
                }

                let html = '';
                obj_key.forEach(function (chave) {
                    html += value_list[chave];
                });
                table.getElementsByTagName('tbody')[0].innerHTML = html;
            }
        });
    });
})();

















