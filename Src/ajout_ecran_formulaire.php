<?php
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_monitor = $_POST['serial_monitor'];
    $manufacturer_monitor = htmlspecialchars(trim($_POST['manufacturer']));
    $model_monitor = $_POST['model_monitor'];
    $size_inch = $_POST['size_inch'];
    $resolution = $_POST['resolution'];
    $connection = $_POST['connection'];
    $attached_to = $_POST['attached_to'];


    $check_sql = "SELECT serial FROM Monitors WHERE serial = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        echo "Erreur de préparation de la vérification : " . $conn->error;
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->bind_param("s", $serial_monitor);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Erreur : Ce numéro de série existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->close();



    /*$check_sql = "SELECT serial FROM Monitors WHERE serial = ?"; //A modifier pour vérifier que la machine choisis n'a pas deja un écran attaché
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        echo "Erreur de préparation de la vérification : " . $conn->error;
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->bind_param("s", $serial_monitor);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Erreur : Ce numéro de série existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->close();*/




    $sql = "INSERT INTO Monitors(serial,manufacturer,model,size_inch,resolution,connector,attached_to) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Erreur de préparation de la requête d'insertion : " . $conn->error;
    } else {
        $stmt->bind_param("sssisss", $serial_monitor, $manufacturer_monitor, $model_monitor, $size_inch, $resolution, $connection, $attached_to);

        if ($stmt->execute()) {
            echo "Ecran ajouté avec succès.";
        } else {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }
        $stmt->close();
    }
}

mysqli_close($conn);

header("location: inventaire.php");
?>
