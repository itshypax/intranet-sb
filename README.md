# intra.stettbeck.de

MYSQL-Datei wurde __nicht__ gesynct.
Diese unter `/assets/php/` mit dem Namen `mysql-con.php` erstellen.

## Beispiel-Inhalt für die Datei
```php
<?php

// Verbindungsdaten
$db_host = ""; // IP/Host
$db_user = ""; // Benutzername für MYSQL
$db_pass = ""; // Passwort für MYSQL-Benutzer
$db_name = ""; // Name der Datenbank
$dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Verbindung aufbauen
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
$mysql = new PDO($dsn, $db_user, $db_pass, $options);

// Verbindung prüfen
if (!$conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
```

Hiernach müsste eine passende DB erstellt werden. Die zugehörige Struktur ist als Datei beigefügt. Der erste (initiale) Benutzer muss in der DB angelegt werden. Das Passwort wird __nicht__ als Klartext gespeichert.

### Disclaimer: Es handelte sich hierbei um ein __Hobbyprojekt__! Es ist dementsprechend eine Lernerfahrung mit stetigen Anpassungen gewesen. Das System ist sicherlich keinesfalls fehlerfrei oder sicher vor Angriffen.
