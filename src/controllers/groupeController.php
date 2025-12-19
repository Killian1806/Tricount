<?php
session_start(); // ⬅️ AJOUT IMPORTANT

$id = $_GET['id'] ?? null;
$tricountModel = new Models\Tricount();

if (!$id) {
    header('Location: /');
    exit;
}

$group = $tricountModel->getById($id);

if (!$group) {
    header('Location: /');
    exit;
}

// --- LOGIQUE D'AJOUT DE PARTICIPANT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_participant'])) {
    $participantName = trim($_POST['participant_name']);
    $participantEmail = trim($_POST['participant_email']) ?: null;

    // Debug temporaire - À RETIRER après
    error_log("=== DEBUG ADD PARTICIPANT ===");
    error_log("Participant Name: " . $participantName);
    error_log("Participant Email: " . ($participantEmail ?? 'NULL'));
    error_log("Tricount ID: " . $id);

    if (!empty($participantName)) {
        $result = $tricountModel->addParticipant($id, $participantName, $participantEmail);
        
        error_log("Result: " . ($result ? 'TRUE' : 'FALSE'));
        
        if ($result) {
            $_SESSION['success'] = "Participant ajouté avec succès !";
            header("Location: groupe?id=" . $id);
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout du participant.";
        }
    } else {
        $_SESSION['error'] = "Le nom du participant est requis.";
    }
}

// --- LOGIQUE DE SUPPRESSION DE PARTICIPANT ---
if (isset($_GET['remove_participant'])) {
    $participantId = (int)$_GET['remove_participant'];
    
    // Vérifier que le participant n'a pas de dépenses associées
    if ($tricountModel->removeParticipant($participantId, $id)) {
        header("Location: groupe?id=" . $id);
        exit;
    }
}

// --- LOGIQUE D'AJOUT DE DÉPENSE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $description = trim($_POST['description']);
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $payerId = $_POST['payer_id'];

    if (!empty($description) && !empty($amount) && !empty($category) && !empty($payerId)) {
        if ($tricountModel->addExpense($id, $description, $amount, $category, $payerId)) {
            header("Location: groupe?id=" . $id);
            exit;
        }
    }
}

// Récupération des données
$expenses = $tricountModel->getExpensesByGroup($id);
$participants = $tricountModel->getParticipants($id);

$totalSpent = 0;
foreach ($expenses as $e) { 
    $totalSpent += $e['amount']; 
}

render('groupe', false, [
    'group' => $group,
    'expenses' => $expenses,
    'participants' => $participants,
    'totalSpent' => $totalSpent,
]);