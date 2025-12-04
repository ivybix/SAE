<?php
// Active l'affichage des erreurs pour le débogage (à retirer en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusion de la connexion à la base de données (comme dans inventaire.php)
// Récupéré de 'inventaire.php'
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

// --------------------------------------------------------
// PARTIE 1 : RÉCUPÉRATION DES DONNÉES DE L'APPAREIL
// --------------------------------------------------------

$serial = isset($_GET['serial']) ? $_GET['serial'] : null;
$device_data = null;
$error_message = null;

if ($serial) {
    // Requête préparée pour récupérer toutes les données de l'appareil
    $sql_fetch = "SELECT * FROM Devices WHERE serial = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);

    if ($stmt_fetch === false) {
        $error_message = "Erreur de préparation de la requête de récupération : " . $conn->error;
    } else {
        $stmt_fetch->bind_param("s", $serial);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();

        if ($result->num_rows === 1) {
            $device_data = $result->fetch_assoc();
        } else {
            $error_message = "Appareil non trouvé avec le numéro de série : " . htmlspecialchars($serial);
        }
        $stmt_fetch->close();
    }
} else {
    $error_message = "Numéro de série non spécifié.";
}

// --------------------------------------------------------
// PARTIE 2 : GESTION DE LA SOUMISSION DU FORMULAIRE (UPDATE)
// --------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['old_serial'])) {

    // Déclaration des variables (16 champs) avec gestion des INT nullables
    $old_serial = $_POST['old_serial']; // Le numéro de série original (clé de recherche)
    $new_serial = isset($_POST['serial_device']) ? $_POST['serial_device'] : '';

    // Récupération sécurisée et conversion des autres variables
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $manufacturer_device = isset($_POST['manufacturer_device']) ? $_POST['manufacturer_device'] : '';
    $model_device = isset($_POST['model_device']) ? $_POST['model_device'] : '';
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $cpu = isset($_POST['cpu']) ? $_POST['cpu'] : '';
    $os = isset($_POST['os']) ? $_POST['os'] : '';
    $domain = isset($_POST['domain']) ? $_POST['domain'] : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $building = isset($_POST['building']) ? $_POST['building'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $macaddr = isset($_POST['macaddr']) ? $_POST['macaddr'] : '';
    $purchase_date = isset($_POST['purchase_date']) ? $_POST['purchase_date'] : '';
    $warranty_end = isset($_POST['warranty_end']) ? $_POST['warranty_end'] : '';


    $ram_mb = (isset($_POST['ram_mb']) && $_POST['ram_mb'] !== '') ? (int)$_POST['ram_mb'] : null;
    $disk_gb = (isset($_POST['disk_gb']) && $_POST['disk_gb'] !== '') ? (int)$_POST['disk_gb'] : null;

    $update_error = false;

    // A. VÉRIFICATION DU NOUVEAU NUMÉRO DE SÉRIE (s'il a été modifié)
    if ($new_serial !== $old_serial) {
        $check_sql = "SELECT serial FROM Devices WHERE serial = ?";
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

    // B. VÉRIFICATION DE L'ADRESSE MAC (s'il a été modifié)
    // Nous ne vérifions que si la MAC est nouvelle ET n'appartient pas au device que nous modifions.
    $check_mac_sql = "SELECT macaddr FROM Devices WHERE macaddr = ? AND serial != ?";
    $check_mac_stmt = $conn->prepare($check_mac_sql);
    $check_mac_stmt->bind_param("ss", $macaddr, $old_serial);
    $check_mac_stmt->execute();
    $check_mac_stmt->store_result();

    if ($check_mac_stmt->num_rows > 0) {
        $error_message = "Erreur : L'adresse MAC '{$macaddr}' est déjà utilisée par un autre appareil.";
        $update_error = true;
    }
    $check_mac_stmt->close();

    // C. EXÉCUTION DE LA MISE À JOUR (UPDATE)
    if (!$update_error) {
        // Préparation de la requête UPDATE pour les 16 champs
        $sql_update = "UPDATE Devices SET 
                        name=?, serial=?, manufacturer=?, model=?, type=?, cpu=?, ram_mb=?, disk_gb=?, 
                        os=?, domain=?, location=?, building=?, room=?, macaddr=?, purchase_date=?, warranty_end=? 
                        WHERE serial=?";

        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            $error_message = "Erreur de préparation de l'UPDATE : " . $conn->error;
        } else {
            // L'ordre des types et variables doit correspondre à l'ordre de la requête UPDATE (16 champs + 1 WHERE serial) = 17 paramètres
            // Types : 16 champs à mettre à jour (s...s, ii) + 1 champ WHERE (s) = 17 types
            // s s s s s s i i s s s s s s s s s
            $stmt_update->bind_param("ssssssiisssssssss",
                $name, $new_serial, $manufacturer_device, $model_device, $type, $cpu, $ram_mb, $disk_gb, $os,
                $domain, $location, $building, $room, $macaddr, $purchase_date, $warranty_end,
                $old_serial // Le 17ème paramètre pour la clause WHERE
            );

            if ($stmt_update->execute()) {
                $success_message = "Appareil '{$new_serial}' mis à jour avec succès !";

                // IMPORTANT : Si le numéro de série a changé, nous devons mettre à jour l'URL pour afficher les nouvelles données
                if ($new_serial !== $old_serial) {
                    header("Location: modifier.php?serial=" . urlencode($new_serial) . "&success=" . urlencode($success_message));
                    exit;
                }

                // Recharger les données pour que le formulaire affiche les nouvelles valeurs
                $device_data = $device_data = array_combine(
                    ['name', 'serial', 'manufacturer', 'model', 'type', 'cpu', 'ram_mb', 'disk_gb', 'os', 'domain', 'location', 'building', 'room', 'macaddr', 'purchase_date', 'warranty_end'],
                    [$name, $new_serial, $manufacturer_device, $model_device, $type, $cpu, $ram_mb, $disk_gb, $os, $domain, $location, $building, $room, $macaddr, $purchase_date, $warranty_end]
                );

            } else {
                $error_message = "Erreur lors de l'exécution de l'UPDATE : " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}

// Si la soumission a réussi et nous avons été redirigés
if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}

mysqli_close($conn);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="Ressources/logo.ico"/>
    <title>Modifier un appareil</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css"/>
    <link rel="stylesheet" href="CSS/uikit-rtl.css"/>
</head>
<body>

<?php include_once "navbar.php"; ?>

<div class="uk-container uk-margin-large-top">
    <h1 class="sansation-bold uk-text-center">Modification de l'appareil</h1>

    <?php if ($error_message): ?>
        <div class="uk-alert-danger">
            <a class="uk-alert-close"></a>
            <p><?= htmlspecialchars($error_message) ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="uk-alert-success">
            <a class="uk-alert-close"></a>
            <p><?= htmlspecialchars($success_message) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($device_data): ?>
        <div class="uk-card uk-card-default uk-card-body uk-width-1-2@m uk-align-center">
            <h3 class="uk-card-title">Modification de <?= htmlspecialchars($device_data['name']) ?> (<?= htmlspecialchars($device_data['serial']) ?>)</h3>

            <form action="modifier.php?serial=<?= urlencode($device_data['serial']) ?>" method="POST" class="uk-form-stacked">

                <input type="hidden" name="old_serial" value="<?= htmlspecialchars($device_data['serial']) ?>">

                <div class="uk-child-width-1-2@m" >

                    <div>
                        <h4 class="  uk-text-center"><span>Identification</span></h4>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-name">Nom de l'appareil</label>
                            <input class="uk-input uk-width-1-1" id="form-name" type="text" name="name" value="<?= htmlspecialchars($device_data['name']) ?>" maxlength="50" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-serial">Numéro de Série (Clé Primaire)</label>
                             <input class="uk-input uk-width-1-1" id="form-serial" type="text" name="serial_device" value="<?= htmlspecialchars($device_data['serial']) ?>" maxlength="50" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-manufacturer">Fabricant</label>
                             <input class="uk-input uk-width-1-1" id="form-manufacturer" type="text" name="manufacturer_device" value="<?= htmlspecialchars($device_data['manufacturer']) ?>" maxlength="25" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-model">Modèle</label>
                             <input class="uk-input uk-width-1-1" id="form-model" type="text" name="model_device" value="<?= htmlspecialchars($device_data['model']) ?>" maxlength="25" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-type">Type</label>
                             <input class="uk-input uk-width-1-1" id="form-type" type="text" name="type" value="<?= htmlspecialchars($device_data['type']) ?>" maxlength="25" required>
                        </div>
                    </div>

                    <div>
                        <h4 class="  uk-text-center"><span>Technique</span></h4>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-cpu">CPU</label>
                             <input class="uk-input uk-width-1-1" id="form-cpu" type="text" name="cpu" value="<?= htmlspecialchars($device_data['cpu']) ?>" maxlength="20" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-ram">RAM (Mo)</label>
                             <input class="uk-input uk-width-1-1" id="form-ram" type="number" name="ram_mb" value="<?= htmlspecialchars($device_data['ram_mb']) ?>" maxlength="15">
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-disk">Disque (Go)</label>
                             <input class="uk-input uk-width-1-1" id="form-disk" type="number" name="disk_gb" value="<?= htmlspecialchars($device_data['disk_gb']) ?>" maxlength="15">
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-os">OS (Doit exister dans OS_Systems)</label>
                             <input class="uk-input uk-width-1-1" id="form-os" type="text" name="os" value="<?= htmlspecialchars($device_data['os']) ?>" maxlength="15" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-macaddr">Adresse MAC</label>
                             <input class="uk-input uk-width-1-1" id="form-macaddr" type="text" name="macaddr" value="<?= htmlspecialchars($device_data['macaddr']) ?>" maxlength="20" required>
                        </div>
                    </div>
                </div>

                <div class="uk-child-width-1-3@m">
                    <div>
                        <h4 class="  uk-text-center"><span>Emplacement</span></h4>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-domain">Domaine</label>
                             <input class="uk-input uk-width-1-1" id="form-domain" type="text" name="domain" value="<?= htmlspecialchars($device_data['domain']) ?>" maxlength="15" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-location">Localisation</label>
                             <input class="uk-input uk-width-1-1" id="form-location" type="text" name="location" value="<?= htmlspecialchars($device_data['location']) ?>" maxlength="25" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-building">Bâtiment</label>
                             <input class="uk-input uk-width-1-1" id="form-building" type="text" name="building" value="<?= htmlspecialchars($device_data['building']) ?>" maxlength="15" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-room">Salle/Bureau</label>
                             <input class="uk-input uk-width-1-1" id="form-room" type="text" name="room" value="<?= htmlspecialchars($device_data['room']) ?>" maxlength="15" required>
                        </div>
                    </div>

                    <div>
                        <h4 class="  uk-text-center"><span>Dates</span></h4>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-purchase-date">Date d'achat</label>
                             <input class="uk-input uk-width-1-1" id="form-purchase-date" type="date" name="purchase_date" value="<?= htmlspecialchars($device_data['purchase_date']) ?>" required>
                        </div>
                        <div class="uk-margin">
                            <label class="uk-form-label" for="form-warranty-end">Fin de garantie</label>
                             <input class="uk-input uk-width-1-1" id="form-warranty-end" type="date" name="warranty_end" value="<?= htmlspecialchars($device_data['warranty_end']) ?>" required>
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

</body>
<style>
    .uk-input.uk-width-1-1,
    .uk-textarea.uk-width-1-1 {
        /* Augmente la hauteur du champ d'entrée */
        height: 45px;
        width: 1000px;
        /* Augmente légèrement la taille de la police */
        font-size: 16px;

        /* Ajoute un padding interne pour l'alignement */
        padding: 10px 10px;
    }
</style>
</html>

