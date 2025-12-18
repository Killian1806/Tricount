<?php
$id = $_GET['id'] ?? null;
$tricountModel = new Models\Tricount();

if (!$id) {
    header('Location: /');
    exit;
}

// --- LOGIQUE D'AJOUT DE DÉPENSE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $payerId = $_POST['payer_id'];

    if ($tricountModel->addExpense($id, $description, $amount, $category, $payerId)) {
        // Redirection vers la même page pour éviter le renvoi du formulaire au refresh
        header("Location: groupe?id=" . $id);
        exit;
    }
}

$group = $tricountModel->getById($id);
$expenses = $tricountModel->getExpensesByGroup($id);
$participants = $tricountModel->getParticipants($id);

$totalSpent = 0;
foreach ($expenses as $e) { $totalSpent += $e['amount']; }

render('groupe', false, [
    'group' => $group,
    'expenses' => $expenses,
    'participants' => $participants,
    'totalSpent' => $totalSpent,
    'js' => 'groupe', // Utilise groupe.js pour que switchTab fonctionne
    'css' => 'groupe'
]);