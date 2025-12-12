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

$sql = "SELECT login, role, creation_date, creation_time FROM Users ORDER BY role, login";
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
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav2.ico"/>
    <title>Administration Web - Gestion</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css"/>
    <script src="Ressources/js/uikit.js"></script>
    <link rel="stylesheet" href="CSS/uikit-rtl.css"/>
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div class="uk-container uk-margin-medium-top">
    <?php include_once 'alerts.php'; ?>

    <h1 class="uk-text-center sansation-bold">Espace Administrateur Web</h1>


    <div class="uk-container uk-margin-top">
        <h1>Gestion des Utilisateurs</h1>

        <?php if ($user_count > 0): ?>
            <p>Nombre total d'utilisateurs : <b class="sansation-bold"> <?php echo $user_count; ?> </b>
            </p>

            <table class="uk-table uk-table-striped uk-table-hover uk-table-responsive uk-table-divider">
                <thead>
                <tr>
                    <th>Identifiant (Login)</th>
                    <th>Rôle</th>
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
                        <td data-label="Rôle"><?php echo htmlspecialchars($row['role']); ?></td>
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
            <div class="uk-alert-warning" uk-alert>
                <p>Aucun utilisateur trouvé dans la base de données.</p>
            </div>
        <?php endif; ?>
        <hr>
        <h4 class="sansation-regular">Ajouter un technicien</h4>
        <form class="uk-grid-small" action="ajouter_technicien.php" method="post">
            <div class="uk-width-1-3@s">
                <label>
                    <input class="uk-input" type="text" name="login" placeholder="Identifiant (ex: tech2)" required>
                </label>
            </div>
            <div class="uk-width-1-3@s">
                <label>
                    <input class="uk-input" type="password" name="password" placeholder="Mot de passe" required>
                </label>
            </div>
            <div class="uk-width-1-3@s">
                <button class="uk-button uk-button-primary" type="submit">Créer le compte</button>
            </div>
        </form>
    </div>

    <div class="uk-grid-match uk-child-width-1-2@m" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">Systèmes d'Exploitation (OS)</h3>
                <form class="uk-flex uk-margin-top" method="POST" action="ajout_os.php">
                    <label>
                        <input class="uk-input uk-width-expand" type="text" name="os" id="os" placeholder="Nouvel OS">
                    </label>
                    <button class="uk-button uk-button-secondary uk-margin-left">Ajouter</button>
                </form>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">Constructeurs</h3>
                <form class="uk-flex uk-margin-top" method="POST" action="ajout_manufacturer.php">
                    <label>
                        <input class="uk-input uk-width-expand" type="text" name="manufacturer" id="manufacturer"
                               placeholder="Nouveau Constructeur">
                    </label>
                    <button class="uk-button uk-button-secondary uk-margin-left">Ajouter</button>
                </form>
            </div>
        </div>
    </div>

    <div class="uk-margin-medium-top uk-card uk-card-secondary uk-card-body">
        <h3 class="uk-card-title">Zone de Rebut</h3>
        <p>Verrouiller la liste du rebut empêche tout nouvel ajout ou modification par les techniciens pour préparer
            l'exportation définitive.</p>
        <button class="uk-button uk-button-danger">Verrouiller la liste du rebut (Audit)</button>
        <button class="uk-button uk-button-default">Exporter CSV Rebut</button>
    </div>
</div>

</body>
</html>
