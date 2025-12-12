<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'adminweb')) {
    header('Location: index.php');
    exit;
}

$target_login = isset($_GET['login']) ? $_GET['login'] : null;

if (isset($target_login)) {
    $_SESSION['loginOriginal'] = $target_login;
} elseif (!isset($_SESSION['loginOriginal'])) {
    header('location: adminweb.php?error=' . urlencode('Aucun utilisateur cible spécifié.'));
    exit;
}

$login_original = $_SESSION['loginOriginal'];
$user_data = null;
$error_message = null;
$success_message = null;

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion à la base de données échouée : " . mysqli_connect_error());
}

$sql_fetch = "SELECT login, role, password, creation_date, creation_time FROM Users WHERE login = ?";
$stmt_fetch = $conn->prepare($sql_fetch);

if ($stmt_fetch === false) {
    $error_message = "Erreur de préparation de la requête de récupération : " . $conn->error;
} else {
    $stmt_fetch->bind_param("s", $login_original);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        if ($user_data['role'] == 'adminweb' ) {
            $error_message = "Vous ne pouvez pas modifier d'admin web ou de sys admin !";
            unset($_SESSION['loginOriginal']);
            header('location: adminweb.php?error=' . urlencode($error_message));
            exit();
        }
    } else {
        $error_message = "Utilisateur non trouvé avec le login : " . htmlspecialchars($login_original);
    }
    $stmt_fetch->close();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['old_login'])) {

    $old_login = $_POST['old_login'];
    $new_login = isset($_POST['login']) ? $_POST['login'] : '';
    $new_role = isset($_POST['role']) ? $_POST['role'] : 'tech'; // S'assurer qu'un rôle est toujours défini
    $new_password = isset($_POST['password']) ? $_POST['password'] : '';


    $update_error = false;

    if ($new_login !== $old_login) {
        $check_sql = "SELECT login FROM Users WHERE login = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $new_login);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error_message = "Erreur : Le nouveau login '{$new_login}' existe déjà.";
            $update_error = true;
        }
        $check_stmt->close();
    }


    $sql_update = "UPDATE Users SET login=?, role=? WHERE login=?";

    $stmt_update = $conn->prepare($sql_update);

    if ($stmt_update === false) {
        $error_message = "Erreur de préparation de l'UPDATE : " . $conn->error;
    } else {
        $stmt_update->bind_param("sss",
            $new_login, $new_role, $old_login
        );

        if ($stmt_update->execute()) {
            $success_message = "Technicien '{$new_login}' mis à jour avec succès !";

            if ($new_login !== $old_login) {
                $_SESSION['loginOriginal'] = $new_login;
            }

            $user_data['login'] = $new_login;
            $user_data['role'] = $new_role;
            $user_data['password'] = $new_password;

            header('location: adminweb.php?success=' . urlencode($success_message));
            exit();


        } else {
            $error_message = "Erreur lors de l'exécution de l'UPDATE : " . $stmt_update->error;
        }
        $stmt_update->close();
    }

}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav2.ico"/>
    <title>Modifier un technicien</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css"/>
    <link rel="stylesheet" href="CSS/uikit-rtl.css"/>
</head>
<body>

<?php include_once "navbar.php"; ?>
<div class="uk-container uk-margin-large-top">
    <h1 class="sansation-bold uk-text-center">Modification du technicien</h1>

    <?php include_once 'alerts.php'; ?>

    <?php if ($user_data): ?>
        <div class="uk-card uk-card-default uk-card-body uk-width-large@l uk-margin-auto">
            <h3 class="uk-card-title uk-text-center">Modification de <?= htmlspecialchars($user_data['login']) ?></h3>

            <form action="modifier_tech.php" method="POST" class="uk-form-stacked">

                <input type="hidden" name="old_login" value="<?= htmlspecialchars($login_original) ?>">

                <div>
                    <h4 class="uk-text-center"><span>Informations d'utilisateur</span></h4>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-login">Login</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-login" type="text" name="login"
                                   value="<?= htmlspecialchars($user_data['login']) ?>" maxlength="50" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-role">Rôle</label>
                        <div class="uk-form-controls">
                            <select class="uk-select uk-width-1-1" id="form-role" name="role" required>
                                <option value="tech" <?= ($user_data['role'] == 'tech') ? 'selected' : '' ?>>
                                    Technicien
                                </option>

                                <option value="sysadmin" <?= ($user_data['role'] == 'sysadmin') ? 'selected' : '' ?>>
                                    Sysadmin
                                </option>
                                <option value="visitor" <?= ($user_data['role'] == 'visitor') ? 'selected' : '' ?>>
                                    Visiteur
                                </option>

                            </select>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-login">Mot de passe</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-login" type="text" name="password"
                                   value="<?= htmlspecialchars($user_data['password']) ?>" maxlength="50" required>
                        </div>
                    </div>
                    <hr class="uk-margin-medium">

                    <div class="uk-text-center uk-text-small uk-text-muted">
                        <p>Date de création : <?= htmlspecialchars($user_data['creation_date']) ?>
                            à <?= htmlspecialchars($user_data['creation_time']) ?></p>
                    </div>

                </div>

                <div class="uk-margin uk-text-center">
                    <button class="uk-button uk-button-primary uk-button-large" type="submit">Enregistrer les
                        modifications
                    </button>
                </div>

            </form>
        </div>

    <?php else: ?>
        <div class="uk-text-center">
            <h2 class="uk-text-danger">Impossible d'afficher le formulaire. <?= htmlspecialchars($error_message) ?></h2>
            <a href="adminweb.php" class="uk-button uk-button-default">Retour à l'administration</a>
        </div>
    <?php endif; ?>

</div>

</body>
</html>