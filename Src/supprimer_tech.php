<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'adminweb')) {
    header('Location: index.php');
    exit;
}
if (isset($_GET['login'])) {
    unset($_SESSION['loginOriginal']);
    $_SESSION['loginOriginal'] = $_GET['login'];
}

if (!isset($_SESSION['loginOriginal'])) {
    header('location: adminweb.php?error=' . urlencode('Aucun utilisateur cible spécifié.'));
    exit;
}

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion à la base de données échouée : " . mysqli_connect_error());
}
$sql_prep = "SELECT role FROM Users WHERE login = ?;";
$stmt = mysqli_prepare($conn, $sql_prep);

mysqli_stmt_bind_param($stmt, 's', $_SESSION['loginOriginal']);
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, $role);
if (mysqli_stmt_fetch($stmt)) {
    if ($role == 'adminweb') {
        $error_message = "Vous ne pouvez pas supprimer d'admin web !";
        if (isset($_SESSION['loginOriginal'])) {
            unset($_SESSION['loginOriginal']);
        }
        header('location: adminweb.php?' . 'error=' . $error_message);
        exit();
    }
}
mysqli_stmt_close($stmt);


if (isset($_POST['login'])) {
    if (($_POST['login'] != $_SESSION['loginOriginal'])) {
        header('location: supprimer_tech.php?login=' . urlencode($_SESSION['loginOriginal']) . '&error=' . urlencode('Le login que vous avez inscrit est incorrect ! ' . 'Ce que vous avez inscrit :' . $_POST['login']));
        exit();
    }


    $sql_prep = "DELETE FROM Users WHERE login = ?;";
    $stmt = mysqli_prepare($conn, $sql_prep);
    if ($stmt === false) {
        $error_message = "Erreur de préparation de la requête de suppression : " . mysqli_error($conn);
        header('location: supprimer_tech.php?login=' . urlencode($_SESSION['loginOriginal']) . '&error=' . $error_message);
        exit();

    } else {

        mysqli_stmt_bind_param($stmt, 's', $_SESSION['loginOriginal']);
        if (mysqli_stmt_execute($stmt)) {
            unset($_SESSION['loginOriginal']);
            header('location: adminweb.php?success=' . urlencode('L\'utilisateur ' . $_POST['login'] . ' a été supprimé.'));

            mysqli_stmt_close($stmt);

            mysqli_close($conn);
            exit();
        } else {
            $error_message = "Erreur lors de la suppression de l'utilisateur : " . mysqli_error($conn);

            mysqli_stmt_close($stmt);

            mysqli_close($conn);
            header('location: supprimer_tech.php?login=' . urlencode($_SESSION['loginOriginal']) . '&error=' . $error_message);
            exit();

        }


    }
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

<div class="uk-container uk-margin-medium-top uk-text-center">
    <?php include_once 'alerts.php'; ?>

    <article class="uk-article">
        <h1 class="sansation-bold">Suppression du technicien : <?php echo $_SESSION['loginOriginal'] ?> </h1>
        <p class="sansation-regular-italic">Veuillez confirmer la suppression en réecrivant le nom de l'utilisateur </p>

        <form id="loginForm"
              class="uk-form-stacked uk-width-1-3@s uk-align-center uk-box-shadow-small uk-padding-small  uk-border-rounded"
              action="supprimer_tech.php" method="post">


            <div class="uk-margin">

                <label class="uk-form-label sansation-regular" for="login">Identifiant :</label>
                <div class="uk-form-controls">
                    <input class="uk-input" id="login" name="login" type="text" required>
                </div>

            </div>

            <button id="boutonEnvoyer" class="uk-button uk-button-danger" type="submit">Supprimer</button>
        </form>


    </article>
</div>
<?php include_once "footer.php"; ?>
</body>


</html>
