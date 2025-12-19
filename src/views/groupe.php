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

    <div class="invite-bar">
        <span>👤+ Inviter des participants</span>
    </div>

    <nav class="tabs">
        <button class="tab-link active" onclick="switchTab(event, 'tab-depenses')">Dépenses</button>
        <button class="tab-link" onclick="switchTab(event, 'tab-equilibre')">Équilibre</button>
        <button class="tab-link" onclick="switchTab(event, 'tab-photos')">Photos</button>
    </nav>

    <div id="tab-depenses" class="tab-content active">
        <div class="expense-list">
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
        </div>
        
        <div class="total-bar">
            <span>Total dépensé</span>
            <strong><?= number_format($totalSpent, 2) ?> <?= $group['money'] ?></strong>
        </div>
    </div>

    <div id="tab-equilibre" class="tab-content">
        <div class="balance-list">
            <?php foreach ($participants as $p): ?>
                <div class="balance-item">
                    <span><?= htmlspecialchars($p['alias_name']) ?></span>
                    <span class="balance-value <?= $p['balance'] >= 0 ? 'positive' : 'negative' ?>">
                        <?= $p['balance'] >= 0 ? "On lui doit" : "Il doit" ?> <?= abs($p['balance']) ?> <?= $group['money'] ?>
                    </span>
                </div>
            <?php endforeach; ?>
            <button class="btn-settle">Équilibrer les comptes</button>
        </div>
    </div>

    <button id="openModal" class="fab">+</button>
</div>

<div id="modal-add" class="modal">
    <div class="modal-content">
        <form action="groupe?id=<?= $group['id'] ?>" method="POST">
            <h3>Nouvelle dépense</h3>
            <input type="text" name="description" placeholder="De quoi s'agit-il ?" required>
            <input type="number" step="0.01" name="amount" placeholder="0.00" required>
            
            <select name="category">
                <option value="transport">🚗 Transport (Essence...)</option>
                <option value="groceries">🛒 Courses</option>
                <option value="restaurant_bar">🍴 Restaurant</option>
                <option value="housing">🏠 Logement</option>
            </select>
            
            <select name="payer_id">
                <?php foreach ($participants as $p): ?>
                    <option value="<?= $p['id'] ?>">Payé par <?= htmlspecialchars($p['alias_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-submit">Ajouter</button>
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