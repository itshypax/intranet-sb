<?php

session_start();
$pdo = new PDO('mysql:host=web-snake02.native-webspace.com;dbname=hypaxna1_cirs', 'cirsmanager', '3?8h74vaO');

if (isset($_POST['new']) && $_POST['new'] == 1) {
    $enr = $_POST['enr'];
    // check if there already is a protocol with this enr in the database
    $check = $pdo->prepare("SELECT * FROM cirs_rd_protokolle WHERE enr = :enr");
    $check->execute(array('enr' => $enr));
    $check = $check->fetch();
    if ($check) {
        // if there is, do nothing
    } else {
        // if there is not, create a new protocol
        $statement = $pdo->prepare("INSERT INTO cirs_rd_protokolle (enr) VALUES (:enr)");
        $result = $statement->execute(array('enr' => $enr));
    }
    header('Location: /edivi/protokoll.php?id=' . $enr);
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>eDIVI &rsaquo; intraSB</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/style.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
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
</head>

<body id="dashboard" class="d-flex justify-content-center align-items-center">
    <div class="row">
        <div class="col">
            <div class="card px-4 py-3">
                <h1 id="loginHeader">intra<span class="text-sh-red">SB</span></h1>
                <p class="subtext">Das Intranet der Hansestadt!</p>
                <h4 class="text-center my-2">Protokoll öffnen/anlegen</h4>
                <form name="form" method="post" action="">
                    <input type="hidden" name="new" value="1" />
                    <strong>Einsatznummer:</strong><br>
                    <input class="form-control" type="text" size="40" maxlength="250" name="enr"><br><br>

                    <input class="btn btn-primary w-100" type="submit" value="Suchen">
                </form>
            </div>
        </div>
    </div>
    <footer>
        <div class="footerCopyright">
            <a href="https://hypax.wtf" target="_blank"><i class="fa-solid fa-code"></i> hypax</a>
            <span>© 2023 | v0.1 WIP</span>
        </div>
        <div class="footerNN">
            <span class="nnText">
                <a href="https://nordnetzwerk.eu" target="blank">
                    NordNetzwerk.eu
                    <svg id="Ebene_2" data-name="Ebene 2" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 608.35 950.68">
                        <defs>
                            <style>
                                .cls-1 {
                                    fill: #fff;
                                }
                            </style>
                        </defs>
                        <g id="Ebene_1-2" data-name="Ebene 1">
                            <path class="cls-1" d="M150.61,0c108.24,125.16,216.07,249.84,323.9,374.52,.18-.06,.37-.11,.55-.17,0-.91,0-1.83,0-2.74,0-31.34,.01-62.69-.03-94.03,0-1.63,.35-2.8,1.66-3.93,43.3-37.39,86.56-74.81,129.83-112.23,.52-.45,1.06-.86,1.82-1.47V700.37c-108.26-125.2-216.11-249.93-324.35-375.11v184.46c-4.09-4.71-7.74-8.9-11.37-13.09-24.25-28.04-48.49-56.09-72.74-84.14-16.04-18.56-32.1-37.1-48.09-55.7-.69-.81-1.14-2.11-1.14-3.18-.05-117.06-.05-234.12-.05-351.18V0Z" />
                            <path class="cls-1" d="M.54,790.05c-.18-1.06-.52-2.11-.52-3.17C0,627.06,0,467.23,0,307.41c0-17.83,0-35.66,.02-53.49,0-.91,.3-1.81,.46-2.72,20.55,23.73,41.12,47.46,61.66,71.2,27.29,31.54,54.55,63.11,81.83,94.66,29.16,33.73,58.32,67.46,87.48,101.19,23.99,27.74,47.98,55.48,71.97,83.21,6.74,7.79,13.47,15.58,20.65,23.88v-183.7l.52-.16c4.11,4.74,8.22,9.47,12.32,14.21,28.38,32.82,56.75,65.65,85.13,98.47,11.44,13.24,22.91,26.45,34.31,39.73,.65,.76,1.09,1.96,1.11,2.97,.11,4.95,.05,9.91,.05,14.87v335.59c0,.86,0,1.71,0,3.36-108.34-125.27-216.22-250.01-324.57-375.29v3.03c0,31.58-.01,63.17,.03,94.75,0,1.64-.37,2.8-1.66,3.92-43.35,37.44-86.68,74.93-130,112.4-.24,.21-.51,.38-.76,.56Z" />
                        </g>
                    </svg>
                    FiveM
                </a>
            </span>
        </div>
        <div class="footerLegal">
            <span>
                <a href="https://nordnetzwerk.eu/app/imprint/">
                    Impressum
                </a>
            </span>
            <span>
                <a href="https://nordnetzwerk.eu/app/datenschutzerklaerung/">
                    Datenschutzerklärung
                </a>
            </span>
        </div>
    </footer>
</body>

</html>