document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-add');
    const btnOpen = document.getElementById('openModal');
    
    // Ouvrir la modal
    btnOpen.onclick = () => {
        modal.style.display = "block";
    }

    // Fermer si on clique en dehors de la modal
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

function switchTab(evt, tabId) {
    // Cacher tous les contenus
    const contents = document.getElementsByClassName("tab-content");
    for (let content of contents) {
        content.classList.remove("active");
    }

    // Désactiver tous les boutons
    const links = document.getElementsByClassName("tab-link");
    for (let link of links) {
        link.classList.remove("active");
    }

    // Afficher l'onglet actuel
    document.getElementById(tabId).classList.add("active");
    evt.currentTarget.classList.add("active");
}