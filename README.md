# intra.stettbeck.de

Das System war in dieser Form auf NordNetzwerk (FiveM Server) einige Monate im Einsatz. Es wird aktuell vom NordNetzwerk Team weiterentwickelt - für diese Version wurde die Entwicklung eingestellt.

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


## Bilder

#### eDIVI
![8A9M](https://github.com/itshypax/intranet-sb/assets/33388336/3cb082b7-598c-4116-877e-9ff3170d5d59)
![35KR](https://github.com/itshypax/intranet-sb/assets/33388336/bf344046-699b-4c54-aa99-d38c9ae34395)
![oFFh](https://github.com/itshypax/intranet-sb/assets/33388336/6f5a113d-7764-4b6e-942e-be17ff39cc13)

#### Statistik im Dashboard
![5M21](https://github.com/itshypax/intranet-sb/assets/33388336/ff4c043c-0d72-4087-a35b-3336ef0a137e)
![YcHl](https://github.com/itshypax/intranet-sb/assets/33388336/9a681eb0-c939-4ae1-a601-420b0d432eed)


#### Mitarbeiter / IC Profile
![eG2j](https://github.com/itshypax/intranet-sb/assets/33388336/43a06269-bc90-476d-9407-dd3b7183625e)
![xtQI](https://github.com/itshypax/intranet-sb/assets/33388336/3f6f7ce4-194e-42bf-936b-27bfb7255996)
![Xbjr](https://github.com/itshypax/intranet-sb/assets/33388336/6f5542e6-fb05-4a2c-8be8-4fc40aeda7f6)
![52PU](https://github.com/itshypax/intranet-sb/assets/33388336/6429d2ae-b5c1-4028-8692-325c41f645d3)
![vIc9](https://github.com/itshypax/intranet-sb/assets/33388336/9e4dcb8d-8cd3-4ff4-81db-9cec1ff387dd)
![sZW6](https://github.com/itshypax/intranet-sb/assets/33388336/d3993dbb-b3eb-418b-a70d-809c9c564139)


#### Dashboard (eingesetzt im IC Tablet)
![2oqx](https://github.com/itshypax/intranet-sb/assets/33388336/ced6d802-a6fb-4070-8e19-f15f14100f54)

#### Automatisch generiertes Dokument
![HIME](https://github.com/itshypax/intranet-sb/assets/33388336/73f4514b-9791-4343-b960-656b88f49940)

#### Login Seite
![NPPB](https://github.com/itshypax/intranet-sb/assets/33388336/1049a05a-7776-4ed7-890a-ed35de40f182)
