<?php

session_start();
$is_tech = isset($_SESSION['role']) && $_SESSION['role'] === 'tech';

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée");
}

$sql_devices = "SELECT * FROM Devices WHERE serial IN (SELECT serial FROM Active_Devices)";
$result_devices = mysqli_query($conn, $sql_devices);

$sql_monitors = "SELECT * FROM Monitors WHERE serial IN (SELECT serial FROM Active_Monitors)";
$result_monitors = mysqli_query($conn, $sql_monitors);

$sql_retired_devices = "SELECT * FROM Devices WHERE serial IN (SELECT serial FROM Retired_Devices)";
$result_retired_devices = mysqli_query($conn, $sql_retired_devices);

$sql_retired_monitors = "SELECT * FROM Monitors WHERE serial IN (SELECT serial FROM Retired_Monitors)";
$result_retired_monitors = mysqli_query($conn, $sql_retired_monitors);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav2.ico"/>
    <title>Inventaire - Espace Technicien</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css"/>
    <link rel="stylesheet" href="CSS/uikit-rtl.css"/>
</head>
<body>
<?php include_once "navbar.php"; ?>
<div class="uk-container uk-margin-medium-top">
    <h1 class="uk-text-center sansation-bold">Espace Technicien - Inventaire</h1>
    <div class="uk-margin-medium-bottom collapsible-section">
        <input type="checkbox" id="toggle-inventaire" class="toggle-checkbox" checked>
        <label for="toggle-inventaire" class="toggle-label sansation-regular">Inventaire</label>
        <div class="collapsible-content">
            <div class="uk-margin-medium-bottom collapsible-section">
                <input type="checkbox" id="toggle-machines" class="toggle-checkbox" checked>
                <label for="toggle-machines" class="toggle-label sansation-regular">Machines</label>
                <div class="collapsible-content">
                    <div class="uk-margin-small-bottom collapsible-section">
                        <input type="checkbox" id="toggle-machines-consulter" class="toggle-checkbox" checked>
                        <label for="toggle-machines-consulter" class="toggle-label sansation-regular">Consulter</label>
                        <div class="collapsible-content">
                            <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">
                                Exporter en CSV
                            </button>
                            <div class="uk-overflow-auto" style="max-height: 300px;">
                                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                                    <thead>
                                    <tr>
                                        <?php if ($is_tech): ?>
                                            <th>Actions</th><?php endif; ?>
                                        <th>Nom</th>
                                        <th>Numéro de Série</th>
                                        <th>Constructeur</th>
                                        <th>Modèle</th>
                                        <th>OS</th>
                                        <th>Domaine</th>
                                        <th>Location</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    if ($result_devices) {
                                        while ($row = mysqli_fetch_assoc($result_devices)) {
                                            echo "<tr>";
                                            if ($is_tech) {
                                                echo "<td>
                
                <a href='modifier_machine.php?serial=" . urlencode($row['serial']) . "' 
                   class='uk-button uk-button-small uk-button-primary uk-border-rounded'>
                   Modifier
                </a>
                                                        
                <button class='uk-button uk-button-small uk-button-danger uk-border-rounded'>Supprimer</button>
            </td>";
                                            }
                                            echo "<td>" . (isset($row['name']) ? $row['name'] : '') . "</td>";
                                            echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                                            echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                                            echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                                            echo "<td>" . (isset($row['os']) ? $row['os'] : '') . "</td>";
                                            echo "<td>" . (isset($row['domain']) ? $row['domain'] : '') . "</td>";
                                            echo "<td>" . (isset($row['location']) ? $row['location'] : '') . "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php if ($is_tech): ?>
                        <div class="uk-margin-small-bottom collapsible-section">
                            <input type="checkbox" id="toggle-machines-ajouter" class="toggle-checkbox">
                            <label for="toggle-machines-ajouter" class="toggle-label sansation-regular">Ajouter</label>
                            <div class="collapsible-content">
                                <form action="csv_import.php" method="POST" enctype="multipart/form-data">

                                    <input type="file" name="csv_file_upload" id="csv_file_upload" required>

                                    <input type="hidden" name="target_table" value="Devices">>

                                    <button type="submit" class="uk-button uk-button-secondary"> Importer depuis un
                                        CSV
                                    </button>

                                </form>

                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Ajouter une Machine</h2>
                                    <form class="uk-form-stacked" method="POST" action="ajout_machine_formulaire.php">
                                        <div class="uk-grid-small uk-child-width-1-3@s uk-grid">
                                            <div>
                                                <label class="uk-form-label" for="name">Nom</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="name" id="name"
                                                           placeholder="Nom">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="serial_device">Numéro de Série</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="serial_device"
                                                           id="serial_device" placeholder="Numéro de Série">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label"
                                                       for="manufacturer_select">Constructeur</label>
                                                <div class="uk-form-controls">

                                                    <div class="uk-form-controls">
                                                        <select class="uk-select" id="manufacturer_select"
                                                                name="manufacturer" required>

                                                            <option value="">-- Sélectionnez un constructeur --</option>

                                                            <?php

                                                            $sql_manufacturers = "SELECT name FROM Manufacturers ORDER BY name";
                                                            $result_manufacturers = mysqli_query($conn, $sql_manufacturers);
                                                            if (mysqli_num_rows($result_manufacturers) > 0) {
                                                                while ($row = mysqli_fetch_assoc($result_manufacturers)) {

                                                                    $manufacturer_name = htmlspecialchars($row['name']);


                                                                    echo "<option value=\"$manufacturer_name\">$manufacturer_name</option>";
                                                                }
                                                            }


                                                            mysqli_free_result($result_manufacturers);
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="model_device">Modèle</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="model_device"
                                                           id="model_device" placeholder="Modèle">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="type">Type</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="type" id="type"
                                                           placeholder="Type">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="cpu">CPU</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="cpu" id="cpu"
                                                           placeholder="CPU">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="ram_mb">RAM (MB)</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="number" name="ram_mb" id="ram_mb"
                                                           placeholder="RAM en MB">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="disk_gb">Disque (GB)</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="number" name="disk_gb" id="disk_gb"
                                                           placeholder="Disque en GB">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="os">OS</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="os" id="os"
                                                           placeholder="OS">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="domain">Domaine</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="domain" id="domain"
                                                           placeholder="Domaine">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="location">Location</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="location" id="location"
                                                           placeholder="Location">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="building">Bâtiment</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="building" id="building"
                                                           placeholder="Bâtiment">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="room">Salle</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="room" id="room"
                                                           placeholder="Salle">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="macaddr">Adresse MAC</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="macaddr" id="macaddr"
                                                           placeholder="Adresse MAC">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="purchase_date">Date Achat</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="date" name="purchase_date"
                                                           id="purchase_date">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="warranty_end">Garantie</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="date" name="warranty_end"
                                                           id="warranty_end">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">

                                            <input type="hidden" name="target_table" value="Devices">>

                                            <button class="uk-button uk-button-primary uk-border-rounded" type="submit">
                                                Ajouter
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-bottom collapsible-section">
                            <input type="checkbox" id="toggle-machines-supprimer" class="toggle-checkbox">
                            <label for="toggle-machines-supprimer"
                                   class="toggle-label sansation-regular">Supprimer</label>
                            <div class="collapsible-content">
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Supprimer une Machine</h2>
                                    <form class="uk-form-stacked" method="POST" action="suppression_machine.php">
                                        <div class="uk-grid-small uk-child-width-1-2@s uk-grid">
                                            <div>
                                                <label class="uk-form-label">Numéro de Série</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="serial_devices" id="serial_devices" placeholder="SNXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-danger uk-border-rounded" type="submit">
                                                Supprimer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="uk-margin-medium-bottom collapsible-section">
                <input type="checkbox" id="toggle-ecrans" class="toggle-checkbox" checked>
                <label for="toggle-ecrans" class="toggle-label sansation-regular">Écrans</label>
                <div class="collapsible-content">
                    <div class="uk-margin-small-bottom collapsible-section">
                        <input type="checkbox" id="toggle-ecrans-consulter" class="toggle-checkbox" checked>
                        <label for="toggle-ecrans-consulter" class="toggle-label sansation-regular">Consulter</label>
                        <div class="collapsible-content">
                            <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">
                                Exporter en CSV
                            </button>
                            <div class="uk-overflow-auto" style="max-height: 300px;">
                                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                                    <thead>
                                    <tr>
                                        <?php if ($is_tech): ?>
                                            <th>Actions</th><?php endif; ?>
                                        <th>Numéro de Série</th>
                                        <th>Constructeur</th>
                                        <th>Modèle</th>
                                        <th>Dimensions_pouces</th>
                                        <th>Résolution</th>
                                        <th>Machine Associée</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql_monitors = "SELECT * FROM Monitors WHERE serial IN (SELECT serial FROM Active_Monitors)";
                                    $result_monitors = mysqli_query($conn, $sql_monitors);

                                    if ($result_monitors) {
                                        while ($row = mysqli_fetch_assoc($result_monitors)) {
                                            echo "<tr>";
                                            if ($is_tech) {
                                                echo "<td>
                
                <a href='modifier_ecran.php?serial=" . urlencode($row['serial']) . "' 
                   class='uk-button uk-button-small uk-button-primary uk-border-rounded'>
                   Modifier
                </a>
                                                        
                <button class='uk-button uk-button-small uk-button-danger uk-border-rounded'>Supprimer</button>
            </td>";
                                            }
                                            echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                                            echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                                            echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                                            echo "<td>" . (isset($row['size_inch']) ? $row['size_inch'] : '') . "</td>";
                                            echo "<td>" . (isset($row['resolution']) ? $row['resolution'] : '') . "</td>";
                                            echo "<td>" . (isset($row['attached_to']) ? $row['attached_to'] : '') . "</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php if ($is_tech): ?>
                        <div class="uk-margin-small-bottom collapsible-section">
                            <input type="checkbox" id="toggle-ecrans-ajouter" class="toggle-checkbox">
                            <label for="toggle-ecrans-ajouter" class="toggle-label sansation-regular">Ajouter</label>
                            <div class="collapsible-content">
                                <form action="csv_import.php" method="POST" enctype="multipart/form-data">

                                    <input type="file" name="csv_file_upload" id="csv_file_upload" required>

                                    <input type="hidden" name="target_table" value="Monitors">>

                                    <button type="submit" class="uk-button uk-button-secondary"> Importer depuis un
                                        CSV
                                    </button>

                                </form>
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Ajouter un Écran</h2>
                                    <form class="uk-form-stacked" action="ajout_ecran_formulaire.php" method="post">
                                        <div class="uk-grid-small uk-child-width-1-3@s uk-grid">
                                            <div>
                                                <label class="uk-form-label" for="serial_monitor">Numéro de
                                                    Série</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="serial_monitor" id="serial_monitor"
                                                           placeholder="Numéro de Série">
                                                </div>
                                            </div>

                                            <div>
                                                <label class="uk-form-label"
                                                       for="manufacturer_select">Constructeur</label>
                                                <div class="uk-form-controls">

                                                    <div class="uk-form-controls">
                                                        <select class="uk-select" id="manufacturer_select"
                                                                name="manufacturer" required>

                                                            <option value="">-- Sélectionnez un constructeur --</option>

                                                            <?php

                                                            $sql_manufacturers = "SELECT name FROM Manufacturers ORDER BY name";
                                                            $result_manufacturers = mysqli_query($conn, $sql_manufacturers);
                                                            if (mysqli_num_rows($result_manufacturers) > 0) {
                                                                while ($row = mysqli_fetch_assoc($result_manufacturers)) {

                                                                    $manufacturer_name = htmlspecialchars($row['name']);


                                                                    echo "<option value=\"$manufacturer_name\">$manufacturer_name</option>";
                                                                }
                                                            }


                                                            mysqli_free_result($result_manufacturers);
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="model_monitor">Modèle</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="model_monitor" id="model_monitor"
                                                           placeholder="Modèle">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="size_inch">Dimensions (pouces)</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="size_inch" id="size_inch"
                                                           placeholder="Dimensions">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="resolution">Résolution</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="resolution" id="resolution"
                                                           placeholder="Résolution">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="connection">Connection</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="connection" id="connection"
                                                           placeholder="Connection">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="attached_to">Machine Associée</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" name="attached_to" id="attached_to"
                                                           placeholder="Machine Associée">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-primary uk-border-rounded" type="submit">
                                                Ajouter
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-bottom collapsible-section">
                            <input type="checkbox" id="toggle-ecrans-supprimer" class="toggle-checkbox">
                            <label for="toggle-ecrans-supprimer"
                                   class="toggle-label sansation-regular">Supprimer</label>
                            <div class="collapsible-content">
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Supprimer un Écran</h2>
                                    <form class="uk-form-stacked" method="POST" action="suppression_ecran.php">
                                        <div class="uk-grid-small uk-child-width-1-2@s uk-grid">
                                            <div>
                                                <label class="uk-form-label">Numéro de Série</label>
                                                <div class="uk-form-controls">
                                                    <label for="serial_monitors"></label><input class="uk-input" type="text" name="serial_monitors" id="serial_monitors" placeholder="DMXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-danger uk-border-rounded" type="submit">
                                                Supprimer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-margin-medium-bottom collapsible-section">
        <input type="checkbox" id="toggle-rebut" class="toggle-checkbox" checked>
        <label for="toggle-rebut" class="toggle-label sansation-regular">Matériel au Rebut</label>
        <div class="collapsible-content">
            <div class="uk-margin-medium-bottom collapsible-section">
                <input type="checkbox" id="toggle-rebut-machines" class="toggle-checkbox" checked>
                <label for="toggle-rebut-machines" class="toggle-label sansation-regular">Machines</label>
                <div class="collapsible-content">
                    <div class="uk-overflow-auto" style="max-height: 300px;">
                        <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                            <thead>
                            <tr>
                                <?php if ($is_tech): ?>
                                    <th>Actions</th><?php endif; ?>
                                <th id="name_rebut">Nom</th>
                                <th id="serial_device_rebut">Numéro de Série</th>
                                <th id="manufacturer_device_rebut">Constructeur</th>
                                <th id="model_device_rebut">Modèle</th>
                                <th id="os_rebut">OS</th>
                                <th id="domain_rebut">Domaine</th>
                                <th id="location_rebut">Location</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($result_retired_devices) {
                                while ($row = mysqli_fetch_assoc($result_retired_devices)) {
                                    echo "<tr>";
                                    if ($is_tech) {
                                        echo "<td class='uk-width-small'>
                                            <button class='uk-button uk-button-small uk-button-secondary uk-border-rounded'>Restaurer</button>
                                        </td>";
                                    }
                                    echo "<td>" . (isset($row['name']) ? $row['name'] : '') . "</td>";
                                    echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                                    echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                                    echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                                    echo "<td>" . (isset($row['os']) ? $row['os'] : '') . "</td>";
                                    echo "<td>" . (isset($row['domain']) ? $row['domain'] : '') . "</td>";
                                    echo "<td>" . (isset($row['location']) ? $row['location'] : '') . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="uk-margin-medium-bottom collapsible-section">
                <input type="checkbox" id="toggle-rebut-ecrans" class="toggle-checkbox" checked>
                <label for="toggle-rebut-ecrans" class="toggle-label sansation-regular">Écrans</label>
                <div class="collapsible-content">
                    <div class="uk-overflow-auto" style="max-height: 300px;">
                        <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                            <thead>
                            <tr>
                                <?php if ($is_tech): ?>
                                    <th>Actions</th><?php endif; ?>
                                <th id="serial_monitor_rebut">Numéro de Série</th>
                                <th id="manufacturer_monitor_rebut">Constructeur</th>
                                <th id="model_monitor_rebut">Modèle</th>
                                <th id="size_inch_rebut">Dimensions_pouces</th>
                                <th id="resolution_rebut">Résolution</th>
                                <th id="attached_to_rebut">Machine Associée</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($result_retired_monitors) {
                                while ($row = mysqli_fetch_assoc($result_retired_monitors)) {
                                    echo "<tr>";
                                    if ($is_tech) {
                                        echo "<td class='uk-width-small'>
                                            <button class='uk-button uk-button-small uk-button-secondary uk-border-rounded'>Restaurer</button>
                                        </td>";
                                    }
                                    echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                                    echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                                    echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                                    echo "<td>" . (isset($row['size_inch']) ? $row['size_inch'] : '') . "</td>";
                                    echo "<td>" . (isset($row['resolution']) ? $row['resolution'] : '') . "</td>";
                                    echo "<td>" . (isset($row['attached_to']) ? $row['attached_to'] : '') . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>