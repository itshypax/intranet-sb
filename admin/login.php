<?php
ini_set('session.gc_maxlifetime', 604800);
ini_set('session.cookie_path', '/');  // Set the cookie path to the root directory
ini_set('session.cookie_domain', '.stettbeck.de');  // Set the cookie domain to your domain
ini_set('session.cookie_lifetime', 604800);  // Set the cookie lifetime (in seconds)
ini_set('session.cookie_secure', true);  // Set to true if using HTTPS, false otherwise

session_start();
include '../assets/php/mysql-con.php';
$pdo = $mysql;

if (isset($_SESSION['userid']) && isset($_SESSION['permissions'])) {
    header('Location: /admin/index.php');
}

if (isset($_GET['login'])) {
    $username = $_POST['username'];
    $passwort = $_POST['passwort'];

    $statement = $pdo->prepare("SELECT * FROM cirs_users WHERE username = :username");
    $result = $statement->execute(array('username' => $username));
    $user = $statement->fetch();

    //Überprüfung des Passworts
    if ($user !== false && password_verify($passwort, $user['passwort'])) {
        $_SESSION['userid'] = $user['id'];
        $_SESSION['cirs_user'] = $user['fullname'];
        $permissions = json_decode($user['permissions'], true) ?? [];
        $_SESSION['permissions'] = $permissions;

        if ($user['aktenid'] != null) {
            $statement = $pdo->prepare("SELECT * FROM personal_profile WHERE id = :id");
            $result = $statement->execute(array('id' => $user['aktenid']));
            $profile = $statement->fetch();

            $_SESSION['cirs_dg'] = $profile['dienstgrad'];
            $_SESSION['ic_name'] = $profile['fullname'];
        }

        if (isset($_SESSION['redirect_url'])) {
            $redirect_url = $_SESSION['redirect_url'];
            unset($_SESSION['redirect_url']); // Remove the stored URL
            header("Location: $redirect_url");
            exit();
        } else {
            header('Location: /admin/index.php');
        }
    } else {
        $errorMessage = "Benutzername oder Passwort ungültig.<br>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login &rsaquo; intraSB</title>
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
                <?php
                if (isset($errorMessage)) {
                    echo '<div class="alert alert-danger mb-5" role="alert">';
                    echo $errorMessage;
                    echo '</div>';
                }
                ?>

                <form action="?login=1" method="post">
                    <strong>Benutzername:</strong><br>
                    <input class="form-control" type="text" size="40" maxlength="250" name="username"><br><br>

                    <strong>Passwort:</strong><br>
                    <input class="form-control" type="password" size="40" maxlength="250" name="passwort"><br>

                    <input class="btn btn-primary w-100" type="submit" value="Anmelden">
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