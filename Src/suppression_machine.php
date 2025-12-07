<?php
$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serial_device = $_POST['serial_device'];

    $check_sql = "SELECT serial FROM Active_Devices WHERE serial = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if ($check_stmt === false) {
        mysqli_close($conn);
        header("location: inventaire.php");
        exit;
    }

    mysqli_stmt_bind_param($check_stmt, "s", $serial_device);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        mysqli_stmt_close($check_stmt);
        mysqli_close($conn);
        header("location: inventaire.php");
        exit;
    }

    mysqli_stmt_close($check_stmt);

    $insert_sql = "INSERT INTO Waiting_Devices (serial, name) SELECT serial, name FROM Active_Devices WHERE serial = ?";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);

    if ($insert_stmt === false) {

    } else {
        mysqli_stmt_bind_param($insert_stmt, "s", $serial_device);

        if (mysqli_stmt_execute($insert_stmt)) {
            echo "Machine supprimée avec succès (en attente de confirmation par l'administrateur web)";
        }
        mysqli_stmt_close($insert_stmt);
    }
}

mysqli_close($conn);

header("location: inventaire.php");
?>