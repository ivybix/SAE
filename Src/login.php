<?php
session_start();

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "Parc_informatique");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['username'];
    $pass = MD5($_POST['password']);

    $sql = "SELECT password FROM Users WHERE usertype = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        if (mysqli_stmt_bind_param($stmt, "s", $user)) {
            if (mysqli_stmt_execute($stmt)) {
                
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    $db_password = $row['password'];
                    
                    if ($pass == $db_password) { 
                        $_SESSION['user'] = $user;
                        
                        if ($user == "tech1") {
                            header("Location: technicien.html");
                        }
                        else if ($user == "sysadmin") {
                            header("Location: adminsystem.html");
                        }
                        else if ($user == "adminweb") {
                            header("Location: adminweb.html");
                        } 
                        else {
                            header("Location: Index.html");
                        }
                        exit;
                    } else {
                        echo "Mot de passe incorrect."; 
                    }
                } else {
                    echo "Utilisateur non trouvé."; 
                }
            } else {
                echo "Erreur d'exécution de la requête.";
            }
        } else {
            echo "Erreur de liaison des paramètres.";
        }
        mysqli_stmt_close($stmt); 
    } else {
        echo "Erreur de préparation de la requête.";
    }
}

mysqli_close($conn);
?>