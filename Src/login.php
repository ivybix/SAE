<?php
session_start();

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "Parc_informatique");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT password FROM Users WHERE user =". $user;
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($pass === $row['password']) {
            $_SESSION['user'] = $user;

            if ($user == "technicien") {
                header("Location: technicien.html");
            }
            else if ($user == "admin_system") {
                header("Location: adminsystem.html");
            }
            else if ($user == "admin_web") {
                header("Location: adminweb.html");
            }else {
                header("Location: index.html");
            }
            exit;
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Utilisateur non trouvé.";
    }
}

mysqli_close($conn);

