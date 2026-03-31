const searchInput = document.getElementById('sortie_search_nom');
const client_search = document.getElementById('client_search');
const HTML_clear_btn = document.getElementById('clear-btn');
const HTML_nbSortiesTrouvees = document.getElementById('nbSortiesTrouvees');

function filterSorties() {
    if (searchInput.value.length > 0) {
        console.log("hello");
        HTML_clear_btn.style.display = '';
    }
    if (client_search.checked) {
        const query = searchInput.value.trim().toLowerCase();
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
        HTML_nbSortiesTrouvees.innerText = i;
    }
}

client_search.addEventListener('click', () => {
    if (!client_search.checked) {
        let i = 0;
        document.querySelectorAll("tr").forEach(tr => {
            tr.style.display = '';
            i++;
        })
        HTML_nbSortiesTrouvees.innerText = i;
        clearInput();
    } else {
        filterSorties();
    }
})

HTML_clear_btn.addEventListener('click', clearInput)

function clearInput() {
    searchInput.value = '';
    HTML_clear_btn.style.display = 'none';
    filterSorties();
}

searchInput.addEventListener('keyup', filterSorties);
