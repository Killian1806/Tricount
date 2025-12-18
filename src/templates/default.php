<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (!empty($css)): ?>
        <link rel="stylesheet" href="assets/css/<?= $css ?>.css">
    <?php endif; ?>
    <title>Tricount<?= isset($title) ? ' - ' . $title : '' ?></title>
</head>
<body>
    <header class="<?= (isset($header_type) && $header_type === 'group') ? 'header-group' : 'header-main' ?>">
        <?php if (isset($header_type) && $header_type === 'group'): ?>
            <a href="/" class="back-btn">←</a>
            <h1><?= htmlspecialchars($title ?? 'Groupe') ?></h1>
            <div class="header-options">⋮</div>
        <?php else: ?>
            <img src="../assets/img/profil.svg" alt="profil" class="profile-icon">
            <span class="app-logo-title">Tricount</span>
        <?php endif; ?>
    </header>

    <main>
        <?= $content ?>

        <?php if (!empty($js)): ?>
            <script src="assets/js/<?= $js ?>.js"></script>
        <?php endif; ?>
    </main>
</body>
</html>