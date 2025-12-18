<?php
$id = $_GET['id'] ?? null;
$tricountModel = new Models\Tricount();

if (!$id) {
    header('Location: /');
    exit;
}

$group = $tricountModel->getById($id);
$expenses = $tricountModel->getExpensesByGroup($id);
$participants = $tricountModel->getParticipants($id);

// Calcul du total
$totalSpent = 0;
foreach ($expenses as $e) { $totalSpent += $e['amount']; }

render('groupe', false, [
    'group' => $group,
    'expenses' => $expenses,
    'participants' => $participants,
    'totalSpent' => $totalSpent,
    'js' => 'home',
    'css' => 'home'
]);