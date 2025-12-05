<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_device = $_POST['serial_device'];

    $check_sql = "SELECT serial FROM Active_Devices WHERE serial = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        echo "Erreur de préparation de la vérification : " . $conn->error;
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->bind_param("s", $serial_device);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows == 0) {
        echo "Erreur : Aucune machine ne possède ce numéro de série.";
        $check_stmt->close();
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->close();

    $sql = "DELETE FROM Active_Devices WHERE serial = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Erreur de préparation de la requête de suppression : " . $conn->error;
    } else {
        $stmt->bind_param("s", $serial_device);

        if ($stmt->execute()) {
            echo "Machine supprimée avec succès.";
        } else {
            echo "Erreur lors de la suppression : " . $stmt->error;
        }
        $stmt->close();
    }
}

mysqli_close($conn);

header("location: inventaire.php");
?>
