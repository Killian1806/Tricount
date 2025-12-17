<?php ob_start() ?>

<div class="container">
    <header>
        <h1>Mes Groupes</h1>
        <p class="subtitle">Gérez vos dépenses partagées facilement</p>
    </header>

    <div class="tricount-list">
        <?php if (empty($tricounts)): ?>
            <p style="text-align:center; color:#888;">Aucun groupe pour le moment. Cliquez sur +</p>
        <?php else: ?>
            <?php foreach ($tricounts as $t): ?>
                <a href="/tricount/detail?id=<?= $t['id'] ?>" class="tricount-card">
                    <h3><?= htmlspecialchars($t['title']) ?></h3>
                    <div class="card-meta">
                        <span>👥 Partagé en <?= $t['money'] ?></span>
                        <span>📅 <?= date('d/m/Y', strtotime($t['creation'])) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button id="openModal" class="fab">+</button>

    <div id="modal-add" class="modal">
        <div class="modal-content">
            <form action="/" method="POST">
                <h3>Nouveau Groupe</h3>
                <input type="text" name="title" placeholder="Nom (ex: Vacances Ski)" required>
                <select name="currency">
                    <option value="EUR">Euro (€)</option>
                    <option value="USD">Dollar ($)</option>
                    <option value="CHF">Franc Suisse</option>
                </select>
                <button type="submit" class="btn-submit">Créer le groupe</button>
            </form>
        </div>
    </div>
</div>

<?php
render('default', true, [
    'title' => 'Mes Groupes',
    'css' => 'home',
    'content' => ob_get_clean(),
    'js' => 'home',
]);
?>