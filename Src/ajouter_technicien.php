<?php

if (($_SESSION['role']) != 'adminweb'): {
    header('Location: index.php');
};
endif;

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifiant = $_POST['login'];
    $mot_de_passe = $_POST['password'];

    $check_sql = "SELECT login FROM Users WHERE login = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        echo "Erreur de préparation de la vérification : " . $conn->error;
        mysqli_close($conn);
        exit;
    }

    $check_stmt->bind_param("s", $identifiant);
    $check_stmt->execute();
    $check_stmt->store_result(); 

    if ($check_stmt->num_rows > 0) {
        echo "Erreur : Cet identifiant existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);
        exit;
    }

    $check_stmt->close(); 

    $mot_de_passe_hash = md5($mot_de_passe);

    $sql = "INSERT INTO Users (login, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Erreur de préparation de la requête d'insertion : " . $conn->error;
    } else {
        $stmt->bind_param("ss", $identifiant, $mot_de_passe_hash);

        if ($stmt->execute()) {
            echo "Technicien ajouté avec succès.";
        } else {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }
        $stmt->close();
    }
}

mysqli_close($conn);
?>
