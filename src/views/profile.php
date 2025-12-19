<?php ob_start() ?>

<?php if (!empty($error['global'])): ?>
    <p class="message error"><?= $error['global'] ?></p>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <p class="message success"><?= $_SESSION['success'] ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!isset($_SESSION['connect']) || !$_SESSION['connect']) { ?>
    <!-- FORMULAIRE DE CRÉATION DE COMPTE -->
    <h2>Créer un compte</h2>
    <form method="post">
        <label for="firstname">Prénom : </label>
        <input id="firstname" type="text" name="firstname" value="<?= $_POST['firstname'] ?? '' ?>">
        <?php if (!empty($error['firstname'])): ?>
            <small><?= $error['firstname'] ?></small>
        <?php endif; ?>

        <label for="name">Nom : </label>
        <input id="name" type="text" name="name" value="<?= $_POST['name'] ?? '' ?>">
        <?php if (!empty($error['name'])): ?>
            <small><?= $error['name'] ?></small>
        <?php endif; ?>

        <label for="password">Mot de Passe : </label>
        <input id="password" type="password" name="password">
        <?php if (!empty($error['password'])): ?>
            <small><?= $error['password'] ?></small>
        <?php endif; ?>

        <label for="verifyPassword">Vérifier le mot de passe : </label>
        <input id="verifyPassword" type="password" name="verifyPassword">

        <label for="email">E-mail : </label>
        <input id="email" type="email" name="email" value="<?= $_POST['email'] ?? '' ?>">
        <?php if (!empty($error['email'])): ?>
            <small><?= $error['email'] ?></small>
        <?php endif; ?>

        <label for="iban">Numéro d'IBAN : </label>
        <input id="iban" type="text" name="iban" value="<?= $_POST['iban'] ?? '' ?>">
        <?php if (!empty($error['iban'])): ?>
            <small><?= $error['iban'] ?></small>
        <?php endif; ?>

        <button type="submit" name="create" value="1">Envoyer</button>
    </form>

    <!-- FORMULAIRE DE CONNEXION -->
    <h2>Se connecter</h2>
    <form method="post">
        <label for="email-login">E-mail : </label>
        <input id="email-login" type="email" name="email">
        <?php if (!empty($error['login'])): ?>
            <small><?= $error['login'] ?></small>
        <?php endif; ?>

        <label for="password-login">Mot de Passe : </label>
        <input id="password-login" type="password" name="password">

        <button type="submit" name="connect" value="1">Envoyer</button>
    </form>

<?php } else { ?>
    <!-- UTILISATEUR CONNECTÉ -->

    <?php if (isset($_GET['action']) && $_GET['action'] === 'update_info'): ?>
        <!-- FORMULAIRE DE MISE À JOUR DES INFOS -->
        <h2>Mettre à jour mes informations</h2>
        <?php if ($userData): ?>
            <form method="post">
                <label for="firstname-update">Prénom : </label>
                <input id="firstname-update" type="text" name="firstname" value="<?= htmlspecialchars($userData['firstname']) ?>">
                <?php if (!empty($error['firstname'])): ?>
                    <small><?= $error['firstname'] ?></small>
                <?php endif; ?>

                <label for="name-update">Nom : </label>
                <input id="name-update" type="text" name="name" value="<?= htmlspecialchars($userData['name']) ?>">
                <?php if (!empty($error['name'])): ?>
                    <small><?= $error['name'] ?></small>
                <?php endif; ?>

                <label for="email-update">E-mail : </label>
                <input id="email-update" type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>">
                <?php if (!empty($error['email'])): ?>
                    <small><?= $error['email'] ?></small>
                <?php endif; ?>

                <label for="iban-update">Numéro d'IBAN : </label>
                <input id="iban-update" type="text" name="iban" value="<?= htmlspecialchars($userData['iban']) ?>">
                <?php if (!empty($error['iban'])): ?>
                    <small><?= $error['iban'] ?></small>
                <?php endif; ?>

                <button type="submit" name="updateInfo" value="1">Mettre à jour</button>
            </form>
            <div style="text-align: center; margin-top: 15px;">
                <a href="profile">Annuler</a>
            </div>
        <?php endif; ?>

    <?php elseif (isset($_GET['action']) && $_GET['action'] === 'change_password'): ?>
        <!-- FORMULAIRE DE CHANGEMENT DE MOT DE PASSE -->
        <h2>Changer mon mot de passe</h2>
        <form method="post">
            <label for="current-password">Mot de passe actuel : </label>
            <input id="current-password" type="password" name="current_password" required>
            <?php if (!empty($error['current_password'])): ?>
                <small><?= $error['current_password'] ?></small>
            <?php endif; ?>

            <label for="new-password">Nouveau mot de passe : </label>
            <input id="new-password" type="password" name="new_password" required>
            <?php if (!empty($error['new_password'])): ?>
                <small><?= $error['new_password'] ?></small>
            <?php endif; ?>

            <label for="verify-new-password">Confirmer le nouveau mot de passe : </label>
            <input id="verify-new-password" type="password" name="verify_new_password" required>

            <?php if (!empty($error['password_global'])): ?>
                <small><?= $error['password_global'] ?></small>
            <?php endif; ?>

            <button type="submit" name="updatePassword" value="1">Changer le mot de passe</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="profile">Annuler</a>
        </div>

    <?php elseif (isset($_GET['action']) && $_GET['action'] === 'delete_account'): ?>
        <!-- CONFIRMATION DE SUPPRESSION DE COMPTE -->
        <h2>Supprimer mon compte</h2>
        <p class="message error">Cette action est irréversible. Veuillez confirmer avec votre mot de passe.</p>
        <form method="post">
            <label for="password-delete">Mot de passe : </label>
            <input id="password-delete" type="password" name="password" required>
            <?php if (!empty($error['delete'])): ?>
                <small><?= $error['delete'] ?></small>
            <?php endif; ?>
            <button type="submit" name="confirmDelete" value="1" style="background-color: #d32f2f;">Supprimer définitivement</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="profile">Annuler</a>
        </div>

    <?php else: ?>
        <!-- VUE PRINCIPALE DU PROFIL -->
        
        <!-- Photo de profil -->
        <div class="profile-avatar">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23e0e0e0'/%3E%3Ccircle cx='50' cy='40' r='18' fill='%23999'/%3E%3Cpath d='M 20 85 Q 20 60 50 60 Q 80 60 80 85 Z' fill='%23999'/%3E%3C/svg%3E" alt="Avatar">
        </div>

        <!-- Carte d'informations personnelles -->
        <?php if ($userData): ?>
            <div class="info-card">
                <div class="info-item">
                    <div class="info-label">Nom</div>
                    <div class="info-value"><?= htmlspecialchars($userData['name']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Prénom</div>
                    <div class="info-value"><?= htmlspecialchars($userData['firstname']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($userData['email']) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Informations bancaires -->
        <h2>Informations bancaires</h2>
        <?php if ($userData): ?>
            <div class="bank-info">
                <div class="bank-item">
                    <div class="icon">🏦</div>
                    <div class="bank-content">
                        <div class="bank-label">IBAN</div>
                        <div class="bank-value"><?= htmlspecialchars($userData['iban']) ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Boutons d'action -->
        <a href="?action=update_info" class="action-button">
            <span class="icon">✏️</span>
            <span>Modifier mes informations</span>
        </a>

        <a href="?action=change_password" class="action-button">
            <span class="icon">🔒</span>
            <span>Changer mon mot de passe</span>
        </a>

        <!-- Paramètres -->
        <h2>Paramètres</h2>
        <div class="settings-container">
            <div class="setting-item">
                <span>Mode Sombre</span>
                <label class="switch">
                    <input type="checkbox" id="dark-mode-toggle" name="dark_mode">
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="setting-item">
                <span>Notifications</span>
                <label class="switch">
                    <input type="checkbox" id="notifications-toggle" name="notifications" checked>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <!-- Déconnexion -->
        <form method="post">
            <a href="#" onclick="this.closest('form').submit(); return false;" class="action-button">
                <span class="icon">🚪</span>
                <span>Déconnexion</span>
            </a>
            <button type="submit" name="deconnect" value="1" style="display: none;"></button>
        </form>

        <!-- Suppression du compte -->
        <a href="?action=delete_account" class="action-button delete">
            <span class="icon">🗑️</span>
            <span>Supprimer le compte</span>
        </a>

    <?php endif; ?>

<?php } ?>

<?php
render('default', true, [
    'title' => 'Mon Profil',
    'header_type' => 'profile',
    'css' => 'profile',
    'content' => ob_get_clean(),
    'userData' => $userData ?? null
]);
?>