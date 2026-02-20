<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'adminweb')) {
    header('Location: index.php');
    exit;
}

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion à la base de données échouée : " . mysqli_connect_error());
}

$sql = "SELECT login, creation_date, creation_time FROM Users WHERE role = 'tech' ORDER BY login";
$result = mysqli_query($conn, $sql);

if ($result === false) {
    $error_message = "Erreur lors de la récupération des utilisateurs : " . mysqli_error($conn);

} else {
    $user_count = mysqli_num_rows($result);
}
$error_message = null;

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}

$success = null;
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav.ico">
    <title>Administration Web - Gestion</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css">
    <script src="Ressources/js/uikit.js"></script>
    <link rel="stylesheet" href="CSS/uikit-rtl.css">
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div class="uk-container uk-margin-medium-top">
    <?php include_once 'alerts.php'; ?>

    <h1 class="uk-text-center sansation-bold">Espace Administrateur Web</h1>


    <div class="uk-container uk-margin-top">
        <h1>Gestion des Utilisateurs</h1>

        <?php if ($user_count > 0): ?>

            <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-divider">

            <caption class="uk-text-left">Nombre total d'utilisateurs : <b class="sansation-bold"> <?php echo $user_count; ?>
            </caption>

                <thead>
                <tr>
                    <th>Identifiant (Login)</th>
                    <th>Date de création</th>
                    <th>Heure de création</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php

                while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td data-label="Identifiant"><strong><?php echo htmlspecialchars($row['login']); ?></strong>
                        </td>
                        <td data-label="Date de création"><?php echo htmlspecialchars($row['creation_date']); ?></td>
                        <td data-label="Heure de création"><?php echo htmlspecialchars($row['creation_time']); ?></td>
                        <td data-label="Actions">
                            <a href="modifier_tech.php?login=<?php echo urlencode($row['login']); ?>"
                               class="uk-button uk-button-small uk-button-primary">Modifier</a>
                            <a href="supprimer_tech.php?login=<?php echo urlencode($row['login']); ?>"
                               class="uk-button uk-button-small uk-button-danger">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="uk-alert-warning">
                <p>Aucun utilisateur trouvé dans la base de données.</p>
            </div>
        <?php endif; ?>
        <hr>

<div class="uk-margin-medium-top uk-card uk-card-default uk-card-body">
        <div class="uk-grid-match ">
            <h4 class="sansation-regular">Ajouter un technicien</h4>
            <form class="uk-grid-small" action="ajouter_technicien.php" method="post">
                <div class="uk-width-1-3@s">
                    <label for="login">Login</label>
                        <input class="uk-input" type="text" name="login" id="login" placeholder="Identifiant (ex: tech2)" required>
                </div>
                <div class="uk-width-1-3@s">
                    <label for="password">Password</label>
                        <input class="uk-input" type="password" id="password" name="password" placeholder="Mot de passe" minlength="6" maxlength="16" required>
                </div>
                <div class="uk-width-1-3@s">
                    <label for="passwordCheck">Confirmez le mot de passe</label>
                    <input class="uk-input" type="password" id="passwordCheck" name="passwordCheck" placeholder="Mot de passe" minlength="6" maxlength="16" required>
                </div>



                <div class="uk-width-1-3@s uk-margin-medium-top uk-flex-center">
                    <button class="uk-button uk-button-primary" type="submit" style="background: #76D7C4; color: #000000; ">Créer le compte</button>
                </div>
            </form>
        </div>
</div>
    </div>

    <div class="uk-grid-match uk-child-width-1-2@m uk-margin-medium-top">
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">Systèmes d'Exploitation (OS)</h3>
                <form class="uk-flex uk-margin-top" method="POST" action="ajout_os.php">
                    <div>
                    <label class="uk-form-label" for="os">OS</label>
                    <div class="uk-form-controls">
                        <input class="uk-input uk-width-expand" type="text" name="os" id="os" placeholder="Nouvel OS">
                    </div>
                </div>
                    <button class="uk-button uk-button-secondary uk-margin-left">Ajouter</button>
                </form>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">Constructeurs</h3>
                <form class="uk-flex uk-margin-top" method="POST" action="ajout_manufacturer.php">
                    <div>
                        <label for="manufacturer">Manufacturer</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-expand" type="text" name="manufacturer" id="manufacturer"
                                   placeholder="Nouveau Constructeur">
                        </div>
                    </div>
                    <button class="uk-button uk-button-secondary uk-margin-left">Ajouter</button>
                </form>
            </div>
        </div>
    </div>

    <div class="uk-margin-medium-top uk-card uk-card-secondary uk-card-body">
        <h3 class="uk-card-title">Zone de Rebut</h3>
        <p>Verrouiller la liste du rebut empêche tout nouvel ajout ou modification par les techniciens pour préparer
            l'exportation définitive.</p>
        <button class="uk-button uk-button-danger" style="background: #8F1E24">Verrouiller la liste du rebut (Audit)</button>
        <button class="uk-button uk-button-default">Exporter CSV Rebut</button>
    </div>
</div>


<?php include_once "footer.php"; ?>
</body>

</html>
