<?php
session_start();



$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");
if (!$conn) die("Connexion échouée : " . mysqli_connect_error());

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['login'];
    $pass = $_POST['password'];

    // Récupérer le nonce
    $sql = "SELECT login, nonce FROM Nonces WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { header("Location: formulaire.php?error=" . urlencode("Erreur requête")); exit; }
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result_nonce = mysqli_stmt_get_result($stmt);

    // Récupérer le mot de passe et rôle
    $sql = "SELECT password, role FROM Users WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { header("Location: formulaire.php?error=" . urlencode("Erreur requête")); exit; }
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $db_password = $row['password'];
        $db_role = $row['role'];

        if ($row_nonce = mysqli_fetch_assoc($result_nonce)) {
            $nonce = $row_nonce['nonce'];

            // Appel du script Python pour le mot de passe
            shell_exec("source /home/sae2025/venv/bin/activate");
            $cmd = "/var/www/venv/bin/python /var/www/rpi11/Ressources/py/login.py "
                . escapeshellarg($pass) . " " . escapeshellarg($nonce) ." 2>&1";
            $output = shell_exec($cmd);
            $pass = trim($output);
        } else {
            header("Location: formulaire.php?error=" . urlencode("Identifiant inconnu dans la table des clés"));
            exit;
        }

        if ($pass == $db_password) {
            // --- SESSION ---
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $db_role;
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['login_time'] = time();

            $login_time_formatted = date('H:i:s', $_SESSION['login_time']);
            $login_date = date('Y-m-d', $_SESSION['login_time']);
            $duration_seconds = 0; // au login, la durée est 0

            $sql_log = "INSERT INTO Connections (login, ip_address, time, date, duration_seconds) 
                        VALUES (?, ?, ?, ?, ?)";
            $stmt_log = mysqli_prepare($conn, $sql_log);
            if ($stmt_log) {
                mysqli_stmt_bind_param($stmt_log, "ssssi", $user, $_SESSION['user_ip'], $login_time_formatted, $login_date, $duration_seconds);
                if (mysqli_stmt_execute($stmt_log)) {
                    echo "Connexion enregistrée dans la base de données!";
                } else {
                    echo "Erreur lors de l'enregistrement dans la base de données: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt_log);
            }
            setcookie('essais', '', time() - 3600);
            setcookie('date', '', time() - 3600);

            if ($db_role == "tech") header("Location: inventaire.php");
            else if ($db_role == "sysadmin") header("Location: adminsystem.php");
            else if ($db_role == "adminweb") header("Location: adminweb.php");


            exit;
        } else {



            header("Location: formulaire.php?error=" . urlencode("Mot de passe incorrect"));
            exit;
        }
    } else {
        $essais_count = isset($_COOKIE['essais']) ? $_COOKIE['essais'] + 1 : 1;
        setcookie('essais', $essais_count, time() + 900);

        if (!isset($_COOKIE['date'])) {
            $date_expiration = time() + 300;
            setcookie('date', $date_expiration, time() + 300);
        } else {
            $date_expiration = $_COOKIE['date'];
        }

        if ($essais_count >= 3) {
            $temps_restant = $date_expiration - time();

            $minutes = ceil($temps_restant / 60);

            if ($temps_restant > 0) {
                $message = "Trop de tentatives ! Veuillez réessayer dans environ $minutes minute(s).";
                header('Location: formulaire.php?error=' . urlencode($message));
                exit();
            }
        } else {

            header("Location: formulaire.php?error=" . urlencode("Identifiant ou mot de passe incorrect"));
            exit;
        }
    }

    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
