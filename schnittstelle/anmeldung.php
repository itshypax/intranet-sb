<?php
session_start();

include '../assets/php/mysql-con.php';

if (isset($_POST['new']) && $_POST['new'] == 1) {
    // TRANSPORTDATEN
    $ankunftzeit = $_POST['ankunftzeit'];
    $zielklinik = $_POST['zielklinik'];
    $zielobjtyp = $_POST['zielobjtyp'];
    // NOTFALLKATEGORIE
    $kat_1 = isset($_POST['kat_1']) && $_POST['kat_1'] >= 1 ? $_POST['kat_1'] : 0;
    $kat_2 = isset($_POST['kat_2']) && $_POST['kat_2'] >= 1 ? $_POST['kat_2'] : 0;
    $kat_3 = isset($_POST['kat_3']) && $_POST['kat_3'] >= 1 ? $_POST['kat_3'] : 0;
    // DIAGNOSE 
    $diagnose = $_POST['diagnose'];
    // ABCDE
    $a_1 = isset($_POST['a_1']) && $_POST['a_1'] >= 1 ? $_POST['a_1'] : 0;
    $a_2 = isset($_POST['a_2']) && $_POST['a_2'] >= 1 ? $_POST['a_2'] : 0;
    $b_1 = isset($_POST['b_1']) && $_POST['b_1'] >= 1 ? $_POST['b_1'] : 0;
    $b_2 = isset($_POST['b_2']) && $_POST['b_2'] >= 1 ? $_POST['b_2'] : 0;
    $c_1 = isset($_POST['c_1']) && $_POST['c_1'] >= 1 ? $_POST['c_1'] : 0;
    $c_2 = isset($_POST['c_2']) && $_POST['c_2'] >= 1 ? $_POST['c_2'] : 0;
    $c_3 = isset($_POST['c_3']) && $_POST['c_3'] >= 1 ? $_POST['c_3'] : 0;
    $d_1 = isset($_POST['d_1']) && $_POST['d_1'] >= 1 ? $_POST['d_1'] : 0;
    $d_2 = isset($_POST['d_2']) && $_POST['d_2'] >= 1 ? $_POST['d_2'] : 0;
    $d_3 = isset($_POST['d_3']) && $_POST['d_3'] >= 1 ? $_POST['d_3'] : 0;
    $s_1 = isset($_POST['s_1']) && $_POST['s_1'] >= 1 ? $_POST['s_1'] : 0;
    $s_2 = isset($_POST['s_2']) && $_POST['s_2'] >= 1 ? $_POST['s_2'] : 0;
    // PROTOKOLLDATEN
    $estichwort = $_POST['estichwort'];
    $enr = $_POST['enr'];
    // SQL-Query ausführen
    $query = "INSERT INTO klinik_anmeldungen (ankunftzeit, zielklinik, zielobjtyp, kat_1, kat_2, kat_3, a_1, a_2, b_1, b_2, c_1, c_2, c_3, d_1, d_2, d_3, s_1, s_2, diagnose, estichwort, enr)
    VALUES ('$ankunftzeit', '$zielklinik', '$zielobjtyp', '$kat_1','$kat_2', '$kat_3', '$a_1', '$a_2', '$b_1', '$b_2', '$c_1', '$c_2', '$c_3', '$d_1', '$d_2',  '$d_3', '$s_1', '$s_2', '$diagnose', '$estichwort', '$enr')";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Voranmeldung Patient &rsaquo; intraSB</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/schnittstelle.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/assets/bootstrap-5.3/css/bootstrap.min.css">
    <script src="/assets/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/sweetalert2/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="/assets/sweetalert2/sweetalert2.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/assets/favicon/site.webmanifest">
    <!-- Metas -->
    <meta name="theme-color" content="#d4004b" />
    <meta property="og:site_name" content="NordNetzwerk" />
    <meta property="og:url" content="https://intra.stettbeck.de/dash.html" />
    <meta property="og:title" content="Intranet - Hansestadt Stettbeck" />
    <meta property="og:image" content="https://stettbeck.de/assets/img/STETTBECK_1.png" />
    <meta property="og:description" content="Intranet/Verwaltungsportal der Hansestadt Stettbeck" />
</head>

<body style="background-color: #ebf4ff;">
    <form name="form" method="post" action="">
        <input type="hidden" name="new" value="1" />
        <div class="container-fluid bg-secondary text-light py-1">
            <div class="row">
                <div class="col">
                    <h3 class="fw-bold">Patienten Anmeldung</h3>
                </div>
                <div class="col text-end">
                    <input class="btn btn-success btn-sm h-100" name="submit" type="submit" value="Anmeldung senden" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>Klinik / Transportziel</h6>
                    </div>
                </div>
                <div class="row py-1">
                    <div class="col mx-2">
                        <div class="row py-2">
                            <div class="col-4">Geschätzte Ankunft:</div>
                            <div class="col"><input class="form-control" type="time" name="ankunftzeit" id="ankunftzeit" required></div>
                        </div>
                        <div class="row py-2">
                            <div class="col-4">Klinik:</div>
                            <div class="col">
                                <select class="form-select" name="zielklinik" id="zielklinik" required>
                                    <option value="1" selected>ANEOS Klinikum</option>
                                </select>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-4">Objektteil:</div>
                            <div class="col">
                                <select class="form-select" name="zielobjtyp" id="zielobjtyp" autocomplete="off" required>
                                    <option value="0" selected disabled hidden>---</option>
                                    <option value="1">ZNA</option>
                                    <option value="2">ZNA/Schockraum</option>
                                    <option value="3">Intensivstation</option>
                                    <option value="4">OP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>Ankunft</h6>
                    </div>
                </div>
                <div class="row py-1">
                    <div class="col mx-2" id="add-time">
                        <div class="row py-2">
                            <div class="col">
                                <button data-time-value="5">in 5min</button>
                            </div>
                            <div class="col">
                                <button data-time-value="10">in 10min</button>
                            </div>
                            <div class="col">
                                <button data-time-value="15">in 15min</button>
                            </div>
                            <div class="col">
                                <button data-time-value="20">in 20min</button>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col-3">
                                <button data-time-value="25">in 25min</button>
                            </div>
                            <div class="col-3">
                                <button data-time-value="30">in 30min</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>Notfallkategorie</h6>
                    </div>
                </div>
                <div class="row py-1" id="notf_categories">
                    <div class="col mx-2">
                        <div class="row py-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check deselect-checkbox" id="kat_1" name="kat_1" value="1" autocomplete="off">
                                <label class="btn btn-danger w-100" for="kat_1">Sofort</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" class="btn-check deselect-checkbox" id="kat_2" name="kat_2" value="1" autocomplete="off">
                                <label class="btn btn-warning w-100" for="kat_2">Dringend</label>
                            </div>
                        </div>
                        <div class="row py-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check deselect-checkbox" id="kat_3" name="kat_3" value="1" autocomplete="off">
                                <label class="btn btn-success w-100" for="kat_3">Normal</label>
                            </div>
                            <div class="col"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>Diagnose/Symptom</h6>
                    </div>
                </div>
                <textarea name="diagnose" id="diagnose" rows="5" class="w-100 form-control" style="resize: none" required></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>A - Atemwege</h6>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="a_1" name="a_1" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="a_1">Intubiert</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="a_2" name="a_2" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="a_2">Koniotomie</label>
                    </div>
                </div>
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>B - Atmung</h6>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="b_1" name="b_1" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="b_1">Beatmet</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="b_2" name="b_2" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="b_2">Entlastungspunktion</label>
                    </div>
                </div>
            </div>
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>C - Kreislauf</h6>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="c_1" name="c_1" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="c_1">STEMI Verdacht</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="c_2" name="c_2" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="c_2">Lebensbedrohliche Blutungen</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="c_3" name="c_3" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="c_3">Laufende Reanimation</label>
                    </div>
                </div>
            </div>
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>D - Neurologie</h6>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="d_1" name="d_1" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="d_1">STROKE Verdacht</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="d_2" name="d_2" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="d_2">Narkose</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="d_3" name="d_3" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="d_3">Betäubungsmittel Verdacht</label>
                    </div>
                </div>
            </div>
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>Sonstige</h6>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="s_1" name="s_1" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="s_1">Transport liegend</label>
                    </div>
                </div>
                <div class="row py-1 px-2">
                    <div class="col">
                        <input type="checkbox" class="btn-check" id="s_2" name="s_2" value="1" autocomplete="off">
                        <label class="btn btn-ss-blue w-100" for="s_2">Transport sitzend</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col border border-dark bg-ss-blue">
                <div class="row bg-secondary text-light py-1">
                    <div class="col mx-2">
                        <h6>Einsatzdaten</h6>
                    </div>
                </div>
                <div class="row py-2">
                    <div class="col-4">Einsatzstichwort:</div>
                    <div class="col">
                        <select class="form-select" name="estichwort" id="estichwort" autocomplete="off" required>
                            <option value="NULL" selected disabled hidden>---</option>
                            <option value="NOTF K">NOTFALL K</option>
                            <option value="NOTF 01">NOTFALL 01</option>
                            <option value="NOTF 11">NOTFALL 11</option>
                        </select>
                    </div>
                </div>
                <div class="row p-2">
                    <div class="col-4">Einsatznummer:</div>
                    <div class="col">
                        <input class="form-control" type="number" name="enr" id="enr" required>
                    </div>
                </div>
            </div>
            <div class="col"></div>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Ankunftszeit Knöpfe
        document.addEventListener("DOMContentLoaded", function() {
            const ankunftzeitInput = document.getElementById("ankunftzeit");
            const addTimeButtons = document.querySelectorAll("#add-time button");

            addTimeButtons.forEach(button => {
                button.addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    const timeValueToAdd = parseInt(button.getAttribute("data-time-value"));
                    const currentTime = new Date();

                    currentTime.setMinutes(currentTime.getMinutes() + timeValueToAdd);

                    const formattedTime = currentTime.toISOString().substr(11, 5);

                    ankunftzeitInput.value = formattedTime;
                });
            });
        });
    </script>
    <script>
        // Notfallkategorie auswahl
        const deselectCheckboxes = document.querySelectorAll('.deselect-checkbox');

        deselectCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    deselectCheckboxes.forEach(otherCheckbox => {
                        if (otherCheckbox !== this) {
                            otherCheckbox.checked = false;
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>