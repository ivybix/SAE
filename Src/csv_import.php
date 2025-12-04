<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $target_table = isset($_POST['target_table']) ? $_POST['target_table'] : null;
    $is_device_import = ($target_table === 'Devices');
    $is_monitor_import = ($target_table === 'Monitors');


    $required_fields = [
        'name', 'serial', 'model', 'type', 'cpu', 'ram_mb', 'disk_gb', 'os',
        'domain', 'location', 'building', 'room', 'macaddr', 'purchase_date', 'warranty_end'
    ];

    if (!$is_device_import) {

        die("<p style='color: red;'>Erreur : Ce script gère uniquement l'importation des Devices. Type d'importation reçu: " . htmlspecialchars($target_table) . "</p>");
    }

    if (isset($_FILES['csv_file_upload']) && $_FILES['csv_file_upload']['error'] === UPLOAD_ERR_OK) {

        $file_tmp_path = $_FILES['csv_file_upload']['tmp_name'];
        $errors = [];
        $line_number = 1;
        $column_index_map = [];

        if (($handle = fopen($file_tmp_path, "r")) !== FALSE) {

            if (($header_row = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $normalized_header = array_map('trim', array_map('strtolower', $header_row));

                foreach ($normalized_header as $index => $col_name) {
                    if (in_array($col_name, $required_fields)) {
                        $column_index_map[$col_name] = $index;
                    }
                }

                $missing_fields = array_diff($required_fields, array_keys($column_index_map));

                if (!empty($missing_fields)) {
                    $missing_list = implode(', ', $missing_fields);
                    die("<h2 style='color: red;'>Erreur : Schéma CSV Invalide.</h2><p>Les colonnes requises suivantes sont manquantes : <strong>{$missing_list}</strong>.</p>");
                }

                echo "<h3>DEBUG : Ordre des colonnes détecté :</h3><pre>";
                print_r($column_index_map);
                echo "</pre>";

            } else {
                die("<p style='color: red;'>Erreur : Le fichier CSV est vide ou ne contient pas d'en-tête.</p>");
            }


            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                if (count($data) !== count($header_row)) {
                    $errors[] = "Ligne {$line_number} : Le nombre de colonnes de la ligne de données (" . count($data) . ") ne correspond pas au nombre de colonnes de l'en-tête (" . count($header_row) . ").";
                    $line_number++;
                    continue;
                }

                $device_data = [];
                foreach ($required_fields as $field) {
                    // Utiliser la map pour trouver l'index correct pour chaque champ
                    $index = $column_index_map[$field];
                    $device_data[$field] = trim($data[$index]);
                }

                $name = $device_data['name'];
                $serial = $device_data['serial'];
                $purchase_date = $device_data['purchase_date'];
                // faire les autre copier sur nicola

                if (empty($serial)) {
                    $errors[] = "Ligne {$line_number} : Le champ 'serial' ne peut pas être vide.";
                }


                if (!DateTime::createFromFormat('Y-m-d', $purchase_date)) {
                    $errors[] = "Ligne {$line_number} : Le champ 'purchase_date' n'est pas au format YYYY-MM-DD valide.";
                }

                // etc. pour tous les 15 champs...


                // C. TRAITEMENT / INSERTION
                if (empty($errors)) {
                    // Si aucune erreur, le traitement peut continuer (Insertion SQL, etc.)
                    echo "<p style='color: blue;'>Ligne {$line_number} : **VALIDE**. Prête pour l'insertion (Serial: {$serial}).</p>";
                    // Exemple d'insertion (ATTENTION: Ceci est une simulation, vous devez utiliser des requêtes préparées!)
                    // $sql = "INSERT INTO Devices (name, serial, model, ...) VALUES (?, ?, ?)";

                } else {
                    // Afficher les erreurs de la ligne
                    echo "<p style='color: orange;'>Ligne {$line_number} : **ERREUR DE VALIDATION** :</p><ul>";
                    foreach ($errors as $error) {
                        echo "<li>{$error}</li>";
                    }
                    echo "</ul>";

                    // IMPORTANT : Vider le tableau d'erreurs pour la prochaine ligne
                    $errors = [];
                }

                $line_number++;
            }

            fclose($handle);

        } else {
            die("<p style='color: red;'>Erreur : Impossible d'ouvrir le fichier temporaire.</p>");
        }

    } else {
        die("<p style='color: red;'>Erreur d'upload du fichier.</p>");
    }
}
?>