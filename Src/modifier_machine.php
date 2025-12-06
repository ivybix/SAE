<?php


$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$serial = isset($_GET['serial']) ? $_GET['serial'] : null;
$device_data = null;
$error_message = null;

if ($serial) {
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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['old_serial'])) {

    $old_serial = $_POST['old_serial'];
    $new_serial = isset($_POST['serial_device']) ? $_POST['serial_device'] : '';

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

    if (!$update_error) {

        $sql_update = "UPDATE Devices SET 
                        name=?, serial=?, manufacturer=?, model=?, type=?, cpu=?, ram_mb=?, disk_gb=?, 
                        os=?, domain=?, location=?, building=?, room=?, macaddr=?, purchase_date=?, warranty_end=? 
                        WHERE serial=?";

        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update === false) {
            $error_message = "Erreur de préparation de l'UPDATE : " . $conn->error;
        } else {

            $stmt_update->bind_param("ssssssiisssssssss",
                $name, $new_serial, $manufacturer_device, $model_device, $type, $cpu, $ram_mb, $disk_gb, $os,
                $domain, $location, $building, $room, $macaddr, $purchase_date, $warranty_end,
                $old_serial
            );

            if ($stmt_update->execute()) {
                $success_message = "Appareil '{$new_serial}' mis à jour avec succès !";
                header('location: inventaire.php');
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

    <?php if ($device_data): ?>
        <div class="uk-card uk-card-default uk-card-body uk-width-large@l uk-margin-auto">
            <h3 class="uk-card-title uk-text-center">Modification de <?= htmlspecialchars($device_data['name']) ?> (<?= htmlspecialchars($device_data['serial']) ?>)</h3>

            <form action="modifier_machine.php?serial=<?= urlencode($device_data['serial']) ?>" method="POST" class="uk-form-stacked">

                <input type="hidden" name="old_serial" value="<?= htmlspecialchars($device_data['serial']) ?>">

                <div>
                    <h4 class="uk-text-center   "><span>Identification</span></h4>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-name">Nom de l'appareil</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-name" type="text" name="name" value="<?= htmlspecialchars($device_data['name']) ?>" maxlength="50" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-serial">Numéro de Série (Clé Primaire)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-serial" type="text" name="serial_device" value="<?= htmlspecialchars($device_data['serial']) ?>" maxlength="50" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-manufacturer">Fabricant</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-manufacturer" type="text" name="manufacturer_device" value="<?= htmlspecialchars($device_data['manufacturer']) ?>" maxlength="25" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-model">Modèle</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-model" type="text" name="model_device" value="<?= htmlspecialchars($device_data['model']) ?>" maxlength="25" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-type">Type</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-type" type="text" name="type" value="<?= htmlspecialchars($device_data['type']) ?>" maxlength="25" required>
                        </div>
                    </div>
                </div>

                <hr class="uk-margin-medium">

                <div>
                    <h4 class="uk-text-center   "><span>Technique</span></h4>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-cpu">CPU</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-cpu" type="text" name="cpu" value="<?= htmlspecialchars($device_data['cpu']) ?>" maxlength="20" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-ram">RAM (Mo)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-ram" type="number" name="ram_mb" value="<?= htmlspecialchars($device_data['ram_mb']) ?>" maxlength="15">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-disk">Disque (Go)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-disk" type="number" name="disk_gb" value="<?= htmlspecialchars($device_data['disk_gb']) ?>" maxlength="15">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-os">OS (Doit exister dans OS_Systems)</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-os" type="text" name="os" value="<?= htmlspecialchars($device_data['os']) ?>" maxlength="15" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-macaddr">Adresse MAC</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-macaddr" type="text" name="macaddr" value="<?= htmlspecialchars($device_data['macaddr']) ?>" maxlength="20" required>
                        </div>
                    </div>
                </div>

                <hr class="uk-margin-medium">

                <div>
                    <h4 class="uk-text-center   "><span>Emplacement</span></h4>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-domain">Domaine</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-domain" type="text" name="domain" value="<?= htmlspecialchars($device_data['domain']) ?>" maxlength="15" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-location">Localisation</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-location" type="text" name="location" value="<?= htmlspecialchars($device_data['location']) ?>" maxlength="25" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-building">Bâtiment</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-building" type="text" name="building" value="<?= htmlspecialchars($device_data['building']) ?>" maxlength="15" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-room">Salle/Bureau</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-room" type="text" name="room" value="<?= htmlspecialchars($device_data['room']) ?>" maxlength="15" required>
                        </div>
                    </div>
                </div>

                <hr class="uk-margin-medium">

                <div>
                    <h4 class="uk-text-center   "><span>Dates</span></h4>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-purchase-date">Date d'achat</label>
                        <div class="uk-form-controls">
                            <input class="uk-input uk-width-1-1" id="form-purchase-date" type="date" name="purchase_date" value="<?= htmlspecialchars($device_data['purchase_date']) ?>" required>
                        </div>
                    </div>
                    <div class="uk-margin">
                        <label class="uk-form-label" for="form-warranty-end">Fin de garantie</label>
                        <div class="uk-form-controls">
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
    .uk-input {
        height: 45px;
        font-size: 16px;

        padding: 10px 10px;
    }
</style>
</html>

