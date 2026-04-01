const dateHeureDebut = document.getElementById('sortie_dateHeureDebut');
const dateLimiteInscription = document.getElementById('sortie_dateLimiteInscription');
dateHeureDebut.addEventListener('change', () => {
    const dateDebut = dateHeureDebut.value.slice(0, 10);
    dateLimiteInscription.setAttribute('max', dateDebut);
    if (dateLimiteInscription.value > dateDebut) {
        dateLimiteInscription.value = dateDebut;
    }
});
