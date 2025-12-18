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

