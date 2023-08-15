<!DOCTYPE html>
<html>
<head>
    <title>Krankenhaus Kapazitätsübersicht</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #34383b;
            margin: 0;
            padding: 0;

        }

        .header {
            text-align: center;
            padding: 10px;
            background-color: #3bd4ae;
            color: white;
            margin: 0;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: white;
            width: 920px;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            background-color: rgba(82, 87, 92, 0.9); /* Transparent background */
        }

        .abgemeldet-card {
            background-color: rgba(82, 87, 92, 0.9); /* Transparent background */
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h2 {
            margin-top: 0;
            color: #3bd4ae;
            font-size: 38px;
            display: flex;
            align-items: center;
        }

        .card h2 .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            color: white;
            width: 150px;
            text-align: center;
            margin-left: 10px;
        }

        .status-angemeldet {
            background-color: #27ae60;
        }

        .status-abgemeldet {
            background-color: #e74c3c;
        }

        .card ul {
            list-style: none;
            padding: 0;
        }

        .card li {
            margin: 10px 0;
            color: #ffffff;
			font-size: 20px;
        }
		
		

        .fachabteilungen {
            margin-top: 15px;
            color: #ffffff;
            font-size: 14px;
        }

        .fachabteilung {
            display: flex;
            justify-content: space-between;
			color: #ffffff;
            align-items: center;
            padding: 5px 0;
        }

        .fachabteilung-status {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            width: 90px;
            text-align: center;
        }

        .fachabteilung-angemeldet {
            background-color: #27ae60;
        }

        .fachabteilung-abgemeldet {
            background-color: #e74c3c;
        }

        .fachabteilung-name {
            flex: 1;
			font-size: 20px;
			background-color: transparent; /* Transparent background */
        }

    </style>
</head>

<body>

<div class="header">
    <h1>Krankenhaus Kapazitätsübersicht</h1>
</div>

<div class="card-container">
    <?php
    $host = "localhost";
    $username = "klinik";
    $password = "exU738t0?";
    $database = "hypaxna1_kliniken";

    $mysqli = new mysqli($host, $username, $password, $database);

    if ($mysqli->connect_error) {
        die('Verbindung fehlgeschlagen: ' . $mysqli->connect_error);
    }

    $query = "SELECT * FROM krankenhaeuser";
    $result = $mysqli->query($query);

    if (!$result) {
        die('Abfrage fehlgeschlagen: ' . $mysqli->error);
    }

    $krankenhaeuser = [];
    while ($row = $result->fetch_assoc()) {
        $krankenhaeuser[] = $row;
    }

    foreach ($krankenhaeuser as $krankenhaus) {
        $fachabteilungenQuery = "SELECT * FROM fachabteilungen WHERE krankenhaus_id = " . $krankenhaus['id'];
        $fachabteilungenResult = $mysqli->query($fachabteilungenQuery);

        $fachabteilungen = [];
        while ($fachabteilungRow = $fachabteilungenResult->fetch_assoc()) {
            $fachabteilungen[] = $fachabteilungRow;
        }

        // Add a CSS class based on the status of the clinic
        $clinicStatusClass = $krankenhaus['angemeldet'] ? 'status-angemeldet' : 'status-abgemeldet';
        $cardClass = $krankenhaus['angemeldet'] ? '' : 'abgemeldet-card';

        echo "<div class='card $cardClass'>";
        echo "<h2 class='d-flex align-items-center justify-content-between'>" . $krankenhaus['name'] . "<span class='status $clinicStatusClass'>" . ($krankenhaus['angemeldet'] ? 'Angemeldet' : 'Abgemeldet') . "</span></h2>";
        echo "<ul>";
        echo "<center><li><strong>Verfügbare Betten:</strong> " . $krankenhaus['verfuegbare_betten'] . "     <strong>Belegte Betten:</strong> " . $krankenhaus['belegte_betten'] .     "</li></center>";

        echo "<li class='fachabteilungen'><strong>Fachabteilungen:</strong>";

        // Display Fachabteilungen in tables with 3 rows, filling left to right
        $fachabteilungCount = count($fachabteilungen);
        $rows = ceil($fachabteilungCount / 3); // Calculate the number of rows

        for ($row = 0; $row < $rows; $row++) {
            echo "<div class='row'>";
            for ($col = 0; $col < 3; $col++) {
                $index = $row * 3 + $col;
                if ($index < $fachabteilungCount) {
                    echo "<div class='col'>";
                    echo "<div class='table' style='background-color: transparent; border: none; '>"; // Transparent table
                    echo "<div class='table-body'>";
                    echo "<div class='table-row'>";
                    echo "<div class='table-cell fachabteilung-name' style='background-color: transparent; color: white;'>" . $fachabteilungen[$index]['name'] . "</div>";
                    echo "<div class='table-cell' style='background-color: transparent;'><span class='fachabteilung-status " . ($fachabteilungen[$index]['angemeldet'] ? 'fachabteilung-angemeldet' : 'fachabteilung-abgemeldet') . "'>" . ($fachabteilungen[$index]['angemeldet'] ? 'Angemeldet' : 'Abgemeldet') . "</span></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                } else {
                    echo "<div class='col'></div>"; // Empty column for alignment
                }
            }
            echo "</div>";
        }

        echo "</li>";
        echo "</ul>";
        echo "</div>";
    }
    ?>
</div>
<script>
// Refresh the page every 15 seconds
setInterval(function() {
    location.reload();
}, 15000); // 15 seconds in milliseconds
</script>
</body>
</html>
