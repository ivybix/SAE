<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$is_tech = isset($_SESSION['role']) && $_SESSION['role'] === 'tech';


if (!$is_tech) {
    header('Location: inventaire.php');
    exit;
}


$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$serial = isset($_GET['serial']) ? $_GET['serial'] : null;
$monitor_data = null;
$error_message = null;

if ($serial) {
    $sql_fetch = "SELECT * FROM Monitors WHERE serial = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);

    if ($stmt_fetch === false) {
        $error_message = "Erreur de préparation de la requête de récupération : " . $conn->error;
    } else {
        $stmt_fetch->bind_param("s", $serial);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();

        if ($result->num_rows === 1) {
            $monitor_data = $result->fetch_assoc();
        } else {
            $error_message = "Écran non trouvé avec le numéro de série : " . htmlspecialchars($serial);
        }
        $stmt_fetch->close();
    }
} else {
    $error_message = "Numéro de série non spécifié.";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['old_serial'])) {

    $old_serial = $_POST['old_serial'];
    $new_serial = isset($_POST['serial_monitor']) ? $_POST['serial_monitor'] : '';

    $manufacturer = isset($_POST['manufacturer']) ? $_POST['manufacturer'] : '';
    $model = isset($_POST['model_monitor']) ? $_POST['model_monitor'] : '';
    $size_inch = isset($_POST['size_inch']) ? $_POST['size_inch'] : '';
    $resolution = isset($_POST['resolution']) ? $_POST['resolution'] : '';
    $connector = isset($_POST['connector']) ? $_POST['connector'] : '';
    $attached_to = isset($_POST['attached_to']) ? $_POST['attached_to'] : '';

    $update_error = false;

    if ($new_serial !== $old_serial) {
        $check_sql = "SELECT serial FROM Monitors WHERE serial = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $new_serial);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error_message = "Erreur : Le nouveau numéro de série '{$new_serial}' existe déjà.";
            $update_error = true;
        }
        $check_stmt->close();
    }

    if (!$update_error) {

        $sql_update = "UPDATE Monitors SET 
                        serial=?, manufacturer=?, model=?, size_inch=?, resolution=?, connector=?, attached_to=? 
                        WHERE serial=?";

        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            $error_message = "Erreur de préparation de l'UPDATE : " . $conn->error;
        } else {

            $stmt_update->bind_param("ssssssss",
                $new_serial, $manufacturer, $model, $size_inch, $resolution, $connector, $attached_to,
                $old_serial
            );

            if ($stmt_update->execute()) {
                $success_message = "Écran '{$new_serial}' mis à jour avec succès !";
                header('location: inventaire.php');
                $monitor_data = array_combine(
                    ['serial', 'manufacturer', 'model', 'size_inch', 'resolution', 'connector', 'attached_to'],
                    [$new_serial, $manufacturer, $model, $size_inch, $resolution, $connector, $attached_to]
                );

            } else {
                $error_message = "Erreur lors de l'exécution de l'UPDATE : " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}


if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav.ico">
    <title>Modifier un écran</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css">
    <link rel="stylesheet" href="CSS/uikit-rtl.css">
</head>
<body>

<?php include_once "navbar.php"; ?>
<div class="uk-container uk-margin-large-top">
    <h1 class="sansation-bold uk-text-center">Modification de l'écran</h1>

    <?php if ($error_message): ?>
        <div class="uk-alert-danger" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p><?= htmlspecialchars($error_message) ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="uk-alert-success" uk-alert>
            <a class="uk-alert-close" uk-close></a>
            <p><?= htmlspecialchars($success_message) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($monitor_data): ?>
        <div class="uk-card uk-card-default uk-card-body uk-width-large@l uk-margin-auto">
            <h3 class="uk-card-title uk-text-center">Modification de l'écran (<?= htmlspecialchars($monitor_data['serial']) ?>)</h3>

            <form action="modifier_ecran.php?serial=<?= urlencode($monitor_data['serial']) ?>" method="POST" class="uk-form-stacked">

                <input type="hidden" name="old_serial" value="<?= htmlspecialchars($monitor_data['serial']) ?>">

                <div>
                    <h4 class="uk-text-center   "><span>Identification</span></h4>

                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-serial">Numéro de Série (Clé Primaire)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-serial" type="text" name="serial_monitor" value="<?= htmlspecialchars($monitor_data['serial']) ?>" maxlength="50" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-manufacturer">Fabricant</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-manufacturer" type="text" name="manufacturer" value="<?= htmlspecialchars($monitor_data['manufacturer']) ?>" maxlength="25" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-model">Modèle</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-model" type="text" name="model_monitor" value="<?= htmlspecialchars($monitor_data['model']) ?>" maxlength="25" required>
                        </div>
                    </div>
                </div>

                <hr class="uk-margin-medium">

                <div>
                    <h4 class="uk-text-center   "><span>Caractéristiques</span></h4>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-size">Dimensions (pouces)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-size" type="text" name="size_inch" value="<?= htmlspecialchars($monitor_data['size_inch']) ?>" maxlength="10" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-resolution">Résolution</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-resolution" type="text" name="resolution" value="<?= htmlspecialchars($monitor_data['resolution']) ?>" maxlength="15" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-connector">connector</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-connector" type="text" name="connector" value="<?= htmlspecialchars(isset($monitor_data['connector']) ? $monitor_data['connector'] : '') ?>" maxlength="15" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-attached-to">Machine Associée (Numéro de Série)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-attached-to" type="text" name="attached_to" value="<?= htmlspecialchars($monitor_data['attached_to']) ?>" maxlength="50">
                        </div>
                    </div>
                </div>

                <div class="uk-margin uk-text-center">
                    <button class="uk-button uk-button-primary uk-button-large" type="submit">Enregistrer les modifications</button>
                </div>

            </form>
        </div>

    <?php else: ?>
        <div class="uk-text-center">
            <h2 class="uk-text-danger">Impossible d'afficher le formulaire. <?= htmlspecialchars($error_message) ?></h2>
            <a href="inventaire.php" class="uk-button uk-button-default">Retour à l'inventaire</a>
        </div>
    <?php endif; ?>

</div>

<?php include_once "footer.php"; ?>
</body>

<style>
    .uk-input {
        height: 45px;
        font-size: 16px;

        padding: 10px 10px;
    }
</style>
</html>