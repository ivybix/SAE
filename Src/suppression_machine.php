<?php
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_device = $_POST['serial_devices'];

    $check_sql = "SELECT serial FROM Active_Devices WHERE serial = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if ($check_stmt === false) {
        mysqli_close($conn);
        header("location: inventaire.php?equipment_type=machines&error=" . mysqli_error($conn));
        exit;
    }

    mysqli_stmt_bind_param($check_stmt, "s", $serial_device);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($conn);
        header("location: inventaire.php?equipment_type=machines&error=" . mysqli_error($conn));
        exit;
    }

    mysqli_stmt_close($check_stmt);
    
    $insert_sql = "INSERT INTO Waiting_Devices (serial) SELECT serial FROM Active_Devices WHERE serial = ?";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);

    if ($insert_stmt === false) {

    } else {
        mysqli_stmt_bind_param($insert_stmt, "s", $serial_device);

        if (mysqli_stmt_execute($insert_stmt)) {
            header("location: inventaire.php?equipment_type=machines&success=Machine supprimée avec succès (en attente de confirmation par l'administrateur web)");
        }
        mysqli_stmt_close($insert_stmt);
    }
}

mysqli_close($conn);

?>