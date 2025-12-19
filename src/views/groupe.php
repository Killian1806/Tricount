<?php 
// Helper pour les icônes selon ton ENUM de base de données
function getCategoryIcon($cat) {
    $icons = [
        'housing' => '🏠',
        'restaurant_bar' => '🍴',
        'entertainment' => '🎭',
        'groceries' => '🛒',
        'health' => '🏥',
        'transport' => '🚗',
        'shopping' => '🛍️',
        'other' => '💰'
    ];
    return $icons[$cat] ?? '❓';
}
ob_start(); 
?>

<div class="app-container">

    <!-- Messages de succès/erreur -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            ✅ <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error">
            ❌ <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Barre d'invitation cliquable -->
    <div class="invite-bar" id="openInviteModal">
        <span>👤+ Inviter des participants</span>
    </div>

    <nav class="tabs">
        <button class="tab-link active" onclick="switchTab(event, 'tab-depenses')">Dépenses</button>
        <button class="tab-link" onclick="switchTab(event, 'tab-equilibre')">Équilibre</button>
        <button class="tab-link" onclick="switchTab(event, 'tab-participants')">Participants</button>
    </nav>

    <!-- ONGLET DÉPENSES -->
    <div id="tab-depenses" class="tab-content active">
        <div class="expense-list">
            <?php if (empty($expenses)): ?>
                <p style="text-align:center; color:#888; padding: 40px 20px;">
                    Aucune dépense pour le moment.<br>
                    Cliquez sur le bouton + pour ajouter une dépense.
                </p>
            <?php else: ?>
                <?php foreach ($expenses as $e): ?>
                    <div class="expense-card">
                        <div class="expense-icon"><?= getCategoryIcon($e['category']) ?></div>
                        <div class="expense-info">
                            <strong><?= htmlspecialchars($e['description']) ?></strong>
                            <span>Payé par <?= htmlspecialchars($e['payer_name']) ?> • <?= date('d M', strtotime($e['date'])) ?></span>
                        </div>
                        <div class="expense-amount">
                            <?= number_format($e['amount'], 2) ?> <?= $group['money'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="total-bar">
            <span>Total dépensé</span>
            <strong><?= number_format($totalSpent, 2) ?> <?= $group['money'] ?></strong>
        </div>
    </div>

    <!-- ONGLET ÉQUILIBRE -->
    <div id="tab-equilibre" class="tab-content">
        <div class="balance-list">
            <?php if (empty($participants)): ?>
                <p style="text-align:center; color:#888; padding: 40px 20px;">
                    Aucun participant pour le moment.
                </p>
            <?php else: ?>
                <?php foreach ($participants as $p): ?>
                    <div class="balance-item">
                        <span><?= htmlspecialchars($p['alias_name']) ?></span>
                        <span class="balance-value <?= $p['balance'] >= 0 ? 'positive' : 'negative' ?>">
                            <?= $p['balance'] >= 0 ? "On lui doit" : "Il doit" ?> <?= number_format(abs($p['balance']), 2) ?> <?= $group['money'] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <button class="btn-settle">Équilibrer les comptes</button>
        </div>
    </div>

    <!-- ONGLET PARTICIPANTS -->
    <div id="tab-participants" class="tab-content">
        <div class="participants-list">
            <?php if (empty($participants)): ?>
                <p style="text-align:center; color:#888; padding: 40px 20px;">
                    Aucun participant pour le moment.
                </p>
            <?php else: ?>
                <?php foreach ($participants as $p): ?>
                    <div class="participant-card">
                        <div class="participant-avatar">
                            <?= strtoupper(substr($p['alias_name'], 0, 1)) ?>
                        </div>
                        <div class="participant-info">
                            <strong><?= htmlspecialchars($p['alias_name']) ?></strong>
                            <span class="participant-balance">
                                Balance: 
                                <span class="<?= $p['balance'] >= 0 ? 'positive' : 'negative' ?>">
                                    <?= number_format($p['balance'], 2) ?> <?= $group['money'] ?>
                                </span>
                            </span>
                        </div>
                        <?php if ($p['alias_name'] !== 'Moi'): ?>
                            <a href="?id=<?= $group['id'] ?>&remove_participant=<?= $p['id'] ?>" 
                               class="btn-remove-participant"
                               onclick="return confirm('Supprimer ce participant ?')">
                                ×
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bouton flottant pour ajouter une dépense -->
    <button id="openExpenseModal" class="fab">+</button>
</div>

<!-- MODAL: AJOUTER UNE DÉPENSE -->
<div id="modal-add-expense" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nouvelle dépense</h3>
            <span class="modal-close" data-modal="modal-add-expense">&times;</span>
        </div>
        <form action="groupe?id=<?= $group['id'] ?>" method="POST">
            <input type="text" name="description" placeholder="De quoi s'agit-il ?" required>
            <input type="number" step="0.01" name="amount" placeholder="0.00" required>
            
            <select name="category" required>
                <option value="">Choisir une catégorie</option>
                <option value="transport">🚗 Transport</option>
                <option value="groceries">🛒 Courses</option>
                <option value="restaurant_bar">🍴 Restaurant / Bar</option>
                <option value="housing">🏠 Logement</option>
                <option value="entertainment">🎭 Divertissement</option>
                <option value="health">🏥 Santé</option>
                <option value="shopping">🛍️ Shopping</option>
                <option value="other">💰 Autre</option>
            </select>
            
            <select name="payer_id" required>
                <option value="">Qui a payé ?</option>
                <?php foreach ($participants as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['alias_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="add_expense" class="btn-submit">Ajouter la dépense</button>
        </form>
    </div>
</div>

<!-- MODAL: INVITER UN PARTICIPANT -->
<div id="modal-invite" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Inviter un participant</h3>
            <span class="modal-close" data-modal="modal-invite">&times;</span>
        </div>
        <form action="groupe?id=<?= $group['id'] ?>" method="POST">
            <label for="participant-name">Nom du participant :</label>
            <input type="text" 
                   id="participant-name" 
                   name="participant_name" 
                   placeholder="Ex: Marie, Thomas..." 
                   required 
                   maxlength="50">
            
            <label for="participant-email">Email (optionnel) :</label>
            <input type="email" 
                   id="participant-email" 
                   name="participant_email" 
                   placeholder="exemple@email.com">
            
            <p class="info-text">
                💡 L'email permettra d'inviter cette personne à rejoindre le groupe.
            </p>

            <button type="submit" name="add_participant" class="btn-submit">Ajouter</button>
        </form>
    </div>
</div>

<?php
render('default', true, [
    'title' => $group['title'],
    'header_type' => 'group', 
    'css' => 'groupe',
    'content' => ob_get_clean(),
    'js' => 'groupe',
]);
?>