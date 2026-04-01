const SEARCH_INPUT = document.querySelector('input[id*=_search_nom]');
const JAVASCRIPT_SEARCH = document.getElementById('javascript_search');
const HTML_CLEAR_BTN = document.getElementById('clear-btn');
const HTML_NB_SEARCH_RESULTS = document.getElementById('nbSearchResults');

function filterSorties() {
    if (SEARCH_INPUT.value.length > 0) {
        HTML_CLEAR_BTN.style.display = '';
    }
    if (JAVASCRIPT_SEARCH.checked) {
        const query = SEARCH_INPUT.value.trim().toLowerCase();
        let i = 0;
        const rows = document.querySelectorAll('tr:has(.recherchable)');
        rows.forEach(tr => {
            const cells = Array.from(tr.querySelectorAll('.recherchable'));
            const isMatch = query === '' || cells.some(td =>
                td.innerText.trim().toLowerCase().includes(query.toLowerCase())
            );
            if (isMatch) {
                tr.style.display = '';
                i++;
            } else {
                tr.style.display = 'none';
            }
        });
        HTML_NB_SEARCH_RESULTS.innerText = i;
    }
}

if (JAVASCRIPT_SEARCH) {
    JAVASCRIPT_SEARCH.addEventListener('click', () => {
        if (!JAVASCRIPT_SEARCH.checked) {
            document.querySelectorAll("tr").forEach(tr => {
                tr.style.display = '';
            })
            clearInput();
        } else {
            filterSorties();
        }
    })
}

if (HTML_CLEAR_BTN) {
    HTML_CLEAR_BTN.addEventListener('click', clearInput)
}

function clearInput() {
    SEARCH_INPUT.value = '';
    HTML_CLEAR_BTN.style.display = 'none';
    filterSorties();
}

if (SEARCH_INPUT) {
    SEARCH_INPUT.addEventListener('keyup', filterSorties);
}
