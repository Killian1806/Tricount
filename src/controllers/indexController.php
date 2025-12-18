<?php
$tricountModel = new Models\Tricount();
$userId = 1; 
$errors = [];

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $currency = $_POST['currency'] ?? 'EUR';

    if (!empty($title)) {
        if ($tricountModel->create($title, $currency, $userId)) {
            redirectTo('/'); 
            exit;
        } else {
            $errors['global'] = "Erreur lors de la création.";
        }
    }
}

if (isset($_GET['delete'])) {
    $idToDelete = (int)$_GET['delete'];
    if ($tricountModel->delete($idToDelete)) {
        redirectTo('/');
        exit;
    }
}

// Récupération des groupes pour la vue
$myTricounts = $tricountModel->getUserTricounts($userId);

render('index', false, [
    'tricounts' => $myTricounts,
    'js' => 'home',
    'css' => 'home'
]);