<?php
session_start();

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $user_ip = $_SESSION['user_ip'] ?? $_SERVER['REMOTE_ADDR'];
    $login_time = $_SESSION['login_time'] ?? time();
    $logout_time = time();

    $duration_seconds = $logout_time - $login_time;
    $logout_time_formatted = date('H:i:s', $logout_time);
    $logout_date = date('Y-m-d', $logout_time);

    // Mise à jour de la durée dans la base de données
    $sql_update = "
    UPDATE Connections
    SET duration_seconds = ?, time = ?, date = ?
    WHERE login = ? AND duration_seconds = 0
    ORDER BY id DESC LIMIT 1";

    $stmt_update = mysqli_prepare($conn, $sql_update);
    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "isss", $duration_seconds, $logout_time_formatted, $logout_date, $user);
        if (mysqli_stmt_execute($stmt_update)) {
            echo "Durée de connexion mise à jour dans la base de données.";
        } else {
            echo "Erreur lors de la mise à jour de la durée de connexion : " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_update);
    }

    // Mise à jour dans le fichier JSON
    $file = '../Ressources/JSON/sessions.json';
    $json_array = ["users" => []];
    if (file_exists($file)) {
        $json_contents = file_get_contents($file);
        $json_array = json_decode($json_contents, true) ?: ["users" => []];
    }
}

session_unset();
session_destroy();
mysqli_close($conn);
header("Location: index.php");
exit;
?>
