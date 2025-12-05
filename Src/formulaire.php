<?php
$error_message = null;

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="Ressources/logo.ico"/>

    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css" />
    <link rel="stylesheet" href="CSS/uikit-rtl.css" />
    <script src="/Ressources/js/uikit.js"></script>

    <title>Connexion InkWare</title>
</head>
<body>


<?php include_once "navbar.php"; ?>

<div class="uk-container uk-margin-large-top uk-text-center">
    <article class="uk-article">
        <h1 class="sansation-bold">Connexion</h1>
        <p class="sansation-regular-italic">Connectez-vous pour accéder à votre espace.</p>

        <?php if ($error_message): ?>
            <div class="uk-alert-danger uk-width-1-3@s uk-align-center uk-margin-medium" uk-alert style="background: #8F1E24">
                <a class="uk-alert-close" uk-close></a>
                <p><?= $error_message ?></p>
            </div>
        <?php endif; ?>
        <form id="loginForm" class="uk-form-stacked uk-width-1-3@s uk-align-center uk-box-shadow-small uk-padding-small  uk-border-rounded"
              action="login.php" method="post">

            <div class="uk-margin">
                <label class="uk-form-label sansation-regular" for="login">Identifiant :</label>
                <div class="uk-form-controls">
                    <input class="uk-input" id="login" name="login" type="text" required>
                </div>
            </div>

            <div class="uk-margin">
                <label class="uk-form-label sansation-regular" for="password">Mot de passe :</label>
                <div class="uk-form-controls">
                    <input class="uk-input" id="password" name="password" type="password" required>
                </div>
            </div>

            <button id="boutonEnvoyer" class="uk-button uk-button-primary" type="submit">Se connecter</button>
        </form>



    </article>
</div>



</body>
</html>
