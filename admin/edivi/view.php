<?php
session_start();
require_once '../../assets/php/permissions.php';

if (!isset($_SESSION['userid']) || !isset($_SESSION['permissions'])) {
    // Store the current page's URL in a session variable
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

    // Redirect the user to the login page
    header("Location: /admin/login.php");
    exit();
} else if ($notadmincheck && !$edview) {
    header("Location: /admin/index.php");
}

include '../../assets/php/mysql-con.php';

$result = mysqli_query($conn, "SELECT * FROM cirs_rd_protokolle WHERE id = " . $_GET['id']) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);
$rowamount = mysqli_num_rows($result);

if ($rowamount == 0) {
    header("Location: /admin/edivi/list.php");
}

if ($row['freigegeben'] == 1) {
    $ist_freigegeben = true;
} else {
    $ist_freigegeben = false;
}

$row['last_edit'] = date("d.m.Y H:i", strtotime($row['last_edit']));

if (isset($_POST['new']) && $_POST['new'] == 1) {
    $bearbeiter = $_POST['bearbeiter'];
    $protokoll_status = $_POST['protokoll_status'];
    $qmkommentar = $_POST['qmkommentar'];

    if ($qmkommentar != NULL) {
        $queryins = "INSERT INTO cirs_rd_prot_kommentare (protokoll_id, kommentar, bearbeiter) VALUES (" . $_GET['id'] . ", '$qmkommentar', '$bearbeiter')";
    }
    $query = "UPDATE cirs_rd_protokolle SET bearbeiter = '$bearbeiter', protokoll_status = '$protokoll_status' WHERE id = " . $_GET['id'];
    if (isset($queryins)) {
        mysqli_query($conn, $queryins);
    }
    mysqli_query($conn, $query);
    header("Refresh: 0");
}

$prot_url = "https://intra.stettbeck.de/admin/edivi/divi" . $row['id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>[#<?= $row['enr'] . "] " . $row['patname'] ?> &rsaquo; eDIVI &rsaquo; intraSB</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/divi.min.css" />
    <link rel="stylesheet" href="/assets/css/admin.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/assets/bootstrap-5.3/css/bootstrap.min.css">
    <script src="/assets/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/jquery/jquery-3.7.0.min.js"></script>
    <!-- html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/assets/favicon/site.webmanifest">
    <!-- Metas -->
    <meta name="theme-color" content="#ffaf2f" />
    <meta property="og:site_name" content="NordNetzwerk" />
    <meta property="og:url" content="<?= $prot_url ?>" />
    <meta property="og:title" content="[#<?= $row['enr'] . "] " . $row['patname'] ?> &rsaquo; eDIVI &rsaquo; intraSB" />
    <meta property="og:image" content="https://intra.stettbeck.de/assets/img/aelrd.png" />
    <meta property="og:description" content="Intranet/Verwaltungsportal der Hansestadt Stettbeck" />

</head>

<body>
    <form name="form" method="post" action="">
        <input type="hidden" name="new" value="1" />
        <div class="container-fluid" id="edivi__container">
            <?php if ($ist_freigegeben) : ?>
                <div class="container-full mb-2 edivi__notice edivi__notice-freigeber">
                    <div class="row">
                        <div class="col-1 text-end"><i class="fa-solid fa-info"></i></div>
                        <div class="col">
                            Das Protokoll wurde durch <strong><?= $row['freigeber_name'] ?></strong> am <strong><?= $row['last_edit'] ?></strong> Uhr freigegeben. Es kann nicht mehr bearbeitet werden.
                        </div>
                        <div class="col-2 d-flex align-content-center justify-content-center">
                            <?php if ($row['protokoll_status'] == 1) : ?>
                                <div class="badge bg-warning text-dark" style="line-height: var(--bs-body-line-height); border-radius: 0;">in Prüfung</div>
                            <?php elseif ($row['protokoll_status'] == 2) : ?>
                                <div class="badge bg-success" style="line-height: var(--bs-body-line-height); border-radius: 0;">Geprüft</div>
                            <?php elseif ($row['protokoll_status'] == 3) : ?>
                                <div class="badge bg-danger" style="line-height: var(--bs-body-line-height); border-radius: 0;">Ungenügend</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="container-full mb-2 edivi__notice edivi__notice-freigeber">
                    <div class="row">
                        <div class="col-1 text-end"><i class="fa-solid fa-info"></i></div>
                        <div class="col">
                            Das Protokoll wurde <u>noch nicht</u> freigegben! Es kann noch bearbeitet und geändert werden!
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($row['qmkommentar'] != NULL) : ?>
                <div class="container-full edivi__notice edivi__notice-qm">
                    <div class="row">
                        <div class="col-1 text-end"><i class="fa-solid fa-eye"></i></div>
                        <div class="col">
                            <small>QM-Kommentar von <strong><?= $row['bearbeiter'] ?></strong>:</small><br>
                            <?= $row['qmkommentar'] ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="row h-100">
                <div class="col">
                    <!-- ------------ -->
                    <!-- ! STAMMDATEN -->
                    <!-- ------------ -->
                    <div class="row shadow edivi__box">
                        <div class="col">
                            <h5 class="text-light p-1">Stammdaten</h5>
                            <div class="col">
                                <div class="row my-2">
                                    <div class="col-4 edivi__description">Name</div>
                                    <div class="col"><input type="text" name="patname" id="patname" placeholder="Max Mustermann" class="w-100 form-control" value="<?= $row['patname'] ?>"></div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Geburtsdatum</div>
                                <div class="col"><input type="date" name="patgebdat" id="patgebdat" class="w-100 form-control" value="<?= $row['patgebdat'] ?>"></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Geschlecht</div>
                                <div class="col">
                                    <div class="row">
                                        <?php
                                        if ($row['patsex'] == 0) {
                                        ?>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="0" checked> männlich</div>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="1"> weiblich</div>
                                        <?php
                                        } elseif ($row['patsex'] == 1) {
                                        ?>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="0"> männlich</div>
                                            <div class="col"><input class="form-check-input" type="radio" name="patsex" id="patsex" value="1" checked> weiblich</div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Einsatzdatum u. -zeit</div>
                                <div class="col">
                                    <input type="date" name="edatum" id="edatum" class="w-100 form-control edivi__input-check" value="<?= $row['edatum'] ?>">
                                </div>
                                <div class="col">
                                    <input type="time" name="ezeit" id="ezeit" class="w-100 form-control edivi__input-check" value="<?= $row['ezeit'] ?>">
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Einsatznummer u. -ort</div>
                                <div class="col">
                                    <input type="text" name="enr" id="enr" class="w-100 form-control" placeholder="Einsatznummer" value="<?= $row['enr'] ?>">
                                </div>
                                <div class="col">
                                    <input type="text" name="eort" id="eort" class="w-100 form-control edivi__input-check" placeholder="Einsatzort" value="<?= $row['eort'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- A - ATEMWEGE -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">A - Atemwege <em>(Airway)</em></h5>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Atemwege</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awfrei_1" name="awfrei_1" value="1" <?php echo ($row['awfrei_1'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-success w-100" for="awfrei_1">frei</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awfrei_3" name="awfrei_3" value="1" <?php echo ($row['awfrei_3'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-warning w-100" for="awfrei_3">gefährdet</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="awfrei_2" name="awfrei_2" value="1" <?php echo ($row['awfrei_2'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-danger w-100" for="awfrei_2">verlegt</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Atemwegssicherung</div>
                                <div class="col">
                                    <?php
                                    if ($row['awsicherung_neu'] == NULL) {
                                    ?>
                                        <div class="row">
                                            <div class="col">
                                                <input type="checkbox" class="btn-check" id="awsicherung_1" name="awsicherung_1" value="1" <?php echo ($row['awsicherung_1'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                                <label class="btn btn-sm btn-outline-light w-100" for="awsicherung_1">Nein</label>
                                            </div>
                                            <div class="col">
                                                <input type="checkbox" class="btn-check" id="awsicherung_2" name="awsicherung_2" value="1" <?php echo ($row['awsicherung_2'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                                <label class="btn btn-sm btn-outline-light w-100" for="awsicherung_2">Ja</label>
                                            </div>
                                        </div>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="awsicherung_neu" id="awsicherung_neu" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['awsicherung_neu'] == 0 ? 'selected' : '') ?>>keine</option>
                                            <option value="1" <?php echo ($row['awsicherung_neu'] == 1 ? 'selected' : '') ?>>Endotrachealtubus</option>
                                            <option value="2" <?php echo ($row['awsicherung_neu'] == 2 ? 'selected' : '') ?>>Larynxtubus</option>
                                            <option value="3" <?php echo ($row['awsicherung_neu'] == 3 ? 'selected' : '') ?>>Guedel- / Wendltubus</option>
                                            <option value="99" <?php echo ($row['awsicherung_neu'] == 99 ? 'selected' : '') ?>>Sonstige</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Zyanose</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="zyanose_1" name="zyanose_1" value="1" <?php echo ($row['zyanose_1'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-light w-100" for="zyanose_1">Nein</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="zyanose_2" name="zyanose_2" value="1" <?php echo ($row['zyanose_2'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-light w-100" for="zyanose_2">Ja</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">O2 Gabe</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" min="0" max="15" placeholder="" name="o2gabe" id="o2gabe" value="<?= $row['o2gabe'] ?>" style="display:inline"> <small>L/min</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- B - ATMUNG -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">B - Atmung <em>(Breathing)</em></h5>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Atmung</div>
                                <div class="col">
                                    <?php
                                    if ($row['b_symptome'] == NULL) {
                                    ?>
                                        <select name="b_symptome" id="b_symptome" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>Symptomauswahl</option>
                                            <option value="0">unauffällig</option>
                                            <option value="1">Dyspnoe</option>
                                            <option value="2">Apnoe</option>
                                            <option value="3">Schnappatmung</option>
                                            <option value="4">Andere pathol.</option>
                                            <option value="99">nicht untersucht</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="b_symptome" id="b_symptome" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>Symptomauswahl</option>
                                            <option value="0" <?php echo ($row['b_symptome'] == 0 ? 'selected' : '') ?>>unauffällig</option>
                                            <option value="1" <?php echo ($row['b_symptome'] == 1 ? 'selected' : '') ?>>Dyspnoe</option>
                                            <option value="2" <?php echo ($row['b_symptome'] == 2 ? 'selected' : '') ?>>Apnoe</option>
                                            <option value="3" <?php echo ($row['b_symptome'] == 3 ? 'selected' : '') ?>>Schnappatmung</option>
                                            <option value="4" <?php echo ($row['b_symptome'] == 4 ? 'selected' : '') ?>>Andere pathol.</option>
                                            <option value="99" <?php echo ($row['b_symptome'] == 99 ? 'selected' : '') ?>>nicht untersucht</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Auskultation</div>
                                <div class="col">
                                    <?php
                                    if ($row['b_auskult'] == NULL) {
                                    ?>
                                        <select name="b_auskult" id="b_auskult" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">unauffällig</option>
                                            <option value="1">Spastik</option>
                                            <option value="2">Stridor</option>
                                            <option value="3">Rasselgeräusche</option>
                                            <option value="4">Andere pathol.</option>
                                            <option value="99">nicht untersucht</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="b_auskult" id="b_auskult" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['b_auskult'] == 0 ? 'selected' : '') ?>>unauffällig</option>
                                            <option value="1" <?php echo ($row['b_auskult'] == 1 ? 'selected' : '') ?>>Spastik</option>
                                            <option value="2" <?php echo ($row['b_auskult'] == 2 ? 'selected' : '') ?>>Stridor</option>
                                            <option value="3" <?php echo ($row['b_auskult'] == 3 ? 'selected' : '') ?>>Rasselgeräusche</option>
                                            <option value="4" <?php echo ($row['b_auskult'] == 4 ? 'selected' : '') ?>>Andere pathol.</option>
                                            <option value="99" <?php echo ($row['b_auskult'] == 99 ? 'selected' : '') ?>>nicht untersucht</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Beatmung</div>
                                <div class="col">
                                    <?php
                                    if ($row['b_beatmung'] == NULL) {
                                    ?>
                                        <select name="b_beatmung" id="b_beatmung" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="4">keine</option>
                                            <option value="0">Spontanatmung</option>
                                            <option value="1">Assistierte Beatmung</option>
                                            <option value="2">Kontrollierte Beatmung</option>
                                            <option value="3">Maschinelle Beatmung</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="b_beatmung" id="b_beatmung" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="4" <?php echo ($row['b_beatmung'] == 4 ? 'selected' : '') ?>>keine</option>
                                            <option value="0" <?php echo ($row['b_beatmung'] == 0 ? 'selected' : '') ?>>Spontanatmung</option>
                                            <option value="1" <?php echo ($row['b_beatmung'] == 1 ? 'selected' : '') ?>>Assistierte Beatmung</option>
                                            <option value="2" <?php echo ($row['b_beatmung'] == 2 ? 'selected' : '') ?>>Kontrollierte Beatmung</option>
                                            <option value="3" <?php echo ($row['b_beatmung'] == 3 ? 'selected' : '') ?>>Maschinelle Beatmung</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">SpO2</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" placeholder="" name="spo2" id="spo2" value="<?= $row['spo2'] ?>" style="display:inline"> <small>%</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Atemfrequenz</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="atemfreq" id="atemfreq" value="<?= $row['atemfreq'] ?>" style="display:inline"> <small>/min</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">etCO2</div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="etco2" id="etco2" value="<?= $row['etco2'] ?>" style="display:inline"> <small>mmHg</small></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- C - KREISLAUF -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">C - Kreislauf <em>(Circulation)</em></h5>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Kreislauf</div>
                                <div class="col">
                                    <?php
                                    if ($row['c_kreislauf'] == NULL) {
                                    ?>
                                        <select name="c_kreislauf" id="c_kreislauf" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">stabil</option>
                                            <option value="1">instabil</option>
                                            <option value="2">nicht beurteilbar</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_kreislauf" id="c_kreislauf" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['c_kreislauf'] == 0 ? 'selected' : '') ?>>stabil</option>
                                            <option value="1" <?php echo ($row['c_kreislauf'] == 1 ? 'selected' : '') ?>>instabil</option>
                                            <option value="2" <?php echo ($row['c_kreislauf'] == 2 ? 'selected' : '') ?>>nicht beurteilbar</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">RR</div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col"><small class="fw-bold">sys</small> <input class="w-100 vitalparam form-control" type="text" name="rrsys" id="rrsys" value="<?= $row['rrsys'] ?>" style="display:inline"> <small class="fw-bold">/ dias</small> <input class="w-100 vitalparam form-control" type="text" name="rrdias" id="rrdias" value="<?= $row['rrdias'] ?>" style="display:inline"> <small>mmHg</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">HF</div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col"><input class="w-100 vitalparam form-control" type="text" name="herzfreq" id="herzfreq" value="<?= $row['herzfreq'] ?>" style="display:inline"> <small>/min</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">EKG</div>
                                <div class="col">
                                    <?php
                                    if ($row['c_ekg'] == NULL) {
                                    ?>
                                        <select name="c_ekg" id="c_ekg" class="w-100 form-select edivi__input-check">
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
                                            <option value="99">nicht erhoben</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_ekg" id="c_ekg" class="w-100 form-select edivi__input-check" autocomplete="off">
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
                                            <option value="99" <?php echo ($row['c_ekg'] == 99 ? 'selected' : '') ?>>nicht erhoben</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Zugänge</div>
                                <div class="col-2">
                                    <?php
                                    if ($row['c_zugang_art_1'] == NULL) {
                                    ?>
                                        <select name="c_zugang_art_1" id="c_zugang_art_1" class="w-100 form-select">
                                            <option selected>Art</option>
                                            <option value="3">pvk</option>
                                            <option value="1">zvk</option>
                                            <option value="2">i.o.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_zugang_art_1" id="c_zugang_art_1" class="w-100 form-select" autocomplete="off">
                                            <option selected>Art</option>
                                            <option value="3" <?php echo ($row['c_zugang_art_1'] == 3 ? 'selected' : '') ?>>pvk</option>
                                            <option value="1" <?php echo ($row['c_zugang_art_1'] == 1 ? 'selected' : '') ?>>zvk</option>
                                            <option value="2" <?php echo ($row['c_zugang_art_1'] == 2 ? 'selected' : '') ?>>i.o.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-2 edivi__zugang-container">
                                    <?php
                                    if ($row['c_zugang_gr_1'] == NULL) {
                                    ?>
                                        <select name="c_zugang_gr_1" id="c_zugang_gr_1" class="w-100 form-select edivi__zugang-list">
                                            <option selected>Gr.</option>
                                            <option disabled>-- i.v. --</option>
                                            <option value="10">G24</option>
                                            <option value="1">G22</option>
                                            <option value="2">G20</option>
                                            <option value="3">G18</option>
                                            <option value="4">G17</option>
                                            <option value="5">G16</option>
                                            <option value="6">G14</option>
                                            <option disabled>-- i.o. --</option>
                                            <option value="7">15mm</option>
                                            <option value="8">25mm</option>
                                            <option value="9">45mm</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_zugang_gr_1" id="c_zugang_gr_1" class="w-100 form-select edivi__zugang-list" autocomplete="off">
                                            <option selected>Gr.</option>
                                            <option disabled>-- i.v. --</option>
                                            <option value="10" <?php echo ($row['c_zugang_gr_1'] == 10 ? 'selected' : '') ?>>G24</option>
                                            <option value="1" <?php echo ($row['c_zugang_gr_1'] == 1 ? 'selected' : '') ?>>G22</option>
                                            <option value="2" <?php echo ($row['c_zugang_gr_1'] == 2 ? 'selected' : '') ?>>G20</option>
                                            <option value="3" <?php echo ($row['c_zugang_gr_1'] == 3 ? 'selected' : '') ?>>G18</option>
                                            <option value="4" <?php echo ($row['c_zugang_gr_1'] == 4 ? 'selected' : '') ?>>G17</option>
                                            <option value="5" <?php echo ($row['c_zugang_gr_1'] == 5 ? 'selected' : '') ?>>G16</option>
                                            <option value="6" <?php echo ($row['c_zugang_gr_1'] == 6 ? 'selected' : '') ?>>G14</option>
                                            <option disabled>-- i.o. --</option>
                                            <option value="7" <?php echo ($row['c_zugang_gr_1'] == 7 ? 'selected' : '') ?>>15mm</option>
                                            <option value="8" <?php echo ($row['c_zugang_gr_1'] == 8 ? 'selected' : '') ?>>25mm</option>
                                            <option value="9" <?php echo ($row['c_zugang_gr_1'] == 9 ? 'selected' : '') ?>>45mm</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col">
                                    <input type="text" name="c_zugang_ort_1" id="c_zugang_ort_1" class="w-100 form-control" placeholder="Ort" value="<?= $row['c_zugang_ort_1'] ?>">
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description"></div>
                                <div class="col-2">
                                    <?php
                                    if ($row['c_zugang_art_2'] == NULL) {
                                    ?>
                                        <select name="c_zugang_art_2" id="c_zugang_art_2" class="w-100 form-select">
                                            <option selected>Art</option>
                                            <option value="3">pvk</option>
                                            <option value="1">zvk</option>
                                            <option value="2">i.o.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_zugang_art_2" id="c_zugang_art_2" class="w-100 form-select" autocomplete="off">
                                            <option selected>Art</option>
                                            <option value="3" <?php echo ($row['c_zugang_art_2'] == 3 ? 'selected' : '') ?>>pvk</option>
                                            <option value="1" <?php echo ($row['c_zugang_art_2'] == 1 ? 'selected' : '') ?>>zvk</option>
                                            <option value="2" <?php echo ($row['c_zugang_art_2'] == 2 ? 'selected' : '') ?>>i.o.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-2 edivi__zugang-container">
                                    <?php
                                    if ($row['c_zugang_gr_2'] == NULL) {
                                    ?>
                                        <select name="c_zugang_gr_2" id="c_zugang_gr_2" class="w-100 form-select edivi__zugang-list">
                                            <option selected>Gr.</option>
                                            <option disabled>-- i.v. --</option>
                                            <option value="10">G24</option>
                                            <option value="1">G22</option>
                                            <option value="2">G20</option>
                                            <option value="3">G18</option>
                                            <option value="4">G17</option>
                                            <option value="5">G16</option>
                                            <option value="6">G14</option>
                                            <option disabled>-- i.o. --</option>
                                            <option value="7">15mm</option>
                                            <option value="8">25mm</option>
                                            <option value="9">45mm</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_zugang_gr_2" id="c_zugang_gr_2" class="w-100 form-select edivi__zugang-list" autocomplete="off">
                                            <option selected>Gr.</option>
                                            <option disabled>-- i.v. --</option>
                                            <option value="10" <?php echo ($row['c_zugang_gr_2'] == 10 ? 'selected' : '') ?>>G24</option>
                                            <option value="1" <?php echo ($row['c_zugang_gr_2'] == 1 ? 'selected' : '') ?>>G22</option>
                                            <option value="2" <?php echo ($row['c_zugang_gr_2'] == 2 ? 'selected' : '') ?>>G20</option>
                                            <option value="3" <?php echo ($row['c_zugang_gr_2'] == 3 ? 'selected' : '') ?>>G18</option>
                                            <option value="4" <?php echo ($row['c_zugang_gr_2'] == 4 ? 'selected' : '') ?>>G17</option>
                                            <option value="5" <?php echo ($row['c_zugang_gr_2'] == 5 ? 'selected' : '') ?>>G16</option>
                                            <option value="6" <?php echo ($row['c_zugang_gr_2'] == 6 ? 'selected' : '') ?>>G14</option>
                                            <option disabled>-- i.o. --</option>
                                            <option value="7" <?php echo ($row['c_zugang_gr_2'] == 7 ? 'selected' : '') ?>>15mm</option>
                                            <option value="8" <?php echo ($row['c_zugang_gr_2'] == 8 ? 'selected' : '') ?>>25mm</option>
                                            <option value="9" <?php echo ($row['c_zugang_gr_2'] == 9 ? 'selected' : '') ?>>45mm</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col">
                                    <input type="text" name="c_zugang_ort_2" id="c_zugang_ort_2" class="w-100 form-control" placeholder="Ort" value="<?= $row['c_zugang_ort_2'] ?>">
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description"></div>
                                <div class="col-2">
                                    <?php
                                    if ($row['c_zugang_art_3'] == NULL) {
                                    ?>
                                        <select name="c_zugang_art_3" id="c_zugang_art_3" class="w-100 form-select">
                                            <option selected>Art</option>
                                            <option value="3">pvk</option>
                                            <option value="1">zvk</option>
                                            <option value="2">i.o.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_zugang_art_3" id="c_zugang_art_3" class="w-100 form-select" autocomplete="off">
                                            <option selected>Art</option>
                                            <option value="3" <?php echo ($row['c_zugang_art_3'] == 3 ? 'selected' : '') ?>>pvk</option>
                                            <option value="1" <?php echo ($row['c_zugang_art_3'] == 1 ? 'selected' : '') ?>>zvk</option>
                                            <option value="2" <?php echo ($row['c_zugang_art_3'] == 2 ? 'selected' : '') ?>>i.o.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-2 edivi__zugang-container">
                                    <?php
                                    if ($row['c_zugang_gr_3'] == NULL) {
                                    ?>
                                        <select name="c_zugang_gr_3" id="c_zugang_gr_3" class="w-100 form-select edivi__zugang-list">
                                            <option selected>Gr.</option>
                                            <option disabled>-- i.v. --</option>
                                            <option value="10">G24</option>
                                            <option value="1">G22</option>
                                            <option value="2">G20</option>
                                            <option value="3">G18</option>
                                            <option value="4">G17</option>
                                            <option value="5">G16</option>
                                            <option value="6">G14</option>
                                            <option disabled>-- i.o. --</option>
                                            <option value="7">15mm</option>
                                            <option value="8">25mm</option>
                                            <option value="9">45mm</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="c_zugang_gr_3" id="c_zugang_gr_3" class="w-100 form-select edivi__zugang-list" autocomplete="off">
                                            <option selected>Gr.</option>
                                            <option disabled>-- i.v. --</option>
                                            <option value="10" <?php echo ($row['c_zugang_gr_3'] == 10 ? 'selected' : '') ?>>G24</option>
                                            <option value="1" <?php echo ($row['c_zugang_gr_3'] == 1 ? 'selected' : '') ?>>G22</option>
                                            <option value="2" <?php echo ($row['c_zugang_gr_3'] == 2 ? 'selected' : '') ?>>G20</option>
                                            <option value="3" <?php echo ($row['c_zugang_gr_3'] == 3 ? 'selected' : '') ?>>G18</option>
                                            <option value="4" <?php echo ($row['c_zugang_gr_3'] == 4 ? 'selected' : '') ?>>G17</option>
                                            <option value="5" <?php echo ($row['c_zugang_gr_3'] == 5 ? 'selected' : '') ?>>G16</option>
                                            <option value="6" <?php echo ($row['c_zugang_gr_3'] == 6 ? 'selected' : '') ?>>G14</option>
                                            <option disabled>-- i.o. --</option>
                                            <option value="7" <?php echo ($row['c_zugang_gr_3'] == 7 ? 'selected' : '') ?>>15mm</option>
                                            <option value="8" <?php echo ($row['c_zugang_gr_3'] == 8 ? 'selected' : '') ?>>25mm</option>
                                            <option value="9" <?php echo ($row['c_zugang_gr_3'] == 9 ? 'selected' : '') ?>>45mm</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col">
                                    <input type="text" name="c_zugang_ort_3" id="c_zugang_ort_3" class="w-100 form-control" placeholder="Ort" value="<?= $row['c_zugang_ort_3'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mx-2">
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- D - NEUROLOGIE -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">D - Neurologie <em>(Disability)</em></h5>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Bewusstseinslage</div>
                                <div class="col">
                                    <?php
                                    if ($row['d_bewusstsein'] == NULL) {
                                    ?>
                                        <select name="d_bewusstsein" id="d_bewusstsein" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">wach</option>
                                            <option value="1">somnolent</option>
                                            <option value="2">sopor</option>
                                            <option value="3">komatös</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="d_bewusstsein" id="d_bewusstsein" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['d_bewusstsein'] == 0 ? 'selected' : '') ?>>wach</option>
                                            <option value="1" <?php echo ($row['d_bewusstsein'] == 1 ? 'selected' : '') ?>>somnolent</option>
                                            <option value="2" <?php echo ($row['d_bewusstsein'] == 2 ? 'selected' : '') ?>>sopor</option>
                                            <option value="3" <?php echo ($row['d_bewusstsein'] == 3 ? 'selected' : '') ?>>komatös</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Pupillenweite</div>
                                <div class="col">
                                    <?Php if ($row['d_pupillenw_1'] == NULL) {
                                    ?>
                                        <small>li</small> <select name="d_pupillenw_1" id="d_pupillenw_1" class="form-select edivi__input-check" style="display:inline; max-width: 150px">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">entrundet</option>
                                            <option value="1">weit</option>
                                            <option value="2">mittel</option>
                                            <option value="3">eng</option>
                                            <option value="99">n. unters.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <small>li</small> <select name="d_pupillenw_1" id="d_pupillenw_1" class="form-select edivi__input-check" style="display:inline; max-width: 150px" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['d_pupillenw_1'] == 0 ? 'selected' : '') ?>>entrundet</option>
                                            <option value="1" <?php echo ($row['d_pupillenw_1'] == 1 ? 'selected' : '') ?>>weit</option>
                                            <option value="2" <?php echo ($row['d_pupillenw_1'] == 2 ? 'selected' : '') ?>>mittel</option>
                                            <option value="3" <?php echo ($row['d_pupillenw_1'] == 3 ? 'selected' : '') ?>>eng</option>
                                            <option value="99" <?php echo ($row['d_pupillenw_1'] == 99 ? 'selected' : '') ?>>n. unters.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col">
                                    <?Php if ($row['d_pupillenw_2'] == NULL) {
                                    ?>
                                        <small>re</small> <select name="d_pupillenw_2" id="d_pupillenw_2" class="form-select edivi__input-check" style="display:inline; max-width: 150px">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">entrundet</option>
                                            <option value="1">weit</option>
                                            <option value="2">mittel</option>
                                            <option value="3">eng</option>
                                            <option value="99">n. unters.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <small>re</small> <select name="d_pupillenw_2" id="d_pupillenw_2" class="form-select edivi__input-check" style="display:inline; max-width: 150px" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['d_pupillenw_2'] == 0 ? 'selected' : '') ?>>entrundet</option>
                                            <option value="1" <?php echo ($row['d_pupillenw_2'] == 1 ? 'selected' : '') ?>>weit</option>
                                            <option value="2" <?php echo ($row['d_pupillenw_2'] == 2 ? 'selected' : '') ?>>mittel</option>
                                            <option value="3" <?php echo ($row['d_pupillenw_2'] == 3 ? 'selected' : '') ?>>eng</option>
                                            <option value="99" <?php echo ($row['d_pupillenw_2'] == 99 ? 'selected' : '') ?>>n. unters.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Lichtreaktion</div>
                                <div class="col">
                                    <?php
                                    if ($row['d_lichtreakt_1'] == NULL) {
                                    ?>
                                        <small>li</small> <select name="d_lichtreakt_1" id="d_lichtreakt_1" class="form-select edivi__input-check" style="display:inline; max-width: 150px">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">prompt</option>
                                            <option value="1">träge</option>
                                            <option value="2">keine</option>
                                            <option value="99">n. unters.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <small>li</small> <select name="d_lichtreakt_1" id="d_lichtreakt_1" class="form-select edivi__input-check" style="display:inline; max-width: 150px" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['d_lichtreakt_1'] == 0 ? 'selected' : '') ?>>prompt</option>
                                            <option value="1" <?php echo ($row['d_lichtreakt_1'] == 1 ? 'selected' : '') ?>>träge</option>
                                            <option value="2" <?php echo ($row['d_lichtreakt_1'] == 2 ? 'selected' : '') ?>>keine</option>
                                            <option value="99" <?php echo ($row['d_lichtreakt_1'] == 99 ? 'selected' : '') ?>>n. unters.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col">
                                    <?php
                                    if ($row['d_lichtreakt_2'] == NULL) {
                                    ?>
                                        <small>re</small> <select name="d_lichtreakt_2" id="d_lichtreakt_2" class="form-select edivi__input-check" style="display:inline; max-width: 150px">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">prompt</option>
                                            <option value="1">träge</option>
                                            <option value="2">keine</option>
                                            <option value="99">n. unters.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <small>re</small> <select name="d_lichtreakt_2" id="d_lichtreakt_2" class="form-select edivi__input-check" style="display:inline; max-width: 150px" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['d_lichtreakt_2'] == 0 ? 'selected' : '') ?>>prompt</option>
                                            <option value="1" <?php echo ($row['d_lichtreakt_2'] == 1 ? 'selected' : '') ?>>träge</option>
                                            <option value="2" <?php echo ($row['d_lichtreakt_2'] == 2 ? 'selected' : '') ?>>keine</option>
                                            <option value="99" <?php echo ($row['d_lichtreakt_2'] == 99 ? 'selected' : '') ?>>n. unters.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col fw-bold">Glasgow Coma Scale</div>
                            </div>
                            <div class="row ms-1">
                                <div class="col-4 edivi__description"><small>Augen öffnen</small></div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col">
                                            <?php
                                            if ($row['d_gcs_1'] == NULL) {
                                            ?>
                                                <select class="w-100 form-select edivi__input-check" name="d_gcs_1" id="d_gcs_1">
                                                    <option disabled hidden selected>---</option>
                                                    <option value="0">spontan (4)</option>
                                                    <option value="1">auf Aufforderung (3)</option>
                                                    <option value="2">auf Schmerzreiz (2)</option>
                                                    <option value="3">kein Öffnen (1)</option>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <select class="w-100 form-select edivi__input-check" name="d_gcs_1" id="d_gcs_1" autocomplete="off">
                                                    <option disabled hidden selected>---</option>
                                                    <option value="0" <?php echo ($row['d_gcs_1'] == 0 ? 'selected' : '') ?>>spontan (4)</option>
                                                    <option value="1" <?php echo ($row['d_gcs_1'] == 1 ? 'selected' : '') ?>>auf Aufforderung (3)</option>
                                                    <option value="2" <?php echo ($row['d_gcs_1'] == 2 ? 'selected' : '') ?>>auf Schmerzreiz (2)</option>
                                                    <option value="3" <?php echo ($row['d_gcs_1'] == 3 ? 'selected' : '') ?>>kein Öffnen (1)</option>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(4)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-1">
                                <div class="col-4 edivi__description"><small>beste verbale Reaktion</small></div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col">
                                            <?php
                                            if ($row['d_gcs_2'] == NULL) {
                                            ?>
                                                <select class="w-100 form-select edivi__input-check" name="d_gcs_2" id="d_gcs_2">
                                                    <option disabled hidden selected>---</option>
                                                    <option value="0">orientiert (5)</option>
                                                    <option value="1">desorientiert (4)</option>
                                                    <option value="2">inadäquate Äußerungen (3)</option>
                                                    <option value="3">unverständliche Laute (2)</option>
                                                    <option value="4">keine Reaktion (1)</option>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <select class="w-100 form-select edivi__input-check" name="d_gcs_2" id="d_gcs_2" autocomplete="off">
                                                    <option disabled hidden selected>---</option>
                                                    <option value="0" <?php echo ($row['d_gcs_2'] == 0 ? 'selected' : '') ?>>orientiert (5)</option>
                                                    <option value="1" <?php echo ($row['d_gcs_2'] == 1 ? 'selected' : '') ?>>desorientiert (4)</option>
                                                    <option value="2" <?php echo ($row['d_gcs_2'] == 2 ? 'selected' : '') ?>>inadäquate Äußerungen (3)</option>
                                                    <option value="3" <?php echo ($row['d_gcs_2'] == 3 ? 'selected' : '') ?>>unverständliche Laute (2)</option>
                                                    <option value="4" <?php echo ($row['d_gcs_2'] == 4 ? 'selected' : '') ?>>keine Reaktion (1)</option>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(5)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row ms-1">
                                <div class="col-4 edivi__description"><small>beste motorische Reaktion</small></div>
                                <div class="col">
                                    <div class="row mb-1">
                                        <div class="col">
                                            <?php
                                            if ($row['d_gcs_3'] == NULL) {
                                            ?>
                                                <select class="w-100 form-select edivi__input-check" name="d_gcs_3" id="d_gcs_3">
                                                    <option disabled hidden selected>---</option>
                                                    <option value="0">folgt Aufforderung (6)</option>
                                                    <option value="1">gezielte Abwehrbewegungen (5)</option>
                                                    <option value="2">ungezielte Abwehrbewegungen (4)</option>
                                                    <option value="3">Beugesynergismen (3)</option>
                                                    <option value="4">Strecksynergismen (2)</option>
                                                    <option value="5">keine Reaktion (1)</option>
                                                </select>
                                            <?php
                                            } else {
                                            ?>
                                                <select class="w-100 form-select edivi__input-check" name="d_gcs_3" id="d_gcs_3" autocomplete="off">
                                                    <option disabled hidden selected>---</option>
                                                    <option value="0" <?php echo ($row['d_gcs_3'] == 0 ? 'selected' : '') ?>>folgt Aufforderung (6)</option>
                                                    <option value="1" <?php echo ($row['d_gcs_3'] == 1 ? 'selected' : '') ?>>gezielte Abwehrbewegungen (5)</option>
                                                    <option value="2" <?php echo ($row['d_gcs_3'] == 2 ? 'selected' : '') ?>>ungezielte Abwehrbewegungen (4)</option>
                                                    <option value="3" <?php echo ($row['d_gcs_3'] == 3 ? 'selected' : '') ?>>Beugesynergismen (3)</option>
                                                    <option value="4" <?php echo ($row['d_gcs_3'] == 4 ? 'selected' : '') ?>>Strecksynergismen (2)</option>
                                                    <option value="5" <?php echo ($row['d_gcs_3'] == 5 ? 'selected' : '') ?>>keine Reaktion (1)</option>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="col-2"><small class="fw-bold">(6)</small></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 edivi__description">Extremitätenbewegung</div>
                                <div class="col">
                                    <?php
                                    if ($row['d_ex_1'] == NULL) {
                                    ?>
                                        <select name="d_ex_1" id="d_ex_1" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">stark eingeschränkt</option>
                                            <option value="2">leicht eingeschränkt</option>
                                            <option value="1">uneingeschränkt</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="d_ex_1" id="d_ex_1" class="w-100 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['d_ex_1'] == 0 ? 'selected' : '') ?>>stark eingeschränkt</option>
                                            <option value="2" <?php echo ($row['d_ex_1'] == 2 ? 'selected' : '') ?>>leicht eingeschränkt</option>
                                            <option value="1" <?php echo ($row['d_ex_1'] == 1 ? 'selected' : '') ?>>uneingeschränkt</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="mb-3"></div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- EXPOSURE -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">E - Entkleiden/Erweitern <em>(Exposure)</em></h5>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Blutzucker</div>
                                <div class="col"><input class="w-100 vitalparam form-control" type="text" name="bz" id="bz" value="<?= $row['bz'] ?>" style="display:inline; max-width: 75px"> <small>mg/dl</small></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Temperatur</div>
                                <div class="col"><input class="w-100 vitalparam form-control" type="text" name="temp" id="temp" value="<?= $row['temp'] ?>" style="display:inline; max-width: 75px"> <small>°C</small></div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Kopf</div>
                                <div class="col edivi__verletzungen-col">
                                    <?php
                                    if ($row['v_muster_k'] == NULL) {
                                    ?>
                                        <select name="v_muster_k" id="v_muster_k" class="w-100 edivi__verletzungen form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">schwer</option>
                                            <option value="1">mittel</option>
                                            <option value="2">leicht</option>
                                            <option value="3">keine</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_k" id="v_muster_k" class="w-100 edivi__verletzungen form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['v_muster_k'] == 0 ? 'selected' : '') ?>>schwer</option>
                                            <option value="1" <?php echo ($row['v_muster_k'] == 1 ? 'selected' : '') ?>>mittel</option>
                                            <option value="2" <?php echo ($row['v_muster_k'] == 2 ? 'selected' : '') ?>>leicht</option>
                                            <option value="3" <?php echo ($row['v_muster_k'] == 3 ? 'selected' : '') ?>>keine</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-3 ms-1">
                                    <?php if ($row['v_muster_k1'] == NULL) {
                                    ?>
                                        <select name="v_muster_k1" id="v_muster_k1" class="w-100 form-select">
                                            <option value="0" selected>---</option>
                                            <option value="1">offen</option>
                                            <option value="2">geschl.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_k1" id="v_muster_k1" class="w-100 form-select" autocomplete="off">
                                            <option value="0" <?php echo ($row['v_muster_k1'] == 0 ? 'selected' : '') ?>>---</option>
                                            <option value="1" <?php echo ($row['v_muster_k1'] == 1 ? 'selected' : '') ?>>offen</option>
                                            <option value="2" <?php echo ($row['v_muster_k1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Wirbelsäule</div>
                                <div class="col edivi__verletzungen-col">
                                    <?php
                                    if ($row['v_muster_w'] == NULL) {
                                    ?>
                                        <select name="v_muster_w" id="v_muster_w" class="w-100 edivi__verletzungen form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">schwer</option>
                                            <option value="1">mittel</option>
                                            <option value="2">leicht</option>
                                            <option value="3">keine</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_w" id="v_muster_w" class="w-100 edivi__verletzungen form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['v_muster_w'] == 0 ? 'selected' : '') ?>>schwer</option>
                                            <option value="1" <?php echo ($row['v_muster_w'] == 1 ? 'selected' : '') ?>>mittel</option>
                                            <option value="2" <?php echo ($row['v_muster_w'] == 2 ? 'selected' : '') ?>>leicht</option>
                                            <option value="3" <?php echo ($row['v_muster_w'] == 3 ? 'selected' : '') ?>>keine</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-3 ms-1">
                                    <?php if ($row['v_muster_w1'] == NULL) {
                                    ?>
                                        <select name="v_muster_w1" id="v_muster_w1" class="w-100 form-select">
                                            <option value="0" selected>---</option>
                                            <option value="1">offen</option>
                                            <option value="2">geschl.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_w1" id="v_muster_w1" class="w-100 form-select" autocomplete="off">
                                            <option value="0" <?php echo ($row['v_muster_w1'] == 0 ? 'selected' : '') ?>>---</option>
                                            <option value="1" <?php echo ($row['v_muster_w1'] == 1 ? 'selected' : '') ?>>offen</option>
                                            <option value="2" <?php echo ($row['v_muster_w1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Thorax</div>
                                <div class="col edivi__verletzungen-col">
                                    <?php
                                    if ($row['v_muster_t'] == NULL) {
                                    ?>
                                        <select name="v_muster_t" id="v_muster_t" class="w-100 edivi__verletzungen form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">schwer</option>
                                            <option value="1">mittel</option>
                                            <option value="2">leicht</option>
                                            <option value="3">keine</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_t" id="v_muster_t" class="w-100 edivi__verletzungen form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['v_muster_t'] == 0 ? 'selected' : '') ?>>schwer</option>
                                            <option value="1" <?php echo ($row['v_muster_t'] == 1 ? 'selected' : '') ?>>mittel</option>
                                            <option value="2" <?php echo ($row['v_muster_t'] == 2 ? 'selected' : '') ?>>leicht</option>
                                            <option value="3" <?php echo ($row['v_muster_t'] == 3 ? 'selected' : '') ?>>keine</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-3 ms-1">
                                    <?php if ($row['v_muster_t1'] == NULL) {
                                    ?>
                                        <select name="v_muster_t1" id="v_muster_t1" class="w-100 form-select">
                                            <option value="0" selected>---</option>
                                            <option value="1">offen</option>
                                            <option value="2">geschl.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_t1" id="v_muster_t1" class="w-100 form-select" autocomplete="off">
                                            <option value="0" <?php echo ($row['v_muster_t1'] == 0 ? 'selected' : '') ?>>---</option>
                                            <option value="1" <?php echo ($row['v_muster_t1'] == 1 ? 'selected' : '') ?>>offen</option>
                                            <option value="2" <?php echo ($row['v_muster_t1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Abdomen</div>
                                <div class="col edivi__verletzungen-col">
                                    <?php
                                    if ($row['v_muster_a'] == NULL) {
                                    ?>
                                        <select name="v_muster_a" id="v_muster_a" class="w-100 edivi__verletzungen form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">schwer</option>
                                            <option value="1">mittel</option>
                                            <option value="2">leicht</option>
                                            <option value="3">keine</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_a" id="v_muster_a" class="w-100 edivi__verletzungen form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['v_muster_a'] == 0 ? 'selected' : '') ?>>schwer</option>
                                            <option value="1" <?php echo ($row['v_muster_a'] == 1 ? 'selected' : '') ?>>mittel</option>
                                            <option value="2" <?php echo ($row['v_muster_a'] == 2 ? 'selected' : '') ?>>leicht</option>
                                            <option value="3" <?php echo ($row['v_muster_a'] == 3 ? 'selected' : '') ?>>keine</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-3 ms-1">
                                    <?php if ($row['v_muster_a1'] == NULL) {
                                    ?>
                                        <select name="v_muster_a1" id="v_muster_a1" class="w-100 form-select">
                                            <option value="0" selected>---</option>
                                            <option value="1">offen</option>
                                            <option value="2">geschl.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_a1" id="v_muster_a1" class="w-100 form-select" autocomplete="off">
                                            <option value="0" <?php echo ($row['v_muster_a1'] == 0 ? 'selected' : '') ?>>---</option>
                                            <option value="1" <?php echo ($row['v_muster_a1'] == 1 ? 'selected' : '') ?>>offen</option>
                                            <option value="2" <?php echo ($row['v_muster_a1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Obere Extremitäten</div>
                                <div class="col edivi__verletzungen-col">
                                    <?php
                                    if ($row['v_muster_al'] == NULL) {
                                    ?>
                                        <select name="v_muster_al" id="v_muster_al" class="w-100 edivi__verletzungen form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">schwer</option>
                                            <option value="1">mittel</option>
                                            <option value="2">leicht</option>
                                            <option value="3">keine</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_al" id="v_muster_al" class="w-100 edivi__verletzungen form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['v_muster_al'] == 0 ? 'selected' : '') ?>>schwer</option>
                                            <option value="1" <?php echo ($row['v_muster_al'] == 1 ? 'selected' : '') ?>>mittel</option>
                                            <option value="2" <?php echo ($row['v_muster_al'] == 2 ? 'selected' : '') ?>>leicht</option>
                                            <option value="3" <?php echo ($row['v_muster_al'] == 3 ? 'selected' : '') ?>>keine</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-3 ms-1">
                                    <?php if ($row['v_muster_al1'] == NULL) {
                                    ?>
                                        <select name="v_muster_al1" id="v_muster_al1" class="w-100 form-select">
                                            <option value="0" selected>---</option>
                                            <option value="1">offen</option>
                                            <option value="2">geschl.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_al1" id="v_muster_al1" class="w-100 form-select" autocomplete="off">
                                            <option value="0" <?php echo ($row['v_muster_al1'] == 0 ? 'selected' : '') ?>>---</option>
                                            <option value="1" <?php echo ($row['v_muster_al1'] == 1 ? 'selected' : '') ?>>offen</option>
                                            <option value="2" <?php echo ($row['v_muster_al1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row my-2">
                                <div class="col-4 edivi__description">Untere Extremitäten</div>
                                <div class="col edivi__verletzungen-col">
                                    <?php
                                    if ($row['v_muster_bl'] == NULL) {
                                    ?>
                                        <select name="v_muster_bl" id="v_muster_bl" class="w-100 edivi__verletzungen form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0">schwer</option>
                                            <option value="1">mittel</option>
                                            <option value="2">leicht</option>
                                            <option value="3">keine</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_bl" id="v_muster_bl" class="w-100 edivi__verletzungen form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['v_muster_bl'] == 0 ? 'selected' : '') ?>>schwer</option>
                                            <option value="1" <?php echo ($row['v_muster_bl'] == 1 ? 'selected' : '') ?>>mittel</option>
                                            <option value="2" <?php echo ($row['v_muster_bl'] == 2 ? 'selected' : '') ?>>leicht</option>
                                            <option value="3" <?php echo ($row['v_muster_bl'] == 3 ? 'selected' : '') ?>>keine</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="col-3 ms-1">
                                    <?php if ($row['v_muster_bl1'] == NULL) {
                                    ?>
                                        <select name="v_muster_bl1" id="v_muster_bl1" class="w-100 form-select">
                                            <option value="0" selected>---</option>
                                            <option value="1">offen</option>
                                            <option value="2">geschl.</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="v_muster_bl1" id="v_muster_bl1" class="w-100 form-select" autocomplete="off">
                                            <option value="0" <?php echo ($row['v_muster_bl1'] == 0 ? 'selected' : '') ?>>---</option>
                                            <option value="1" <?php echo ($row['v_muster_bl1'] == 1 ? 'selected' : '') ?>>offen</option>
                                            <option value="2" <?php echo ($row['v_muster_bl1'] == 2 ? 'selected' : '') ?>>geschl.</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- MEDIKAMENTE -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">Medikamente <small style="font-size:.7em">(Wirkstoff - Dosierung - Dareichungsform)</small></h5>
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="medis" id="medis" rows="10" class="w-100 form-control" style="resize: none" placeholder="..."><?= $row['medis'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- DIAGNOSE -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">Verdachts-/Erstdiagnose</h5>
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="diagnose" id="diagnose" rows="3" class="w-100 form-control" style="resize: none" placeholder="..."><?= $row['diagnose'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <!-- ------------ -->
                            <!-- SONSTIGES -->
                            <!-- ------------ -->
                            <h5 class="text-light p-1">Notfallsituation, SAMPLER(+S), Bemerkungen</h5>
                            <div class="row my-2">
                                <div class="col">
                                    <textarea name="anmerkungen" id="anmerkungen" rows="20" class="w-100 form-control" style="resize: none" placeholder="..."><?= $row['anmerkungen'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row edivi__box">
                        <div class="col">
                            <div class="row my-2">
                                <div class="col">
                                    <div class="row">
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="notfallteam" name="notfallteam" value="1" <?php echo ($row['notfallteam'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-danger w-100" for="notfallteam">Übergabe Notfallteam</label>
                                        </div>
                                        <div class="col">
                                            <input type="checkbox" class="btn-check" id="transportverw" name="transportverw" value="1" <?php echo ($row['transportverw'] == 1 ? 'checked' : '') ?> autocomplete="off">
                                            <label class="btn btn-sm btn-outline-warning w-100" for="transportverw">Transportverweigerung</label>
                                        </div>
                                        <!-- <div class="col">
                                            <button class="btn btn-sm btn-info w-100" type="button" data-bs-toggle="modal" data-bs-target="#myModal3">Voranmeldung</button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <!-- MODAL -->
                            <div class="modal fade" id="myModal3" tabindex="-1" aria-labelledby="myModalLabel3" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myModalLabel3">Voranmeldung Klinik</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mt-2 mb-1">
                                                <div class="col">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="10" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            Schockraum
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="11" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            ZNA
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="12" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            Herzkatheter
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" value="13" name="nacascore" id="nacascore">
                                                        <label class="form-check-label" for="nacascore">
                                                            Stroke-Unit
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
                                <div class="col-3 fw-bold">Protokollant</div>
                                <div class="col"><input type="text" name="pfname" id="pfname" class="w-100 form-control edivi__input-check" value="<?= $row['pfname'] ?>"></div>
                            </div>
                            <?php if ($row['naname'] != NULL) : ?>
                                <div class="row mt-2">
                                    <div class="col-3 fw-bold">Bet. RM</div>
                                    <div class="col"><input type="text" name="naname" id="naname" class="w-100 form-control edivi__input-check" value="<?= $row['naname'] ?>"></div>
                                </div>
                            <?php else : ?>
                                <div class="row mt-2">
                                    <div class="col-3 fw-bold">Rettungsmittel</div>
                                    <div class="col-3">
                                        <?php if ($row['fzg_transp'] == NULL) : ?>
                                            <select name="fzg_transp" id="fzg_transp" class="w-100 form-select">
                                                <option selected value="NULL">Fzg.</option>
                                                <option disabled>-- FuRw 1 --</option>
                                                <option value="10-83-01">10-83-01</option>
                                                <option value="10-83-02">10-83-02</option>
                                                <option value="10-83-03">10-83-03</option>
                                                <option value="10-83-04">10-83-04</option>
                                                <option value="10-83-05">10-83-05</option>
                                                <option value="10-83-06">10-83-06</option>
                                                <option value="10-83-10">10-83-10</option>
                                                <option value="10-85-01">10-85-01</option>
                                                <option value="10-85-02">10-85-02</option>
                                                <option value="10-85-03">10-85-03</option>
                                                <option value="10-85-04">10-85-04</option>
                                                <option value="10-85-05">10-85-05</option>
                                                <option value="10-93-01">10-93-01</option>
                                                <option disabled>-- RW 17 --</option>
                                                <option value="17-83-01">17-83-01</option>
                                                <option value="17-83-02">17-83-02</option>
                                                <option value="17-83-03">17-83-03</option>
                                                <option value="17-83-04">17-83-04</option>
                                                <option value="17-83-05">17-83-05</option>
                                                <option value="17-83-06">17-83-06</option>
                                                <option value="17-85-01">17-85-01</option>
                                                <option value="17-85-02">17-85-02</option>
                                                <option value="17-85-03">17-85-03</option>
                                                <option value="17-87-01">17-87-01</option>
                                                <option disabled>-- SEG --</option>
                                                <option value="42-83-01">42-83-01</option>
                                                <option value="42-83-02">42-83-02</option>
                                                <option value="42-90-01">42-90-01</option>
                                                <option value="42-90-02">42-90-02</option>
                                                <option value="42-90-03">42-90-03</option>
                                                <option value="42-93-01">42-93-01</option>
                                                <option disabled>-- Schule --</option>
                                                <option value="50-83-01">50-83-01</option>
                                                <option value="50-83-02">50-83-02</option>
                                            </select>
                                        <?php else : ?>
                                            <select name="fzg_transp" id="fzg_transp" class="w-100 form-select">
                                                <option selected value="NULL">Fzg.</option>
                                                <option disabled>-- FuRw 1 --</option>
                                                <option value="10-83-01" <?php echo ($row['fzg_transp'] == "10-83-01" ? 'selected' : '') ?>>10-83-01</option>
                                                <option value="10-83-02" <?php echo ($row['fzg_transp'] == "10-83-02" ? 'selected' : '') ?>>10-83-02</option>
                                                <option value="10-83-03" <?php echo ($row['fzg_transp'] == "10-83-03" ? 'selected' : '') ?>>10-83-03</option>
                                                <option value="10-83-04" <?php echo ($row['fzg_transp'] == "10-83-04" ? 'selected' : '') ?>>10-83-04</option>
                                                <option value="10-83-05" <?php echo ($row['fzg_transp'] == "10-83-05" ? 'selected' : '') ?>>10-83-05</option>
                                                <option value="10-83-06" <?php echo ($row['fzg_transp'] == "10-83-06" ? 'selected' : '') ?>>10-83-06</option>
                                                <option value="10-83-10" <?php echo ($row['fzg_transp'] == "10-83-10" ? 'selected' : '') ?>>10-83-10</option>
                                                <option value="10-85-01" <?php echo ($row['fzg_transp'] == "10-85-01" ? 'selected' : '') ?>>10-85-01</option>
                                                <option value="10-85-02" <?php echo ($row['fzg_transp'] == "10-85-02" ? 'selected' : '') ?>>10-85-02</option>
                                                <option value="10-85-03" <?php echo ($row['fzg_transp'] == "10-85-03" ? 'selected' : '') ?>>10-85-03</option>
                                                <option value="10-85-04" <?php echo ($row['fzg_transp'] == "10-85-04" ? 'selected' : '') ?>>10-85-04</option>
                                                <option value="10-85-05" <?php echo ($row['fzg_transp'] == "10-85-05" ? 'selected' : '') ?>>10-85-05</option>
                                                <option value="10-93-01" <?php echo ($row['fzg_transp'] == "10-93-01" ? 'selected' : '') ?>>10-93-01</option>
                                                <option disabled>-- RW 17 --</option>
                                                <option value="17-83-01" <?php echo ($row['fzg_transp'] == "17-83-01" ? 'selected' : '') ?>>17-83-01</option>
                                                <option value="17-83-02" <?php echo ($row['fzg_transp'] == "17-83-02" ? 'selected' : '') ?>>17-83-02</option>
                                                <option value="17-83-03" <?php echo ($row['fzg_transp'] == "17-83-03" ? 'selected' : '') ?>>17-83-03</option>
                                                <option value="17-83-04" <?php echo ($row['fzg_transp'] == "17-83-04" ? 'selected' : '') ?>>17-83-04</option>
                                                <option value="17-83-05" <?php echo ($row['fzg_transp'] == "17-83-05" ? 'selected' : '') ?>>17-83-05</option>
                                                <option value="17-83-06" <?php echo ($row['fzg_transp'] == "17-83-06" ? 'selected' : '') ?>>17-83-06</option>
                                                <option value="17-85-01" <?php echo ($row['fzg_transp'] == "17-85-01" ? 'selected' : '') ?>>17-85-01</option>
                                                <option value="17-85-02" <?php echo ($row['fzg_transp'] == "17-85-02" ? 'selected' : '') ?>>17-85-02</option>
                                                <option value="17-85-03" <?php echo ($row['fzg_transp'] == "17-85-03" ? 'selected' : '') ?>>17-85-03</option>
                                                <option value="17-87-01" <?php echo ($row['fzg_transp'] == "17-87-01" ? 'selected' : '') ?>>17-87-01</option>
                                                <option disabled>-- SEG --</option>
                                                <option value="42-83-01" <?php echo ($row['fzg_transp'] == "42-83-01" ? 'selected' : '') ?>>42-83-01</option>
                                                <option value="42-83-02" <?php echo ($row['fzg_transp'] == "42-83-02" ? 'selected' : '') ?>>42-83-02</option>
                                                <option value="42-90-01" <?php echo ($row['fzg_transp'] == "42-90-01" ? 'selected' : '') ?>>42-90-01</option>
                                                <option value="42-90-02" <?php echo ($row['fzg_transp'] == "42-90-02" ? 'selected' : '') ?>>42-90-02</option>
                                                <option value="42-90-03" <?php echo ($row['fzg_transp'] == "42-90-03" ? 'selected' : '') ?>>42-90-03</option>
                                                <option value="42-93-01" <?php echo ($row['fzg_transp'] == "42-93-01" ? 'selected' : '') ?>>42-93-01</option>
                                                <option disabled>-- Schule --</option>
                                                <option value="50-83-01" <?php echo ($row['fzg_transp'] == "50-83-01" ? 'selected' : '') ?>>50-83-01</option>
                                                <option value="50-83-02" <?php echo ($row['fzg_transp'] == "50-83-02" ? 'selected' : '') ?>>50-83-02</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col">
                                        <input type="text" name="fzg_transp_perso" id="fzg_transp_perso" class="w-100 form-control" placeholder="Personal" value="<?= $row['fzg_transp_perso'] ?>">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-3 fw-bold"></div>
                                    <div class="col-3">
                                        <?php if ($row['fzg_na'] == NULL) : ?>
                                            <select name="fzg_na" id="fzg_na" class="w-100 form-select">
                                                <option selected value="NULL">Fzg.</option>
                                                <option disabled>-- Andere --</option>
                                                <option value="01-10-01">01-10-01</option>
                                                <option value="04-82-01">04-82-01</option>
                                                <option value="CHX Stettbeck">CHX Stettbeck</option>
                                                <option disabled>-- FuRw 1 --</option>
                                                <option value="10-80-01">10-80-01</option>
                                                <option value="10-81-01">10-81-01</option>
                                                <option value="10-82-01">10-82-01</option>
                                                <option value="10-82-02">10-82-02</option>
                                                <option value="10-82-03">10-82-03</option>
                                                <option value="10-86-01">10-86-01</option>
                                                <option disabled>-- RW 17 --</option>
                                                <option value="17-81-01">17-81-01</option>
                                                <option value="17-82-01">17-82-01</option>
                                                <option value="17-82-02">17-82-02</option>
                                            </select>
                                        <?php else : ?>
                                            <select name="fzg_na" id="fzg_na" class="w-100 form-select">
                                                <option selected value="NULL">Fzg.</option>
                                                <option disabled>-- Andere --</option>
                                                <option value="01-10-01" <?php echo ($row['fzg_na'] == "01-10-01" ? 'selected' : '') ?>>01-10-01</option>
                                                <option value="04-82-01" <?php echo ($row['fzg_na'] == "04-82-01" ? 'selected' : '') ?>>04-82-01</option>
                                                <option value="CHX Stettbeck" <?php echo ($row['fzg_na'] == "CHX Stettbeck" ? 'selected' : '') ?>>CHX Stettbeck</option>
                                                <option disabled>-- FuRw 1 --</option>
                                                <option value="10-80-01" <?php echo ($row['fzg_na'] == "10-80-01" ? 'selected' : '') ?>>10-80-01</option>
                                                <option value="10-81-01" <?php echo ($row['fzg_na'] == "10-81-01" ? 'selected' : '') ?>>10-81-01</option>
                                                <option value="10-82-01" <?php echo ($row['fzg_na'] == "10-82-01" ? 'selected' : '') ?>>10-82-01</option>
                                                <option value="10-82-02" <?php echo ($row['fzg_na'] == "10-82-02" ? 'selected' : '') ?>>10-82-02</option>
                                                <option value="10-82-03" <?php echo ($row['fzg_na'] == "10-82-03" ? 'selected' : '') ?>>10-82-03</option>
                                                <option value="10-86-01" <?php echo ($row['fzg_na'] == "10-86-01" ? 'selected' : '') ?>>10-86-01</option>
                                                <option disabled>-- RW 17 --</option>
                                                <option value="17-81-01" <?php echo ($row['fzg_na'] == "17-81-01" ? 'selected' : '') ?>>17-81-01</option>
                                                <option value="17-82-01" <?php echo ($row['fzg_na'] == "17-82-01" ? 'selected' : '') ?>>17-82-01</option>
                                                <option value="17-82-02" <?php echo ($row['fzg_na'] == "17-82-02" ? 'selected' : '') ?>>17-82-02</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col">
                                        <input type="text" name="fzg_na_perso" id="fzg_na_perso" class="w-100 form-control" placeholder="Personal" value="<?= $row['fzg_transp_perso'] ?>">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-3 fw-bold"></div>
                                    <div class="col">
                                        <input type="text" name="fzg_sonst" id="fzg_sonst" class="w-100 form-control" placeholder="Weitere Rettungsmittel" value="<?= $row['fzg_sonst'] ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row mt-2">
                                <div class="col-3 fw-bold">Transportziel</div>
                                <div class="col">
                                    <?php
                                    if ($row['transportziel2'] == NULL) {
                                    ?>
                                        <select name="transportziel2" id="transportziel2" class="w-100 form-select edivi__input-check">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" selected>Kein Transport</option>
                                            <!-- <option value="1">Uniklinik SB</option> -->
                                            <option value="2">Städtisches Klinikum</option>
                                        </select>
                                    <?php
                                    } else {
                                    ?>
                                        <select name="transportziel2" id="transportziel2" class="w-100 mb-2 form-select edivi__input-check" autocomplete="off">
                                            <option disabled hidden selected>---</option>
                                            <option value="0" <?php echo ($row['transportziel2'] == 0 ? 'selected' : '') ?>>Kein Transport</option>
                                            <!-- <option value="1" <?php echo ($row['transportziel2'] == 1 ? 'selected' : '') ?>>Uniklinik SB</option> -->
                                            <option value="2" <?php echo ($row['transportziel2'] == 2 ? 'selected' : '') ?>>Städtisches Klinikum</option>
                                        </select>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($admincheck || $ededit) { ?>
            <!-- ------------ -->
            <!-- PRÜFUNG -->
            <!-- ------------ -->
            <button id="qm-quick-menu" type="button">QM-Optionen</button>
            <button id="qm-log-menu" type="button">QM-Log</button>
        <?php } ?>
    </form>
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
        function setSelectElementStyles() {
            const selectElements = document.querySelectorAll(".edivi__verletzungen");

            selectElements.forEach((selectElement) => {
                const parentCol = selectElement.closest(".edivi__verletzungen-col");

                if (selectElement.value === "0") {
                    parentCol.classList.remove("edivi__verletzungen-yellow", "edivi__verletzungen-green");
                    parentCol.classList.add("edivi__verletzungen-red");
                } else if (selectElement.value === "1") {
                    parentCol.classList.remove("edivi__verletzungen-red", "edivi__verletzungen-green");
                    parentCol.classList.add("edivi__verletzungen-yellow");
                } else if (selectElement.value === "2") {
                    parentCol.classList.remove("edivi__verletzungen-red", "edivi__verletzungen-yellow");
                    parentCol.classList.add("edivi__verletzungen-green");
                } else {
                    parentCol.classList.remove("edivi__verletzungen-red", "edivi__verletzungen-yellow", "edivi__verletzungen-green");
                }
            });
        }

        // Call the function when the page loads
        window.addEventListener("load", setSelectElementStyles);

        // Add event listeners for change events (as you already did)
        const selectElements = document.querySelectorAll(".edivi__verletzungen");

        selectElements.forEach((selectElement) => {
            selectElement.addEventListener("change", setSelectElementStyles);
        });
    </script>
    <script>
        // Get all input elements with the class "edivi__input-check"
        const inputElements = document.querySelectorAll('.edivi__input-check');

        // Function to add or remove the class based on input value
        function handleInputChange(event) {
            const inputElement = event.target;

            // Check if the input is a select element
            if (inputElement.tagName === 'SELECT') {
                const selectedOption = inputElement.querySelector('option:checked');
                if (selectedOption && !selectedOption.disabled) {
                    inputElement.classList.add('edivi__input-checked');
                } else {
                    inputElement.classList.remove('edivi__input-checked');
                }
            } else {
                // Check if the input has a value (excluding select elements)
                if (inputElement.value.trim() === '') {
                    inputElement.classList.remove('edivi__input-checked');
                } else {
                    inputElement.classList.add('edivi__input-checked');
                }
            }
        }

        // Check the initial state of the input elements and add "edivi__input-checked" if not empty (excluding select elements with disabled options)
        inputElements.forEach(inputElement => {
            if (inputElement.tagName === 'SELECT') {
                const selectedOption = inputElement.querySelector('option:checked');
                if (selectedOption && !selectedOption.disabled) {
                    inputElement.classList.add('edivi__input-checked');
                }
            } else if (inputElement.value.trim() !== '') {
                inputElement.classList.add('edivi__input-checked');
            }
        });

        // Add an event listener for the "input" event on each input element
        inputElements.forEach(inputElement => {
            inputElement.addEventListener('input', handleInputChange);
        });
    </script>
    <script>
        // Get all form elements
        var formElements = document.querySelectorAll('input:not(.edivi__admin), textarea:not(.edivi__admin)');
        var selectElements2 = document.querySelectorAll('select:not(.edivi__admin)');
        var inputElements2 = document.querySelectorAll('.btn-check');
        var inputElements3 = document.querySelectorAll('.form-check-input');

        // Set all form elements to readonly
        formElements.forEach(function(element) {
            element.setAttribute('readonly', 'readonly');
        });

        selectElements2.forEach(function(element) {
            element.setAttribute('disabled', 'disabled');
        });

        inputElements2.forEach(function(element) {
            element.setAttribute('disabled', 'disabled');
        });

        inputElements3.forEach(function(element) {
            element.setAttribute('disabled', 'disabled');
        });
    </script>
    <script>
        function updateContainerClass(index) {
            const containers = document.querySelectorAll('.edivi__zugang-container');
            const selects = document.querySelectorAll('.edivi__zugang-list');

            // Remove any existing classes starting with "edivi__zugang-option"
            containers[index].classList.remove(
                ...Array.from(containers[index].classList).filter(className => className.startsWith('edivi__zugang-opt'))
            );

            // Get the selected value
            const selectedValue = selects[index].value;

            // Add the corresponding class to the container
            containers[index].classList.add(`edivi__zugang-opt${selectedValue}`);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Run the script once on page load
            const selects = document.querySelectorAll('.edivi__zugang-list');

            selects.forEach((select, index) => {
                select.addEventListener('change', () => {
                    // Call the updateContainerClass function on select change
                    updateContainerClass(index);
                });

                // Call the updateContainerClass function on page load
                updateContainerClass(index);
            });
        });
    </script>
    <script>
        document.getElementById('qm-quick-menu').addEventListener('click', function() {
            window.open('/admin/edivi/qm-actions.php?id=<?= $_GET['id'] ?>', '_blank', 'width=850,height=580');
        });

        document.getElementById('qm-log-menu').addEventListener('click', function() {
            window.open('/admin/edivi/qm-log.php?id=<?= $_GET['id'] ?>', '_blank', 'width=850,height=580');
        });
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
</body>

</html>