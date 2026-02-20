<?php
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {

    die("Connexion échouée : " . mysqli_connect_error());

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['manufacturer'];


    $check_sql = "SELECT name FROM Manufacturers WHERE name = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) { 
        mysqli_close($conn);

        header("location: adminweb.php?error=". urlencode("Erreur de préparation de la vérification : " . $conn->error));
        exit;
    }

    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) { 
        $check_stmt->close();
        mysqli_close($conn);

        header("location: adminweb.php?error=".urlencode( "Erreur : Ce manufactureur existe déjà. Veuillez en choisir un autre."));
        exit;
    }

    $check_stmt->close();

    $sql = "INSERT INTO Manufacturers(name) VALUES (?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        header("location: adminweb.php?error=".urlencode("Erreur de préparation de la requête d'insertion : " . $conn->error));;
    } else {
        $stmt->bind_param("s", $name);

        if ($stmt->execute()) {
            header("location: adminweb.php?success=".urlencode( "Manufactureur ajouté avec succès."));;
        } else {
            header("location: adminweb.php?error=".urlencode( "Erreur lors de l'insertion : " . $stmt->error));
        }
        $stmt->close();
    }
}

mysqli_close($conn);
 
?>
