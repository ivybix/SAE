<?php

session_start();
if ((isset($_SESSION['role']) ? $_SESSION['role'] : '') != 'sysadmin'): {
    header('Location: index.php');
    exit();
}
endif;

$limit = 50;

$db_host = "localhost";
$db_user = "inkware";
$db_pass = "!sae2025!";
$db_name = "INVENTORY";
$conn_table = "Connections";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) die("Erreur de connexion : " . mysqli_connect_error());

$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
$filter_login = isset($_GET['filter_login']) ? $_GET['filter_login'] : 'all';

$where_clauses = [];

if ($filter_date) {
    $safe_date = mysqli_real_escape_string($conn, $filter_date);
    $where_clauses[] = "date = '{$safe_date}'";
}

if ($filter_login !== 'all') {
    $safe_login = mysqli_real_escape_string($conn, $filter_login);
    $where_clauses[] = "login = '{$safe_login}'";
}

$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$connections_sql = "SELECT login, ip_address, duration_seconds, date, time 
                    FROM {$conn_table} 
                    {$where_sql}
                    ORDER BY date DESC, time DESC 
                    LIMIT {$limit}";

$connections_result = mysqli_query($conn, $connections_sql);

$logins_result = mysqli_query($conn, "SELECT DISTINCT login FROM {$conn_table} ORDER BY login ASC");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" type="image/x-icon" href="Ressources/logo-nav.ico">
    <title>Administration Système - Connexions</title>
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="stylesheet" href="CSS/uikit.css">
    <link rel="stylesheet" href="CSS/uikit-rtl.css">
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div class="uk-container uk-margin-medium-top">
    <h1 class="uk-text-center sansation-bold">Historique des Connexions</h1>
    <p class="uk-text-center uk-text-meta">Espace réservé à l'Administrateur Système (sysadmin)</p>

    <div class="uk-card uk-card-default uk-card-body uk-margin-bottom">
        <h2 class="sansation-regular">Filtres</h2>
        <form class="uk-grid-small" uk-grid method="GET">
            <div class="uk-width-1-4@s">
                <label for="filter_date" class="uk-form-label">Date</label>
                <input class="uk-input" id="filter_date" type="date" name="filter_date" placeholder="Date"
                       value="<?php echo htmlspecialchars($filter_date); ?>">
            </div>
            <div class="uk-width-1-4@s">
                <label for="filter_login" class="uk-form-label">Utilisateur</label>
                <select class="uk-select" id="filter_login" name="filter_login">
                    <option value="all">Tous les utilisateurs</option>
                    <?php
                    if ($logins_result) {
                        while ($login_row = mysqli_fetch_assoc($logins_result)) {
                            $login = $login_row['login'];
                            $selected = ($filter_login === $login) ? 'selected' : '';
                            echo "<option value='{$login}' {$selected}>{$login}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="uk-width-1-4@s">
                <button class="uk-button uk-button-primary">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="uk-overflow-auto uk-margin-xlarge-bottom">
        <table class="uk-table uk-table-striped uk-table-hover uk-table-small">
            <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Utilisateur</th>
                <th>Adresse IP</th>
                <th>Durée de connexion (s)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($connections_result && mysqli_num_rows($connections_result) > 0) {
                while ($conn_data = mysqli_fetch_assoc($connections_result)) {
                    $date = empty($conn_data['date']) ? 'indéterminée' : htmlspecialchars($conn_data['date']);
                    $time = empty($conn_data['time']) ? 'indéterminée' : htmlspecialchars($conn_data['time']);
                    $login = empty($conn_data['login']) ? 'indéterminé' : htmlspecialchars($conn_data['login']);
                    $ip_address = empty($conn_data['ip_address']) ? 'indéterminée' : htmlspecialchars($conn_data['ip_address']);
                    $duration = empty($conn_data['duration_seconds']) ? 'indéterminée' : htmlspecialchars($conn_data['duration_seconds']);

                    echo "<tr>";
                    echo "<td>{$date}</td>";
                    echo "<td>{$time}</td>";
                    echo "<td>{$login}</td>";
                    echo "<td>{$ip_address}</td>";
                    echo "<td>{$duration}</td>";
                    echo "</tr>";
                }
                mysqli_free_result($connections_result);
            } else {
                echo "<tr><td colspan='5' class='uk-text-center'>Aucun historique de connexion trouvé pour ces critères de filtre.</td></tr>";
            }

            mysqli_close($conn);
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="js/uikit.min.js"></script>
<script src="js/uikit-icons.min.js"></script>

<?php include_once "footer.php"; ?>
</body>

</html>
