<?php

require('login.php');

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifiant = $_POST['login'];
    $mot_de_passe = $_POST['password'];

    $mot_de_passe_hash = md5($mot_de_passe);

    $sql = "INSERT INTO Users (login, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifiant, $mot_de_passe_hash);

    if ($stmt->execute()) {
        echo "Technicien ajouté avec succès.";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



