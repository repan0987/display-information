<?php
// Start with database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection setup
$conn = new mysqli('localhost', 'root', '', 'db_disfo');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="300"> <!-- Refresh setiap 300 detik (5 menit) -->
    <title>Data List</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        let slideshowInterval;
        let inactivityTimer;
        const SLIDESHOW_INTERVAL = 8000; // Interval for slideshow
        const INACTIVITY_TIMEOUT = 10000; // Time to wait before restarting slideshow after inactivity

        function updatePengguna(selectElement, aula, tanggal, jam, table) {
    var rowElement = selectElement.closest('tr');

    if (table === 'mobil_dinas') {
        var tglpemakaianElement = rowElement.querySelector('input[name^="tglpemakaian"]');
        var lamapemakaianElement = rowElement.querySelector('input[name^="lamapemakaian"]');
        var driverElement = rowElement.querySelector('select[name^="driver"]');
        var penggunaElement = rowElement.querySelector('input[name^="pengguna"]');

        if (selectElement.value === "Tersedia") {
            // Set Tgl Pemakaian and Lama Pemakaian and Driver and Pengguna to "-"
            if (tglpemakaianElement) {
                tglpemakaianElement.value = "";
                rowElement.style.backgroundColor = ""; // Reset background
            }
            if (lamapemakaianElement) {
                lamapemakaianElement.value = "-";
                rowElement.style.backgroundColor = ""; // Reset background
            }
            if (driverElement) {
                driverElement.value = ""; // Set value to " " or top value
                rowElement.style.backgroundColor = ""; // Reset background
            }
            if (penggunaElement) {
                penggunaElement.value = "-";
            }
        } else if (selectElement.value === "Terpakai") {
            if (tglpemakaianElement) {
                tglpemakaianElement.focus();
                if (tglpemakaianElement.value === "") {
                    tglpemakaianElement.value = ""; // Clear "-" if the user focuses on the field
                }
            }
            if (lamapemakaianElement) {
                lamapemakaianElement.focus();
                if (lamapemakaianElement.value === "-") {
                    lamapemakaianElement.value = ""; // Clear "-" if the user focuses on the field
                }
            }
            if (driverElement) {
                driverElement.focus();
                if (driverElement.value === " ") {
                    driverElement.value = ""; // Clear "-" if the user focuses on the field
                }
            }
            if (penggunaElement) {
                penggunaElement.focus();
                if (penggunaElement.value === "-") {
                    penggunaElement.value = ""; // Clear "-" if the user focuses on the field
                }
            }
            rowElement.style.backgroundColor = "#FFFFFF"; // Set background color for "Terpakai"
        }
    } else {
        if (selectElement.value === "Tersedia") {
            // Send request to delete row from database
            var form = document.getElementById('delete-form');
            form.aula.value = aula;
            form.tanggal.value = tanggal;
            form.jam.value = jam;
            form.submit();
        } else if (selectElement.value === "Terpakai") {
            var penggunaElement = rowElement.querySelector('input[name^="pengguna_rapat"]');
            penggunaElement.focus();
            if (penggunaElement.value === "-") {
                penggunaElement.value = ""; // Clear "-" if the user focuses on the field
            }
            rowElement.style.backgroundColor = "#FFFFFF"; // Set background color for "Terpakai"
        }
    }
}

function startSlideshow(initialIndex = 0) {
    const tables = document.querySelectorAll('.table-container');
    let currentIndex = initialIndex;

    // Ensure the initial table is visible
    tables.forEach((table, index) => {
        if (index === currentIndex) {
            table.style.opacity = 1;
            table.style.zIndex = 1;
        } else {
            table.style.opacity = 0;
            table.style.zIndex = 0;
        }
    });

    slideshowInterval = setInterval(() => {
        tables.forEach((table, index) => {
            if (index === currentIndex) {
                table.style.opacity = 1;
                table.style.zIndex = 1;
            } else {
                table.style.opacity = 0;
                table.style.zIndex = 0;
            }
        });
        currentIndex = (currentIndex + 1) % tables.length;
    }, SLIDESHOW_INTERVAL);
}

function stopSlideshow() {
    clearInterval(slideshowInterval);
}

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(() => {
        startSlideshow();
    }, INACTIVITY_TIMEOUT);
}

function toggleTables() {
    // Start slideshow with the initial index of 0 for "List Mobil Dinas"
    startSlideshow(0);

    const tables = document.querySelectorAll('.table-container');

    tables.forEach(table => {
        table.addEventListener('click', () => {
            stopSlideshow();
            resetInactivityTimer();
        });
    });

    document.body.addEventListener('mousemove', resetInactivityTimer);
    document.body.addEventListener('keydown', resetInactivityTimer);
}

function toggleForm() {
    var form = document.getElementById('add-data-form');
    if (form.style.display === "none" || form.style.display === "") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

window.onload = toggleTables;

    </script>
    <style>
        body {
            background-image: url(gambar/g5.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            width: 95%;
            position: relative;
            height: 500px; /* Tentukan tinggi container jika perlu */
        }

        .table-container {
            position: absolute;
            width: 90%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: opacity 1s ease-in-out;
            background-color: #4682b4;
            padding: 15px;
        }

        table { 
            width: 100%;
            margin: auto;
            border-collapse: collapse;
            background-color: #4682b4;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #B2FFFF;
        }

        .button-container {
            position: fixed;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px; /* Space between buttons */
        }

        .add-data-form {
            display: none; /* Initially hidden */
            background-color: #4682b4;
            padding: 15px;
            margin: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3); /* Shadow to make it stand out */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000; /* Ensure it is above other content */
            width: 80%; /* Adjust width as needed */
            max-width: 600px; /* Prevent it from being too wide */
        }

        .add-data-form table {
            width: 100%;
        }

        .add-data-form td {
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Existing Table Containers -->
        <form action="update_data.php" method="post">
            <div class="table-container">
                <h2 style="text-align: center;">List Mobil Dinas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Jenis Mobil</th>
                            <th>Plat Nomor</th>
                            <th>Tanggal Pemakaian</th>
                            <th>Lama Pemakaian</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th>Pengguna</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query the database
                        $sql = "SELECT jenismobil, nopol, tglpemakaian, lamapemakaian, driver, status, pengguna FROM mobil_dinas";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                $penggunaId = "pengguna_" . htmlspecialchars($row["nopol"]);
                                $rowBgColor = $row["status"] == "Terpakai" ? "#B2FFFF" : "";
                                echo "<tr style='background-color: $rowBgColor;'>
                                    <td>" . htmlspecialchars($row["jenismobil"]) . "</td>
                                    <td>" . htmlspecialchars($row["nopol"]) . "</td>
                                    <td><input type='date' name='tglpemakaian[" . htmlspecialchars($row["nopol"]) . "]' value='" . htmlspecialchars($row["tglpemakaian"]) . "' /></td>
                                    <td><input type='text' name='lamapemakaian[" . htmlspecialchars($row["nopol"]) . "]' value='" . htmlspecialchars($row["lamapemakaian"]) . "' /></td>
                                    <td>
                                        <select name='driver[" . htmlspecialchars($row["nopol"]) . "]' onchange=\"updatePengguna(this, '', '', '', 'mobil_dinas')\">
                                            <option value=''" .        ($row["driver"] == ""        ? " selected" : "") . "> </option>
                                            <option value='Driver 1'" . ($row["driver"] == "Driver 1" ? " selected" : "") . ">Driver 1</option>
                                            <option value='Driver 2'" . ($row["driver"] == "Driver 2" ? " selected" : "") . ">Driver 2</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name='status[" . htmlspecialchars($row["nopol"]) . "]' onchange=\"updatePengguna(this, '', '', '', 'mobil_dinas')\">
                                            <option value='Terpakai'" . ($row["status"] == "Terpakai" ? " selected" : "") . ">Terpakai</option>
                                            <option value='Tersedia'" . ($row["status"] == "Tersedia" ? " selected" : "") . ">Tersedia</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type='text' name='pengguna[" . htmlspecialchars($row["nopol"]) . "]' id='$penggunaId' value='" . htmlspecialchars($row["pengguna"]) . "' />
                                    </td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <h2 style="text-align: center;">List Ruang Rapat</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Aula</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Kegiatan</th>
                            <th>Status</th>
                            <th>Pengguna</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query the database
                        $sql = "SELECT id_key, aula, tanggal, jam, kegiatan, status, pengguna, keterangan FROM ruang_rapat";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while ($row = $result->fetch_assoc()) {
                                $id_key = htmlspecialchars($row["id_key"]);
                                $aula = htmlspecialchars($row["aula"]);
                                $tanggal = htmlspecialchars($row["tanggal"]);
                                $jam = htmlspecialchars($row["jam"]);
                                $penggunaId = htmlspecialchars($row["pengguna"]);
                                $keterangan = htmlspecialchars($row["keterangan"]);
                                echo "<tr>
                                    <td>" . $aula . "</td>
                                    <td>" . $tanggal . "</td>
                                    <td>" . $jam . "</td>
                                    <td>" . htmlspecialchars($row["kegiatan"]) . "</td>
                                    <td>
                                        <select name='status_rapat[" . $aula . "]' onchange=\"updatePengguna(this, '$aula', '$tanggal', '$jam', 'ruang_rapat')\">
                                            <option value='Terpakai'" . ($row["status"] == "Menunggu" ? " selected" : "") . ">Menunggu</option>
                                            <option value='Tersedia'" . ($row["status"] == "Selesai" ? " selected" : "") . ">Selesai</option>
                                        </select>
                                    </td>
                                    <td>" . htmlspecialchars($row["pengguna"]) . "</td>
                                    <td>" . htmlspecialchars($row["keterangan"]) . "</td>
                                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No data available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="button-container">
                <button type="submit">Update Data</button>
                <button type="button" onclick="toggleForm()">Add Data</button>
            </div>
        </form>

        <!-- Add Data Form -->
        <div class="add-data-form" id="add-data-form">
            <h2 style="text-align: center;">Add Data to List Ruang Rapat</h2>
            <form action="add_data.php" method="post">
                <table>
                <tr>
                        <td style="font-weight: bold;">ID Key</td>
                        <td><input type="text" name="id_key" required /></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Aula</td>
                        <td>
                            <select name="aula" required>
                            <option value=" "> </option>
                                <option value="Aula Utama">Aula Utama</option>
                                <option value="Aula Pustaka">Aula Pustaka</option>
                                <option value="Aula Well Being">Aula Well Being</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Tanggal</td>
                        <td><input type="date" name="tanggal" required /></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Jam</td>
                        <td><input type="time" name="jam" required /></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Kegiatan</td>
                        <td><input type="text" name="kegiatan" required /></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Status</td>
                        <td>
                            <select name="status" required>
                                <option value=" "> </option>
                                <option value="Menunggu">Menunggu</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Pengguna</td>
                        <td>
                        <select name="pengguna" required>
                                <option value=" "> </option>
                                <option value="Pengguna 1">Pengguna 1</option>
                                <option value="Pengguna 2">Pengguna 2</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Keterangan</td>
                        <td><input type="text" name="keterangan" required /></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit">Add Data</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <!-- Hidden Form for Deleting Data -->
        <form id="delete-form" action="delete_data.php" method="post" style="display: none;">
            <input type="hidden" name="aula" />
            <input type="hidden" name="tanggal" />
            <input type="hidden" name="jam" />
        </form>
    </div>

    <?php
    // Ensure closing of connection
    $conn->close();
    ?>
</body>
</html>
