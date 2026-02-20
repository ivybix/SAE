<?php
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!$conn) {

    die("Connexion échouée : " . mysqli_connect_error());

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $serial_device = $_POST['serial_device'];
    $manufacturer_device = $_POST['manufacturer'];
    $model_device = $_POST['model_device'];
    $type = $_POST['type'];
    $cpu = $_POST['cpu'];
    $ram_mb = $_POST['ram_mb'];
    $ram_mb = (int)$ram_mb;
    $disk_gb= $_POST['disk_gb'];
    $disk_gb = (int)$disk_gb;
    $os = $_POST['os'];
    $domain = $_POST['domain'];
    $location = $_POST['location'];
    $building = $_POST['building'];
    $room = $_POST['room'];
    $macaddr = $_POST['macaddr'];
    $purchase_date = $_POST['purchase_date'];
    $warranty_end = $_POST['warranty_end'];
    $mac = $_POST['macaddr'];

    if (!empty($mac) && !preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac)) {
        header('Location: inventaire.php?equipment_type=machines&error=Format d\'adresse MAC invalide (format: XX:XX:XX:XX:XX:XX ou XX-XX-XX-XX-XX-XX), adresse indiquée : '.$mac);
        exit();
    }

    $check_sql = "SELECT serial FROM Devices WHERE serial = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {

        $message_echec = "Erreur de préparation de la vérification : " . $conn->error;
        mysqli_close($conn);

        header("location: inventaire.php?equipment_type=machines&error=" . urlencode($message_echec));
        exit;
    }

    $check_stmt->bind_param("s", $serial_device);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message_echec = "Erreur : Ce numéro de série existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);

        header("location: inventaire.php?equipment_type=machines&error=" . urlencode($message_echec));
        exit;
    }

    $check_stmt->close();



    $sql = "INSERT INTO Devices(name,serial,manufacturer,model,type,cpu,ram_mb,disk_gb,os,domain,location,building,room,macaddr,purchase_date,warranty_end) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $message_echec = "Erreur de préparation de la requête d'insertion : " . $conn->error;
        header("location: inventaire.php?equipment_type=machines&error=" . urlencode($message_echec));
    } else {
        $stmt->bind_param("ssssssiissssssss", $name, $serial_device, $manufacturer_device, $model_device, $type, $cpu, $ram_mb, $disk_gb, $os, $domain, $location, $building, $room, $macaddr, $purchase_date, $warranty_end);

        if ($stmt->execute()) {
            $message =  "Machine ajoutée avec succès.";

            header("location: inventaire.php?equipment_type=machines&success=" . urlencode($message));
        } else {
            $message = "Erreur lors de l'insertion : " . $stmt->error;

            header("location: inventaire.php?equipment_type=machines&error=" . urlencode($message));
        }
        $stmt->close();
    }
}

mysqli_close($conn);


