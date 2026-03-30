const searchInput = document.getElementById('sortie_search_nom');
const HTML_nbSortiesTrouvees = document.getElementById('nbSortiesTrouvees');

function filterSorties() {
    const query = searchInput.value.trim().toLowerCase();
    let i = 0;
    document.querySelectorAll('tbody .event-name a').forEach(a => {
        const tr = a.closest('tr');
        if (query === '' || a.innerText.trim().toLowerCase().includes(query)) {
            tr.style.display = '';
            i++;
            HTML_nbSortiesTrouvees.innerText = i;
        } else {
            tr.style.display = 'none';
        }
    });
}

searchInput.addEventListener('keyup', filterSorties);
