<?php
session_start();

include '../assets/php/mysql-con.php';

if (isset($_POST['new']) && $_POST['new'] == 1) {
    // STAMMDATEN
    $patname = $_POST['patname'] ?? NULL;
    $patgebdat = $_POST['patgebdat'] ?? NULL;
    $patsex = $_POST['patsex'];
    $ezeit = $_POST['ezeit'];
    $enr = $_POST['enr'];
    // A - ATEMWEGE
    $awfrei_1 = $_POST['awfrei_1'] >= 1 ? $_POST['awfrei_1'] : 0;
    $awfrei_2 = $_POST['awfrei_2'] >= 1 ? $_POST['awfrei_2'] : 0;
    $awsicherung_1 = $_POST['awsicherung_1'] >= 1 ? $_POST['awsicherung_1'] : 0;
    $awsicherung_2 = $_POST['awsicherung_2'] >= 1 ? $_POST['awsicherung_2'] : 0;
    $zyanose_1 = $_POST['zyanose_1'] >= 1 ? $_POST['zyanose_1'] : 0;
    $zyanose_2 = $_POST['zyanose_2'] >= 1 ? $_POST['zyanose_2'] : 0;
    $o2gabe = $_POST['o2gabe'] >= 1 ? $_POST['o2gabe'] : 0;
    // B - ATMUNG
    $b_symptome = $_POST['b_symptome'];
    $b_auskult = $_POST['b_auskult'];
    $spo2 = $_POST['spo2'] ?? NULL;
    $atemfreq = $_POST['atemfreq'] ?? NULL;
    $etco2 = $_POST['etco2'] ?? NULL;
    // C - KREISLAUF
    $c_kreislauf = $_POST['c_kreislauf'];
    $rrsys = $_POST['rrsys'] ?? NULL;
    $rrdias = $_POST['rrdias'] ?? NULL;
    $herzfreq = $_POST['herzfreq'] ?? NULL;
    $c_ekg = $_POST['c_ekg'];
    // D - NEUROLOGIE
    $d_bewusstsein = $_POST['d_bewusstsein'];
    $d_pupillenw_1 = $_POST['d_pupillenw_1'];
    $d_pupillenw_2 = $_POST['d_pupillenw_2'];
    $d_lichtreakt_1 = $_POST['d_lichtreakt_1'];
    $d_lichtreakt_2 = $_POST['d_lichtreakt_2'];
    $d_gcs_1 = $_POST['d_gcs_1'];
    $d_gcs_2 = $_POST['d_gcs_2'];
    $d_gcs_3 = $_POST['d_gcs_3'];
    $d_ex_1 = $_POST['d_ex_1'];
    // BZ + TEMP
    $bz = $_POST['bz'] ?? NULL;
    $temp = $_POST['temp'] ?? NULL;
    // VERLETZUNGEN
    $v_muster_k = $_POST['v_muster_k'];
    $v_muster_k1 = $_POST['v_muster_k1'];
    $v_muster_t = $_POST['v_muster_t'];
    $v_muster_t1 = $_POST['v_muster_t1'];
    $v_muster_al = $_POST['v_muster_al'];
    $v_muster_al1 = $_POST['v_muster_al1'];
    $v_muster_a = $_POST['v_muster_a'];
    $v_muster_a1 = $_POST['v_muster_a1'];
    $v_muster_bl = $_POST['v_muster_bl'];
    $v_muster_bl1 = $_POST['v_muster_bl1'];
    $v_muster_w = $_POST['v_muster_w'];
    $v_muster_w1 = $_POST['v_muster_w1'];
    // MEDIKAMENTE
    $medis = $_POST['medis'] ?? NULL;
    // DIAGNOSE
    $diagnose = $_POST['diagnose'] ?? NULL;
    // ANMERKUNGEN
    $anmerkungen = $_POST['anmerkungen'] ?? NULL;
    // PROTOKOLLDATEN
    $notfallteam = $_POST['notfallteam'] >= 1 ? $_POST['notfallteam'] : 0;
    $transportverw = $_POST['transportverw'] >= 1 ? $_POST['transportverw'] : 0;
    $nacascore = $_POST['nacascore'] >= 1 ? $_POST['nacascore'] : 0;
    $pfname =  $_POST['pfname'];
    $naname =  $_POST['naname'] ?? NULL;
    $transportziel =  $_POST['transportziel'] ?? NULL;
    // SQL-Query ausführen
    $query = "INSERT INTO cirs_rd_protokolle (patname, patgebdat, patsex, ezeit, enr, awfrei_1, awfrei_2, awsicherung_1, awsicherung_2, zyanose_1, zyanose_2, o2gabe, b_symptome, b_auskult, spo2, atemfreq, etco2, c_kreislauf, rrsys, rrdias, rrmad, herzfreq, c_ekg, d_bewusstsein, d_pupillenw_1, d_pupillenw_2, d_lichtreakt_1, d_lichtreakt_2, d_gcs_1, d_gcs_2, d_gcs_3, d_ex_1, d_ex_2, d_ex_3, d_ex_4, bz, temp, v_muster_k, v_muster_k1, v_muster_t, v_muster_t1, v_muster_al, v_muster_al1, v_muster_a, v_muster_a1, v_muster_bl, v_muster_bl1, v_muster_w, v_muster_w1, medis, diagnose, anmerkungen, notfallteam, transportverw, nacascore, pfname, naname, transportziel)
    VALUES ('$patname', '$patgebdat', '$patsex', '$ezeit', '$enr', '$awfrei_1', '$awfrei_2', '$awsicherung_1', '$awsicherung_2', '$zyanose_1', '$zyanose_2', '$o2gabe', '$b_symptome',  '$b_auskult', '$spo2', '$atemfreq', '$etco2', '$c_kreislauf', '$rrsys', '$rrdias', '$rrmad', '$herzfreq', '$c_ekg', '$d_bewusstsein', '$d_pupillenw_1', '$d_pupillenw_2', '$d_lichtreakt_1', '$d_lichtreakt_2', '$d_gcs_1', '$d_gcs_2', '$d_gcs_3', '$d_ex_1', '$d_ex_2', '$d_ex_3', '$d_ex_4', '$bz', '$temp', '$v_muster_k', '$v_muster_k1', '$v_muster_t', '$v_muster_t1', '$v_muster_al', '$v_muster_al1', '$v_muster_a', '$v_muster_a1', '$v_muster_bl', '$v_muster_bl1', '$v_muster_w', '$v_muster_w1', '$medis', '$diagnose', '$anmerkungen', '$notfallteam', '$transportverw', '$nacascore', '$pfname', '$naname', '$transportziel')";
    mysqli_query($conn, $query);
    header("Refresh: 0");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>eDIVI &rsaquo; intraSB</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/divi.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
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

<body>
    <form name="form" method="post" action="">
        <input type="hidden" name="new" value="1" />
        <div class="container-fluid bg-light">
            <div class="row h-100">
                <div class="col">
                    <!-- ------------ -->
                    <!-- ! STAMMDATEN -->
                    <!-- ------------ -->
                    <div class="row">
                        <div class="col border border-1 border-bottom-0 border-dark">
                            <h6 class="bg-dark text-light text-center p-1">Stammdaten</h6>
                            <div class="col">
                                <div class="row my-2">
                                    <div class="col-4 fw-bold">Name</div>
                                    <div class="col"><input type="text" name="patname" id="patname" class="w-100 form-control"></div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Geburtsdatum</div>
                                <div class="col"><input type="date" name="patgebdat" id="patgebdat" class="w-100 form-control"></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Geschlecht</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="0"> männlich</div>
                                        <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="1"> weiblich</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Einsatzzeit</div>
                                <div class="col">
                                    <input type="time" name="ezeit" id="ezeit" class="w-100 form-control" required>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Einsatznummer</div>
                                <div class="col">
                                    <input type="text" name="enr" id="enr" class="w-100 form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- A - ATEMWEGE -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">A - Atemwege <em>(Airway)</em></h6>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Atemwege frei</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awfrei_1" name="awfrei_1" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="awfrei_1">Ja</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awfrei_2" name="awfrei_2" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="awfrei_2">Nein</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Atemwegssicherung</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awsicherung_1" name="awsicherung_1" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="awsicherung_1">Nein</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awsicherung_2" name="awsicherung_2" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="awsicherung_2">Ja</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Zyanose</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="zyanose_1" name="zyanose_1" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="zyanose_1">Nein</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="zyanose_2" name="zyanose_2" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="zyanose_2">Ja</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">O2 Gabe</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="o2gabe" name="o2gabe" value="0" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="o2gabe" data-bs-toggle="modal" data-bs-target="#myModal">
                                                <span id="o2gabe-value"></span> l/min
                                            </label>
                                        </div>
                                        <!-- MODAL -->
                                        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="myModalLabel">Wert eingeben</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="o2gabe-input" class="form-label">Liter pro Minute:</label>
                                                            <input type="number" class="form-control" id="o2gabe-input" name="o2gabe-input" min="0" max="15" value="0">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                                                        <button type="button" class="btn btn-primary" id="save-btn">Speichern</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- MODAL ENDE -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- B - ATMUNG -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">B - Atmung <em>(Breathing)</em></h6>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Atmung</div>
                                <div class="col">
                                    <select name="b_symptome" id="b_symptome" class="form-select w-100" required>
                                        <option disabled hidden selected>Symptomauswahl</option>
                                        <option value="0">unauffällig</option>
                                        <option value="1">Dyspnoe</option>
                                        <option value="2">Apnoe</option>
                                        <option value="3">Schnappatmung</option>
                                        <option value="4">Andere pathol.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Auskultation</div>
                                <div class="col">
                                    <select name="b_auskult" id="b_auskult" class="form-select w-100" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">unauffällig</option>
                                        <option value="1">Spastik</option>
                                        <option value="2">Stridor</option>
                                        <option value="3">Rasselgeräusche</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">SpO2</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="spo2" id="spo2" style="display:inline"> <small>%</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Atemfrequenz</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="atemfreq" id="atemfreq" style="display:inline"> <small>/min</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">etCO2</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="etco2" id="etco2" style="display:inline"> <small>mmHg</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- C - KREISLAUF -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">C - Kreislauf <em>(Circulation)</em></h6>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Kreislauf</div>
                                <div class="col">
                                    <select name="c_kreislauf" id="c_kreislauf" class="form-select w-100" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">stabil</option>
                                        <option value="1">instabil</option>
                                        <option value="2">nicht beurteilbar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">RR</div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col"><small class="fw-bold">sys</small> <input class="w-100 vitalparam form-control" type="text" name="rrsys" id="rrsys" style="display:inline"> <small class="fw-bold">/ dias</small> <input class="w-100 vitalparam form-control" type="text" name="rrdias" id="rrdias" style="display:inline"> <small>mmHg</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">HF</div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="herzfreq" id="herzfreq" style="display:inline"> <small>/min</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">EKG</div>
                                <div class="col">
                                    <select name="c_ekg" id="c_ekg" class="form-select w-100" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">Sinusrhythmus</option>
                                        <option value="1">STEMI</option>
                                        <option value="2">Abs. Arrhythmie</option>
                                        <option value="3">Kammerflimmern</option>
                                        <option value="4">Tachykardie</option>
                                        <option value="5">AV-Block II°/III°</option>
                                        <option value="6">Asystolie</option>
                                        <option value="7">Vorhofflimmern</option>
                                        <option value="8">Bradykardie</option>
                                        <option value="9">nicht beurteilbar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mx-2">
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- D - NEUROLOGIE -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">D - Neurologie <em>(Disability)</em></h6>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Bewusstseinslage</div>
                                <div class="col">
                                    <select name="d_bewusstsein" id="d_bewusstsein" class="form-select w-100" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">wach</option>
                                        <option value="1">somnolent</option>
                                        <option value="2">sopor</option>
                                        <option value="3">komatös</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Pupillenweite</div>
                                <div class="col">
                                    <small>li</small> <select name="d_pupillenw_1" id="d_pupillenw_1" class="form-select" style="display:inline; max-width: 150px" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">entrundet</option>
                                        <option value="1">weit</option>
                                        <option value="2">mittel</option>
                                        <option value="3">eng</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <small>re</small> <select name="d_pupillenw_2" id="d_pupillenw_2" class="form-select" style="display:inline; max-width: 150px" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">entrundet</option>
                                        <option value="1">weit</option>
                                        <option value="2">mittel</option>
                                        <option value="3">eng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Lichtreaktion</div>
                                <div class="col">
                                    <small>li</small> <select name="d_lichtreakt_1" id="d_lichtreakt_1" class="form-select" style="display:inline; max-width: 150px" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">prompt</option>
                                        <option value="1">träge</option>
                                        <option value="2">keine</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <small>re</small> <select name="d_lichtreakt_2" id="d_lichtreakt_2" class="form-select" style="display:inline; max-width: 150px" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">prompt</option>
                                        <option value="1">träge</option>
                                        <option value="2">keine</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col fw-bold">Glasgow Coma Scale</div>
                            </div>
                            <div class="row ms-1">
                                <div class="col-4 fw-bold"><small>Augen öffnen</small></div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col">
                                            <select class="form-select w-100" name="d_gcs_1" id="d_gcs_1" required>
                                                <option disabled hidden selected>---</option>
                                                <option value="0">spontan (4)</option>
                                                <option value="1">auf Aufforderung (3)</option>
                                                <option value="2">auf Schmerzreiz (2)</option>
                                                <option value="3">kein Öffnen (1)</option>
                                            </select>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(4)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-1">
                                <div class="col-4 fw-bold"><small>beste verbale Reaktion</small></div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col">
                                            <select class="form-select w-100" name="d_gcs_2" id="d_gcs_2" required>
                                                <option disabled hidden selected>---</option>
                                                <option value="0">orientiert (5)</option>
                                                <option value="1">desorientiert (4)</option>
                                                <option value="2">inadäquate Äußerungen (3)</option>
                                                <option value="3">unverstädnliche Laute (2)</option>
                                                <option value="4">keine Reaktion (1)</option>
                                            </select>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(5)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-1">
                                <div class="col-4 fw-bold"><small>beste motorische Reaktion</small></div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col">
                                            <select class="form-select w-100" name="d_gcs_3" id="d_gcs_3" required>
                                                <option disabled hidden selected>---</option>
                                                <option value="0">folg Aufforderung (6)</option>
                                                <option value="1">gezielte Abwehrbewegungen (5)</option>
                                                <option value="2">ungezielte Abwehrbewegungen (4)</option>
                                                <option value="3">Bezgesynergismen (3)</option>
                                                <option value="4">Strecksynergismen (2)</option>
                                                <option value="5">keine Reaktion (1)</option>
                                            </select>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(6)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 fw-bold">Extremitätenbewegung</div>
                                <div class="col">
                                    <select name="d_ex_1" id="d_ex_1" class="form-select w-100" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">stark eingeschränkt</option>
                                        <option value="2">leicht eingeschränkt</option>
                                        <option value="1">uneingeschränkt</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- EXPOSURE -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">E - Entkleiden/Erweitern <em>(Exposure)</em></h6>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Blutzucker</div>
                                <div class="col"><input class="w-100 vitalparam form-control" type="text" name="bz" id="bz" style="display:inline; max-width: 75px"> <small>mg/dl</small></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Temperatur</div>
                                <div class="col"><input class="w-100 vitalparam form-control" type="text" name="temp" id="temp" style="display:inline; max-width: 75px"> <small>°C</small></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Kopf</div>
                                <div class="col">
                                    <select name="v_muster_k" id="v_muster_k" class="w-100 divi-v-select form-select" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">schwer</option>
                                        <option value="1">mittel</option>
                                        <option value="2">leicht</option>
                                        <option value="3">keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_k1" id="v_muster_k1" class="w-100 form-select">
                                        <option value="0" selected>---</option>
                                        <option value="1">offen</option>
                                        <option value="2">geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Wirbelsäule</div>
                                <div class="col">
                                    <select name="v_muster_w" id="v_muster_w" class="w-100 divi-v-select form-select" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">schwer</option>
                                        <option value="1">mittel</option>
                                        <option value="2">leicht</option>
                                        <option value="3">keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_w1" id="v_muster_w1" class="w-100 form-select">
                                        <option value="0" selected>---</option>
                                        <option value="1">offen</option>
                                        <option value="2">geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Thorax</div>
                                <div class="col">
                                    <select name="v_muster_t" id="v_muster_t" class="w-100 divi-v-select form-select" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">schwer</option>
                                        <option value="1">mittel</option>
                                        <option value="2">leicht</option>
                                        <option value="3">keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_t1" id="v_muster_t1" class="w-100 form-select">
                                        <option value="0" selected>---</option>
                                        <option value="1">offen</option>
                                        <option value="2">geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Abdomen</div>
                                <div class="col">
                                    <select name="v_muster_a" id="v_muster_a" class="w-100 divi-v-select form-select" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">schwer</option>
                                        <option value="1">mittel</option>
                                        <option value="2">leicht</option>
                                        <option value="3">keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_a1" id="v_muster_a1" class="w-100 form-select">
                                        <option value="0" selected>---</option>
                                        <option value="1">offen</option>
                                        <option value="2">geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Obere Extremitäten</div>
                                <div class="col">
                                    <select name="v_muster_al" id="v_muster_al" class="w-100 divi-v-select form-select" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">schwer</option>
                                        <option value="1">mittel</option>
                                        <option value="2">leicht</option>
                                        <option value="3">keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_al1" id="v_muster_al1" class="w-100 form-select">
                                        <option value="0" selected>---</option>
                                        <option value="1">offen</option>
                                        <option value="2">geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Untere Extremitäten</div>
                                <div class="col">
                                    <select name="v_muster_bl" id="v_muster_bl" class="w-100 divi-v-select form-select" required>
                                        <option disabled hidden selected>---</option>
                                        <option value="0">schwer</option>
                                        <option value="1">mittel</option>
                                        <option value="2">leicht</option>
                                        <option value="3">keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_bl1" id="v_muster_bl1" class="w-100 form-select">
                                        <option value="0" selected>---</option>
                                        <option value="1">offen</option>
                                        <option value="2">geschl.</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- MEDIKAMENTE -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">Medikamente</h6>
                            <div class="row mt-2 mb-1">
                                <div class="col fw-bold">Medikamentengabe dokumentieren</div>
                                <div class="col mt-1 mb-2"><button class="btn btn-sm btn-danger w-100" type="button" data-bs-toggle="modal" data-bs-target="#myModal2">Hinzufügen +</button></div>
                            </div>
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="medis" id="medis" rows="10" class="w-100 form-control" style="resize: none" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL -->
                    <div class="modal fade" id="myModal2" tabindex="-1" aria-labelledby="myModalLabel2" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel2">Medikamentgabe dokumentieren</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mt-2 mb-1">
                                        <div class="col-3 fw-bold">Zeit</div>
                                        <div class="col">
                                            <input type="time" id="time" name="time" class="form-control w-100">
                                        </div>
                                    </div>
                                    <div class="row my-1">
                                        <div class="col-3 fw-bold">Medikament</div>
                                        <div class="col">
                                            <input type="text" id="medikament" name="medikament" class="form-control w-100" placeholder="Epinephrin 1mg">
                                        </div>
                                    </div>
                                    <div class="row my-1">
                                        <div class="col-3 fw-bold">Applikation</div>
                                        <div class="col">
                                            <select name="applikation" id="applikation" class="w-100 form-select" required>
                                                <option value="i.v.">intravenös</option>
                                                <option value="i.m.">intramuskulär</option>
                                                <option value="i.o.">intraossär</option>
                                                <option value="i.n.">intranasal</option>
                                                <option value="s.l.">sublingual</option>
                                                <option value="s.c.">subcutan</option>
                                                <option value="p.o.">per os</option>
                                                <option value="rec.">rectal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-1">
                                            <label for="notarzt" class="fw-bold">Von Notarzt gegeben/angeordnet:</label>
                                            <input class="form-check-input" type="checkbox" id="notarzt" name="notarzt">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                                    <button type="button" class="btn btn-primary" onclick="addData()">Speichern</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL ENDE -->
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- DIAGNOSE -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">Verdachts-/Erstdiagnose</h6>
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="diagnose" id="diagnose" rows="3" class="w-100 form-control" style="resize: none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- SONSTIGES -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">Notfallsituation, Anamnese, Bemerkungen</h6>
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="anmerkungen" id="anmerkungen" rows="20" class="w-100 form-control" style="resize: none"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-top-0 border-dark">
                            <div class="row my-2">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="notfallteam" name="notfallteam" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-danger w-100" for="notfallteam">Übergabe Notfallteam</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="transportverw" name="transportverw" value="1" autocomplete="off">
                                            <label class="btn btn-sm btn-outline-warning w-100" for="transportverw">Transportverweigerung</label>
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-sm btn-info w-100" type="button" data-bs-toggle="modal" data-bs-target="#myModal3">NACA-Score</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- MODAL -->
                            <div class="modal fade" id="myModal3" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myModalLabel3">NACA-Scoring</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mt-2 mb-1">
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="0" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            NACA I - geringfügige Störung
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="1" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            NACA II - ambulante Abklärung
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="2" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            NACA III - stationäre Behandlung
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="3" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            NACA IV - akute Lebensgefahr nicht auszuschließen
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="4" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            NACA V - akute Lebensgefahr
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="5" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            NACA VI - Reanimation
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- MODAL ENDE -->
                            <div class="row mt-3">
                                <div class="col-3 fw-bold">Protokollführer</div>
                                <div class="col"><input type="text" name="pfname" id="pfname" class="w-100 form-control" required></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3 fw-bold">Notarzt</div>
                                <div class="col"><input type="text" name="naname" id="naname" class="w-100 form-control"></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3 fw-bold">Transportziel</div>
                                <div class="col"><input type="text" name="transportziel" id="transportziel" class="w-100 form-control"></div>
                            </div>
                            <div class="row mt-3 mb-2">
                                <div class="col">
                                    <input class="btn btn-success btn-sm w-100" name="submit" type="submit" value="Protokoll speichern" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // eDIVI Buttons
        const o2gabe = document.getElementById("o2gabe");

        function checkCheckbox() {
            if (o2gabe.value > 0) {
                o2gabe.checked = true;
            } else {
                o2gabe.checked = false;
            }
        }

        o2gabe.addEventListener("click", checkCheckbox);
    </script>
    <script>
        // eDIVI Verletzungen
        const selectElements = document.querySelectorAll(".divi-v-select");

        selectElements.forEach((selectElement) => {
            selectElement.addEventListener("change", () => {
                if (selectElement.value === "0") {
                    selectElement.style.backgroundColor = "red";
                } else if (selectElement.value === "1") {
                    selectElement.style.backgroundColor = "yellow";
                } else if (selectElement.value === "2") {
                    selectElement.style.backgroundColor = "green";
                } else {
                    selectElement.style.backgroundColor = "";
                }
            });
        });
    </script>
    <script>
        // eDIVI Medikamente
        function addData() {
            const time = document.getElementById("time").value;
            const medicament = document.getElementById("medikament").value;
            const notarzt = document.getElementById("notarzt").checked;

            if (!time || !medicament) {
                alert("Bitte bei den Medikamenten alle Felder ausfüllen!");
                return;
            }

            let newData = `${time} - ${medicament} ${document.getElementById("applikation").value}`;

            if (notarzt) {
                newData += "(NA)";
            }

            const output = document.getElementById("medis");

            if (output.value) {
                output.value += "\n";
            }

            output.value += newData;
        }
    </script>
    <script>
        const input = document.getElementById('o2gabe-input');
        const value = document.getElementById('o2gabe-value');
        const saveBtn = document.getElementById('save-btn');
        const o2gabeCheckbox = document.getElementById('o2gabe');

        saveBtn.addEventListener('click', () => {
            if (input.value > 0) {
                value.innerText = input.value;
            }
            o2gabeCheckbox.value = input.value;
            o2gabeCheckbox.checked = input.value !== '0';
            $('#myModal').modal('hide');
        });

        $('#myModal').on('shown.bs.modal', function() {
            input.focus();
        });

        $('#myModal2').on('shown.bs.modal', function() {
            input.focus();
        });

        $('#myModal3').on('shown.bs.modal', function() {
            input.focus();
        });
    </script>
</body>

</html>