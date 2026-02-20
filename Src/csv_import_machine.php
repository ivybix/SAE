<?php

$required_fields_device = [
    'name', 'serial', 'manufacturer', 'model', 'type', 'cpu', 'ram_mb', 'disk_gb', 'os',
    'domain', 'location', 'building', 'room', 'macaddr', 'purchase_date', 'warranty_end'
];

$conn = mysqli_connect("localhost", "inkware", "!sae2025!", "INVENTORY");

$target_table = "Devices";
$fields_count = count($required_fields_device);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $file_info = $_FILES["csv_file_upload"];
    $file_tmp_path = $file_info["tmp_name"];

    $fields_list = implode(', ', $required_fields_device);
    $placeholders = implode(', ', array_fill(0, $fields_count, '?'));

    $sql = "INSERT IGNORE INTO {$target_table} ({$fields_list}) VALUES ({$placeholders})";

    $stmt = mysqli_prepare($conn, $sql);

    $param_types = str_repeat('s', $fields_count);
    $success_count = 0;
    $row_count = 0;

    if (($handle = fopen($file_tmp_path, "r")) !== FALSE) {

        fgetcsv($handle, 0, ",", "\\");

        while (($data = fgetcsv($handle, 0, ",", "\\")) !== FALSE) {
            $row_count++;

            if (count($data) !== $fields_count) continue;

            $bind_names[] = $param_types;
            for ($i = 0; $i < $fields_count; $i++) {
                $bind_name = 'p'.$i;
                $$bind_name = trim($data[$i]);
                $bind_names[] = &$$bind_name;
            }

            call_user_func_array([$stmt, 'bind_param'], $bind_names);

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            }
            $bind_names = [];
        }

        fclose($handle);
        mysqli_stmt_close($stmt);

        echo "<h2>Résultat de l'importation CSV</h2>";
        echo "<p>Méthode fopen : {$success_count} ligne(s) insérée(s) dans {$target_table} (sur {$row_count} lignes de données traitées).</p>";

    }

    mysqli_close($conn);

} else {
    echo "Accès invalide.";
}
?>