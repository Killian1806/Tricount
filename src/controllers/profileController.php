<?php

session_start();

$error = [];
$userData = null;

$user = new Models\User();

if (!empty($_SESSION['user_id'])) {
    $userData = $user->getUserById($_SESSION['user_id']);
}

if (!empty($_POST)) {
    // redirige vers l'accueil si l'utilisateur veut se déconnecter
    if (isset($_POST['deconnect'])) {
        unset($_SESSION['connect']);
        unset($_SESSION['user_id']);
        redirectTo('/');
        // pour mettre les informations à jour
    } elseif (isset($_POST['updateInfo'])) {

        if (!empty($_SESSION['user_id'])) {
            try {
                $user->setFirstname($_POST['firstname']);
            } catch (\Exception $e) {
                $error['firstname'] = $e->getMessage();
            }
            try {
                $user->setName($_POST['name']);
            } catch (\Exception $e) {
                $error['name'] = $e->getMessage();
            }
            try {
                $user->setEmail($_POST['email']);
            } catch (\Exception $e) {
                $error['email'] = $e->getMessage();
            }
            try {
                $user->setIban($_POST['iban']);
            } catch (\Exception $e) {
                $error['iban'] = $e->getMessage();
            }

            if (empty($error)) {
                if (
                    $user->updateUser(
                        $_SESSION['user_id'],
                        $user->getFirstname(),
                        $user->getName(),
                        $user->getEmail(),
                        $user->getIban()
                    )
                ) {
                    $_SESSION['success'] = 'Vos informations ont été mises à jour avec succès';
                    redirectTo('/profile');
                } else {
                    $error['global'] = 'Échec de la mise à jour';
                }
            }
        }
        // dans le cas de la modification de password
    } elseif (isset($_POST['updatePassword'])) {

        if (!empty($_SESSION['user_id']) && !empty($_POST['current_password'])) {
            $currentUser = $user->getUserById($_SESSION['user_id']);

            if ($currentUser && password_verify($_POST['current_password'], $currentUser['password'])) {

                if ($_POST['new_password'] !== $_POST['verify_new_password']) {
                    $error['new_password'] = 'Les mots de passe ne correspondent pas';
                } else {
                    try {
                        $user->setPassword($_POST['new_password']);

                        if ($user->updatePassword($_SESSION['user_id'], $user->getPassword())) {
                            $_SESSION['success'] = 'Votre mot de passe a été modifié avec succès';
                            redirectTo('/profile');
                        } else {
                            $error['password_global'] = 'Échec de la modification du mot de passe';
                        }
                    } catch (\Exception $e) {
                        $error['new_password'] = $e->getMessage();
                    }
                }
            } else {
                $error['current_password'] = 'Mot de passe actuel incorrect';
            }
        } else {
            $error['current_password'] = 'Veuillez saisir votre mot de passe actuel';
        }
        // confirmer la suppression du compte
    } elseif (isset($_POST['confirmDelete'])) {

        if (!empty($_SESSION['user_id']) && !empty($_POST['password'])) {
            $currentUser = $user->getUserById($_SESSION['user_id']);

            if ($currentUser && password_verify($_POST['password'], $currentUser['password'])) {
                if ($user->deleteUserById($_SESSION['user_id'])) {
                    unset($_SESSION['connect']);
                    unset($_SESSION['user_id']);
                    $_SESSION['success'] = 'Votre compte a été supprimé avec succès';
                    redirectTo('/');
                } else {
                    $error['delete'] = 'Échec de la suppression du compte';
                }
            } else {
                $error['delete'] = 'Mot de passe incorrect';
            }
        } else {
            $error['delete'] = 'Veuillez saisir votre mot de passe';
        }
        // si on crée son compte
    } elseif (isset($_POST['create'])) {

        try {
            $user->setFirstname($_POST['firstname']);
        } catch (\Exception $e) {
            $error['firstname'] = $e->getMessage();
        }
        try {
            $user->setName($_POST['name']);
        } catch (\Exception $e) {
            $error['name'] = $e->getMessage();
        }
        try {
            $user->setEmail($_POST['email']);
        } catch (\Exception $e) {
            $error['email'] = $e->getMessage();
        }

        if ($_POST['password'] !== $_POST['verifyPassword']) {
            $error['password'] = 'Les mots de passe ne correspondent pas';
        } else {
            try {
                $user->setPassword($_POST['password']);
            } catch (\Exception $e) {
                $error['password'] = $e->getMessage();
            }
        }

        try {
            $user->setIban($_POST['iban']);
        } catch (\Exception $e) {
            $error['iban'] = $e->getMessage();
        }

        if (empty($error)) {
            $userId = $user->register(
                $user->getFirstname(),
                $user->getName(),
                $user->getPassword(),
                $user->getEmail(),
                $user->getIban()
            );

            if ($userId) {
                $_SESSION['connect'] = true;
                $_SESSION['user_id'] = $userId;
                redirectTo('/');
            } else {
                $error['global'] = 'Échec de l\'enregistrement';
            }
        }

    } elseif (isset($_POST['connect'])) {

        $foundUser = $user->getUserByEmail($_POST['email']);

        if ($foundUser && password_verify($_POST['password'], $foundUser['password'])) {
            $_SESSION['connect'] = true;
            $_SESSION['user_id'] = $foundUser['id'];
            redirectTo('/');
        } else {
            $error['login'] = 'Email ou mot de passe incorrect';
        }
    }
}

render('profile', false, [
    'title' => 'Mon Profil',
    'header_type' => 'profile', 
    'css' => 'profile',
    'error' => $error,           
    'userData' => $userData      
]);