<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée");
}

// --- Calcul du nombre total de machines ---
$total_machines = 0;
$sql_total = "SELECT COUNT(*) AS total FROM Devices";
$result_total = mysqli_query($conn, $sql_total);
if ($result_total) {
    $row_total = mysqli_fetch_assoc($result_total);
    $total_machines = (int)$row_total['total'];
    mysqli_free_result($result_total);
}
// La variable $total_machines contient maintenant le nombre total.

$total_ecran = 0;
$sql_total = "SELECT COUNT(*) AS total FROM Monitors";
$result_total = mysqli_query($conn, $sql_total);
if ($result_total) {
    $row_total = mysqli_fetch_assoc($result_total);
    $total_ecran = (int)$row_total['total'];
}



// --- Statistiques par Constructeur ---
$stats_constructeur = [];
// Utilisation de IFNULL(manufacturer, 'Non Renseigné') pour gérer les valeurs NULL ou vides
$sql_manuf = "SELECT IFNULL(NULLIF(TRIM(manufacturer), ''), 'Non Renseigné') AS manufacturer_clean, 
              COUNT(*) AS count 
              FROM Devices 
              GROUP BY manufacturer_clean 
              ORDER BY count DESC";
$result_manuf = mysqli_query($conn, $sql_manuf);

if ($result_manuf) {
    while ($row = mysqli_fetch_assoc($result_manuf)) {
        $count = (int)$row['count'];
        $percentage = ($total_machines > 0) ? round(($count / $total_machines) * 100, 2) : 0;
        $stats_constructeur[] = [
            'categorie' => $row['manufacturer_clean'],
            'count' => $count,
            'percentage' => $percentage
        ];
    }
    mysqli_free_result($result_manuf);
}
// $stats_constructeur est un tableau contenant les stats pour chaque constructeur.

// --- Statistiques par Domaine ---
$stats_domaine = [];
// Utilisation de IFNULL(domain, 'Non Renseigné') pour gérer les valeurs NULL ou vides
$sql_domain = "SELECT IFNULL(NULLIF(TRIM(domain), ''), 'Non Renseigné') AS domain_clean, 
               COUNT(*) AS count 
               FROM Devices 
               GROUP BY domain_clean 
               ORDER BY count DESC";
$result_domain = mysqli_query($conn, $sql_domain);

if ($result_domain) {
    while ($row = mysqli_fetch_assoc($result_domain)) {
        $count = (int)$row['count'];
        $percentage = ($total_machines > 0) ? round(($count / $total_machines) * 100, 2) : 0;
        $stats_domaine[] = [
            'categorie' => $row['domain_clean'],
            'count' => $count,
            'percentage' => $percentage
        ];
    }
    mysqli_free_result($result_domain);
}
// $stats_domaine est un tableau contenant les stats pour chaque domaine.

// --- Statistiques par Localisation ---
$stat_ecran_manu = [];
// Utilisation de IFNULL(location, 'Non Renseigné') pour gérer les valeurs NULL ou vides
$sql_ecran_manu = "SELECT IFNULL(NULLIF(TRIM(manufacturer), ''), 'Non Renseigné') AS manu_clean, 
            COUNT(*) AS count 
            FROM Monitors 
            GROUP BY manu_clean 
            ORDER BY count DESC";
$sql_ecran_manu = mysqli_query($conn, $sql_ecran_manu);

if ($sql_ecran_manu) {
    while ($row = mysqli_fetch_assoc($sql_ecran_manu)) {
        $count = (int)$row['count'];
        $percentage = ($total_machines > 0) ? round(($count / $total_machines) * 100, 2) : 0;
        $stat_ecran_manu[] = [
            'categorie' => $row['manu_clean'],
            'count' => $count,
            'percentage' => $percentage
        ];
    }
    mysqli_free_result($sql_ecran_manu);
}

// --- Statistiques par Localisation ---
$stats_localisation = [];
// Utilisation de IFNULL(location, 'Non Renseigné') pour gérer les valeurs NULL ou vides
$sql_loc = "SELECT IFNULL(NULLIF(TRIM(location), ''), 'Non Renseigné') AS location_clean, 
            COUNT(*) AS count 
            FROM Devices 
            GROUP BY location_clean 
            ORDER BY count DESC";
$result_loc = mysqli_query($conn, $sql_loc);
if ($result_loc) {
    while ($row = mysqli_fetch_assoc($result_loc)) {
        $count = (int)$row['count'];
        $percentage = ($total_machines > 0) ? round(($count / $total_machines) * 100, 2) : 0;
        $stats_localisation[] = [
            'categorie' => $row['location_clean'],
            'count' => $count,
            'percentage' => $percentage
        ];
    }
    mysqli_free_result($result_loc);
}



if (isset($_POST['btn_generer_pie_anciennete'])) {


    putenv('MPLCONFIGDIR=/tmp');
    $command = 'python3 /var/www/rpi11/Ressources/py/stat_1.py 2>&1';
    $output = shell_exec($command);

    $command = 'python3 /var/www/rpi11/Ressources/py/stat_2.py 2>&1';
    $output = shell_exec($command);

    $command = 'python3 /var/www/rpi11/Ressources/py/stat_3.py 2>&1';
    $output = shell_exec($command);


    header('location: stats.php');
    exit();


}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav2.ico"/>
    <title>Administration Web - Statistiques</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css"/>
    <script src="Ressources/js/uikit.js"></script>
    <link rel="stylesheet" href="CSS/uikit-rtl.css"/>
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div class="uk-container uk-margin-medium-top">
    <?php include_once 'alerts.php'; ?>
    <h1 class="sansation uk-text-center"><span>Statistiques du Parc</span></h1>


    <div class="uk-card uk-card-default uk-card-body uk-width-1-1 uk-animation-fade">
        <div class="uk-text-center">

            <h3 class="sansation uk-card-title">Analyse Graphique</h3>


            <div class="uk-grid uk-child-width-1-2@m uk-text-center" uk-grid>

                <div>
                    <div class="uk-margin">
                        <img src="/Ressources/image_stat/stat1.webp"
                             alt="Graphique Statistique 1 : Répartition par ancienneté"
                             style="max-height: 400px; width: auto;">
                    </div>


                    <h4 class="sansation-bold">Description de l'Ancienneté :</h4>
                    <p class="sansation-regular">

                        Le camembert représente la répartition des machines en fonction de leur ancienneté,
                        calculée sur la base de leur date d'achat. L’objectif est de suivre l’évolution du parc
                        informatique au fil du temps et d'identifier les machines qui risquent d’être obsolètes.
                    </p>
                </div>

                <div class="uk-padding-remove-left@m uk-border-left@m" style="border-left: 1px solid white">
                    <div class="uk-margin">
                        <img src="/Ressources/image_stat/stat2.webp"
                             alt="Graphique Statistique 2 : Répartition par type"
                             style="max-height: 400px; width: auto;">
                    </div>

                    <h4 class="sansation-bold">Description de la répartition des temps de connexion des utilisateurs
                        :</h4>
                    <p>
                        Cet histogramme représente la répartition des temps de connexion des utilisateurs.
                        L’objectif est d’offrir une vision claire des habitudes d’utilisation et d’identifier le niveau
                        d’activité global sur la plateforme.
                    </p>
                </div>
            </div>

            <div class="uk-margin-large-top uk-border-top uk-padding-small " style="border-top: 1px solid white;">
    <div class="uk-text-center"> 
        <div class="uk-margin-medium"> 
            <img src="/Ressources/image_stat/stat3.webp"
                 alt="Graphique Statistique 3 : Répartition par type"
                 style="max-height: 400px; width: auto;">
        </div>

        <h4 class="sansation-bold">Notre leaderboard :</h4>
        <p>
        On représente la synthèse sous la forme d’une grille composée de 4 rectangles. 3 en haut de la grille qui représentent dans l’ordre les statistiques suivantes :
        </p><ul class="sansation-light"> 
    <li> L'utilisateur avec la plus longue session. </li>
    <li> L'utilisateurs avec le plus de connexions.</li>
    <li> La machine avec le plus de connexions (identifiée par son adresse IP).</li>
        </ul>
        <p>
Dans un second temps,  voir quelle machine est la plus usée et en même temps celle qui risque d’être dégradée plus vite.
 Aussi, cela pourrait permettre de visualiser les écrans les plus utilisés (ceux qui sont associés à la machine)

Enfin, on implémente un grand rectangle en bas de la grille qui représente sous la forme d’un graphique les 5 utilisateurs avec la plus longue session en moyenne.


        </p>
    </div>
</div>

        </div>


        <?php if (($_SESSION['role']) == 'sysadmin'): ?>
            <div class="uk-flex uk-flex-center uk-margin-bottom">
                <form method="post" action="stats.php">
                    <button class="uk-button uk-button-primary uk-button-large" type="submit"
                            name="btn_generer_pie_anciennete">
                        <span uk-icon="icon: database"></span> Générer un nouveau rapport statistique
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
    <div class="uk-container uk-margin-medium-top">
        <div class="uk-card uk-card-default uk-card-body">
            <h1 class="sansation uk-text-center"><span>Statistiques du Parc (Machines)</span></h1>
            <h3 class="uk-text-left sansation-regular uk-margin-large-top">Statistiques par constructeur (Total
                : <?php echo $total_machines; ?>)</h3>
            <div class="uk-overflow-auto uk-margin-bottom-small" style="border-bottom: 3px solid white">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>
                        <th>Constructeur</th>
                        <th>Nombre de Machines</th>
                        <th>Pourcentage (%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats_constructeur as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['categorie']); ?></td>
                            <td><?php echo $stat['count']; ?></td>
                            <td><?php echo $stat['percentage']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h3 class="uk-text-left sansation-regular uk-margin-large-top">Statistiques par domaine (Total
                : <?php echo $total_machines; ?>)</h3>
            <div class="uk-overflow-auto uk-margin" style="border-bottom: 3px solid white">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>
                        <th>Domaine</th>
                        <th>Nombre de Machines</th>
                        <th>Pourcentage (%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats_domaine as $stat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stat['categorie']); ?></td>
                            <td><?php echo $stat['count']; ?></td>
                            <td><?php echo $stat['percentage']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <h3 class="uk-text-left sansation-regular uk-margin-large-top">Statistiques par localisation (Total
                : <?php echo $total_machines; ?>)</h3>
            <div class="uk-overflow-auto uk-margin">
                <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
                    <thead>
                    <tr>
                        <th>Constructeur</th>
                        <th>Nombre d'écrans</th>
                        <th>Pourcentage (%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stats_localisation as $stat): ?>

                        <tr>
                            <td><?php echo htmlspecialchars($stat['categorie']); ?></td>
                            <td><?php echo $stat['count']; ?></td>
                            <td><?php echo $stat['percentage']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<div class="uk-container uk-margin-medium-top">

    <div class="uk-card uk-card-default uk-card-body">
    <h1 class="sansation uk-text-center"><span>Statistiques du Parc (Écrans)</span></h1>
    <h3 class="uk-text-left sansation-regular uk-margin-large-top">Statistiques par constructeur (Total
        : <?php echo $total_ecran; ?>)</h3>
    <div class="uk-overflow-auto uk-margin">
        <table class="uk-table uk-table-hover uk-table-divider uk-table-striped">
            <thead>
            <tr>
                <th>Constructeurs</th>
                <th>Nombre d'écrans</th>
                <th>Pourcentage (%)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stat_ecran_manu as $stat): ?>

                <tr>
                    <td><?php echo htmlspecialchars($stat['categorie']); ?></td>
                    <td><?php echo $stat['count']; ?></td>
                    <td><?php echo $stat['percentage']; ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


</body>
</html>