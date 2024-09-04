<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'db_disfo');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_key = $conn->real_escape_string($_POST['id_key']);
    $aula = $conn->real_escape_string($_POST['aula']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $jam = $conn->real_escape_string($_POST['jam']);
    $kegiatan = $conn->real_escape_string($_POST['kegiatan']);
    $status = $conn->real_escape_string($_POST['status']);
    $pengguna = $conn->real_escape_string($_POST['pengguna']);
    $keterangan = $conn->real_escape_string($_POST['keterangan']);

    if ($status === "Selesai") {
        // Insert data into history_ruang_rapat before deleting
        $sql_history = "INSERT INTO history_ruang_rapat (id_key, aula, tanggal, jam, kegiatan, status, pengguna, keterangan, action)
                        SELECT id_key, aula, tanggal, jam, kegiatan, status, pengguna, keterangan, 'Deleted'
                        FROM ruang_rapat WHERE aula='$aula' AND tanggal='$tanggal' AND jam='$jam'";
        if (!$conn->query($sql_history)) {
            echo "Error: " . $sql_history . "<br>" . $conn->error;
            exit();
        }

        // Delete the record from ruang_rapat
        $sql_delete = "DELETE FROM ruang_rapat WHERE aula='$aula' AND tanggal='$tanggal' AND jam='$jam'";
        if ($conn->query($sql_delete) === TRUE) {
            header("Location: index.php"); // Redirect back to the main page
            exit();
        } else {
            echo "Error: " . $sql_delete . "<br>" . $conn->error;
        }
    } else {
        // Insert data into ruang_rapat
        $sql_insert = "INSERT INTO ruang_rapat (id_key, aula, tanggal, jam, kegiatan, status, pengguna, keterangan)
                       VALUES ('$id_key', '$aula', '$tanggal', '$jam', '$kegiatan', '$status', '$pengguna', '$keterangan')";
        if ($conn->query($sql_insert) === TRUE) {
            // Insert data into history_ruang_rapat
            $sql_history = "INSERT INTO history_ruang_rapat (id_key, aula, tanggal, jam, kegiatan, status, pengguna, keterangan)
                            VALUES ('$id_key', '$aula', '$tanggal', '$jam', '$kegiatan', '$status', '$pengguna', '$keterangan')";
            if (!$conn->query($sql_history)) {
                echo "Error: " . $sql_history . "<br>" . $conn->error;
                exit();
            }
            
            header("Location: index.php"); // Redirect back to the main page
            exit();
        } else {
            echo "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
