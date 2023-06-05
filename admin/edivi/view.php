<?php
session_start();
if (!isset($_SESSION['userid']) && !isset($_SESSION['permissions'])) {
    header("Location: /admin/login.php");
} else if (!in_array('full_admin', $_SESSION['permissions']) && !in_array('admin', $_SESSION['permissions']) && !in_array('edivi_view', $_SESSION['permissions'])) {
    header("Location: /admin/index.php");
}

include '../../assets/php/mysql-con.php';

$result = mysqli_query($conn, "SELECT * FROM cirs_rd_protokolle WHERE id = " . $_GET['id']) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

if (isset($_POST['new']) && $_POST['new'] == 1) {
    $bearbeiter = $_POST['bearbeiter'];
    $protokoll_status = $_POST['protokoll_status'];

    $query = "UPDATE cirs_rd_protokolle SET bearbeiter = '$bearbeiter', protokoll_status = '$protokoll_status' WHERE id = " . $_GET['id'];
    mysqli_query($conn, $query);
    header("Location: /admin/edivi/list.php");
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
    <link rel="stylesheet" href="/assets/css/admin.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/assets/bootstrap-5.3/css/bootstrap.min.css">
    <script src="/assets/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>
    <!-- html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Name</div>
                                <div class="col"><input type="text" name="patname" id="patname" class="w-100 form-control" value="<?= $row['patname'] ?>" readonly></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Geburtsdatum</div>
                                <div class="col"><input type="date" name="patgebdat" id="patgebdat" class="w-100 form-control" value="<?= $row['patgebdat'] ?>" readonly></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Geschlecht</div>
                                <div class="col">
                                    <div class="row">
                                        <?php if ($row['patsex'] == 0) { ?>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="0" checked disabled> männlich</div>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="1" disabled> weiblich</div>
                                        <?php } else if ($row['patsex'] == 1) { ?>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="0" disabled> männlich</div>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="1" checked disabled> weiblich</div>
                                        <?php } else { ?>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="0" disabled> männlich</div>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="1" disabled> weiblich</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Einsatzzeit</div>
                                <div class="col">
                                    <input type="time" name="ezeit" id="ezeit" class="w-100 form-control" value="<?= $row['ezeit'] ?>" readonly>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Einsatznummer</div>
                                <div class="col">
                                    <input type="text" name="enr" id="enr" class="w-100 form-control" value="<?= $row['enr'] ?>" readonly>
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
                                            <input type="checkbox" class="btn-check" id="awfrei_1" <?php echo ($row['awfrei_1'] == 1 ? 'checked' : '') ?> disabled autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="awfrei_1">Ja</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awfrei_2" <?php echo ($row['awfrei_2'] == 1 ? 'checked' : '') ?> disabled autocomplete="off">
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
                                            <input type="checkbox" class="btn-check" id="awsicherung_1" <?php echo ($row['awsicherung_1'] == 1 ? 'checked' : '') ?> disabled autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="awsicherung_1">Nein</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awsicherung_2" <?php echo ($row['awsicherung_2'] == 1 ? 'checked' : '') ?> disabled autocomplete="off">
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
                                            <input type="checkbox" class="btn-check" id="zyanose_1" <?php echo ($row['zyanose_1'] == 1 ? 'checked' : '') ?> disabled autocomplete="off">
                                            <label class="btn btn-sm btn-outline-dark w-100" for="zyanose_1">Nein</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="zyanose_2" <?php echo ($row['zyanose_2'] == 1 ? 'checked' : '') ?> disabled autocomplete="off">
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
                                            <?php if ($row['o2gabe'] > 0) { ?>
                                                <input type="checkbox" class="btn-check" id="o2gabe" checked disabled autocomplete="off">
                                                <label class="btn btn-sm btn-outline-dark w-100" for="o2gabe"><?= $row['o2gabe'] ?> l/min</label>
                                            <?php } else { ?>
                                                <input type="checkbox" class="btn-check" id="o2gabe" disabled autocomplete="off">
                                                <label class="btn btn-sm btn-outline-dark w-100" for="o2gabe">l/min</label>
                                            <?php } ?>
                                        </div>
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
                                    <select name="b_symptome" id="b_symptome" class="w-100 form-select" disabled autocomplete="off">
                                        <option disabled hidden selected>Symptomauswahl</option>
                                        <option value="0" <?php echo ($row['b_symptome'] == 0 ? 'selected' : '') ?>>unauffällig</option>
                                        <option value="1" <?php echo ($row['b_symptome'] == 1 ? 'selected' : '') ?>>Dyspnoe</option>
                                        <option value="2" <?php echo ($row['b_symptome'] == 2 ? 'selected' : '') ?>>Apnoe</option>
                                        <option value="3" <?php echo ($row['b_symptome'] == 3 ? 'selected' : '') ?>>Schnappatmung</option>
                                        <option value="4" <?php echo ($row['b_symptome'] == 4 ? 'selected' : '') ?>>Andere pathol.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Auskultation</div>
                                <div class="col">
                                    <select name="b_auskult" id="b_auskult" class="w-100 form-select" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['b_auskult'] == 0 ? 'selected' : '') ?>>unauffällig</option>
                                        <option value="1" <?php echo ($row['b_auskult'] == 1 ? 'selected' : '') ?>>Spastik</option>
                                        <option value="2" <?php echo ($row['b_auskult'] == 2 ? 'selected' : '') ?>>Stridor</option>
                                        <option value="3" <?php echo ($row['b_auskult'] == 3 ? 'selected' : '') ?>>Rasselgeräusche</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">SpO2</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" style="display:inline" type="text" name="spo2" id="spo2" value="<?= $row['spo2'] ?>" readonly> <small>%</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Atemfrequenz</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" style="display:inline" type="text" name="atemfreq" id="atemfreq" value="<?= $row['atemfreq'] ?>" readonly> <small>/min</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">etCO2</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" style="display:inline" type="text" name="etco2" id="etco2" value="<?= $row['etco2'] ?>" readonly> <small>mmHg</small></div>
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
                                    <select name="c_kreislauf" id="c_kreislauf" class="w-100 form-select" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['c_kreislauf'] == 0 ? 'selected' : '') ?>>stabil</option>
                                        <option value="1" <?php echo ($row['c_kreislauf'] == 1 ? 'selected' : '') ?>>instabil</option>
                                        <option value="2" <?php echo ($row['c_kreislauf'] == 2 ? 'selected' : '') ?>>nicht beurteilbar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">RR</div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col"><small class="fw-bold">sys</small> <input class="w-100 vitalparam form-control" style="display:inline" type="text" name="rrsys" id="rrsys" value="<?= $row['rrsys'] ?>" readonly> <small class="fw-bold">/ dias</small> <input class="w-100 vitalparam form-control" style="display:inline" type="text" name="rrdias" id="rrdias" value="<?= $row['rrdias'] ?>" readonly> <small>mmHg</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">HF</div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col"><input class="w-100 vitalparam form-control" style="display:inline" type="text" name="herzfreq" id="herzfreq" value="<?= $row['herzfreq'] ?>" readonly> <small>/min</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">EKG</div>
                                <div class="col">
                                    <select name="c_ekg" id="c_ekg" class="w-100 form-select" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['c_ekg'] == 0 ? 'selected' : '') ?>>Sinusrhythmus</option>
                                        <option value="1" <?php echo ($row['c_ekg'] == 1 ? 'selected' : '') ?>>STEMI</option>
                                        <option value="2" <?php echo ($row['c_ekg'] == 2 ? 'selected' : '') ?>>Abs. Arrhythmie</option>
                                        <option value="3" <?php echo ($row['c_ekg'] == 3 ? 'selected' : '') ?>>Kammerflimmern</option>
                                        <option value="4" <?php echo ($row['c_ekg'] == 4 ? 'selected' : '') ?>>Tachykardie</option>
                                        <option value="5" <?php echo ($row['c_ekg'] == 5 ? 'selected' : '') ?>>AV-Block II°/III°</option>
                                        <option value="6" <?php echo ($row['c_ekg'] == 6 ? 'selected' : '') ?>>Asystolie</option>
                                        <option value="7" <?php echo ($row['c_ekg'] == 7 ? 'selected' : '') ?>>Vorhofflimmern</option>
                                        <option value="8" <?php echo ($row['c_ekg'] == 8 ? 'selected' : '') ?>>Bradykardie</option>
                                        <option value="9" <?php echo ($row['c_ekg'] == 9 ? 'selected' : '') ?>>nicht beurteilbar</option>
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
                                    <select name="d_bewusstsein" id="d_bewusstsein" class="w-100 form-select" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['d_bewusstsein'] == 0 ? 'selected' : '') ?>>wach</option>
                                        <option value="1" <?php echo ($row['d_bewusstsein'] == 1 ? 'selected' : '') ?>>somnolent</option>
                                        <option value="2" <?php echo ($row['d_bewusstsein'] == 2 ? 'selected' : '') ?>>sopor</option>
                                        <option value="3" <?php echo ($row['d_bewusstsein'] == 3 ? 'selected' : '') ?>>komatös</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Pupillenweite</div>
                                <div class="col">
                                    <small>li</small> <select name="d_pupillenw_1" id="d_pupillenw_1" class="form-select" style="display:inline; max-width: 150px" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['d_pupillenw_1'] == 0 ? 'selected' : '') ?>>entrundet</option>
                                        <option value="1" <?php echo ($row['d_pupillenw_1'] == 1 ? 'selected' : '') ?>>weit</option>
                                        <option value="2" <?php echo ($row['d_pupillenw_1'] == 2 ? 'selected' : '') ?>>mittel</option>
                                        <option value="3" <?php echo ($row['d_pupillenw_1'] == 3 ? 'selected' : '') ?>>eng</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <small>re</small> <select name="d_pupillenw_2" id="d_pupillenw_2" class="form-select" style="display:inline; max-width: 150px" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['d_pupillenw_2'] == 0 ? 'selected' : '') ?>>entrundet</option>
                                        <option value="1" <?php echo ($row['d_pupillenw_2'] == 1 ? 'selected' : '') ?>>weit</option>
                                        <option value="2" <?php echo ($row['d_pupillenw_2'] == 2 ? 'selected' : '') ?>>mittel</option>
                                        <option value="3" <?php echo ($row['d_pupillenw_2'] == 3 ? 'selected' : '') ?>>eng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Lichtreaktion</div>
                                <div class="col">
                                    <small>li</small> <select name="d_lichtreakt_1" id="d_lichtreakt_1" class="form-select" style="display:inline; max-width: 150px" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['d_lichtreakt_1'] == 0 ? 'selected' : '') ?>>prompt</option>
                                        <option value="1" <?php echo ($row['d_lichtreakt_1'] == 1 ? 'selected' : '') ?>>träge</option>
                                        <option value="2" <?php echo ($row['d_lichtreakt_1'] == 2 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <small>re</small> <select name="d_lichtreakt_2" id="d_lichtreakt_2" class="form-select" style="display:inline; max-width: 150px" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['d_lichtreakt_2'] == 0 ? 'selected' : '') ?>>prompt</option>
                                        <option value="1" <?php echo ($row['d_lichtreakt_2'] == 1 ? 'selected' : '') ?>>träge</option>
                                        <option value="2" <?php echo ($row['d_lichtreakt_2'] == 2 ? 'selected' : '') ?>>keine</option>
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
                                            <select class="w-100 form-select" name="d_gcs_1" id="d_gcs_1" disabled autocomplete="off">
                                                <option disabled hidden selected>---</option>
                                                <option value="0" <?php echo ($row['d_gcs_1'] == 0 ? 'selected' : '') ?>>spontan (4)</option>
                                                <option value="1" <?php echo ($row['d_gcs_1'] == 1 ? 'selected' : '') ?>>auf Aufforderung (3)</option>
                                                <option value="2" <?php echo ($row['d_gcs_1'] == 2 ? 'selected' : '') ?>>auf Schmerzreiz (2)</option>
                                                <option value="3" <?php echo ($row['d_gcs_1'] == 3 ? 'selected' : '') ?>>kein Öffnen (1)</option>
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
                                            <select class="w-100 form-select" name="d_gcs_2" id="d_gcs_2" disabled autocomplete="off">
                                                <option disabled hidden selected>---</option>
                                                <option value="0" <?php echo ($row['d_gcs_2'] == 0 ? 'selected' : '') ?>>orientiert (5)</option>
                                                <option value="1" <?php echo ($row['d_gcs_2'] == 1 ? 'selected' : '') ?>>desorientiert (4)</option>
                                                <option value="2" <?php echo ($row['d_gcs_2'] == 2 ? 'selected' : '') ?>>inadäquate Äußerungen (3)</option>
                                                <option value="3" <?php echo ($row['d_gcs_2'] == 3 ? 'selected' : '') ?>>unverstädnliche Laute (2)</option>
                                                <option value="4" <?php echo ($row['d_gcs_2'] == 4 ? 'selected' : '') ?>>keine Reaktion (1)</option>
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
                                            <select class="w-100 form-select" name="d_gcs_3" id="d_gcs_3" disabled autocomplete="off">
                                                <option disabled hidden selected>---</option>
                                                <option value="0" <?php echo ($row['d_gcs_3'] == 0 ? 'selected' : '') ?>>folg Aufforderung (6)</option>
                                                <option value="1" <?php echo ($row['d_gcs_3'] == 1 ? 'selected' : '') ?>>gezielte Abwehrbewegungen (5)</option>
                                                <option value="2" <?php echo ($row['d_gcs_3'] == 2 ? 'selected' : '') ?>>ungezielte Abwehrbewegungen (4)</option>
                                                <option value="3" <?php echo ($row['d_gcs_3'] == 3 ? 'selected' : '') ?>>Bezgesynergismen (3)</option>
                                                <option value="4" <?php echo ($row['d_gcs_3'] == 4 ? 'selected' : '') ?>>Strecksynergismen (2)</option>
                                                <option value="5" <?php echo ($row['d_gcs_3'] == 5 ? 'selected' : '') ?>>keine Reaktion (1)</option>
                                            </select>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(6)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 fw-bold">Extremitätenbewegung</div>
                                <div class="col">
                                    <select name="d_ex_1" id="d_ex_1" class="form-select w-100" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['d_ex_1'] == 0 ? 'selected' : '') ?>>stark eingeschränkt</option>
                                        <option value="2" <?php echo ($row['d_ex_1'] == 2 ? 'selected' : '') ?>>leicht eingeschränkt</option>
                                        <option value="1" <?php echo ($row['d_ex_1'] == 1 ? 'selected' : '') ?>>uneingeschränkt</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col border border-1 border-dark">
                            <!-- ------------ -->
                            <!-- VERLETZUNGEN -->
                            <!-- ------------ -->
                            <h6 class="bg-dark text-light text-center p-1">E - Entkleiden/Erweitern <em>(Exposure)</em></h6>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Blutzucker</div>
                                <div class="col"><input class="w-100 vitalparam form-control" style="display:inline; max-width: 75px" type="text" name="bz" id="bz" value="<?= $row['bz'] ?>" readonly> <small>mg/dl</small></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Temperatur</div>
                                <div class="col"><input class="w-100 vitalparam form-control" style="display:inline; max-width: 75px" type="text" name="temp" id="temp" value="<?= $row['temp'] ?>" readonly> <small>°C</small></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Kopf</div>
                                <div class="col">
                                    <select name="v_muster_k" id="v_muster_k" class="w-100 divi-v-select form-control" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['v_muster_k'] == 0 ? 'selected' : '') ?>>schwer</option>
                                        <option value="1" <?php echo ($row['v_muster_k'] == 1 ? 'selected' : '') ?>>mittel</option>
                                        <option value="2" <?php echo ($row['v_muster_k'] == 2 ? 'selected' : '') ?>>leicht</option>
                                        <option value="3" <?php echo ($row['v_muster_k'] == 3 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_k1" id="v_muster_k1" class="w-100 form-control" disabled autocomplete="off">
                                        <option value="0" <?php echo ($row['v_muster_k1'] == 0 ? 'selected' : '') ?>>---</option>
                                        <option value="1" <?php echo ($row['v_muster_k1'] == 1 ? 'selected' : '') ?>>offen</option>
                                        <option value="2" <?php echo ($row['v_muster_k1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Wirbelsäule</div>
                                <div class="col">
                                    <select name="v_muster_w" id="v_muster_w" class="w-100 divi-v-select form-control" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['v_muster_w'] == 0 ? 'selected' : '') ?>>schwer</option>
                                        <option value="1" <?php echo ($row['v_muster_w'] == 1 ? 'selected' : '') ?>>mittel</option>
                                        <option value="2" <?php echo ($row['v_muster_w'] == 2 ? 'selected' : '') ?>>leicht</option>
                                        <option value="3" <?php echo ($row['v_muster_w'] == 3 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_w1" id="v_muster_w1" class="w-100 form-control" disabled autocomplete="off">
                                        <option value="0" <?php echo ($row['v_muster_w1'] == 0 ? 'selected' : '') ?>>---</option>
                                        <option value="1" <?php echo ($row['v_muster_w1'] == 1 ? 'selected' : '') ?>>offen</option>
                                        <option value="2" <?php echo ($row['v_muster_w1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Thorax</div>
                                <div class="col">
                                    <select name="v_muster_t" id="v_muster_t" class="w-100 divi-v-select form-control" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['v_muster_t'] == 0 ? 'selected' : '') ?>>schwer</option>
                                        <option value="1" <?php echo ($row['v_muster_t'] == 1 ? 'selected' : '') ?>>mittel</option>
                                        <option value="2" <?php echo ($row['v_muster_t'] == 2 ? 'selected' : '') ?>>leicht</option>
                                        <option value="3" <?php echo ($row['v_muster_t'] == 3 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_t1" id="v_muster_t1" class="w-100 form-control" disabled autocomplete="off">
                                        <option value="0" <?php echo ($row['v_muster_t1'] == 0 ? 'selected' : '') ?>>---</option>
                                        <option value="1" <?php echo ($row['v_muster_t1'] == 1 ? 'selected' : '') ?>>offen</option>
                                        <option value="2" <?php echo ($row['v_muster_t1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Abdomen</div>
                                <div class="col">
                                    <select name="v_muster_a" id="v_muster_a" class="w-100 divi-v-select form-control" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['v_muster_a'] == 0 ? 'selected' : '') ?>>schwer</option>
                                        <option value="1" <?php echo ($row['v_muster_a'] == 1 ? 'selected' : '') ?>>mittel</option>
                                        <option value="2" <?php echo ($row['v_muster_a'] == 2 ? 'selected' : '') ?>>leicht</option>
                                        <option value="3" <?php echo ($row['v_muster_a'] == 3 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_a1" id="v_muster_a1" class="w-100 form-control" disabled autocomplete="off">
                                        <option value="0" <?php echo ($row['v_muster_a1'] == 0 ? 'selected' : '') ?>>---</option>
                                        <option value="1" <?php echo ($row['v_muster_a1'] == 1 ? 'selected' : '') ?>>offen</option>
                                        <option value="2" <?php echo ($row['v_muster_a1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Obere Extremitäten</div>
                                <div class="col">
                                    <select name="v_muster_al" id="v_muster_al" class="w-100 divi-v-select form-control" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['v_muster_al'] == 0 ? 'selected' : '') ?>>schwer</option>
                                        <option value="1" <?php echo ($row['v_muster_al'] == 1 ? 'selected' : '') ?>>mittel</option>
                                        <option value="2" <?php echo ($row['v_muster_al'] == 2 ? 'selected' : '') ?>>leicht</option>
                                        <option value="3" <?php echo ($row['v_muster_al'] == 3 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_al1" id="v_muster_al1" class="w-100 form-control" disabled autocomplete="off">
                                        <option value="0" <?php echo ($row['v_muster_al1'] == 0 ? 'selected' : '') ?>>---</option>
                                        <option value="1" <?php echo ($row['v_muster_al1'] == 1 ? 'selected' : '') ?>>offen</option>
                                        <option value="2" <?php echo ($row['v_muster_al1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 fw-bold">Untere Extremitäten</div>
                                <div class="col">
                                    <select name="v_muster_bl" id="v_muster_bl" class="w-100 divi-v-select form-control" disabled autocomplete="off">
                                        <option disabled hidden selected>---</option>
                                        <option value="0" <?php echo ($row['v_muster_bl'] == 0 ? 'selected' : '') ?>>schwer</option>
                                        <option value="1" <?php echo ($row['v_muster_bl'] == 1 ? 'selected' : '') ?>>mittel</option>
                                        <option value="2" <?php echo ($row['v_muster_bl'] == 2 ? 'selected' : '') ?>>leicht</option>
                                        <option value="3" <?php echo ($row['v_muster_bl'] == 3 ? 'selected' : '') ?>>keine</option>
                                    </select>
                                </div>
                                <div class="col-3 ms-1">
                                    <select name="v_muster_bl1" id="v_muster_bl1" class="w-100 form-control" disabled autocomplete="off">
                                        <option value="0" <?php echo ($row['v_muster_bl1'] == 0 ? 'selected' : '') ?>>---</option>
                                        <option value="1" <?php echo ($row['v_muster_bl1'] == 1 ? 'selected' : '') ?>>offen</option>
                                        <option value="2" <?php echo ($row['v_muster_bl1'] == 2 ? 'selected' : '') ?>>geschl.</option>
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
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="medis" id="medis" rows="10" class="w-100 form-control" style="resize: none" readonly><?= $row['medis'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <textarea name="diagnose" id="diagnose" rows="3" class="w-100 form-control" style="resize: none" readonly><?= $row['diagnose'] ?></textarea>
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
                                    <textarea name="anmerkungen" id="anmerkungen" rows="20" class="w-100 form-control" style="resize: none" readonly><?= $row['anmerkungen'] ?></textarea>
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
                                            <input type="checkbox" class="btn-check" id="notfallteam" name="notfallteam" value="1" <?php echo ($row['notfallteam'] == 1 ? 'checked' : '') ?> autocomplete="off" disabled>
                                            <label class="btn btn-sm btn-outline-danger w-100" for="notfallteam">Übergabe Notfallteam</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="transportverw" name="transportverw" <?php echo ($row['transportverw'] == 1 ? 'checked' : '') ?> value="1" autocomplete="off" disabled>
                                            <label class="btn btn-sm btn-outline-warning w-100" for="transportverw">Transportverweigerung</label>
                                        </div>
                                        <div class="col">
                                            <?php
                                            if ($row['nacascore'] == 0 && $row['nacascore'] != NULL) {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: I</button>';
                                            } elseif ($row['nacascore'] == 1) {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: II</button>';
                                            } elseif ($row['nacascore'] == 2) {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: III</button>';
                                            } elseif ($row['nacascore'] == 3) {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: IV</button>';
                                            } elseif ($row['nacascore'] == 4) {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: V</button>';
                                            } elseif ($row['nacascore'] == 5) {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: VI</button>';
                                            } else {
                                                echo '<button class="btn btn-sm btn-info w-100" type="button">NACA-Score: /</button>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3 fw-bold">Protokollführer</div>
                                <div class="col"><input type="text" name="pfname" id="pfname" class="w-100 form-control" value="<?= $row['pfname'] ?>" readonly></div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3 fw-bold">Notarzt</div>
                                <div class="col"><input type="text" name="naname" id="naname" class="w-100 form-control" value="<?= $row['naname'] ?>" readonly></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-3 fw-bold">Transportziel</div>
                                <div class="col"><input type="text" name="transportziel" id="transportziel" class="w-100 form-control" value="<?= $row['transportziel'] ?>" readonly></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (in_array('full_admin', $_SESSION['permissions']) || in_array('admin', $_SESSION['permissions']) || in_array('edivi_edit', $_SESSION['permissions'])) { ?>
            <!-- ------------ -->
            <!-- PRÜFUNG -->
            <!-- ------------ -->
            <button title="QM-Funktionen" id="qm-quick-menu" type="button" data-bs-toggle="modal" data-bs-target="#myModal4"><i class="fa-solid fa-ballot-check"></i></button>
            <!-- MODAL -->
            <div class="modal fade" id="myModal4" tabindex="-1" aria-labelledby="myModalLabel4" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel4">QM-Funktionen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-2 mb-1">
                                <div class="col-3 fw-bold">Gesichtet von</div>
                                <div class="col"><input style="border-radius: 0 !important" type="text" name="bearbeiter" id="bearbeiter" class="w-100 form-control" value="<?= $_SESSION['cirs_user'] ?>" required></div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3 fw-bold">Status</div>
                                <div class="col">
                                    <select name="protokoll_status" id="protokoll_status" class="form-select w-100" style="border-radius: 0 !important">
                                        <option value="0" <?php echo ($row['protokoll_status'] == 0 ? 'selected' : '') ?>>Ungesehen</option>
                                        <option value="1" <?php echo ($row['protokoll_status'] == 1 ? 'selected' : '') ?>>in Prüfung</option>
                                        <option value="2" <?php echo ($row['protokoll_status'] == 2 ? 'selected' : '') ?>>Freigegeben</option>
                                        <option value="3" <?php echo ($row['protokoll_status'] == 3 ? 'selected' : '') ?>>Ungenügend</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-3 fw-bold">Bemerkung</div>
                                <div class="col">
                                    <textarea name="qmkommentar" id="qmkommentar" rows="5" class="w-100" style="resize: none"><?= $row['qmkommentar'] ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                                <input class="btn btn-success" name="submit" type="submit" value="Speichern" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL ENDE -->
        <?php } ?>
    </form>
    <script>
        // eDIVI Buttons
        const buttons = document.querySelectorAll('.btn-divi');

        function toggleActive() {
            const isActive = this.classList.toggle('active');
            console.log(`Button ${this.innerText} is now ${isActive ? 'active' : 'inactive'}.`);
        }

        buttons.forEach(button => {
            button.addEventListener('click', toggleActive);
            console.log(`Event listener attached to button ${button.innerText}.`);
        });
    </script>
    <script>
        // eDIVI Verletzungen
        const selectElements = document.querySelectorAll(".divi-v-select");

        // Define the change event listener function
        const onChange = (selectElement) => {
            if (selectElement.value === "0") {
                selectElement.style.backgroundColor = "red";
            } else if (selectElement.value === "1") {
                selectElement.style.backgroundColor = "yellow";
            } else if (selectElement.value === "2") {
                selectElement.style.backgroundColor = "green";
            } else {
                selectElement.style.backgroundColor = "";
            }
        };

        // Call the change event listener function for each select element
        selectElements.forEach((selectElement) => {
            // Call the function initially to set the background color on page load
            onChange(selectElement);

            // Add the change event listener
            selectElement.addEventListener("change", () => {
                onChange(selectElement);
            });
        });
    </script>
</body>

</html>