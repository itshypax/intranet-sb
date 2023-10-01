<?php
// Überprüfen, ob der API-Schlüssel gesetzt ist
$storedApiKey = "116117";  // Ersetzen Sie diesen durch Ihren tatsächlichen API-Schlüssel

// Überprüfen, ob der API-Schlüssel als Query-Parameter übergeben wurde
if (!isset($_GET['api_key']) || $_GET['api_key'] !== $storedApiKey) {
    http_response_code(401);  // Nicht autorisiert
    exit;  // Beenden Sie das Skript
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Krankenhaus Kapazitätsübersicht</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: #2c3e50;
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
            width: 600px;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h2 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 24px;
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
			width: 550px;
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
            color: #555;
        }

        .fachabteilungen {
            margin-top: 15px;
            color: #555;
            font-size: 14px;
        }

        .fachabteilung {
            display: flex;
            justify-content: space-between;
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

    if (isset($_POST['updateKrankenhaus'])) {
        $id = $_POST['id'];
        $newStatus = $_POST['newStatus'];
        $newVerfuegbareBetten = $_POST['newVerfuegbareBetten'];
        $newBelegteBetten = $_POST['newBelegteBetten'];

        $updateQuery = "UPDATE krankenhaeuser SET angemeldet = $newStatus, verfuegbare_betten = $newVerfuegbareBetten, belegte_betten = $newBelegteBetten WHERE id = $id";
        $mysqli->query($updateQuery);
    }

    if (isset($_POST['updateFachabteilungStatus'])) {
        $id = $_POST['id'];
        $newStatus = $_POST['newStatus'];
        $updateQuery = "UPDATE fachabteilungen SET angemeldet = $newStatus WHERE id = $id";
        $mysqli->query($updateQuery);
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

        echo "<div class='card'>";
        echo "<h2 class='d-flex align-items-center justify-content-between'>" . $krankenhaus['name'] . "<span class='status " . ($krankenhaus['angemeldet'] ? 'status-angemeldet' : 'status-abgemeldet') . "'>" . ($krankenhaus['angemeldet'] ? 'Angemeldet' : 'Abgemeldet') . "</span></h2>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='id' value='" . $krankenhaus['id'] . "'>";
        echo "<label for='newStatus'>Status:</label>";
        echo "<select name='newStatus' id='newStatus'>";
        echo "<option value='1'" . ($krankenhaus['angemeldet'] ? ' selected' : '') . ">Angemeldet</option>";
        echo "<option value='0'" . (!$krankenhaus['angemeldet'] ? ' selected' : '') . ">Abgemeldet</option>";
        echo "</select>";
		 echo "<ul>";
        echo "<label for='newVerfuegbareBetten'>Verfügbare Betten:</label>";
		 echo "<ul>";
        echo "<input type='number' name='newVerfuegbareBetten' value='" . $krankenhaus['verfuegbare_betten'] . "' min='0'>";
		 echo "<ul>";
        echo "<label for='newBelegteBetten'>Belegte Betten:</label>";
		 echo "<ul>";
        echo "<input type='number' name='newBelegteBetten' value='" . $krankenhaus['belegte_betten'] . "' min='0'>";
		 echo "<ul>";
        echo "<button type='submit' name='updateKrankenhaus' class='btn btn-primary btn-sm'>Aktualisieren</button>";
        echo "</form>";
        echo "<ul>";
        echo "<li class='fachabteilungen'><strong>Fachabteilungen:</strong>";
        echo "<ul class='fachabteilungen-list'>";
        foreach ($fachabteilungen as $fachabteilung) {
            echo "<li class='fachabteilung'>";
            echo "<span class='fachabteilung-name'>" . $fachabteilung['name'] . "</span>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='id' value='" . $fachabteilung['id'] . "'>";
            echo "<label for='newStatus'>Status:</label>";
            echo "<select name='newStatus' id='newStatus'>";
            echo "<option value='1'" . ($fachabteilung['angemeldet'] ? ' selected' : '') . ">Angemeldet</option>";
            echo "<option value='0'" . (!$fachabteilung['angemeldet'] ? ' selected' : '') . ">Abgemeldet</option>";
            echo "</select>";
            echo "<button type='submit' name='updateFachabteilungStatus' class='btn btn-primary btn-sm'>Status ändern</button>";
            echo "</form>";
            echo "</li>";
        }
        echo "</ul>";
        echo "</li>";
        echo "</ul>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>