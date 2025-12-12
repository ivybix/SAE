<?php
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {

    die("Connexion échouée : " . mysqli_connect_error());

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['os'];


    $check_sql = "SELECT name FROM Operating_Systems WHERE name = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        header("location: adminweb.php?error=" . urlencode( "Erreur de préparation de la vérification : " . $conn->error));
        mysqli_close($conn);
 
        exit;
    }

    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        header("location: adminweb.php?error=" . urlencode( "Erreur : Ce système d'exploitation existe déjà. Veuillez en choisir un autre."));

        $check_stmt->close();
        mysqli_close($conn);
 
        exit;
    }

    $check_stmt->close();

    $sql = "INSERT INTO Operating_Systems(name) VALUES (?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        
        header("location: adminweb.php?error=" . urlencode("Erreur de préparation de la requête d'insertion : " . $conn->error));
    } else {
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            header("location: adminweb.php?success=" . urlencode( "Système d'exploitation ajouté avec succès."));
        } else {
            header("location: adminweb.php?error=" . urlencode( "Erreur lors de l'insertion : " . $stmt->error));
        }
        $stmt->close();
    }
}

mysqli_close($conn);
exit();
?>
