<?php
// Start with database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'db_disfo');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update each entry in the mobil_dinas table
    if (isset($_POST['status']) && is_array($_POST['status'])) {
        foreach ($_POST['status'] as $nopol => $new_status) {
            // Escape input data
            $tglpemakaian = isset($_POST['tglpemakaian'][$nopol]) ? $conn->real_escape_string($_POST['tglpemakaian'][$nopol]) : '';
            $lamapemakaian = isset($_POST['lamapemakaian'][$nopol]) ? $conn->real_escape_string($_POST['lamapemakaian'][$nopol]) : '';
            $driver = isset($_POST['driver'][$nopol]) ? $conn->real_escape_string($_POST['driver'][$nopol]) : '';
            $pengguna = isset($_POST['pengguna'][$nopol]) ? $conn->real_escape_string($_POST['pengguna'][$nopol]) : '';
            $new_status = $conn->real_escape_string($new_status);

            // Retrieve the current status and jenismobil
            $sql_current_status = "SELECT status, jenismobil FROM mobil_dinas WHERE nopol='$nopol'";
            $result = $conn->query($sql_current_status);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $current_status = $row['status'];
                $jenismobil = $row['jenismobil']; // Get the jenismobil value

                // Update the mobil_dinas table
                $sql_update = "UPDATE mobil_dinas SET tglpemakaian='$tglpemakaian', lamapemakaian='$lamapemakaian', driver='$driver', pengguna='$pengguna', status='$new_status' WHERE nopol='$nopol'";

                if (!$conn->query($sql_update)) {
                    echo "Error updating mobil_dinas: " . $conn->error;
                }

                // Insert data into history_mobil_dinas only if status changed to "Terpakai"
                if ($new_status === 'Terpakai' && $current_status !== 'Terpakai') {
                    $sql_history = "INSERT INTO history_mobil_dinas (jenismobil, nopol, tglpemakaian, lamapemakaian, driver, status, pengguna)
                                    VALUES ('$jenismobil', '$nopol', '$tglpemakaian', '$lamapemakaian', '$driver', '$new_status', '$pengguna')";

                    if (!$conn->query($sql_history)) {
                        echo "Error inserting into history_mobil_dinas: " . $conn->error;
                    }
                }
            } else {
                echo "Error retrieving current status for $nopol";
            }
        }
    }

    // Update each entry in the ruang_rapat table
    if (isset($_POST['status_rapat']) && is_array($_POST['status_rapat'])) {
        foreach ($_POST['status_rapat'] as $key => $status) {
            // Split the key to get aula, tanggal, and jam
            $parts = explode('_', $key);
            if (count($parts) === 3) {
                list($aula, $tanggal, $jam) = $parts;
                $aula = $conn->real_escape_string($aula);
                $tanggal = $conn->real_escape_string($tanggal);
                $jam = $conn->real_escape_string($jam);
                $pengguna = isset($_POST['pengguna_rapat'][$key]) ? $conn->real_escape_string($_POST['pengguna_rapat'][$key]) : '';
                $keterangan = isset($_POST['keterangan'][$key]) ? $conn->real_escape_string($_POST['keterangan'][$key]) : '';

                $sql = "UPDATE ruang_rapat SET status='$status', pengguna='$pengguna', keterangan='$keterangan' WHERE aula='$aula' AND tanggal='$tanggal' AND jam='$jam'";

                if (!$conn->query($sql)) {
                    echo "Error updating ruang_rapat: " . $conn->error;
                }
            } else {
                echo "Invalid key format: $key";
            }
        }
    }

    // Ensure no output before redirect
    ob_clean(); 
    header("Location: index.php"); // Redirect back to the main page
    exit();
}

$conn->close();
?>
