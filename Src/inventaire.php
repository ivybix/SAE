<?php
session_start();
$is_tech = isset($_SESSION['role']) && $_SESSION['role'] === 'tech';

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion Ã©chouÃ©e");
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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/x-icon" href="Ressources/logo.ico"/>
    <title>Inventaire - Espace Technicien</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css" />
    <link rel="stylesheet" href="CSS/uikit-rtl.css" />
</head>
<body>
<nav class="uk-navbar-container uk-navbar-transparent uk-align-center">
    <div class="uk-container">
        <div class="uk-navbar">
            <div class="uk-navbar-center">
                <ul class="uk-navbar-nav menu">
                    <li>
                        <a class="uk-navbar-item uk-logo" href="index.html" target="_self" aria-label="Accueil">
                            <img src="Ressources/logo-nav.ico" alt="Logo InkWare (Accueil)" style="height: 40px;">
                        </a>
                    </li>
                    <li class="menu-item"><a href="index.html">Accueil</a></li>
                    <li class="menu-item"><a href="inventaire.php">Inventaire</a></li>
                    <li class="menu-item"><a href="login.html" target="_self">Connexion</a></li>
                </ul>
                <div class="uk-navbar-center-right">
                    <button class="uk-button uk-button-default" hidden><a class="sansation-regular">DÃ©connexion</a></button>
                </div>
            </div>
        </div>
    </div>
</nav>
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
                            <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">Exporter en CSV</button>
                            <div class="uk-overflow-auto" style="max-height: 300px;">
                                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                                    <thead>
                                    <tr>
                                        <?php if ($is_tech): ?><th>Actions</th><?php endif; ?>
                                        <th>Nom</th>
                                        <th>NumÃ©ro de SÃ©rie</th>
                                        <th>Constructeur</th>
                                        <th>ModÃ¨le</th>
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
                                                    <button class='uk-button uk-button-small uk-button-primary uk-border-rounded'>Modifier</button>
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
                                <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">Importer depuis un CSV</button>
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Ajouter une Machine</h2>
                                    <form class="uk-form-stacked">
                                        <div class="uk-grid-small uk-child-width-1-3@s uk-grid">
                                            <div>
                                                <label class="uk-form-label" for="name">Nom</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="name" placeholder="Nom">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="serial_device">NumÃ©ro de SÃ©rie</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="serial_device" placeholder="NumÃ©ro de SÃ©rie">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="manufacturer_device">Constructeur</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="manufacturer_device" placeholder="Constructeur">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="model_device">ModÃ¨le</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="model_device" placeholder="ModÃ¨le">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="type">Type</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="type" placeholder="Type">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="cpu">CPU</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="cpu" placeholder="CPU">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="ram_mb">RAM (MB)</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="number" id="ram_mb" placeholder="RAM en MB">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="disk_gb">Disque (GB)</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="number" id="disk_gb" placeholder="Disque en GB">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="os">OS</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="os" placeholder="OS">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="domain">Domaine</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="domain" placeholder="Domaine">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="location">Location</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="location" placeholder="Location">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="building">BÃ¢timent</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="building" placeholder="BÃ¢timent">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="room">Salle</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="room" placeholder="Salle">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="macaddr">Adresse MAC</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="macaddr" placeholder="Adresse MAC">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="purchase_date">Date Achat</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="date" id="purchase_date">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="warranty_end">Garantie</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="date" id="warranty_end">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-primary uk-border-rounded" type="button">Ajouter</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-bottom collapsible-section">
                            <input type="checkbox" id="toggle-machines-supprimer" class="toggle-checkbox">
                            <label for="toggle-machines-supprimer" class="toggle-label sansation-regular">Supprimer</label>
                            <div class="collapsible-content">
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Supprimer une Machine</h2>
                                    <form class="uk-form-stacked">
                                        <div class="uk-grid-small uk-child-width-1-2@s uk-grid">
                                            <div>
                                                <label class="uk-form-label">NumÃ©ro de SÃ©rie</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" placeholder="SNXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-danger uk-border-rounded" type="button">Supprimer</button>
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
                <label for="toggle-ecrans" class="toggle-label sansation-regular">Ã‰crans</label>
                <div class="collapsible-content">
                    <div class="uk-margin-small-bottom collapsible-section">
                        <input type="checkbox" id="toggle-ecrans-consulter" class="toggle-checkbox" checked>
                        <label for="toggle-ecrans-consulter" class="toggle-label sansation-regular">Consulter</label>
                        <div class="collapsible-content">
                            <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">Exporter en CSV</button>
                            <div class="uk-overflow-auto" style="max-height: 300px;">
                                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                                    <thead>
                                    <tr>
                                        <?php if ($is_tech): ?><th>Actions</th><?php endif; ?>
                                        <th>NumÃ©ro de SÃ©rie</th>
                                        <th>Constructeur</th>
                                        <th>ModÃ¨le</th>
                                        <th>Dimensions_pouces</th>
                                        <th>RÃ©solution</th>
                                        <th>Machine AssociÃ©e</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($result_monitors) {
                                        while ($row = mysqli_fetch_assoc($result_monitors)) {
                                            echo "<tr>";
                                            if ($is_tech) {
                                                echo "<td>
                                                    <button class='uk-button uk-button-small uk-button-primary uk-border-rounded'>Modifier</button>
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
                                <button class="uk-button uk-button-secondary uk-margin-small-bottom uk-border-rounded">Importer depuis un CSV</button>
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Ajouter un Ã‰cran</h2>
                                    <form class="uk-form-stacked">
                                        <div class="uk-grid-small uk-child-width-1-3@s uk-grid">
                                            <div>
                                                <label class="uk-form-label" for="serial_monitor">NumÃ©ro de SÃ©rie</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="serial_monitor" placeholder="NumÃ©ro de SÃ©rie">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="manufacturer_monitor">Constructeur</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="manufacturer_monitor" placeholder="Constructeur">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="model_monitor">ModÃ¨le</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="model_monitor" placeholder="ModÃ¨le">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="size_inch">Dimensions (pouces)</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="size_inch" placeholder="Dimensions">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="resolution">RÃ©solution</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="resolution" placeholder="RÃ©solution">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="connection">Connection</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="connection" placeholder="Connection">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="uk-form-label" for="attached_to">Machine AssociÃ©e</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" id="attached_to" placeholder="Machine AssociÃ©e">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-primary uk-border-rounded" type="button">Ajouter</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-small-bottom collapsible-section">
                            <input type="checkbox" id="toggle-ecrans-supprimer" class="toggle-checkbox">
                            <label for="toggle-ecrans-supprimer" class="toggle-label sansation-regular">Supprimer</label>
                            <div class="collapsible-content">
                                <div class="uk-margin-medium-top uk-card uk-card-default uk-card-body uk-width-1-1">
                                    <h2 class="uk-card-title sansation-regular">Supprimer un Ã‰cran</h2>
                                    <form class="uk-form-stacked">
                                        <div class="uk-grid-small uk-child-width-1-2@s uk-grid">
                                            <div>
                                                <label class="uk-form-label">NumÃ©ro de SÃ©rie</label>
                                                <div class="uk-form-controls">
                                                    <input class="uk-input" type="text" placeholder="DMXXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-margin-top">
                                            <button class="uk-button uk-button-danger uk-border-rounded" type="button">Supprimer</button>
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
        <label for="toggle-rebut" class="toggle-label sansation-regular">MatÃ©riel au Rebut</label>
        <div class="collapsible-content">
            <div class="uk-margin-medium-bottom collapsible-section">
                <input type="checkbox" id="toggle-rebut-machines" class="toggle-checkbox" checked>
                <label for="toggle-rebut-machines" class="toggle-label sansation-regular">Machines</label>
                <div class="collapsible-content">
                    <div class="uk-overflow-auto" style="max-height: 300px;">
                        <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                            <thead>
                            <tr>
                                <?php if ($is_tech): ?><th>Actions</th><?php endif; ?>
                                <th id="name_rebut">Nom</th>
                                <th id="serial_device_rebut">NumÃ©ro de SÃ©rie</th>
                                <th id="manufacturer_device_rebut">Constructeur</th>
                                <th id="model_device_rebut">ModÃ¨le</th>
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
                <label for="toggle-rebut-ecrans" class="toggle-label sansation-regular">Ã‰crans</label>
                <div class="collapsible-content">
                    <div class="uk-overflow-auto" style="max-height: 300px;">
                        <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                            <thead>
                            <tr>
                                <?php if ($is_tech): ?><th>Actions</th><?php endif; ?>
                                <th id="serial_monitor_rebut">NumÃ©ro de SÃ©rie</th>
                                <th id="manufacturer_monitor_rebut">Constructeur</th>
                                <th id="model_monitor_rebut">ModÃ¨le</th>
                                <th id="size_inch_rebut">Dimensions_pouces</th>
                                <th id="resolution_rebut">RÃ©solution</th>
                                <th id="attached_to_rebut">Machine AssociÃ©e</th>
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
