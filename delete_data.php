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
    $aula = $conn->real_escape_string($_POST['aula']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $jam = $conn->real_escape_string($_POST['jam']);

    // Delete entry from the database
    $sql = "DELETE FROM ruang_rapat WHERE aula='$aula' AND tanggal='$tanggal' AND jam='$jam'";

    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }

    header("Location: index.php"); // Redirect back to the main page
    exit();
}

$conn->close();
?>
