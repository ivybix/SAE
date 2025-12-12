<?php

session_start();
$is_tech = isset($_SESSION['role']) && $_SESSION['role'] === 'tech';

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée");
}
$max_lignes =  50; // max de lignes par table


// --- LOGIQUE DE FILTRAGE COMMENCE ICI ---
$equipment_type = isset($_GET['equipment_type']) ? $_GET['equipment_type'] : '';
$filters_applied = !empty($equipment_type); // L'application d'un filtre commence par la sélection du type

$filter_where = "";
$serial_filter = isset($_GET['serial']) ? $_GET['serial'] : '';
$domain_filter = isset($_GET['domain']) ? $_GET['domain'] : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';
$attached_to_filter = isset($_GET['attached_to']) ? $_GET['attached_to'] : '';

$result_data = null;
$table_title = "";
$table_headers = [];


if ($filters_applied) {

    $result_manufacturers = null;
    $result_os = null;
    if ($equipment_type === 'machines') {
        $table_title = "Machines Actives";
        $sql_base = "SELECT * FROM Devices WHERE serial IN (SELECT serial FROM Active_Devices) AND serial NOT IN (SELECT serial FROM Waiting_Devices)";

        // Construction de la clause WHERE pour les machines actives
        if (!empty($serial_filter)) $filter_where .= " AND serial LIKE '%" . mysqli_real_escape_string($conn, $serial_filter) . "%'";
        if (!empty($domain_filter)) $filter_where .= " AND domain LIKE '%" . mysqli_real_escape_string($conn, $domain_filter) . "%'";
        if (!empty($location_filter)) $filter_where .= " AND location LIKE '%" . mysqli_real_escape_string($conn, $location_filter) . "%'";
        $sql = $sql_base . $filter_where;
        $result_data = mysqli_query($conn, $sql);

        $table_headers = ['Nom', 'Numéro de Série', 'Constructeur', 'Modèle', 'OS', 'Domaine', 'Location'];

        if ($is_tech) {
            $sql_manufacturers = "SELECT name FROM Manufacturers ORDER BY name";
            $result_manufacturers = mysqli_query($conn, $sql_manufacturers);
            $sql_os = "SELECT name FROM Operating_Systems ORDER BY name";
            $result_os = mysqli_query($conn, $sql_os);
        }

    } elseif ($equipment_type === 'monitors') {
        $table_title = "Écrans Actifs";
        $sql_base = "SELECT * FROM Monitors WHERE serial IN (SELECT serial FROM Active_Monitors) AND serial NOT IN (SELECT serial FROM Waiting_Monitors)";

        // Construction de la clause WHERE pour les écrans actifs
        if (!empty($serial_filter)) $filter_where .= " AND serial LIKE '%" . mysqli_real_escape_string($conn, $serial_filter) . "%'";
        if (!empty($attached_to_filter)) $filter_where .= " AND attached_to LIKE '%" . mysqli_real_escape_string($conn, $attached_to_filter) . "%'";

        $sql = $sql_base . $filter_where;
        $result_data = mysqli_query($conn, $sql);

        $table_headers = ['Numéro de Série', 'Constructeur', 'Modèle', 'Dimensions_pouces', 'Résolution', 'Machine Associée'];

        // Récupération des listes pour le formulaire d'ajout (si technicien)
        if ($is_tech) {
            $sql_manufacturers = "SELECT name FROM Manufacturers ORDER BY name";
            $result_manufacturers = mysqli_query($conn, $sql_manufacturers);
        }

    } elseif ($equipment_type === 'rebut' && $is_tech) {
        $table_title = "Matériel au Rebut";

        // Machines au rebut
        $filter_where_dev = "";
        $sql_devices_rebut_base = "SELECT * FROM Devices WHERE serial IN (SELECT serial FROM Waiting_Devices)";
        if (!empty($serial_filter)) $filter_where_dev .= " AND serial LIKE '%" . mysqli_real_escape_string($conn, $serial_filter) . "%'";
        if (!empty($domain_filter)) $filter_where_dev .= " AND domain LIKE '%" . mysqli_real_escape_string($conn, $domain_filter) . "%'";
        if (!empty($location_filter)) $filter_where_dev .= " AND location LIKE '%" . mysqli_real_escape_string($conn, $location_filter) . "%'";

        $sql_devices_rebut = $sql_devices_rebut_base . $filter_where_dev;
        $result_devices_rebut = mysqli_query($conn, $sql_devices_rebut);

        // Écrans au rebut
        $filter_where_mon = "";
        $sql_monitors_rebut_base = "SELECT * FROM Monitors WHERE serial IN (SELECT serial FROM Waiting_Monitors)";
        if (!empty($serial_filter)) $filter_where_mon .= " AND serial LIKE '%" . mysqli_real_escape_string($conn, $serial_filter) . "%'";
        if (!empty($attached_to_filter)) $filter_where_mon .= " AND attached_to LIKE '%" . mysqli_real_escape_string($conn, $attached_to_filter) . "%'";

        $sql_monitors_rebut = $sql_monitors_rebut_base . $filter_where_mon;
        $result_monitors_rebut = mysqli_query($conn, $sql_monitors_rebut);
    }
}
// --- LOGIQUE DE FILTRAGE SE TERMINE ICI ---


$error_message = null;


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <script src="Ressources/js/uikit.js"></script>
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

<?php include_once "alerts.php"; ?>

<div class="uk-container uk-margin-medium-top">
    <h1 class="uk-text-center sansation-bold uk-margin-large-bottom">Espace Technicien - Inventaire</h1>

    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin-large-bottom">
        <h2 class="uk-card-title sansation-regular uk-margin-small-bottom">Sélection et Filtrage</h2>

        <form class="uk-form-stacked" method="GET" action="inventaire.php">
            <div class="uk-margin-small-bottom">
                <label class="uk-form-label" for="equipment_type">Type d'Équipement à Afficher</label>
                <div class="uk-form-controls">
                    <select class="uk-select" id="equipment_type" name="equipment_type" required
                            onchange="this.form.submit()">
                        <option value="">-- Sélectionnez un type --</option>
                        <option value="machines" <?php echo $equipment_type === 'machines' ? 'selected' : ''; ?>>
                            Machines Actives
                        </option>
                        <option value="monitors" <?php echo $equipment_type === 'monitors' ? 'selected' : ''; ?>>Écrans
                            Actifs
                        </option>
                        <?php if ($is_tech): ?>
                            <option value="rebut" <?php echo $equipment_type === 'rebut' ? 'selected' : ''; ?>>Matériel
                                au Rebut
                            </option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <hr class="uk-margin-medium-top uk-margin-medium-bottom">

            <div class="uk-grid-small uk-child-width-1-3@s uk-grid">

                <?php if ($filters_applied): ?>

                    <div>
                        <label class="uk-form-label" for="serial">Numéro de Série</label>
                        <div class="uk-form-controls">
                            <input class="uk-input" type="text" name="serial" id="serial"
                                   placeholder="Numéro de Série (partiel)"
                                   value="<?php echo htmlspecialchars($serial_filter); ?>">
                        </div>
                    </div>

                    <?php if ($equipment_type === 'machines' || ($equipment_type === 'rebut' && $is_tech)): ?>
                        <div>
                            <label class="uk-form-label" for="domain">Domaine</label>
                            <div class="uk-form-controls">
                                <input class="uk-input" type="text" name="domain" id="domain" placeholder="Domaine"
                                       value="<?php echo htmlspecialchars($domain_filter); ?>">
                            </div>
                        </div>
                        <div>
                            <label class="uk-form-label" for="location">Location</label>
                            <div class="uk-form-controls">
                                <input class="uk-input" type="text" name="location" id="location" placeholder="Location"
                                       value="<?php echo htmlspecialchars($location_filter); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($equipment_type === 'monitors' || ($equipment_type === 'rebut' && $is_tech)): ?>
                        <div>
                            <label class="uk-form-label" for="attached_to">Machine Associée (SN)</label>
                            <div class="uk-form-controls">
                                <input class="uk-input" type="text" name="attached_to" id="attached_to"
                                       placeholder="Numéro de Série de la machine"
                                       value="<?php echo htmlspecialchars($attached_to_filter); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="uk-text-meta uk-width-1-1">Sélectionnez un type d'équipement pour afficher les options de
                        filtre.</p>
                <?php endif; ?>

            </div>

            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary uk-border-rounded" type="submit">
                    Appliquer le Filtre
                </button>
                <a href="inventaire.php" class="uk-button uk-button-default uk-border-rounded" style="background: white">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <?php if ($filters_applied): ?>

        <h2 class="uk-text-left sansation-bold uk-margin-large-top">Résultats : <?php echo $table_title; ?></h2>

        <?php if ($equipment_type === 'machines'): ?>
            <h3 class="uk-text-left sansation-regular">Liste des Machines</h3>
            <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">
                Exporter en CSV
            </button>

            <div class="uk-overflow-auto uk-margin-small-bottom" style="max-height: 400px;">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>

                        <th>N°</th>
                        <?php if ($is_tech): ?>
                            <th>Actions</th><?php endif; ?>
                        <?php foreach ($table_headers as $header): ?>
                            <th><?php echo $header; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (($result_data && mysqli_num_rows($result_data) > 0)) {
                            $i = 0;
                            while (($row = mysqli_fetch_assoc($result_data)) && ($i < $max_lignes)) {
                                $i = $i + 1;
                                echo "<tr>";
                                echo "<td>" . $i . "</td>";
                                if ($is_tech) {
                                    echo "<td>
                                <a href='modifier_machine.php?serial=" . urlencode($row['serial']) . "' 
                                   class='uk-button uk-button-small uk-button-primary uk-border-rounded'>
                                   Modifier
                                </a>
                                <form action='suppression_machine.php' method='post'>
                                
                                <input type='hidden' name='serial_devices' value=".$row['serial'].">
                                <button class='uk-button uk-button-small uk-button-danger uk-border-rounded'  type='submit' >Supprimer</button>
                                </form>
                              </td>";
                                }

                                echo "<td>" . isset($row). (isset($row['name']) ? $row['name'] : '') . "</td>";
                                echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                                echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                                echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                                echo "<td>" . (isset($row['os']) ? $row['os'] : '') . "</td>";
                                echo "<td>" . (isset($row['domain']) ? $row['domain'] : '') . "</td>";
                                echo "<td>" . (isset($row['location']) ? $row['location'] : '') . "</td>";
                                echo "</tr>";

                            }
                            if (mysqli_num_rows($result_data) >= $max_lignes) {
                                echo "<tr>";
                                echo "<td colspan='9' class='uk-text-center'>Il y a plus de ". ( mysqli_num_rows($result_data) - $max_lignes) ." lignes restantes... Veuillez préciser votre recherche. </td>";
                                echo "<tr>";
                            }
                            mysqli_free_result($result_data);
                        } else {
                            echo "<tr><td colspan='9' class='uk-text-center'>Aucune machine active trouvée pour les filtres appliqués.</td></tr>";
                        }

                    ?>
                    </tbody>
                </table>
            </div>

            <?php if ($is_tech): ?>
                <hr class="uk-margin-large-top">
                <h3 class="uk-text-left sansation-bold uk-margin-small-top">Gestion des Machines</h3>
                <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin-medium-top">
                    <h4 class="sansation-regular uk-margin-small-bottom">Import CSV :</h4>
                    <form action="csv_import_machine.php" method="POST" enctype="multipart/form-data"
                          class="uk-margin-small-bottom">
                        <div class="uk-flex uk-flex-middle">
                            <input type="file" name="csv_file_upload" id="csv_file_upload_machine" required>
                            <input type="hidden" name="target_table" value="Devices">
                            <button type="submit"
                                    class="uk-button uk-button-secondary uk-margin-small-left uk-border-rounded">
                                Importer
                            </button>
                        </div>
                    </form>

                    <div class="uk-margin-medium-top">
                        <h4 class="sansation-regular uk-margin-small-bottom">Saisie manuelle (*: Obligatoire) :</h4>
                        <form class="uk-form-stacked" method="POST" action="ajout_machine_formulaire.php">
                            <div class="uk-grid-small uk-child-width-1-3@s uk-grid">
                                <div>
                                    <label class="uk-form-label" for="name">Nom*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-danger" type="text" name="name" id="name"
                                               placeholder="Nom">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="serial_device">Numéro de Série*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-danger" type="text" minlength="4" maxlength="12"
                                               name="serial_device"
                                               id="serial_device" placeholder="Numéro de Série" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label"
                                           for="manufacturer_select">Constructeur*</label>
                                    <div class="uk-form-controls">
                                        <select class="uk-select" id="manufacturer_select"
                                                name="manufacturer" required>

                                            <option value="">-- Sélectionnez un constructeur --</option>

                                            <?php
                                            if ($result_manufacturers) {
                                                mysqli_data_seek($result_manufacturers, 0);
                                                while ($row = mysqli_fetch_assoc($result_manufacturers)) {
                                                    $manufacturer_name = htmlspecialchars($row['name']);
                                                    echo "<option value=\"$manufacturer_name\">$manufacturer_name</option>";
                                                }
                                                mysqli_free_result($result_manufacturers);
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>


                                <div>
                                    <label class="uk-form-label" for="macaddr">Adresse MAC*</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-danger" type="text" name="macaddr" id="macaddr"
                                               placeholder="Adresse MAC" maxlength="10" value="" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="uk-form-label uk-form-danger" for="os_select_add">OS*</label>
                                    <div class="uk-form-controls">
                                        <select class="uk-select" id="os_select_add"
                                                name="os" required>

                                            <option value="">-- Sélectionnez un OS --</option>
                                            <?php
                                            if ($result_os) {
                                                mysqli_data_seek($result_os, 0);
                                                while ($row = mysqli_fetch_assoc($result_os)) {
                                                    $os_name = htmlspecialchars($row['name']);
                                                    echo "<option value=\"$os_name\">$os_name</option>";
                                                }
                                                mysqli_free_result($result_os);
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="model_device">Modèle</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="model_device"
                                               id="model_device" placeholder="Modèle" maxlength="10" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="type">Type</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="type" maxlength="10" id="type"
                                               placeholder="Type" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="cpu">CPU</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="cpu" id="cpu"
                                               placeholder="CPU" maxlength="18" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="ram_select">RAM (MB)</label>
                                    <div class="uk-form-controls">
                                        <select class="uk-select" id="ram_select"
                                                name="ram_mb">
                                            <option value="0">-- Sélectionnez la taille de la RAM --</option>
                                            <option value="512">512 MB (0.5 GB)</option>
                                            <option value="1024">1024 MB (1 GB)</option>
                                            <option value="2048">2048 MB (2 GB)</option>
                                            <option value="4096">4096 MB (4 GB)</option>
                                            <option value="8192">8192 MB (8 GB)</option>
                                            <option value="12288">12288 MB (12 GB)</option>
                                            <option value="16384">16384 MB (16 GB)</option>
                                            <option value="24576">24576 MB (24 GB)</option>
                                            <option value="32768">32768 MB (32 GB)</option>
                                            <option value="65536">65536 MB (64 GB)</option>
                                            <option value="131072">131072 MB (128 GB)</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="disk_gb">Disque (GB)</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-form-controls">
                                            <select class="uk-select" id="disk_gb"
                                                    name="disk_gb">
                                                <option value="0">-- Sélectionnez la taille du disque --</option>
                                                <option value="4">4 GB</option>
                                                <option value="8">8 GB</option>
                                                <option value="16">16 GB</option>
                                                <option value="32">32 GB</option>
                                                <option value="64">64 GB</option>
                                                <option value="128">128 GB</option>
                                                <option value="256">256 GB</option>
                                                <option value="512">512 GB</option>
                                                <option value="1024">1024 GB</option>
                                                <option value="2048">2048 GB</option>
                                                <option value="4096">4096 GB</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="domain">Domaine</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="domain" id="domain"
                                               placeholder="Domaine" maxlength="10" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="location">Location</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="location" id="location"
                                               placeholder="Location" maxlength="10" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="building">Bâtiment</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="building" id="building"
                                               placeholder="Bâtiment" maxlength="10" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="room">Salle</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="room" id="room"
                                               placeholder="Salle" maxlength="10" value="">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="purchase_date">Date Achat</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="date" name="purchase_date"
                                               id="purchase_date" value="<?php echo date('Y-m-d'); ?>" maxlength="10">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="warranty_end">Garantie</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="date" name="warranty_end"
                                               id="warranty_end" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="uk-margin-top">
                                <input type="hidden" name="target_table" value="Devices">
                                <button class="uk-button uk-button-primary uk-border-rounded" type="submit">
                                    Ajouter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin-medium-top">
                    <h3 class="uk-card-title sansation-regular">Supprimer une Machine</h3>
                    <form class="uk-form-stacked" method="POST" action="suppression_machine.php">
                        <div class="uk-grid-small uk-child-width-1-2@s uk-grid">
                            <div>
                                <label class="uk-form-label">Numéro de Série</label>
                                <div class="uk-form-controls">
                                    <label for="serial_devices_delete"></label><input class="uk-input" type="text"
                                                                               name="serial_devices"
                                                                               id="serial_devices_delete"
                                                                               placeholder="SNXXXXXXX">
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
            <?php endif; ?>

        <?php elseif ($equipment_type === 'monitors'): ?>
            <h3 class="uk-text-left sansation-regular">Liste des Écrans</h3>
            <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">
                Exporter en CSV
            </button>
            <div class="uk-overflow-auto uk-margin-small-bottom" style="max-height: 400px;">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>
                        <?php if ($is_tech): ?>
                            <th>N°</th>
                            <th>Actions</th><?php endif; ?>
                        <?php foreach ($table_headers as $header): ?>
                            <th><?php echo $header; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $i = 0;
                    if (($result_data && mysqli_num_rows($result_data) > 0) && ($i < $max_lignes)) {
                        while (($row = mysqli_fetch_assoc($result_data)) && ($i < $max_lignes)) {
                            echo "<tr>";
                            $i = $i + 1;
                            echo '<td>' . $i . '</td>';
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
                        if (mysqli_num_rows($result_data) >= $max_lignes) {
                            echo "<tr>";
                            echo "<td colspan='8' class='uk-text-center'>Il y a plus de ". ( mysqli_num_rows($result_data) - $max_lignes) ." lignes restantes... Veuillez préciser votre recherche. </td>";
                            echo "<tr>";
                        }

                        mysqli_free_result($result_data);
                    } else {
                        echo "<tr><td colspan='8' class='uk-text-center'>Aucun écran actif trouvé pour les filtres appliqués.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <?php if ($is_tech): ?>
                <hr class="uk-margin-large-top">
                <h3 class="uk-text-left sansation-bold uk-margin-small-top">Gestion des Écrans</h3>
                <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin-medium-top">
                    <h4 class="sansation-regular uk-margin-small-bottom">Import CSV :</h4>
                    <form action="csv_import_ecran.php" method="POST" enctype="multipart/form-data"
                          class="uk-margin-small-bottom">
                        <div class="uk-flex uk-flex-middle">
                            <input type="file" name="csv_file_upload" id="csv_file_upload_ecran" required>
                            <input type="hidden" name="target_table" value="Monitors">
                            <button type="submit"
                                    class="uk-button uk-button-secondary uk-margin-small-left uk-border-rounded">
                                Importer
                            </button>
                        </div>
                    </form>
                    <div class="uk-margin-medium-top">
                        <h4 class="sansation-regular uk-margin-small-bottom">Saisie manuelle :</h4>
                        <form class="uk-form-stacked" action="ajout_ecran_formulaire.php" method="post">
                            <div class="uk-grid-small uk-child-width-1-3@s uk-grid">
                                <div>
                                    <label class="uk-form-label" for="serial_monitor">Numéro de
                                        Série</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="serial_monitor"
                                               id="serial_monitor_add"
                                               placeholder="Numéro de Série">
                                    </div>
                                </div>

                                <div>
                                    <label class="uk-form-label"
                                           for="manufacturer_select">Constructeur</label>
                                    <div class="uk-form-controls">

                                        <select class="uk-select" id="manufacturer_select_monitor_add"
                                                name="manufacturer" required>

                                            <option value="">-- Sélectionnez un constructeur --</option>

                                            <?php
                                            if ($result_manufacturers) {
                                                mysqli_data_seek($result_manufacturers, 0);
                                                while ($row = mysqli_fetch_assoc($result_manufacturers)) {
                                                    $manufacturer_name = htmlspecialchars($row['name']);
                                                    echo "<option value=\"$manufacturer_name\">$manufacturer_name</option>";
                                                }
                                                mysqli_free_result($result_manufacturers);
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="model_monitor">Modèle</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="model_monitor" id="model_monitor_add"
                                               placeholder="Modèle">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="size_inch">Dimensions (pouces)</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="size_inch" id="size_inch_add"
                                               placeholder="Dimensions">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="resolution">Résolution</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="resolution" id="resolution_add"
                                               placeholder="Résolution">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="connection">Connection</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="connection" id="connection_add"
                                               placeholder="Connection">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="attached_to">Machine Associée</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input" type="text" name="attached_to" id="attached_to_add"
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

                <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-margin-medium-top">
                    <h3 class="uk-card-title sansation-regular">Supprimer un Écran</h3>
                    <form class="uk-form-stacked" method="POST" action="suppression_ecran.php">
                        <div class="uk-grid-small uk-child-width-1-2@s uk-grid">
                            <div>
                                <label class="uk-form-label">Numéro de Série</label>
                                <div class="uk-form-controls">
                                    <label for="serial_monitors"></label><input class="uk-input" type="text"
                                                                                name="serial_monitors"
                                                                                id="serial_monitors_delete"
                                                                                placeholder="DMXXXXXXX">
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
            <?php endif; ?>

        <?php elseif ($equipment_type === 'rebut' && $is_tech): ?>

            <h3 class="uk-text-left sansation-regular uk-margin-small-top">Machines au Rebut</h3>
            <div class="uk-overflow-auto uk-margin-small-bottom" style="max-height: 300px;">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>
                        <th>Actions</th>
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
                    if ($result_devices_rebut && mysqli_num_rows($result_devices_rebut) > 0) {
                        while ($row = mysqli_fetch_assoc($result_devices_rebut)) {
                            echo "<tr>";
                            echo "<td class='uk-width-small'>
                                <button class='uk-button uk-button-small uk-button-secondary uk-border-rounded'>Restaurer</button>
                            </td>";
                            echo "<td>" . (isset($row['name']) ? $row['name'] : '') . "</td>";
                            echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                            echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                            echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                            echo "<td>" . (isset($row['os']) ? $row['os'] : '') . "</td>";
                            echo "<td>" . (isset($row['domain']) ? $row['domain'] : '') . "</td>";
                            echo "<td>" . (isset($row['location']) ? $row['location'] : '') . "</td>";
                            echo "</tr>";
                        }
                        mysqli_free_result($result_devices_rebut);
                    } else {
                        echo "<tr><td colspan='8' class='uk-text-center'>Aucune machine en attente trouvée pour les filtres appliqués.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <h3 class="uk-text-left sansation-regular uk-margin-medium-top">Écrans au Rebut</h3>
            <div class="uk-overflow-auto uk-margin-small-bottom" style="max-height: 300px;">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>
                        <th>Actions</th>
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
                    if ($result_monitors_rebut && mysqli_num_rows($result_monitors_rebut) > 0) {
                        while ($row = mysqli_fetch_assoc($result_monitors_rebut)) {
                            echo "<tr>";
                            echo "<td class='uk-width-small'>
                                <button class='uk-button uk-button-small uk-button-secondary uk-border-rounded'>Restaurer</button>
                            </td>";
                            echo "<td>" . (isset($row['serial']) ? $row['serial'] : '') . "</td>";
                            echo "<td>" . (isset($row['manufacturer']) ? $row['manufacturer'] : '') . "</td>";
                            echo "<td>" . (isset($row['model']) ? $row['model'] : '') . "</td>";
                            echo "<td>" . (isset($row['size_inch']) ? $row['size_inch'] : '') . "</td>";
                            echo "<td>" . (isset($row['resolution']) ? $row['resolution'] : '') . "</td>";
                            echo "<td>" . (isset($row['attached_to']) ? $row['attached_to'] : '') . "</td>";
                            echo "</tr>";
                        }
                        mysqli_free_result($result_monitors_rebut);
                    } else {
                        echo "<tr><td colspan='7' class='uk-text-center'>Aucun écran en attente trouvé pour les filtres appliqués.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    <?php else: ?>
        <div class="uk-alert uk-width-1-1" uk-alert style="background-color: #666; color: white;">
            <p>Veuillez sélectionner un type d'équipement ci-dessus pour afficher les options de filtre et les
                résultats.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>