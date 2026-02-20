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
    if ($mot_de_passe != $_POST['passwordCheck']) {
        header('Location: adminweb.php?error=Les mots de passe ne correspondent pas !');
        exit();
    }

    $check_sql = "SELECT login FROM Users WHERE login = ?";
    $check_stmt = $conn->prepare($check_sql);

    if ($check_stmt === false) {

        $message_echec = "Erreur de préparation de la vérification : " . $conn->error;

        mysqli_close($conn);
        header("Location: adminweb.php?error=" . urlencode($message_echec));
        exit;
    }

    $check_stmt->bind_param("s", $identifiant);
    $check_stmt->execute();
    $check_stmt->store_result(); 

    if ($check_stmt->num_rows > 0) {
        $message_echec = "Erreur : Cet identifiant existe déjà. Veuillez en choisir un autre.";
        $check_stmt->close();
        mysqli_close($conn);
        header("Location: adminweb.php?error=" . urlencode($message_echec));
        exit;
    }
    $check_stmt->close();

    shell_exec("source /home/sae2025/venv/bin/activate");


    $cmd = "/var/www/venv/bin/python /var/www/rpi11/Ressources/py/crypter.py ". escapeshellarg($mot_de_passe). " 2>&1";
    $output=null;
    exec($cmd, $output );




    $mot_de_passe_hash = $output[0];
    $nonce = $output[1];
    $role = 'tech';

    $sql = "INSERT INTO Users (login, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $tech_add = false;
    if ($stmt === false) {
        $message_echec =  "Erreur de préparation de la requête d'insertion : " . $conn->error;
        header("Location: adminweb.php?error=" . urlencode($message_echec));
    } else {
        $stmt->bind_param("sss", $identifiant, $mot_de_passe_hash, $role);

        if (!$stmt->execute()) {
            $message_echec =  "Erreur lors de l'insertion (tech) : " . $stmt->error;
            header("Location: adminweb.php?error=" . urlencode($message_echec));
        }
        $stmt->close();
    }

    $sql = "UPDATE Nonces SET nonce=? where login=? ";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $message_echec =  "Erreur de préparation de la requête d'insertion : " . $conn->error;
        header("Location: adminweb.php?error=" . urlencode($message_echec));
    } else {
        $stmt->bind_param("ss", $nonce, $identifiant);

        if ($stmt->execute()) {
            $message= "Technicien ajouté avec succès.";
            header("Location: adminweb.php?success=" . urlencode($message));
        } else {
            $message_echec =  "Erreur lors de l'insertion (nonce) : " . $stmt->error;
            header("Location: adminweb.php?error=" . urlencode($message_echec));
        }
        $stmt->close();
    }
}
mysqli_close($conn);
?>
