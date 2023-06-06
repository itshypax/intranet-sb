<?php
include '../assets/php/mysql-con.php';

$openedID = $_GET['dok'];

$result = mysqli_query($conn, "SELECT * FROM personal_dokumente WHERE docid = " . $_GET['dok']) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

if ($row['anrede'] == 0) {
    $anrede = "Frau";
} else {
    $anrede = "Herr";
}

$erhalter_gebdat = $row['erhalter_gebdat'];
$date = DateTime::createFromFormat('Y-m-d', $erhalter_gebdat);
$month_number = $date->format('m');
$month_names = array(
    'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
    'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
);
$formatted_date = $date->format('d. ') . $month_names[$month_number - 1] . $date->format(' Y');
$dg = $row['erhalter_rang'];
$dienstgrade = [
    0 => "Angestellte/-r",
    1 => "Brandmeisteranwärter/-in",
    2 => "Brandmeister/-in",
    3 => "Oberbrandmeister/-in",
    4 => "Hauptbrandmeister/-in",
    5 => "Hauptbrandmeister/-in mit AZ",
    6 => "Brandinspektor/-in",
    7 => "Oberbrandinspektor/-in",
    8 => "Brandamtmann/frau",
    9 => "Brandamtsrat/rätin",
    10 => "Brandoberamtsrat/rätin",
    11 => "Brandrat/rätin",
    12 => "Oberbrandrat/rätin",
    13 => "Branddirektor/-in",
    14 => "Leitende/-r Branddirektor/-in",
];
$dienstgrad = isset($dienstgrade[$dg]) ? $dienstgrade[$dg] : '';
$ausstelldatum = date("d.m.Y", strtotime($row['ausstelungsdatum']));

$result2 = mysqli_query($conn, "SELECT id,fullname,aktenid FROM cirs_users WHERE id = " . $row['ausstellerid']) or die(mysqli_error($conn));
$adata = mysqli_fetch_array($result2);

$fullname = $adata['fullname'];
$splitname = explode(" ", $fullname);
$lastname = end($splitname);

if ($adata['aktenid'] > 0) {
    $result3 = mysqli_query($conn, "SELECT id,fullname,dienstgrad,qualird FROM personal_profile WHERE id = " . $adata['aktenid']) or die(mysqli_error($conn));
    $rdata = mysqli_fetch_array($result3);
    $bfrang = $rdata['dienstgrad'];
    $dienstgrad2 = isset($dienstgrade[$bfrang]) ? $dienstgrade[$bfrang] : '';
}

$typ = $row['type'];
$typen = [
    0 => "Ernennungsurkunde",
    1 => "Beförderungsurkunde",
    2 => "Entlassungsurkunde",
];
$typtext = isset($typen[$typ]) ? $typen[$typ] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Urkunde &rsaquo; intraSB</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/style.min.css" />
    <link rel="stylesheet" href="/assets/css/dokumente.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/freehand/css/all.min.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/assets/bootstrap-5.3/css/bootstrap.min.css">
    <script src="/assets/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>
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
    <script src="/assets/js/darkmode.js"></script>
</head>

<?php if ($row['type'] == 0) { ?>

    <body class="bg-secondary" data-page-amount="2" data-page-type="0">
    <?php } else if ($row['type'] == 1) { ?>

        <body class="bg-secondary" data-page-amount="2" data-page-type="1">
        <?php } else if ($row['type'] == 2) { ?>

            <body class="bg-secondary" data-page-amount="2" data-page-type="2">
            <?php } else { ?>

                <body class="bg-secondary" data-page-amount="2">
                <?php } ?>
                <div class="page-container">
                    <div class="page shadow mx-auto mt-2 bg-light" id="page1">
                        <table class="docheader">
                            <tbody>
                                <tr>
                                    <td style="width:20%;padding-left:5px;font-size:8pt"><strong>Version</strong><span class="subtext">1.0</span></td>
                                    <td class="text-center" rowspan="2" style="font-size:12pt"><strong><?= $typtext ?></strong><br>Berufsfeuerwehr Stettbeck</td>
                                    <td class="text-center" style="width:20%" rowspan="2"><img src="/assets/img/bfsb_wappen.png" alt="Berufsfeuerwehr Stettbeck"></td>
                                </tr>
                                <tr>
                                    <td style="width:20%;padding-left:5px;font-size:8pt"><strong>Seite</strong><span class="subtext">1 von 2</span></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="firstpage-logos">
                            <img src="/assets/img/schrift_bfsb.png" alt="Schriftzug Berufsfeuerwehr Stettbeck">
                            <div class="my-3"></div>
                            <img src="/assets/img/schrift_fhsb.png" alt="Schriftzug Hansestadt Stettbeck">
                        </div>
                        <div class="document-headtext text-center mt-3">
                            <span class="bfcolor">Berufsfeuerwehr Stettbeck</span><br>
                            <?= $typtext ?>
                        </div>
                        <div class="document-styling">
                            <img src="/assets/img/bf_strich.png" alt="BF Strich">
                        </div>
                    </div>
                    <div class="page shadow mx-auto mt-4 bg-light" id="page2">
                        <table class="docheader">
                            <tbody>
                                <tr>
                                    <td style="width:20%;padding-left:5px;font-size:8pt"><strong>Version</strong><span class="subtext">1.0</span></td>
                                    <td class="text-center" rowspan="2" style="font-size:12pt"><strong><?= $typtext ?></strong><br>Berufsfeuerwehr Stettbeck</td>
                                    <td class="text-center" style="width:20%" rowspan="2"><img src="/assets/img/bfsb_wappen.png" alt="Berufsfeuerwehr Stettbeck"></td>
                                </tr>
                                <tr>
                                    <td style="width:20%;padding-left:5px;font-size:8pt"><strong>Seite</strong><span class="subtext">2 von 2</span></td>
                                </tr>
                            </tbody>
                        </table>
                        <h1 class="text-center">Urkunde</h1>
                        <div class="urkunde-body text-center">
                            <p class="my-5">Im Namen der Hansestadt Stettbeck</p>
                            <p>wird <?= $anrede ?></p>
                            <p class="urkunde-important"><?= $row['erhalter'] ?></p>
                            <p>» geb. am <?= $formatted_date ?> «</p>
                            <!-- Ernennungsurkunde -->
                            <div class="dt-0">
                                <p class="my-5">mit sofortiger Wirkung in das Beamtenverhältnis<br>
                                    auf Widerruf berufen und im Dienstverhältnis als</p>
                            </div>
                            <!-- Beförderungsurkunde -->
                            <div class="dt-1">
                                <p class="my-5">mit sofortiger Wirkung befördert und
                                    <?php if ($row['anrede'] == 0) {
                                        echo "zur";
                                    } else {
                                        echo "zum";
                                    }
                                    ?>
                                </p>
                            </div>
                            <!-- Entlassungsurkunde -->
                            <div class="dt-2">
                                <p class="my-5">auf eigenes Verlangen mit sofortiger Wirkung aus dem Beamtenverhältnis sowie aus dem Dienst der Berufsfeuerwehr Stettbeck entlassen.</p>
                            </div>
                            <p class="urkunde-important dt-0 dt-1"><?= $dienstgrad ?></p>
                            <!-- Ernennungsurkunde -->
                            <div class="dt-0">
                                <p class="my-5">
                                    <?php if ($row['anrede'] == 0) {
                                        echo "zur";
                                    } else {
                                        echo "zum";
                                    }
                                    ?>
                                    Beamten der Berufsfeuerwehr Stettbeck ernannt.</p>
                            </div>
                            <!-- Beförderungsurkunde -->
                            <div class="dt-1">
                                <p class="my-5">der Berufsfeuerwehr Stettbeck ernannt.</p>
                            </div>
                            <!-- Entlassungsurkunde -->
                            <div class="dt-2">
                                <p class="my-5">Für
                                    <?php if ($row['anrede'] == 0) {
                                        echo "ihre";
                                    } else {
                                        echo "seine";
                                    }
                                    ?>
                                    gleisteten Dienste sprechen wir
                                    <?php if ($row['anrede'] == 0) {
                                        echo "ihr";
                                    } else {
                                        echo "ihm";
                                    }
                                    ?>
                                    Dank und Anerkennung aus.
                                </p>
                            </div>
                        </div>
                        <hr class="text-light my-5">
                        <p>Stettbeck, den <?= $ausstelldatum ?>,</p>
                        <div class="row signatures">
                            <div class="col">
                                <table>
                                    <tbody>
                                        <tr class="text-center" style="border-bottom: 2px solid #000">
                                            <td class="signature"><?= $lastname ?></td>
                                        </tr>
                                        <tr>
                                            <td>Berufsfeuerwehr Stettbeck<br><?= $rdata['fullname'] ?> | <?= $dienstgrad2 ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col"></div>
                        </div>
                        <div class="urkunde-disclaimer text-center mt-5">
                            Diese fiktive Urkunde ist lediglich für das GTA Roleplay Projekt „NordNetzwerk.eu“ ausgelegt. Der Besitz dieser Urkunde befugt in keinster Weise zum Führen einer echten Qualifikation.
                        </div>
                        <div class="document-styling">
                            <img src="/assets/img/bf_strich.png" alt="BF Strich">
                        </div>
                    </div>
                </div>
                <button onclick="toggleDarkMode()" class="btn btn-secondary" id="darkmode-switch">
                    <i id="darkModeIcon" class="fas"></i> Lichtschalter
                </button>
                </body>

</html>