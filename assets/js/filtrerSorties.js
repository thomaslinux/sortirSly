const SEARCH_INPUT = document.getElementById('sortie_search_nom');
const CLIENT_SEARCH = document.getElementById('client_search');
const HTML_CLEAR_BTN = document.getElementById('clear-btn');
const HTML_NB_SORTIES_TROUVEES = document.getElementById('nbSortiesTrouvees');
const HTML_INSCRIT = document.getElementById('sortie_search_inscrit');
const HTML_PAS_INSCRIT = document.getElementById('sortie_search_pasInscrit');


function filterSorties() {
    if (SEARCH_INPUT.value.length > 0) {
        HTML_CLEAR_BTN.style.display = '';
    }
    if (CLIENT_SEARCH.checked) {
        const query = SEARCH_INPUT.value.trim().toLowerCase();
        let i = 0;
        document.querySelectorAll('tbody .event-name a').forEach(a => {
            const tr = a.closest('tr');
            if (query === '' || a.innerText.trim().toLowerCase().includes(query)) {
                tr.style.display = '';
                i++;
            } else {
                tr.style.display = 'none';
            }
        });
        HTML_NB_SORTIES_TROUVEES.innerText = i;
    }
}

CLIENT_SEARCH.addEventListener('click', () => {
    if (!CLIENT_SEARCH.checked) {
        document.querySelectorAll("tr").forEach(tr => {
            tr.style.display = '';
        })
        clearInput();
    } else {
        filterSorties();
    }
})

HTML_CLEAR_BTN.addEventListener('click', clearInput)

function clearInput() {
    SEARCH_INPUT.value = '';
    HTML_CLEAR_BTN.style.display = 'none';
    filterSorties();
}

SEARCH_INPUT.addEventListener('keyup', filterSorties);
