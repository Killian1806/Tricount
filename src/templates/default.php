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
	<header class="<?= (isset($header_type) && $header_type === 'group') ? 'header-group' : ((isset($header_type) && $header_type === 'profile') ? 'header-profile' : 'header-main') ?>">
    
    <?php if (isset($header_type) && $header_type === 'profile'): ?>
        <a href="/" class="back-btn">←</a>
        <h1><?= htmlspecialchars($title ?? 'Mon Profil') ?></h1>
        <div class="header-spacer"></div>

    <?php elseif (isset($header_type) && $header_type === 'group'): ?>
        <a href="/" class="back-btn">←</a>
        <h1><?= htmlspecialchars($title ?? 'Groupe') ?></h1>
        <div class="header-options">⋮</div>

    <?php else: ?>
        <a href="/profile"><img src="../assets/img/profil.svg" alt="profil" class="profile-icon"></a>
        <span class="app-logo-title">Tricount</span>
    <?php endif; ?>
</header>

	<main>
		<?= $content ?>

		<?php if (!empty($js)): ?>
			<script src="assets/js/<?= $js ?>.js"></script>
		<?php endif; ?>
	</main>
	<script src="../assets/js/theme.js"></script>
</body>

</html>