<?php
session_start();

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['login'];
    $pass = MD5($_POST['password']);

    $sql = "SELECT password, role FROM Users WHERE login = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "Erreur dans la préparation de la requête.";
        mysqli_close($conn);
        exit;
    }

    if (!mysqli_stmt_bind_param($stmt, "s", $user)) {
        echo "Erreur de liaison des paramètres.";
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit;
    }

    if (!mysqli_stmt_execute($stmt)) {
        echo "Erreur d'exécution de la requête.";
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit;
    }

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $db_password = $row['password'];
        $db_role = $row['role'];

        if ($pass == $db_password) {
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $db_role;

            if ($db_role == "tech") {
                header("Location: inventaire.php");
            } else if ($db_role == "sysadmin") {
                header("Location: adminsystem.html");
            } else if ($db_role == "adminweb") {
                header("Location: adminweb.html");
            }

            exit;
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Utilisateur introuvable.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>