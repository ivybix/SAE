<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {

    die("Connexion échouée : " . mysqli_connect_error());

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $serial_device = $_POST['serial_device'];
    $manufacturer_device = $_POST['manufacturer_device'];
    $model_device = $_POST['model_device'];
    $type = $_POST['type'];
    $cpu = $_POST['cpu'];
    $ram_mb = $_POST['ram_mb'];
    $disk_gb= $_POST['disk_gb'];
    $os = $_POST['os'];
    $domain = $_POST['domain'];
    $location = $_POST['location'];
    $building = $_POST['building'];
    $room = $_POST['room'];
    $macaddr = $_POST['macaddr'];
    $purchase_date = $_POST['purchase_date'];
    $warranty_end = $_POST['warranty_end'];


    $check_sql = "SELECT serial FROM Devices WHERE serial = ?";
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

    if ($check_stmt->num_rows > 0) {
        echo "Erreur : Ce numéro de série existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);

        header("location: inventaire.php");
        exit;
    }

    $check_stmt->close();

    $check_sql = "SELECT macaddr FROM Devices WHERE macaddr = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {
        echo "Erreur de préparation de la vérification : " . $conn->error;
        mysqli_close($conn);

        header("location: inventaire.php");
    }

    $check_stmt->bind_param("s",$macaddr);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Erreur : Cette addresse mac existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);

        header("location: inventaire.php");
    }

    $check_stmt->close();


    $sql = "INSERT INTO Devices(name,serial,manufacturer,model,type,cpu,ram_mb,disk_gb,os,domain,location,building,room,macaddr,purchase_date,warranty_end) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Erreur de préparation de la requête d'insertion : " . $conn->error;
    } else {
        $stmt->bind_param("ssssssiissssssss", $name, $serial_device, $manufacturer_device, $model_device, $type, $cpu, $ram_mb, $disk_gb, $os, $domain, $location, $building, $room, $macaddr, $purchase_date, $warranty_end);

        if ($stmt->execute()) {
            echo "Machine ajoutée avec succès.";
        } else {
            echo "Erreur lors de l'insertion : " . $stmt->error;
        }
        $stmt->close();
    }
}

mysqli_close($conn);

header("location: inventaire.php");
?>
