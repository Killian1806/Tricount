// ===================================
// GESTION DES ONGLETS
// ===================================
function switchTab(event, tabId) {
    // Masquer tous les contenus
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.remove('active'));

    // Désactiver tous les liens
    const links = document.querySelectorAll('.tab-link');
    links.forEach(link => link.classList.remove('active'));

    // Activer l'onglet sélectionné
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}

// ===================================
// GESTION DES MODALES
// ===================================

// Ouvrir la modale d'ajout de dépense
const openExpenseModalBtn = document.getElementById('openExpenseModal');
const expenseModal = document.getElementById('modal-add-expense');

if (openExpenseModalBtn && expenseModal) {
    openExpenseModalBtn.addEventListener('click', function() {
        expenseModal.style.display = 'flex';
    });
}

// Ouvrir la modale d'invitation (via la barre ou le bouton)
const openInviteModalBtn = document.getElementById('openInviteModal');
const inviteModal = document.getElementById('modal-invite');

if (openInviteModalBtn && inviteModal) {
    openInviteModalBtn.addEventListener('click', function() {
        inviteModal.style.display = 'flex';
    });
}

// Fermer les modales avec le bouton X
const closeButtons = document.querySelectorAll('.modal-close');
closeButtons.forEach(button => {
    button.addEventListener('click', function() {
        const modalId = this.getAttribute('data-modal');
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    });
});

// Fermer les modales en cliquant en dehors
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// Fermer avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
    }
});