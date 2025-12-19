// ===================================
// GESTION DU DARK MODE GLOBALEMENT
// ===================================

// 1. Appliquer le thème dès le chargement (évite le flash)
(function() {
  if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark-theme");
  }
})();

// 2. Gérer le toggle seulement s'il existe (page profil)
document.addEventListener("DOMContentLoaded", function() {
  const darkModeToggle = document.getElementById("dark-mode-toggle");
  
  // Si le toggle existe (on est sur la page profil)
  if (darkModeToggle) {
    // Synchroniser l'état du toggle avec le localStorage
    if (localStorage.getItem("theme") === "dark") {
      darkModeToggle.checked = true;
    }

    // Écouter les changements du toggle
    darkModeToggle.addEventListener("change", function() {
      if (darkModeToggle.checked) {
        document.body.classList.add("dark-theme");
        localStorage.setItem("theme", "dark");
      } else {
        document.body.classList.remove("dark-theme");
        localStorage.setItem("theme", "light");
      }
    });
  }
});