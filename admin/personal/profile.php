<?php
session_start();
require_once '../../assets/php/permissions.php';
if (!isset($_SESSION['userid']) || !isset($_SESSION['permissions'])) {
    header("Location: /admin/login.php");
} elseif ($notadmincheck) {
    if ($perview) {
        $canView = true;
        $canEdit = $peredit;
    } else {
        $canView = false;
        $canEdit = false;
    }

    if (!$canView && !$canEdit) {
        header("Location: /admin/index.php");
    }
} else {
    $canView = true;
    $canEdit = true;
}

include '../../assets/php/mysql-con.php';

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];

$result = mysqli_query($conn, "SELECT * FROM personal_profile WHERE id = " . $_GET['id']) or die(mysqli_error($conn));
$row = mysqli_fetch_array($result);

$openedID = $_GET['id'];
$edituser = $_SESSION['cirs_user'];

if (isset($_POST['new'])) {
    if ($_POST['new'] == 1) {
        // Get and sanitize input values
        $id = $_POST['id'];
        $fullname = $_POST['fullname'];
        $gebdatum = $_POST['gebdatum'];
        $charakterid = $_POST['charakterid'];
        $dienstgrad = $_POST['dienstgrad'];
        $forumprofil = $_POST['forumprofil'];
        $discordtag = $_POST['discordtag'];
        $telefonnr = $_POST['telefonnr'];
        $dienstnr = $_POST['dienstnr'];

        // Retrieve the current dienstgrad and other data from the database
        $stmt = mysqli_prepare($conn, "SELECT dienstgrad, fullname, gebdatum, charakterid, forumprofil, discordtag, telefonnr, dienstnr FROM personal_profile WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $currentDienstgrad, $currentFullname, $currentGebdatum, $currentCharakterid, $currentForumprofil, $currentDiscordtag, $currentTelefonnr, $currentDienstnr);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $dienstgradMapping = array(
            16 => "Ehrenamtliche/-r",
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
            15 => "Brandratanwärter/in",
            11 => "Brandrat/rätin",
            12 => "Oberbrandrat/rätin",
            13 => "Branddirektor/-in",
            14 => "Leitende/-r Branddirektor/-in",
        );

        // Update the dienstgrad only if it has changed
        if ($currentDienstgrad != $dienstgrad) {
            // Update the database using prepared statements
            $stmt = mysqli_prepare($conn, "UPDATE personal_profile SET dienstgrad = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $dienstgrad, $id);
            mysqli_stmt_execute($stmt);

            // Retrieve the text representations for the current and new dienstgrad
            $currentDienstgradText = isset($dienstgradMapping[$currentDienstgrad]) ? $dienstgradMapping[$currentDienstgrad] : 'Unknown';
            $newDienstgradText = isset($dienstgradMapping[$dienstgrad]) ? $dienstgradMapping[$dienstgrad] : 'Unknown';

            // Insert a log entry for dienstgrad modification
            $logContent = 'Dienstgrad wurde von <strong>' . $currentDienstgradText . '</strong> auf <strong>' . $newDienstgradText . '</strong> geändert.';
            $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, '4', ?, ?)");
            mysqli_stmt_bind_param($logStmt, "iss", $id, $logContent, $edituser);
            mysqli_stmt_execute($logStmt);
        }

        // Update other profile data if it has changed
        $dataChanged = false;
        if (
            $currentFullname != $fullname ||
            $currentGebdatum != $gebdatum ||
            $currentCharakterid != $charakterid ||
            $currentForumprofil != $forumprofil ||
            $currentDiscordtag != $discordtag ||
            $currentTelefonnr != $telefonnr ||
            $currentDienstnr != $dienstnr
        ) {
            $dataChanged = true;
            // Update the database using prepared statements
            $stmt = mysqli_prepare($conn, "UPDATE personal_profile SET fullname = ?, gebdatum = ?, charakterid = ?, forumprofil = ?, discordtag = ?, telefonnr = ?, dienstnr = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssssssii", $fullname, $gebdatum, $charakterid, $forumprofil, $discordtag, $telefonnr, $dienstnr, $id);
            mysqli_stmt_execute($stmt);
        }

        // Insert a log entry for profile data modification if dienstgrad and/or other data changed
        if ($dataChanged) {
            $logContent = 'Profilangaben wurden bearbeitet.';
            $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, '5', ?, ?)");
            mysqli_stmt_bind_param($logStmt, "iss", $id, $logContent, $edituser);
            mysqli_stmt_execute($logStmt);
        }

        // Redirect to the modified URL
        $currentURL = $_SERVER['REQUEST_URI'];
        $parsedURL = parse_url($currentURL);
        parse_str($parsedURL['query'], $queryParams);
        unset($queryParams['edit']);
        $newQuery = http_build_query($queryParams);
        $modifiedURL = $parsedURL['path'] . '?' . $newQuery;
        header("Location: $modifiedURL");
        exit();
    } elseif ($_POST['new'] == 2) {
        if (isset($_POST['qualifw']) && is_array($_POST['qualifw'])) {
            $qualifikationen_fw = $_POST['qualifw'];
            $qualifw = json_encode($qualifikationen_fw);

            // Retrieve the current value of qualifw from the database
            $stmt = mysqli_prepare($conn, "SELECT qualifw FROM personal_profile WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $openedID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $currentQualifw);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt); // Close the statement

            // Compare the new value with the current value
            if ($qualifw != $currentQualifw) {
                // Update the database using prepared statements
                $updateStmt = mysqli_prepare($conn, "UPDATE personal_profile SET qualifw = ? WHERE id = ?");
                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "si", $qualifw, $openedID);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt); // Close the statement

                    // Insert a log entry for qualifw modification
                    $logContent = 'Feuerwehr Qualifikationen wurden bearbeitet.';
                    $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, '5', ?, ?)");
                    if ($logStmt) {
                        mysqli_stmt_bind_param($logStmt, "iss", $openedID, $logContent, $edituser);
                        mysqli_stmt_execute($logStmt);
                        mysqli_stmt_close($logStmt); // Close the statement
                    }
                }
            }
        }
        header('Refresh: 0');
    } elseif ($_POST['new'] == 3) {
        if (isset($_POST['qualird'])) {
            $qualird = $_POST['qualird'];

            // Retrieve the current value of qualird from the database
            $stmt = mysqli_prepare($conn, "SELECT qualird FROM personal_profile WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $openedID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $currentQualird);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt); // Close the statement

            // Compare the new value with the current value
            if ($qualird != $currentQualird) {
                // Update the database using prepared statements
                $updateStmt = mysqli_prepare($conn, "UPDATE personal_profile SET qualird = ? WHERE id = ?");
                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "si", $qualird, $openedID);
                    $updateResult = mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt); // Close the statement

                    if ($updateResult) {
                        // Insert a log entry for qualird modification
                        $logContent = 'Rettungsdienst Qualifikationen wurden bearbeitet.';
                        $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, '5', ?, ?)");
                        if ($logStmt) {
                            mysqli_stmt_bind_param($logStmt, "iss", $openedID, $logContent, $edituser);
                            mysqli_stmt_execute($logStmt);
                            mysqli_stmt_close($logStmt); // Close the statement
                        }
                    }
                }
            }
        }
        header('Refresh: 0');
    } elseif ($_POST['new'] == 4) {
        if (isset($_POST['fachdienste']) && is_array($_POST['fachdienste'])) {
            $qualifikationen_fd = $_POST['fachdienste'];
            $qualifd = json_encode($qualifikationen_fd);

            // Retrieve the current value of fachdienste from the database
            $stmt = mysqli_prepare($conn, "SELECT fachdienste FROM personal_profile WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $openedID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $currentQualifd);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt); // Close the statement

            // Compare the new value with the current value
            if ($qualifd != $currentQualifd) {
                // Update the database using prepared statements
                $updateStmt = mysqli_prepare($conn, "UPDATE personal_profile SET fachdienste = ? WHERE id = ?");
                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "si", $qualifd, $openedID);
                    $updateResult = mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt); // Close the statement

                    if ($updateResult) {
                        // Insert a log entry for fachdienste modification
                        $logContent = 'Fachdienste wurden bearbeitet.';
                        $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, '5', ?, ?)");
                        if ($logStmt) {
                            mysqli_stmt_bind_param($logStmt, "iss", $openedID, $logContent, $edituser);
                            mysqli_stmt_execute($logStmt);
                            mysqli_stmt_close($logStmt); // Close the statement
                        }
                    }
                }
            }
        }
        header('Refresh: 0');
    } elseif ($_POST['new'] == 5) {
        // Insert a log entry for qualifw modification
        $logContent = $_POST['content'];
        $logType = $_POST['noteType'];
        $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($logStmt, "isss", $openedID, $logType, $logContent, $edituser);
        mysqli_stmt_execute($logStmt);
        header('Refresh: 0');
    } elseif ($_POST['new'] == 6) {
        $erhalter = $_POST['erhalter'];
        $erhalter_gebdat =  $_POST['erhalter_gebdat'];
        $ausstellerid = $_POST['ausstellerid'];
        $profileid = $_POST['profileid'];
        $docType = $_POST['docType'];

        $random_number = mt_rand(1000000, 9999999);
        $rncheck = "SELECT * FROM personal_dokumente WHERE docid = $random_number";
        $rnres = $conn->query($rncheck);
        while ($rnres->num_rows > 0) {
            $random_number = mt_rand(1000000, 9999999);
            $rncheck = "SELECT * FROM personal_dokumente WHERE docid = $random_number";
            $rnres = $conn->query($rncheck);
        }
        $new_number = $random_number;

        if ($docType <= 1) {
            $anrede = $_POST['anrede'];
            $erhalter_rang = $_POST['erhalter_rang'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_0']));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, erhalter_rang, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iisssssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $erhalter_rang, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/02/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/02/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        } elseif ($docType == 2) {
            $anrede = $_POST['anrede'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_' . $docType]));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iissssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/02/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/02/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        } elseif ($docType == 3) {
            $anrede = $_POST['anrede'];
            $erhalter_rang_rd_2 = $_POST['erhalter_rang_rd_2'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_' . $docType]));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, erhalter_rang_rd, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iisssssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $erhalter_rang_rd_2, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/04/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/04/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        } elseif ($docType == 5) {
            $anrede = $_POST['anrede'];
            $erhalter_rang_rd = $_POST['erhalter_rang_rd'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_' . $docType]));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, erhalter_rang_rd, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iisssssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $erhalter_rang_rd, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/03/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/03/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        } elseif ($docType == 6) {
            $anrede = $_POST['anrede'];
            $erhalter_quali = $_POST['erhalter_quali'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_' . $docType]));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, erhalter_quali, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iisssssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $erhalter_quali, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/03/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/03/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        } elseif ($docType == 10 || $docType == 12 || $docType == 13) {
            $anrede = $_POST['anrede'];
            $inhalt = $_POST['inhalt'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_10']));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, inhalt, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iisssssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $inhalt, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/01/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/01/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        } elseif ($docType == 11) {
            $anrede = $_POST['anrede'];
            $inhalt = $_POST['inhalt'];
            $suspendtime = $_POST['suspendtime'];
            $ausstelungsdatum = date('Y-m-d', strtotime($_POST['ausstelungsdatum_10']));

            $docStmt = mysqli_prepare($conn, "INSERT INTO personal_dokumente (docid, type, anrede, erhalter, erhalter_gebdat, inhalt, suspendtime, ausstelungsdatum, ausstellerid, profileid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($docStmt, "iissssssii", $new_number, $docType, $anrede, $erhalter, $erhalter_gebdat, $inhalt, $suspendtime, $ausstelungsdatum, $ausstellerid, $profileid);
            header('Location: /dokumente/01/' . $new_number, true, 302);
            $logContent = 'Ein neues Dokument (<a href="/dokumente/01/' . $new_number . '" target="_blank">' . $new_number . '</a>) wurde erstellt.';
        }

        mysqli_stmt_execute($docStmt);
        $logStmt = mysqli_prepare($conn, "INSERT INTO personal_log (profilid, type, content, paneluser) VALUES (?, '7', ?, ?)");
        if ($logStmt) {
            mysqli_stmt_bind_param($logStmt, "iss", $openedID, $logContent, $edituser);
            mysqli_stmt_execute($logStmt);
            mysqli_stmt_close($logStmt); // Close the statement
        }
        header('Refresh: 0');
    }
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administration &rsaquo; intraSB</title>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="/assets/css/style.min.css" />
    <link rel="stylesheet" href="/assets/css/admin.min.css" />
    <link rel="stylesheet" href="/assets/css/personal.min.css" />
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="/assets/fonts/ptsans/css/all.min.css" />
    <link rel="stylesheet" href="/assets/redactorx/redactorx.min.css" />
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/assets/bootstrap-5.3/css/bootstrap.min.css">
    <script src="/assets/bootstrap-5.3/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/jquery/jquery-3.7.0.min.js"></script>
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

<body data-page="mitarbeiter">
    <!-- PRELOAD -->
    <?php include "../../assets/php/preload.php"; ?>
    <!-- NAVIGATION -->
    <div class="container-fluid d-flex justify-content-center align-items-center pb-5 border-3 border-bottom border-sh-semigray" id="topLogo">
        <a class="" id="sb-logo" href="#">
            <svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2784.97 526.37" height="130" width="auto" style="transform:scale(.5)">
                <g id="b">
                    <path d="M167.38,14.52c54.93,0,109.86,0,164.79,0,3.08,0,2.62-.36,2.62,2.68,0,86.85-.02,173.71,.05,260.56,0,7.37-.48,14.64-1.75,21.89-1.92,11.06-4.34,21.99-8.12,32.57-7.45,20.83-17.97,39.9-32.82,56.42-4.2,4.67-8.77,8.97-13.43,13.18-7.98,7.22-16.55,13.73-25.17,20.12-3.73,2.76-8.03,4.75-12.14,6.98-10.3,5.59-21.14,9.83-32.52,12.68-6.16,1.54-12.41,2.62-18.72,3.27-4.84,.5-9.73,.66-14.55,1.25-7.07,.87-14.07-.05-21.09-.5-3.93-.25-7.86-.67-11.71-1.36-4.06-.73-8.13-1.48-12.18-2.35-8.54-1.86-16.96-4.16-25.08-7.44-7.24-2.93-14.23-6.41-21.14-10.04-.82-.43-1.55-1.03-2.31-1.57-7.31-5.15-14.6-10.33-21.48-16.06-11.28-9.39-20.74-20.37-28.81-32.6-7.08-10.72-13.14-22-18.2-33.81-5.83-13.62-9.77-27.8-12.06-42.45-.37-2.38-.75-4.75-1.14-7.12-.27-1.65-.41-3.3-.41-4.98C.02,196.33,.02,106.8,.03,17.27c0-3.12-.5-2.74,2.76-2.74,54.86,0,109.73,0,164.59,0Z" />
                    <path d="M171.76,234.85c-.69-8.93-.22-17.88-.41-26.82-.01-.47,0-.94,0-1.4,0-.65,.34-.98,.99-.98,.47,0,.94-.01,1.4-.01h152.97c2.69,0,2.48-.11,2.48,2.52v77.96c0,1.07,.05,2.14-.18,3.19-.52,.19-.71,.6-.65,1.11,.17,1.39-.32,2.76-.06,4.15-.9,6.32-2.07,12.59-3.55,18.79-1.72,7.19-3.84,14.25-6.46,21.19-2.69,7.13-5.89,14.01-9.48,20.71-4.35,8.12-9.36,15.83-15.06,23.07-4.47,5.67-9.37,10.95-14.69,15.81-6.98,6.38-14.23,12.44-21.83,18.09-2.72,2.03-5.25,4.33-8.18,6.06-5.91,3.49-11.87,6.88-18.14,9.72-5.23,2.37-10.55,4.46-16.02,6.18-6.16,1.93-12.4,3.48-18.81,4.37-5.27,.73-10.54,1.34-15.85,1.61-1.59,.08-3.17,.37-4.77,.34-1.68-.03-1.61-.03-1.74-1.65-.48-5.93-.28-11.87-.36-17.8-.01-1.03,.17-2.07-.65-3.02-.34-.4-.17-1.28-.17-1.95,0-14.16,0-28.32,0-42.48,.83-.83,1.85-.59,2.83-.49,11.93,1.3,23.78,3.19,35.7,4.55,1.46,.17,2.93,.24,4.39,.28,6.15,.17,11.66,2.62,17.29,4.66,4.14,1.51,8.29,3,12.68,3.6,5.98,.81,11.96,.56,17.91-.42,.46-.08,.91-.2,1.38-.19,.84,.1,1.58-.47,2.4-.4,.21,.12-.09-.03,.17-.01,.12,.01,.08,.02-.14-.01-.82-.1-1.54,.43-2.35,.4-3.65,.33-7.31,.1-10.96,.17-4.97-.42-9.67-1.9-14.27-3.72-1.7-.67-3.7-.75-5-2.32-.16-.5,.08-.86,.44-1.16,2.62-1.13,5.48-.9,8.22-1.32,10.91-1.68,21.43-4.68,31.74-8.58,7.1-2.69,14-5.84,20.92-8.95,.73-.21,1.44-.51,2.14-.76,.09-.07,.2-.11,.32-.11-.26-.11-.4,.17-.63,.2-.81,.31-1.65,.53-2.42,.92-11.64,4.67-23.7,7.8-36.06,9.73-11.06,1.73-22.18,1.72-33.3,.53-1.33-.14-2.66-.22-3.97-.45-2.07-.37-2.4-.82-2.17-2.97,.5-4.55,1.81-8.89,3.43-13.15,1.09-2.89,2.64-5.55,4.3-8.13,1.48-2.32,3.27-4.28,5.71-5.76,10.54-6.42,20.87-13.19,31.42-19.61,1.02-.55,1.99-1.17,3.01-1.71,.24-.17,.55-.24,.77-.48-.33,.21-.66,.35-.98,.53-1.03,.64-2.15,1.11-3.15,1.8-4.43,2.61-9.11,4.75-13.68,7.1-9.01,4.65-17.99,9.38-27.1,13.85-2.5,1.23-2.73,1.13-3.71-1.52-2.33-6.26-4.35-12.63-6.68-18.89-.14-.37-.27-.76-.36-1.15-.37-1.73,.03-2.2,1.83-1.97,2.72,.35,5.35,1.11,7.96,1.94,6.49,2.05,12.86,4.45,19.29,6.69,.61,.21,1.19,.58,2.02,.44-4.62-6.48-9.19-12.89-13.81-19.26-.54-.75-1.25-1.42-1.26-2.44,.05-.32,.23-.55,.53-.68,1.05-.23,1.96,.28,2.89,.6,10.25,3.63,20.54,7.14,30.82,10.66,.49,.17,.95,.48,1.36,.4-6.89-7.51-14.46-14.45-21.35-22.06-2.87-3.16-6.19-5.89-8.81-9.29-.4-.52-.96-.95-1.02-1.68,.03-.32,.2-.54,.49-.67,.73-.22,1.3,.17,1.84,.56,4.53,3.28,8.97,6.67,13.11,10.44,.13,.12,.26,.25,.38,.38,.06,.06,.12,.13,.18,.19,.12,.12,.23,.25,.33,.39,.06,.06,.11,.13,.17,.19,.23,.23,.47,.46,.69,.71,.21,.21,.35,.48,.54,.69,0,0,.04,.17,.04,.17,0,0,.01-.18,0-.18-.19-.21-.32-.47-.51-.68-.27-.29-.55-.56-.79-.87-.1-.13-.18-.27-.27-.41-.1-.13-.2-.26-.31-.39-.06-.06-.11-.13-.17-.19-.13-.12-.25-.25-.37-.39-7.77-10.24-15.53-20.49-23.3-30.74-10.46-13.81-20.91-27.63-31.41-41.4-.44-.58-.93-1.13-1.24-1.78-.16-.19,.16-.26-.05-.09-.15,.12,.07,.24,.06,.37,2.09,6.15,4.19,12.27,6.44,18.35,1.27,3.44,2.7,6.83,3.83,10.32,.02,.3-.03,.58-.2,.83-.13,.12-.29,.19-.47,.2-.15-.04-.29-.1-.42-.18-.05-.03-.11-.07-.16-.1-.13-.06-.18-.02-.14,.16,.02,.1,.04,.15,.07,.16,.08,.14,.14,.29,.21,.43,.16,.32,.31,.64,.44,.97,4.25,10.68,8.62,21.31,12.73,32.04,.09,.46,.11,.92,.22,1.38,.05,.18,.07,.36,.07,.55-.03,.3-.16,.53-.44,.67-.19,.02-.37-.02-.55-.11-.16-.1-.31-.22-.45-.34-5.18-6.85-10.08-13.92-15.11-20.88-1.6-2.21-3.22-4.41-4.71-6.67-.18,.38,.2,.62,.22,.94,1.12,3.44,2.08,6.92,2.82,10.46,.06,.37,.09,.74,.19,1.1,.05,.17,.09,.34,.12,.52,.07,.43,.16,.86,.3,1.28,.05,.17,.08,.35,.1,.53,0,.47-.06,.93-.58,1.14-.19-.02-.34-.11-.44-.27-.18-.32-.29-.66-.43-.99-.07-.16-.14-.33-.2-.5-.13-.34-.32-.65-.48-.96-.02-.04-.07-.1-.07-.17,0-.22-.1-.24-.12-.03-.5,4.4-.13,8.86-.88,13.25-.54,1.03,.28,2.48-1.01,3.28-.61-.24-.72-.83-.92-1.35-.18-.66-.17-1.34-.19-2.02-.08-2.02,.2-4.04-.18-6.05-.07-.22-.12-.45-.15-.68,.05-2.92-.29-5.82-.48-8.73-.04-1.25,.16-2.52-.25-3.75-.2-1.03-.12-2.07-.15-3.11-.03-1.25,.16-2.51-.26-3.73-.17-.93-.12-1.87-.14-2.81,.25-2.38-.54-4.69-.4-7.06-.06-1.51,.2-3.03-.25-4.52-.17-.83-.13-1.66-.15-2.5,.1-4.09-.38-8.16-.64-12.23-.22-1.06-.13-2.13-.16-3.2,.2-1.83-.26-3.61-.39-5.41,0-.31-.02-.62,0-.92,.09-1.1-.02-2.21,.11-3.31,.02-.16,.03-.26,0-.31-.02,.05-.04,.1-.06,.18-3.48,6.1-6.95,12.19-9.95,18.52-1.66,3.5-3.28,7.01-4.93,10.51-.42,.89-.51,2.42-2.22,1.9-.02-2.63-.04-5.27-.04-7.9,0-.86-.02-1.71-.26-2.54,.22-.22,.37-.46,.26-.79h-.01Z" style="fill:#00305c;" />
                    <path d="M146.7,114.78c-7.77-23.03-15.53-46.06-23.3-69.09-.11,.02-.22,.03-.33,.05-8.07,23.45-16.14,46.91-24.26,70.48-.74-.45-.75-1.15-.94-1.72-5.68-16.89-11.34-33.79-17.01-50.68-1.17-3.48-2.39-6.94-3.49-10.44-.34-1.08-.85-1.46-1.98-1.45-16.09,.03-32.17,.02-48.25,.03-1.49,0-1.59,.13-1.66,1.56-.02,.33,0,.67,0,1,0,17.17,.03,34.34-.05,51.51,0,1.63,.57,2.31,1.95,2.97,17.82,8.51,35.61,17.09,53.4,25.65,.7,.34,1.39,.71,2.38,1.22-21.01,12.88-41.79,25.61-62.83,38.5,22.26,3.77,44.23,7.95,66.6,12.14-4.32,3.9-8.65,7.37-12.88,10.96-4.28,3.63-8.58,7.22-12.87,10.83-4.24,3.57-8.48,7.14-12.71,10.71-4.27,3.6-8.52,7.21-12.72,11.01,22.1,.99,44.15,1.81,66.16,2.87,.38,.84-.33,1.15-.67,1.56-8.66,10.25-17.34,20.5-26.03,30.73-5.36,6.31-10.75,12.6-16.12,18.9-.21,.24-.52,.44-.38,.96,20.91-2.28,41.69-5.56,62.49-8.7,.2,.82-.3,1.17-.58,1.58-9.78,14.5-19.55,29.02-29.4,43.48-2.33,3.42-4.58,6.89-6.71,10.43-.17,.28-.42,.52-.32,1.14,23.53-7.28,47.02-14.55,70.54-21.82,.2,.76-.17,1.12-.37,1.52-8.3,16.75-16.61,33.49-24.93,50.24-.74,1.49-.75,1.49,.54,2.58,11.48,9.7,22.94,19.41,34.45,29.06,1.14,.96,1.59,1.95,1.59,3.41,.01,13.76,.09,27.53,.14,41.29,0,1.64-.02,1.68-1.65,1.62-6.67-.26-13.31-.97-19.91-1.97-8.84-1.34-17.57-3.2-26.16-5.7-8.94-2.6-17.37-6.44-25.68-10.58-9.03-4.5-16.85-10.75-24.77-16.87-14.51-11.19-26.08-24.98-35.47-40.67-7.15-11.95-13.32-24.36-17.97-37.51-3.07-8.7-5.35-17.61-6.93-26.7-.65-3.75-1.27-7.5-1.84-11.26-.2-1.31-.25-2.66-.25-3.99-.01-87.78-.01-175.57-.01-263.36,0-.27,0-.53,0-.8,.03-1,.56-1.52,1.6-1.54,.47-.01,.94,0,1.4,0,50.86,0,101.72,0,152.57,0,3.18,0,2.8-.34,2.83,2.73,.11,11.62,.21,23.25,.34,34.87,.01,1.22-.16,2.38-.52,3.54-3.71,11.91-7.41,23.83-11.1,35.75-1.66,5.36-3.28,10.73-4.92,16.09-.19,.63-.41,1.25-.61,1.87-.14,.02-.28,.03-.42,.05h0Z" style="fill:#ffa42f;" />
                    <path d="M329.3,110v87.77c0,2.78,.26,2.5-2.43,2.5-50.91,0-101.82,0-152.74,0-3.28,0-2.93,.5-2.97-3.04-.54-58.17-.71-116.35-.96-174.52-.01-3.19-.48-2.75,2.73-2.75,51.25-.01,102.49,0,153.74,0,2.97,0,2.62-.42,2.62,2.69,0,29.12,0,58.24,0,87.37h0Z" style="fill:#fff;" />
                    <path d="M296.32,359.94c-6.11,3.13-12.42,5.81-18.8,8.32-8.81,3.47-17.8,6.35-27.08,8.27-4.5,.93-9.04,1.6-13.61,2.03-.59,.06-1.18,.18-1.77,.27,.18,.41-1.58-.2-.4,.75,6.08,2.53,12.23,4.82,18.81,5.66,3.32,.38,6.65,.05,9.97,.2,.54,.02,1.38-.53,1.62,.53-10.05,2.27-19.93,1.89-29.65-1.78-4.73-1.79-9.46-3.58-14.34-4.97-.64-.18-1.25-.41-1.94-.43-6.99-.24-13.92-1.18-20.84-2.12-7.84-1.07-15.68-2.16-23.56-3-.72-.08-1.46-.04-2.19-.06-.41-.2-.86-.35-.84-.95,.09-3.75-.23-7.52,.14-11.27,2.91-1.11,6.04-.85,9.04-1.41,12.52-1.3,24.97-3.1,37.46-4.6,1.4-.17,2.39-.68,2.9-2.04,.58-1.56,1.39-3.02,2.11-4.41-14.65,6.02-30.08,9.13-45.71,11.41-1.95,.23-3.88,.63-5.85,.21-.25-1.63-.04-3.27-.13-4.9-.04-.71,.26-1.11,.9-1.31,.82-.69,1.86-.69,2.83-.83,5.27-.75,10.5-1.71,15.71-2.78,3.37-.69,3.37-.68,3.29-4.47-.31,1.39-1.02,1.73-2.09,1.57-6.24-.96-12.49-1.89-18.71-3-.4-.07-.75-.25-1.04-.54-.94-.24-.93-.95-.93-1.72-.01-4.07-.06-8.15-.07-12.22-.02-21.43-.03-42.86-.03-64.3,0-.65-.21-1.34,.23-1.94,.24-.2,.52-.27,.83-.24,5.36,2.02,10.33,4.75,14.84,8.29,.23,.18,.44,.39,.65,.6,.41,.44,.8,.9,1.12,1.41,.76,1.13,1.03,2.41,1.11,3.73,.05,.79,.07,1.57,.52,2.27,.67,1.65,.36,3.42,.6,5.13,.25,1.82,.41,3.69,.53,5.54,.03,1.13-.13,2.28,.22,3.39,.18,.68,.17,1.38,.18,2.08-.03,2.45,.4,4.89,.25,7.34,.06,1.35-.18,2.73,.32,4.05,.05,.22,.07,.45,.07,.68,.02,1.02,0,2.04,0,3.05,.01,.95-.31,1.84-.24,2.43,.34-1.64,.24-3.67,.24-5.69,0-.56-.23-1.06-.34-1.6-.03-.22-.04-.44-.05-.66-.01-1.13,0-2.26,0-3.38-.03-1.16,.09-2.33-.11-3.48-.16-1.62,.17-3.28-.26-4.89-.28-1.17-.14-2.37-.18-3.55-.09-2.12,.24-4.25-.23-6.35-.12-.48-.14-.97-.16-1.46,0-1.4-.06-2.8,.03-4.19,.04-.67-.13-1.55,.95-1.67h0c.5,2.12-.19,4.32,.39,6.45,.21,.62,.17,1.25,.04,1.88,.26,1.33,.53,2.67,.57,4.03,.06,.19,.1,.39,.14,.59,.06,.58,.04,1.15-.14,1.71,0,.21-.04,.42-.04,.67,.06-.33,.02-.65,.13-.95,.3-1.49-.36-3,.13-4.48,.43-1.12-.09-2.32,.33-3.44,.17-3.43,.32-6.85,.51-10.28,.08-1.54,.22-3.07,.34-4.76,.83,.68,.68,1.61,1.19,2.21,.23,.27,.21,.62,.32,.93-.03,.11,0,.2,.09,.27,.22,.25,.17,.61,.34,.89-.03,.11,0,.21,.07,.3,1.06,.9,.9,2.34,1.51,3.44,.17,1.03,.59,1.98,.98,2.93,.11,.35,.03,.74,.2,1.09,.17,.43,.26,.88,.43,1.31,.29,.77,.54,1.56,.76,2.35,.16,.48,.1,1.04,.5,1.43-.41-.52-.34-1.19-.57-1.77-.22-.79-.49-1.55-.77-2.32-.12-.45-.28-.87-.43-1.31-.04-.28-.02-.56-.11-.84-.59-1.12-.78-2.38-1.25-3.54-.49-1.31-.65-2.69-.84-4.05,.03-.12,0-.22-.07-.31-.31-.37-.04-.89-.35-1.26,.03-.13,0-.24-.08-.34-.17-.28-.16-.62-.29-.91-.66-3.3-1.73-6.5-2.56-9.76-.11-.45-.21-.9-.32-1.35-.1-.45-.59-1.02,.34-1.17,4.12,5.72,8.23,11.45,12.35,17.17,2.61,3.62,5.22,7.22,7.84,10.83,.07,.16,.17,.3,.31,.41h0c.3,.17,.59,.34,.75,.66,.11,.38,.39,.67,.56,.99-.01-.03,.03,.06,.03,.06,0,0-.03-.11-.04-.13-.34-.44-.5-.95-.67-1.45-.07-.32,.03-.68-.21-.96h0c-.41-.28-.04-.87-.44-1.15-2.13-5.44-4.26-10.87-6.4-16.3-2.16-5.49-4.32-10.99-6.48-16.48-.13-.29-.07-.66-.35-.9h0c-.18-.39-.38-.81,.37-.77l.02,.02c.16,.08,.27,.2,.34,.37l.09-.03c.46,.17,.59,.01,.44-.44-3.35-8.63-6.56-17.31-9.48-26.1-.38-1.14-.7-2.29-1.05-3.44-.12-.45,.09-.57,.5-.5,4.72,6.2,9.46,12.39,14.17,18.59,8.46,11.14,16.91,22.29,25.36,33.44,5.35,7.06,10.69,14.13,16.03,21.21,.32,.42,.59,.88,.89,1.32,.13,.14,.24,.28,.33,.44l.03,.05c.16,.06,.26,.18,.3,.34,.12,.06,.19,.15,.21,.27,.11,.44-.09,.58-.5,.53h0c-.11-.17-.25-.3-.43-.39h.02c-.1-.18-.26-.32-.47-.38-4.47-3.88-9.04-7.63-13.93-10.98-.22-.15-.46-.25-.69-.38-.63-.07-1.13-.4-1.58-.79,.01,.01-.04-.13-.04-.13,0,0-.02,.15,0,.16,.49,.35,.97,.68,1.05,1.34,3.97,4.59,8.31,8.83,12.49,13.23,4.59,4.83,9.31,9.54,13.99,14.29,1.54,1.57,3.12,3.1,4.67,4.66,.46,.47,1.02,.86,1.41,1.87-6.33-1.98-12.31-4.21-18.35-6.25-6.06-2.04-12.08-4.17-18.12-6.27-.49-.19-.58,.05-.5,.47,5.34,7.45,10.69,14.9,16.27,22.68-2.71-.52-4.85-1.41-7.03-2.17-5.72-1.99-11.38-4.19-17.2-5.89-2.3-.67-4.62-1.29-7-1.59-.33-.04-.66-.11-1.14,.27,2.71,7.76,5.45,15.6,8.23,23.58,2.59-1.31,5.03-2.52,7.45-3.77,11.14-5.73,22.27-11.47,33.42-17.19,.77-.4,1.56-.74,2.34-1.11,.52-.3,.85-.26,.82,.45-5.91,3.74-11.82,7.48-17.73,11.21-5.13,3.23-10.26,6.46-15.41,9.66-.87,.54-1.54,1.25-2.15,2.05-5.51,7.17-8.47,15.35-9.59,24.25-.18,1.42-.09,1.5,1.23,1.68,11.73,1.56,23.49,1.91,35.24,.34,11.46-1.52,22.59-4.45,33.52-8.16,1.76-.6,3.52-1.2,5.29-1.8,.45-.23,.84-.35,.8,.4h0Z" style="fill:#ffa42f;" />
                    <path d="M190.3,280.14c-.68-.37-.5-1.02-.49-1.58,.03-1.62-.34-3.12-1.19-4.5,.15-.52-.32-.72-.55-1.05-.33-.4-.63-.84-1.09-1.12-1.76-2.07-3.35-4.27-5.05-6.39-2.64-3.28-5.77-6.01-9.05-8.59-.44-.32-.81-.71-1.1-1.18-.59-2.41-.3-4.85-.19-7.28,.04-.78,.18-1.57,.18-2.36,.98,.24,1.06-.59,1.32-1.14,1.53-3.24,3.02-6.5,4.55-9.75,3.28-6.98,6.92-13.76,10.98-20.32,.98,.1,.45,.86,.54,1.32,.14,.74-.14,1.53,.18,2.27,0,.25,.01,.51,.02,.76-.54,.64-.36,1.39-.32,2.12,.2,1.88,.05,3.77,.11,5.65,.23,5.33-.5,10.65-.33,15.98,.35,10.84,.57,21.7,1.86,32.49,.18,1.56,.49,3.19-.39,4.68h0Z" style="fill:#ffa42f;" />
                    <path d="M172.56,344.28c3.98,.32,7.89,1.18,11.83,1.75,3.08,.44,6.15,.96,9.19,1.44,.49-.6,.26-1.23,.33-1.8,.04-.39,0-.88,.55-.87,.41,.01,.44,.42,.46,.75,0,.13,0,.27,.01,.4,.07,1.45,.65,3.15-.15,4.28-.7,.98-2.43,.8-3.72,1.07-5.4,1.14-10.84,2.07-16.31,2.86-.72,.1-1.45,.12-2.18,.18,.01-.79,.02-1.59,.03-2.38,.6-.41,1.26-.15,1.9-.18,1.28,.3,2.64,.12,3.9,.52-1.15-.35-2.35-.29-3.53-.44-.71-.34-1.57,.18-2.24-.37-.02-2.39-.04-4.79-.06-7.19h0Z" style="fill:#00305c;" />
                    <path d="M181.11,360.42c-1.51,.62-3.14,.27-4.71,.57-1.48,.29-3.03,.28-4.54,.4,.36-.3,.34-.59-.06-.86,2.01-.09,3.96-.78,5.99-.58,1.09,.24,2.32-.49,3.31,.47h0Z" style="fill:#2d4453;" />
                    <path d="M171.8,255.73c.39,.25,.91,.3,1.21,.71,.33,2.03,.18,4.08,.14,6.12,0,.59-.09,1.19-.62,1.6-.26-.02-.52-.03-.78-.05,.01-2.79,.03-5.59,.04-8.38h0Z" style="fill:#b78238;" />
                    <path d="M328.31,294.57c-.7-1.73-.36-3.52-.21-5.26,.07-.84,.6-.3,.92,.01-.33,1.74-.17,3.54-.71,5.25h0Z" style="fill:#163a5c;" />
                    <path d="M174.58,351.95c-.66,0-1.32,0-1.99,0,0-.16,.02-.32,.02-.48,.78,.02,1.57,.05,2.35,.07,.11,.36,.02,.54-.39,.41h0Z" style="fill:#010b14;" />
                    <path d="M171.76,234.85c.28,.08,.72,.02,.64,.46-.09,.51-.56,.33-.9,.33-.05-.12-.05-.25,0-.37,.07-.16,.16-.3,.27-.42h0Z" style="fill:#30587d;" />
                    <path d="M171.77,234.86c-.09,.14-.18,.28-.27,.42-.02-.21,.08-.34,.27-.42Z" style="fill:#00305c;" />
                    <path d="M96.31,378.41c.11,.06,.38,.14,.39,.25,.04,.42-.29,.51-.62,.49-.17,0-.49-.13-.48-.18,.03-.37,.3-.51,.71-.56h0Z" style="fill:#d08527;" />
                    <path d="M192.64,276.93c-.63,.5-.4,1.2-.42,1.83-.04,1.44-.06,2.89-.08,4.33-.27,0-.47-.11-.6-.35-.66-2.53-.19-5.1-.33-7.65,.04-1.4,.2-2.81-.21-4.19-.22-1.17-.16-2.35-.17-3.53,.01-1.51,.21-3.03-.24-4.52-.21-1.19-.18-2.4-.16-3.61,.06-1.07-.02-2.14,.06-3.2,.05-.63-.04-1.39,.89-1.53,.21,.88,.17,1.76,.08,2.65,.39,1.23-.1,2.52,.29,3.74,.23,1.02,.16,2.05,.11,3.08,.4,1.22-.14,2.52,.32,3.74,.2,3.06,.55,6.12,.46,9.2Z" style="fill:#eb9d33;" />
                    <path d="M189.84,227.98c.07,.11,.21,.21,.21,.32,.11,4.07,.71,8.12,.51,12.21-.44,.76,.12,1.68-.39,2.44-.24,0-.44-.09-.59-.28-.36-.6-.4-1.27-.37-1.93,.13-3.25-.29-6.49-.18-9.74,.37-.98-.09-2.18,.8-3.02h0Z" style="fill:#ac803d;" />
                    <path d="M197.02,207.21c.33,.6,.37,1.25,.4,1.93,.1,2.25-.26,4.52,.25,6.75,.06,.28-.06,.55-.33,.64-.44,.14-.6-.21-.65-.53-.07-.45-.06-.92-.08-1.39-.08-1.92,.18-3.85-.35-5.75-.15-.54-.21-1.2,.34-1.67,.15-.18,.29-.17,.42,.01h0Z" style="fill:#12212e;" />
                    <path d="M191.37,254.53c-.67,.75-.27,1.65-.34,2.49-.07,.79-.04,1.6-.06,2.39-1.1-.37-.92-1.36-.95-2.17-.07-2.32-.04-4.64-.03-6.96,.34-.98-.52-2.35,.96-2.96,.21,2.4,.53,4.79,.42,7.21h0Z" style="fill:#df9634;" />
                    <path d="M265.07,385.97c-.53-.6-1.22-.34-1.85-.34-2.56,0-5.12,.03-7.68,0-.69-.01-1.46,.18-2.07-.38,1.92,.02,3.83,.07,5.75,.05,2.05-.01,4.1-.09,6.15-.13,.72-.02,1.57-.68,2.09,.39-.8,.13-1.55,.55-2.4,.42h0Z" style="fill:#c08739;" />
                    <path d="M190.95,247.32c-.67,.9-.16,1.96-.42,2.91-.34-.09-.62-.3-.67-.63-.33-2.24-.71-4.49,.27-6.69l.18-.05c.12-.16,.24-.15,.34,.01,.37,1.46,.41,2.95,.29,4.44h0Z" style="fill:#d09037;" />
                    <path d="M188.65,220.9c.05-.64-.35-1.48,.72-1.68,.22,1.87,.59,3.74,.4,5.64-.21,.11-.39,.08-.54-.09-.69-1.22-.19-2.61-.57-3.87h0Z" style="fill:#9d783f;" />
                    <path d="M198.22,276.56c-.46-1.18-.97-2.35-1.18-3.62,.48-.32,.3-.84,.42-1.28,.67,1.26,.85,2.67,1.2,4.03,.13,.43,.35,.9-.44,.86h0Z" style="fill:#284254;" />
                    <path d="M265.13,320.31c-.16-.36-.49-.41-.82-.45,.94-.75,1.99-1.27,3.12-1.66,.46-.17,.54,.06,.47,.45-.89,.61-1.71,1.33-2.76,1.66h0Z" style="fill:#cb8c37;" />
                    <path d="M189.36,218.46c-.56-.51-.32-1.18-.35-1.79-.03-.62,.18-1.28-.37-1.8-.03-.58-.22-1.18,.39-2.07,.47,2.07,.44,3.86,.32,5.66h0Z" style="fill:#8f7041;" />
                    <path d="M194.17,283.38c0,1.2-.02,2.39-.03,3.59-.56,.84,.48,2.07-.68,2.76l-.1,.07c-.24-1.08-.32-2.15,.06-3.22,.52-.44,.32-1.05,.36-1.6s-.15-1.16,.38-1.6Z" style="fill:#213e55;" />
                    <path d="M193.02,283.38c-.44-.32-.42-.8-.41-1.26,0-1.73,.02-3.46,.03-5.18,.43,2.13,.39,4.29,.39,6.45Z" style="fill:#eb9d33;" />
                    <path d="M189.44,224.87c.11-.01,.22-.01,.33,0,.02,1.04,.05,2.08,.07,3.12-.22,.94,.25,1.97-.36,2.87-.9-1.08-.3-2.35-.48-3.53,.1-.83-.26-1.73,.43-2.45h0Z" style="fill:#c78d39;" />
                    <path d="M296.32,359.94c-.17-.34-.51-.32-.8-.4,.69-.48,1.47-.73,2.29-.87,.41-.16,.54,.02,.49,.41-.63,.36-1.22,.81-1.98,.86h0Z" style="fill:#bf8639;" />
                    <path d="M267.47,385.54c-.61-.6-1.4-.22-2.09-.39,1.12-.47,2.29-.34,3.64-.38-.44,.69-.98,.75-1.55,.77Z" style="fill:#b2803b;" />
                    <path d="M193.43,286.58c-.02,1.07-.04,2.15-.06,3.22-.74-1.47-.26-3.02-.32-4.54,.13,.44,.25,.88,.38,1.32h0Z" style="fill:#eb9d33;" />
                    <path d="M267.89,318.65c-.16-.15-.32-.3-.47-.45,.67-.55,1.44-.85,2.29-1.06-.32,.88-1.07,1.19-1.82,1.51h0Z" style="fill:#ad7e3c;" />
                    <path d="M192.17,267.74c-.73-1.19-.26-2.49-.32-3.74,.55,1.21,.21,2.49,.32,3.74Z" style="fill:#df9634;" />
                    <path d="M191.74,260.92c-.68-1.2-.23-2.49-.29-3.74,.54,1.21,.27,2.49,.29,3.74Z" style="fill:#df9634;" />
                    <path d="M232.31,281.33c.61,.31,1.21,.63,1.65,1.17-.19,.19-.38,.38-.57,.57-.68-.42-1.19-1.01-1.61-1.68,.16-.2,.34-.21,.54-.06h0Z" style="fill:#324652;" />
                    <path d="M234.66,379.57c-.53,.02-.97-.16-1.46-.67,.68-.23,1.26-.19,1.85-.08-.13,.25-.26,.5-.4,.75h0Z" style="fill:#304553;" />
                    <path d="M194.13,256.57c-.44,.3-.22,.78-.34,1.17-.3-.28-.4-.65-.44-1.03-.03-.29-.28-.73,.09-.84,.48-.15,.55,.37,.69,.71h0Z" style="fill:#bc853a;" />
                    <path d="M201.77,248.54c-.3,.17-.3,.49-.37,.78-.12-.16-.28-.3-.34-.48-.1-.3-.41-.73-.08-.9,.47-.25,.62,.26,.79,.6h0Z" style="fill:#cb8d38;" />
                    <path d="M249.46,294.62c.17-.18,.33-.36,.5-.53,.39,.18,.55,.55,.74,.89,.11,.44-.09,.57-.49,.48-.33-.2-.62-.45-.75-.84h0Z" style="fill:#d29036;" />
                    <path d="M298.3,359.08c-.17-.14-.33-.28-.49-.41,.46-.41,1.01-.48,1.64-.56-.1,.83-.73,.76-1.15,.97Z" style="fill:#a77c3d;" />
                    <path d="M190.66,242.88c-.11,0-.23,0-.34-.01-.02-.79-.33-1.62,.25-2.35,.2,.78,.22,1.57,.09,2.36h0Z" style="fill:#f1a132;" />
                    <path d="M215.08,285.75c-.15-.26-.3-.52-.44-.77,.34-.17,.36-.52,.43-.83,.29,.17,.37,.46,.42,.76,.11,.4,.36,.87-.41,.84Z" style="fill:#354852;" />
                    <path d="M250.2,295.45c.16-.16,.33-.32,.49-.48,.42,.32,.69,.75,.88,1.23-.17,.18-.36,.2-.57,.06-.27-.27-.54-.53-.8-.8h0Z" style="fill:#ab7e3d;" />
                    <path d="M202.66,248.46c.05,.04,.12,.07,.15,.13,.18,.33,.51,.76,.01,.97-.29,.13-.51-.33-.6-.66,.15-.15,.29-.29,.44-.44h0Z" style="fill:#57574c;" />
                    <path d="M228.97,305.06c-.32-.12-.61-.44-.42-.67,.29-.36,.66-.11,.91,.2-.17,.16-.33,.31-.5,.47Z" style="fill:#917141;" />
                    <path d="M192.62,218.42c-.17,.17-.33,.33-.5,.5-.08-.09-.19-.18-.23-.29-.11-.29-.26-.75,.02-.86,.44-.17,.6,.28,.71,.65h0Z" style="fill:#be883a;" />
                    <path d="M214.62,283c.39,.3,.48,.71,.44,1.17-.49-.26-.48-.71-.44-1.17Z" style="fill:#354852;" />
                    <path d="M197.04,270.09c.35,.35,.41,.79,.35,1.26-.46-.32-.41-.79-.35-1.26Z" style="fill:#284254;" />
                    <path d="M196.67,268.84c.28,.25,.35,.56,.29,.91-.34-.22-.38-.55-.29-.91Z" style="fill:#284254;" />
                    <path d="M196.96,272.64c-.3-.23-.37-.53-.34-.89,.36,.2,.41,.52,.34,.89Z" style="fill:#284254;" />
                    <path d="M201.4,249.31c.37,.2,.44,.52,.35,.9-.32-.22-.39-.53-.35-.9Z" style="fill:#cb8d38;" />
                    <path d="M196.54,271.48c-.29-.25-.37-.57-.32-.93,.36,.22,.41,.55,.32,.93Z" style="fill:#284254;" />
                    <path d="M197.02,207.21c-.14,0-.28,0-.42-.01,.05-.16,.09-.44,.15-.45,.34-.02,.22,.27,.27,.46Z" style="fill:#2e5479;" />
                    <path d="M251.01,296.26c.19-.02,.38-.04,.57-.06,.14,.1,.17,.25,.04,.32-.28,.15-.48,.02-.6-.26Z" style="fill:#7d6842;" />
                    <path d="M248.58,293.85c.25,.03,.4,.17,.47,.4-.24-.03-.4-.16-.47-.4Z" style="fill:#d29036;" />
                    <path d="M249.75,293.81c-.1-.11-.2-.23-.3-.34,.19,.03,.3,.14,.3,.34Z" style="fill:#d29036;" />
                    <path d="M249.41,293.42c-.21-.07-.32-.22-.33-.44,.22,.07,.33,.21,.33,.44Z" style="fill:#d29036;" />
                    <path d="M214.63,284.98c-.21-.05-.33-.18-.31-.41,.2,.06,.3,.2,.31,.41Z" style="fill:#354852;" />
                    <path d="M202.12,248.94l-.34-.37c.21,.03,.33,.15,.34,.37Z" style="fill:#cb8d38;" />
                    <path d="M249.03,294.23c.23,.03,.37,.17,.43,.39-.23-.04-.37-.17-.43-.39Z" style="fill:#d29036;" />
                    <path d="M307.93,116.25c.62,.24,1.26,.44,1.86,.72,.99,.45,1.89,1.08,2.28,2.14,.56,1.5,1.06,3.02,1.5,4.55,.2,.69-.18,.99-.89,.83-1.68-.36-3.18-1.19-4.32-2.39-2.35-2.45-5.23-3.01-8.41-2.84-1.73,.09-3.46,.2-5.12,.75-1.44,.47-2.14,1.56-1.99,3.06,.51,5.08,2.06,9.75,5.32,13.78,2.1,2.6,4.64,4.57,7.69,5.92,1.46,.65,2.93,1.3,4.34,2.04,1.88,.99,2.95,2.61,3.47,4.66,.57,2.27,.63,4.61,.92,6.91,.37,2.97,.75,5.95,.58,8.96-.06,1.04,.39,1.47,1.38,1.45,.6-.01,1.2,.03,1.8,0,.78-.04,1.53,.13,2.01,.7,1.03,1.22,3.11,1.42,3.32,3.45,.04,.38,.34,.8,.17,1.15-.29,.58,0,1.54-.73,1.74-.76,.21-.73-.77-1.1-1.17-.46-.51-.82-1.36-1.62-.32-.37,.48-.97,.37-1.5,.32-.33-.03-.64-.18-.96-.26-2-.44-2.97,.36-2.89,2.38,.04,1.1-.07,2.11-1.21,2.74-1.21,.67-1.27,1-.65,2.12,.27,.49,1.13,.9,.63,1.49-.52,.61-1.22-.05-1.81-.24-1.1-.36-1.94-1.13-2.08-2.23-.21-1.61-1.2-3.06-.75-4.84-.61,.32-.69,.79-.89,1.2-.24,.5-.6,1-1.19,.92-1.31-.18-1.22,.64-1.13,1.47,.03,.26,.15,.51,.25,.75,.14,.32,.28,.66-.01,.92-.28,.25-.66,.11-.92-.08-.81-.6-1.73-1.21-1.92-2.23-.35-1.93-.8-3.95,.9-5.58,.14-.13,.22-.32,.32-.49,.02-.05-.02-.13-.05-.3-1.29-.15-2.77,.13-3.74-1.21-.2-.28-.58,.11-.83,.32-.63,.52-.75,1.38-1.17,2.08-1.46-1.64-1.26-4.05,.33-5.21,1.53-1.11,3.34-1.63,5.45-1.56,1.34,.04,1.44-.05,1.48-1.44,.09-3.07-1.06-5.58-3.4-7.56-1.29-1.1-2.78-1.85-4.31-2.52-5.68-2.5-10.56-6.18-15.18-10.25-3.61-3.18-7.08-6.49-10.48-9.89-2.46-2.46-5.33-3.74-8.89-2.6-1.37,.44-2.54,1.23-3.54,2.2-4.62,4.46-9.88,8.12-14.87,12.11-3.51,2.8-5.87,1.88-6.78-2.5-.21-1.02-.38-2.06-.6-3.29-1.2,.58-1.62,1.57-1.99,2.5-.54,1.36-.91,2.78-1.35,4.18-.81,2.59-1.91,4.98-4.13,6.71-.69,.54-1.38,1.1-2.21,1.34-4.92,1.39-8.96,4.38-13.09,7.19-3.64,2.49-7.36,4.83-11.35,6.73-.29,.14-.57,.32-1.06,.59,1.44,1.12,1.29,2.81,1.82,4.24,.62,1.67-.39,3.03-1.86,4.15-.48-.68-.25-1.35-.33-1.97-.14-1.08-.99-1.99-2-1.76-1.27,.29-2.19-.25-3.2-.76-1.03-.53-1.16-.44-1.53,.67-.26,.78-.23,1.57-.26,2.38-.03,1.01-.27,1.84-1.56,1.89-.8,.03-.98,.57-.66,1.26,.33,.71,.69,1.41,1.05,2.14-2.57,.14-4.11-1.09-4.19-3.43-.03-1.07-.53-2.08-.17-3.32-.99,.11-1.89,.81-2.65-.18-.29-.38-.74-.05-.99,.29-.32,.45-.37,.99-.51,1.5-.1,.35-.06,.88-.61,.88-.37,0-.53-.36-.7-.64-.61-1.01-1.06-2.23-.47-3.22,1.01-1.7,1.53-3.86,3.69-4.67,.18-.07,.32-.23,.66-.47-.56-.19-1-.37-1.46-.48-.39-.1-.78-.14-.99-.56-.19-.38,.03-1-.65-1.09-.53-.07-.85,.3-1.26,.5-.44,.21-.8,.95-1.35,.47-.48-.41-.08-1.02,.08-1.49,.55-1.68,1.65-2.7,3.51-2.72,.33,0,.72,.07,.98-.07,1.21-.68,2.38-.41,3.42,.23,1.87,1.15,3.6,.9,5.45-.06,4.07-2.11,7.65-4.86,10.77-8.19,1.64-1.75,3.17-3.59,4.52-5.59,1.09-1.62,2.59-2.87,3.93-4.26,.74-.76,1.47-1.51,1.75-2.73-5.07-.94-9.21-3.53-13.09-6.63-7.11-5.65-11.77-12.97-14.62-21.53-.75-2.26-1.23-4.62-2.26-6.8-1.42,0-1.8,2.04-3.32,1.49-1.27-.45-2.84,.22-3.9-1.09-.54-.67-1.29-1.18-1.24-2.27,.68-.34,1.24,.2,1.84,.3,1.1,.2,1.54-.06,1.45-1.17-.08-.92,.69-1.37,1-2.01-.44-.41-.85-.32-1.23-.29-1.17,.1-2.26,.23-2.77-1.26-.18-.52-1.33-.18-1.25-1.36,.12-1.67,.06-1.5,1.61-2.36,1.21-.67,2.28-.87,3.52-.42,.37-.66-.18-.91-.38-1.26-.23-.41-.49-.81-.37-1.31,.11-.49,.6-.98,.05-1.46-.47-.42-1.06-.51-1.68-.5-.46,0-.94,.12-1.37-.14,.57-2.05,3.41-3.13,5.05-1.7,1.5,1.32,3.61,2.09,4.3,4.34,.5-.95,.3-1.92,.34-2.85,.05-1.09,.24-2.02,1.59-2.1,.87-.05,.75-.71,.53-1.16-.34-.7-.85-1.31-1.31-1.98,1.68-.41,3.36,.05,3.93,1.38,.82,1.92,1.66,3.89,.93,6.13-.64,1.96-1.18,3.96-1.45,6.02-.26,1.98,0,3.85,1.14,5.55,4.7,6.98,10.25,13.12,17.25,17.87,2.23,1.52,2.37,1.49,3.77-.91,1.19-2.03,2.07-4.18,2.26-6.56,.13-1.59,.11-1.62-1.43-1.98-2.43-.58-4.67-1.55-6.69-3.04-.65-.48-1.23-1.01-1.59-1.76-.57-1.19-.17-2.13,1.11-2.41,1.19-.25,2.38-.11,3.54,.27,1.08,.35,2.15,.74,3.25,.99,3.08,.69,7.11-.92,8.82-3.46,2.26-3.36,.98-7.05-2.91-8.23-1.01-.31-2.08-.5-3.13-.57-2.59-.17-5.19-.2-7.79-.35-.99-.06-2-.16-2.96-.42-2.12-.57-3.02-1.95-2.52-4.05,.22-.91,.19-1.37-.62-1.98-1.98-1.49-1.72-3.15,.66-3.9,3.23-1.02,6.49-2.01,9.86-2.51,1.56-.23,2.95-.78,4.25-1.61,3.67-2.35,7.58-2.67,11.68-1.32,2.95,.97,2.97,.97,4.94-1.49,.73-.9,1.61-1.61,2.6-2.16,1.51-.84,2.82-.49,3.82,.91,1.8,2.56,1.77,5.55-.14,8.2-.74,1.03-1.6,1.96-2.39,2.96-.32,.4-.81,.75-.57,1.49,1.47-.02,2.95-.31,4.48,.03,3.23,.72,5.22,2.86,5.46,6.23,.04,.61,.03,1.16,.64,1.57,2.26,1.53,3.2,3.82,3.62,6.42,.28,1.77,.54,3.55,1.14,5.26,.29,.82-.03,1.12-.81,1.13-.73,0-1.45-.03-2.18-.17-1.46-.27-2.92-.2-4.31,.4-1.94,.84-2.82,2.47-2.31,4.52,.37,1.49,.9,2.93,1.28,4.41,.35,1.36,.12,2.1-.79,2.61-.94,.53-1.93,.53-2.82-.15-.58-.44-1.08-.99-1.63-1.47-.51-.44-1.12-.56-1.63-.09-.5,.47-.44,1.03-.02,1.61,1.98,2.73,4.84,3.69,7.44,2.07,1.53-.96,2.19-2.26,1.95-4.04-.2-1.47-.77-2.82-1.18-4.23-.78-2.7,.2-3.7,3.03-3.12,4.31,.88,8.67,1.26,13.04,1.6,5.4,.42,10.81,.38,16.2,.2,7.79-.27,15.53-1.21,22.9-4.02,4.55-1.73,8.65-4.18,11.78-8,1.83-2.23,2.93-4.73,2.4-7.75-.67-3.8-2.56-5.33-6.6-5.63-3.49-.26-6.9,.45-10.26,1.22-5.3,1.2-10.66,1.52-16.05,1.56-3.54,.02-7.07,.01-10.6-.11-2.66-.1-5.32-.29-7.95-.63-6.96-.9-11.88-5.24-13.06-11.87-.07-.39-.16-.79-.15-1.19,.14-4.54-.22-9.07-.33-13.61-.07-2.73-.4-5.45-.98-8.11-.22-1.03-.64-2.04-1.08-3-1.88-4.04-4.85-5.53-9.25-4.8-3.67,.61-6.95,2.23-10.28,3.79-2.56,1.19-5.23,1.93-8.09,1.93-4.22,0-7.42-1.86-9.83-5.21-.62-.86-1.14-1.81-1.61-2.76-.22-.44-.69-.98-.28-1.48,.41-.5,.97-.16,1.48-.01,2.25,.66,4.53,.96,6.89,.97,2.85,.01,5.55-.59,8.18-1.6,2.68-1.03,5.32-2.14,7.97-3.23,2.83-1.17,5.77-1.56,8.81-1.38,3.67,.22,6.43,1.97,8.42,5.01,1.27,1.95,2.2,4.09,3.4,6.55,.72-1.51,1.29-2.73,1.88-3.93,.81-1.68,1.6-3.35,2.89-4.77,2.15-2.37,4.9-3.36,7.94-3.78,3.52-.48,6.89,.29,10.21,1.34,1.46,.46,2.95,.88,4.34,1.5,4.03,1.81,7.8,1,11.48-.98,1.82-.98,3.52-2.16,5.09-3.51,.25-.21,.51-.46,.87-.24,.31,.18,.46,.48,.42,.84-.39,3.5-1.16,6.83-3.94,9.34-1.52,1.37-3.26,2.28-5.14,3.02-4.28,1.67-8.54,1.82-12.84,.08-1.91-.78-3.83-1.56-5.86-2-2.76-.6-5.43-.48-7.79,1.33-1.42,1.08-2.37,2.52-3.06,4.14-1.32,3.1-1.91,6.4-2,9.73-.13,4.4-.1,8.81,.26,13.2,.28,3.35,1.93,5.57,5.06,6.76,2.72,1.03,5.56,1.36,8.42,1.48,3.33,.15,6.65-.12,9.97-.44,1.58-.15,3.17-.28,4.75-.5,.3-.49-.16-.7-.38-.98-.89-1.11-1.49-2.35-1.86-3.73-.67-2.48-.89-5.16-2.96-7.08-.37-.34-.18-.75,.34-.86,3.05-.64,6.45,1.46,7.2,4.55,.52,2.16,1.17,4.23,2.55,6.02,.79,1.02,1.7,1.58,3.01,1.68,2.75,.19,5.44-.25,8.15-.56,.69-.08,1.46,.05,2.1-.33,.12-.64-.32-1.04-.54-1.5-.72-1.47-1.13-3.01-.95-4.64,.17-1.64-.06-3.18-.86-4.64-.25-.46-.6-1-.13-1.46,.48-.48,1.02,0,1.48,.16,2.01,.75,3.53,2.07,4.53,4.01,.83,1.62,1.23,3.38,1.76,5.1,.49,1.6,1.34,2.58,3.13,2.89,2.76,.47,4.79,2.21,6.43,4.42,4.42,5.96,4.11,13.41-.73,18.93-3.63,4.14-7.78,7.65-12.48,10.52-.3,.32-.66,.51-1.07,.63-.17,.02-.35-.02-.51,.07,.17-.06,.35-.04,.53-.05,.26-.02,.51-.03,.66,.23h0Z" style="fill:#da121a;" />
                    <path d="M245.88,167.1c-.96-.4-1.78-.91-1.77-2.04-1.22-.11-1.83,.93-2.8,1.18-.38-2.29,.94-4.1,3.17-4.29,.79-.07,1.56-.03,2.34-.35,.54-.22,1.18-.14,1.76,.1,.49,.2,1.02,.34,1.47,.61,2.83,1.73,5.28,.94,7.39-1.23,2.69-2.76,4.57-5.01,2.27-10.01-.42-.91-.86-1.81-1.29-2.71-.15-.3-.32-.59-.49-.87-1.34-2.23-1.41-4.55-.33-6.88,1.26-2.7,3.09-4.99,5.44-6.83,2.13-1.67,4.44-1.68,6.63-.02,1.16,.88,2.21,1.92,3.25,2.95,1.42,1.41,2.8,2.86,4.19,4.3,1.07,1.11,1.07,1.29,.09,2.47-.78,.94-1.8,1.58-2.77,2.28-.7,.51-1.41,1.01-2.14,1.48-1.08,.69-1.51,1.67-1.29,2.91,.38,2.1,.72,4.24,2.43,5.77,1.68,1.5,.67,5.26-1.1,6.6-1.13,.85-2.38,1.46-3.74,1.87-1.73,.53-3.35,1.29-4.93,2.18-.81,.46-1.05,.99-.78,1.97,.35,1.25,.41,2.58,.59,3.87,.2,1.43-.51,2.42-1.53,3.27-.26,.22-.53,.45-.88,.25s-.28-.57-.26-.89c.02-.4,.19-.8,.14-1.19-.11-.73-.31-1.6-1.25-1.52-1.4,.12-2.02-.74-2.66-1.71-.32-.49-.5-1.11-1.21-1.39-.75,.76-.47,1.72-.37,2.58,.14,1.19,.56,2.38-.86,3.16-.45,.25-.16,.7,.01,1.06,.36,.74,1.07,1.14,1.71,1.64-1.56,1.29-4.1,.59-4.83-1.4-.58-1.59-1.75-3-1.41-5-.78,.21-.87,.78-1.13,1.17-.41,.62-.78,1.46-1.47,1.57-1.5,.23-1.25,.94-.77,1.88,.17,.34,.5,.75,.14,1.09-.42,.41-.89,.03-1.27-.17-1.4-.73-2.33-1.75-2.12-3.5,.25-2.06,.44-4.11,2.47-5.32,.28-.3,.32-.6-.02-.88h0Z" style="fill:#da121a;" />
                    <path d="M217.18,103.44c-3.79,.07-7.55-.2-11.29-.84-3.03-.52-5.12-2.42-6.91-4.7-.99-1.25-.93-2.27-.14-3.06,.81-.82,2.11-.92,3.31-.2,.81,.48,1.5,1.14,1.98,1.94,1.06,1.77,2.73,2.51,4.63,2.71,1.45,.15,2.94,.14,4.38-.04,2.64-.33,5.27-.59,7.93-.53,1.18,.03,2.23,.35,2.85,1.48,.69,1.24,.42,2.12-.83,2.81-.61,.34-1.23,.45-1.92,.44-1.33-.02-2.66,0-4,0h0Z" style="fill:#da131b;" />
                    <path d="M245.88,167.1c.37-.09,.92-.18,.96,.2,.05,.46-.46,.67-.94,.69,0-.29-.01-.59-.02-.88h0Z" style="fill:#f4c2c3;" />
                    <path d="M307.13,115.8c.37-.21,.72-.48,1.19-.44-.13,.29-.26,.59-.39,.88-.25-.02-.51-.03-.76-.05-.14-.07-.2-.16-.16-.25,.04-.1,.08-.14,.12-.14Z" style="fill:#f7d3d4;" />
                    <path d="M181.11,360.42c-1.07-.4-2.31,.4-3.31-.47,3.6-.65,7.19-1.33,10.8-1.95,6.1-1.06,12.13-2.43,18.11-4.04,4.63-1.25,9.22-2.63,13.66-4.46,1.41-.58,2.79-1.24,4.31-1.91,.09,.6-.34,.87-.55,1.22-1.02,1.65-1.97,3.35-2.52,5.21-.36,1.22-1.08,1.63-2.26,1.77-8.06,.97-16.11,1.93-24.16,3.01-4.68,.63-9.37,1.17-14.08,1.62h0Z" style="fill:#01315c;" />
                    <path d="M189.33,227.29c.05,1.19,.1,2.37,.16,3.56,.14,3.84,.28,7.68,.44,11.52,0,.18,.14,.36,.22,.54,.06,2.44-.06,4.9,.4,7.32,.06,2.75,.11,5.5,.17,8.25,0,.33,.04,.65,.26,.92,.03,1.18,.05,2.36,.08,3.54,.41,1.5-.1,3.07,.31,4.57,.03,1.15,.06,2.3,.1,3.45,.41,1.36-.11,2.8,.3,4.16,.02,2.61,.05,5.22,.07,7.82,.61,1.71,.2,3.52,.52,5.26,.11,.84,.03,1.68,.06,2.52,.06,1.31-.09,2.62,.12,3.93,.1,.59,.3,1.36-.68,1.54-.17-.64-.13-1.28-.07-1.92-.44-1.06,.14-2.22-.34-3.27-.29-3.37-.57-6.75-.87-10.12-.02-.25-.17-.49-.25-.73,.08-2.94-.42-5.84-.68-8.75-.51-5.7-.92-11.41-1.06-17.13-.13-5.4-.29-10.8-.17-16.19,.09-3.73,.03-7.46,.25-11.18,.45-.24,.54,.13,.68,.41h0Z" style="fill:#03315b;" />
                    <path d="M172.53,264.16c.02-2.04,.02-4.08,.06-6.11,.01-.55-.24-1.21,.41-1.61,5.77,4.2,10.28,9.59,14.36,15.37,.29,.29,.27,.59,.02,.89-4.4-3.42-9.2-6.13-14.37-8.2-.18-.07-.32-.22-.48-.34h0Z" style="fill:#01315c;" />
                    <path d="M197.12,349.8c-.26-1.2-.44-2.43-.42-3.7,.03-1.38,.09-1.49,1.45-1.55,2.72-.11,5.45-.17,8.17-.24,2.92-.08,5.85-.16,8.77-.24,.31,0,.62,.04,1.32,.08-1.08,.95-2.04,1.35-3.01,1.64-5.23,1.53-10.51,2.87-15.83,4.08-.11,.03-.25-.04-.46-.07h0Z" style="fill:#03315b;" />
                    <path d="M217.44,272.55c-1.13-.55-2.21-1.23-3.39-1.61-1.8-.57-2.67-1.8-3.06-3.52-.17-.47-.28-.91,.47-.9,.69,1.7,2.07,2.69,3.59,3.58,1.09,.64,2.12,1.37,3.18,2.06,.03,.72-.33,.64-.78,.38h0Z" style="fill:#153958;" />
                    <path d="M191.84,296.19c.64-.68,.29-1.5,.33-2.27,.05-1.12,.04-2.24,.05-3.36,.2-.11,.3-.05,.32,.17,.03,1.16,.06,2.32,.09,3.47,.35,1.53,.07,3.08,.16,4.61,.06,1.26-.14,2.54,.12,3.79,.19,1.2,.16,2.41,.06,3.62-.19,.12-.36,.09-.51-.07-.2-.77,.14-1.58-.22-2.34-.03-2.55-.55-5.07-.41-7.64h0Z" style="fill:#274154;" />
                    <path d="M192.56,333.51c.04,2.66,.07,5.32,.11,7.98,.01,.66,.01,1.33,.1,1.99,.05,.44,.43,.97-.33,1.06-.82,.1-.76-.57-.75-1.09,.02-1.33-.05-2.68,.16-3.98,.32-1.99,.04-3.99,.36-5.96,.12-.19,.24-.19,.34,.01h0Z" style="fill:#274156;" />
                    <path d="M211.45,266.53c-.39,.18-.4,.55-.47,.9-.85-1.39-1.45-2.9-2-4.43-.11-.37-.41-.8,.34-.81,.78,1.42,1.62,2.8,2.12,4.35h0Z" style="fill:#344853;" />
                    <path d="M208.18,309.86c-.67-1.96-1.38-3.91-1.52-6,.12-.16,.23-.17,.34,0,.54,1.86,1.07,3.72,1.61,5.58-.14,.13-.17,.39-.43,.41h0Z" style="fill:#be873c;" />
                    <path d="M193.35,307.75c.14,2.18,.17,4.34-.72,6.46v-2.81c.4-.93,.09-1.91,.19-2.86,0-.2,.01-.39,.04-.59,.13-.17,.29-.23,.5-.19h0Z" style="fill:#dd9434;" />
                    <path d="M192.55,290.73c-.11-.06-.22-.11-.32-.17-.02-.79-.04-1.58-.06-2.38-.22-1.34-.31-2.68,.06-4.01,.41,.33,.39,.8,.38,1.26-.02,1.77-.04,3.53-.06,5.3h0Z" style="fill:#eb9d33;" />
                    <path d="M193.47,289.73c.39-.36,.28-.84,.3-1.29,.02-.52-.07-1.06,.38-1.47-.1,1.61,.31,3.25-.34,4.82-.13,.18-.25,.18-.35-.03,0-.68,0-1.35,.01-2.03h0Z" style="fill:#6d6248;" />
                    <path d="M198.22,276.56c.37-.17,.36-.54,.44-.86,.52,1.18,.82,2.42,1.05,3.68-.13,.18-.27,.2-.43,.05-.59-.86-.87-1.84-1.06-2.86h0Z" style="fill:#5a594b;" />
                    <path d="M209.32,313.47c-.66-.99-.64-2.19-1.02-3.27,.07-.17,.18-.3,.35-.39,.66,1.13,.7,2.45,1.13,3.64-.15,.18-.3,.18-.46,.02h0Z" style="fill:#8e7143;" />
                    <path d="M209.33,262.19c-.28,.2-.28,.52-.34,.81-.53-.87-.92-1.8-1.22-2.77-.2-.42,.01-.52,.38-.48,.45,.78,.94,1.55,1.17,2.44h0Z" style="fill:#57584d;" />
                    <path d="M192.22,284.18c-.02,1.34-.04,2.67-.06,4.01-.77-1.71-.23-3.5-.34-5.25,.1,.06,.21,.11,.31,.16,.03,.36,.06,.72,.09,1.07h0Z" style="fill:#274154;" />
                    <path d="M192.69,298.89c-.02-1.56-.03-3.12-.05-4.68,.76,1.58,.23,3.24,.33,4.87-.18,.07-.28,.01-.29-.18Z" style="fill:#eb9d33;" />
                    <path d="M207.35,259.39c-.67-.83-.85-1.87-1.2-2.84-.11-.18-.09-.35,.07-.5,.77,.83,1.11,1.89,1.58,2.89-.13,.17-.18,.42-.45,.45h0Z" style="fill:#957441;" />
                    <path d="M209.32,313.47c.15,0,.3-.01,.46-.02,.27,.94,.54,1.87,.81,2.81-.16,.21-.32,.21-.49,0-.51-.86-.73-1.81-.78-2.8h0Z" style="fill:#7a6847;" />
                    <path d="M217.44,272.55c.3-.05,.63-.04,.78-.38,.67,.4,1.33,.81,1.99,1.21,.09,.36,.02,.56-.41,.4-.79-.41-1.58-.82-2.37-1.23h0Z" style="fill:#50544e;" />
                    <path d="M200.64,284.15c-.26-.81-.51-1.61-.77-2.42,.14-.14,.16-.4,.42-.41,.49,.74,.69,1.58,.76,2.45,.13,.41-.04,.49-.4,.37h0Z" style="fill:#b4823b;" />
                    <path d="M200.64,284.15c.13-.13,.27-.25,.4-.37,.57,.69,.47,1.57,.61,2.41-.51-.6-.99-1.2-1.02-2.04Z" style="fill:#e29734;" />
                    <path d="M207,303.87h-.17s-.17-.01-.17-.01c-.55-.6-.76-1.31-.8-2.41,.91,.8,.99,1.62,1.14,2.42h0Z" style="fill:#f8ac4a;" />
                    <path d="M192.25,303.82c.64,.72,.24,1.63,.45,2.43,.18,.56,.38,1.13-.14,1.63-.48-1.33-.26-2.7-.31-4.06h0Z" style="fill:#4f534d;" />
                    <path d="M192.22,330.3v-3.56c.52,1.17,.35,2.38,.37,3.57-.12,.16-.25,.15-.37,0Z" style="fill:#cf903a;" />
                    <path d="M221.02,297.03c.26,.67,.52,1.34,.78,2.01-.11,.17-.16,.41-.42,.44-.39-.63-.64-1.31-.8-2.03-.11-.42,.07-.52,.44-.42h0Z" style="fill:#a27a3f;" />
                    <path d="M192.69,298.89c.09,.07,.19,.13,.29,.18-.02,1.18-.04,2.36-.06,3.54-.62-1.21-.25-2.48-.23-3.73h0Z" style="fill:#4f534d;" />
                    <path d="M225,276.21c.61,.38,1.22,.75,1.83,1.13-.95,.24-1.58-.25-2.2-.77,.07-.17,.19-.29,.37-.36Z" style="fill:#c78b38;" />
                    <path d="M191.43,290.99c.75,1.02,.29,2.17,.34,3.27-.57-1.04-.28-2.18-.34-3.27Z" style="fill:#274154;" />
                    <path d="M223.84,301.82c.61,.29,1.28,.48,1.98,1.24-1.08-.12-1.78-.37-2.43-.79,.03-.27,.28-.32,.45-.46h0Z" style="fill:#977441;" />
                    <path d="M221.02,297.03c-.15,.14-.3,.28-.44,.42-.49-.74-.76-1.55-.74-2.44h0c.64,.53,.83,1.32,1.19,2.02h0Z" style="fill:#c48a39;" />
                    <path d="M221.38,299.47c.14-.15,.28-.29,.42-.44,.28,.66,.56,1.32,.85,1.98-.16,.22-.33,.23-.51,.03-.38-.46-.6-1-.76-1.57h0Z" style="fill:#7e6945;" />
                    <path d="M193.03,308.25c-.14,1.05,.34,2.18-.4,3.15,0-1.06,0-2.11,0-3.16,.14-.18,.28-.17,.41,.02h0Z" style="fill:#4f534c;" />
                    <path d="M192.22,330.3c.12-.02,.24-.02,.37,0,0,1.07-.02,2.13-.02,3.2-.11,0-.23,0-.34-.01v-3.19Z" style="fill:#5d5d53;" />
                    <path d="M215.08,285.75c.35-.18,.34-.52,.41-.84,.42,.63,.71,1.32,.8,2.08-.16,.16-.3,.14-.44-.02-.26-.41-.51-.81-.77-1.22h0Z" style="fill:#685f48;" />
                    <path d="M219.81,273.78l.41-.4c.46,.17,.9,.38,1.25,.74,.17,.19,.16,.37-.03,.53-.62-.15-1.17-.41-1.62-.87h0Z" style="fill:#826c46;" />
                    <path d="M225,276.21c-.12,.12-.25,.24-.37,.36-.6-.32-1.32-.47-1.6-1.21h.01c.77,.01,1.34,.48,1.95,.84Z" style="fill:#a57b3e;" />
                    <path d="M221.43,274.66c.01-.18,.02-.36,.03-.53,.56,.37,1.3,.51,1.58,1.23h-.01c-.64,.01-1.14-.3-1.6-.7h0Z" style="fill:#9f7941;" />
                    <path d="M210.1,316.27c.16,0,.33,0,.49,0,.14,.39,.72,.58,.53,1.12-.02,.06-.23,.1-.35,.08-.74-.1-.54-.74-.67-1.19h0Z" style="fill:#4d534f;" />
                    <path d="M206.23,256.05c-.02,.17-.05,.33-.07,.5-.46-.5-.5-1.12-.71-1.85,.83,.31,.75,.86,.77,1.35Z" style="fill:#c78b39;" />
                    <path d="M188.28,272.94c.33,.31,.92,.54,.35,1.13-.31-.34-.62-.69-.93-1.03,.16-.25,.35-.28,.58-.1h0Z" style="fill:#7e6945;" />
                    <path d="M200.29,281.32c-.14,.14-.28,.27-.42,.41-.11-.4-.23-.81-.35-1.21,.09-.17,.11-.4,.36-.42,.39,.33,.37,.79,.41,1.23Z" style="fill:#7e6945;" />
                    <path d="M193.03,308.25c-.14,0-.28-.01-.41-.02-.05-.11-.07-.22-.05-.34,.05-.54,.09-1.09,.14-1.63,.09-.01,.18-.02,.28-.03,.07,.1,.1,.21,.07,.33,.19,.45,.21,.89,0,1.34,.02,.12,.01,.24-.03,.35h0Z" style="fill:#143957;" />
                    <path d="M223.84,301.82c-.15,.15-.3,.3-.45,.46-.32-.05-.63-.11-.84-.39,.03-.07,.05-.14,.08-.21,.14-.06,.27-.13,.39-.22,.27,.12,.55,.24,.82,.37h0Z" style="fill:#57574c;" />
                    <path d="M207.35,259.39c.15-.15,.3-.3,.45-.45,.12,.27,.24,.54,.35,.8-.13,.16-.25,.32-.38,.48-.14-.28-.28-.56-.42-.84h0Z" style="fill:#5c5a4c;" />
                    <path d="M232.31,281.33c-.18,.02-.36,.04-.54,.06-.29-.13-.71-.37-.38-.61,.29-.21,.68,.22,.92,.56h0Z" style="fill:#6b6049;" />
                    <path d="M188.28,272.94c-.2,.03-.39,.06-.58,.1-.16-.06-.31-.14-.31-.34,0-.3-.01-.59-.02-.89,.49,.23,.65,.72,.91,1.13h0Z" style="fill:#55574d;" />
                    <path d="M193.06,307.9v-1.34c.32,.34,.21,.79,.29,1.19-.1,.05-.2,.1-.29,.15Z" style="fill:#4f534c;" />
                    <path d="M205.46,300.02c.06-.09,.13-.18,.2-.28,.05,.17,.12,.33,.16,.51,.02,.12,0,.25,0,.37-.23-.13-.36-.33-.36-.6h0Z" style="fill:#f8ac4a;" />
                    <path d="M193.45,291.77c.12,.01,.23,.02,.35,.03,.01,.32,.1,.66-.2,.91-.28-.28-.19-.61-.15-.94Z" style="fill:#a87c3c;" />
                    <path d="M199.88,280.1c-.12,.14-.24,.28-.36,.42-.08-.37-.16-.73-.24-1.1,.14-.02,.29-.03,.43-.05,.06,.24,.11,.48,.17,.72h0Z" style="fill:#58574b;" />
                    <path d="M215.85,286.97c.15,0,.29,.02,.44,.02,.24,.14,.44,.41,.14,.53-.32,.13-.47-.27-.57-.55h0Z" style="fill:#b3813b;" />
                    <path d="M219.84,295c-.2-.08-.4-.37-.3-.41,.47-.2,.2,.26,.3,.41h0Z" style="fill:#c48a39;" />
                    <path d="M208.64,309.81c-.12,.13-.23,.26-.35,.39-.06-.11-.09-.22-.11-.34,.14-.14,.29-.27,.43-.41,.02,.12,.03,.24,.03,.36h0Z" style="fill:#6e6349;" />
                    <path d="M222.14,301.04c.17-.01,.34-.02,.51-.03,.06,.08,.06,.16,0,.24,.11,.15,.13,.32,.07,.49-.06,.05-.11,.1-.17,.15-.31-.2-.38-.51-.41-.84Z" style="fill:#625c4a;" />
                    <path d="M222.73,301.74c-.02-.16-.05-.33-.07-.49,.12,.07,.24,.14,.37,.2-.1,.1-.2,.19-.3,.29h0Z" style="fill:#1c3c56;" />
                    <polygon points="205.82 300.62 205.99 300.71 205.91 300.78 205.82 300.62" style="fill:#f8ac4a;" />
                    <path d="M174.89,248.42c3.02-3.96,5.84-8.04,8.02-12.53,1.48-3.05,2.62-6.23,3.7-9.43,1.06,.32,.5,1.06,.43,1.67-.64,5.9-2.85,11.13-6.68,15.66-1.54,1.82-3.16,3.57-4.99,5.1-.38,.07-.57-.06-.48-.47h0Z" style="fill:#06335b;" />
                    <path d="M189.33,227.29c-.23-.14-.45-.27-.68-.41,0-2,0-3.99,.01-5.99,.53,1.27,.34,2.68,.78,3.97-.04,.81-.07,1.61-.11,2.42h0Z" style="fill:#57574b;" />
                    <path d="M187.04,228.13c-.18-.55,.16-1.23-.43-1.67-.09-.72,.32-1.32,.47-1.98,.91,.21,.41,.81,.37,1.29,0,.81,.06,1.63-.4,2.36h0Z" style="fill:#364852;" />
                    <path d="M187.45,225.77c-.08-.44,.04-.94-.37-1.29,.11-.54,.23-1.08,.34-1.61,.12-.19,.24-.2,.36,0,.05,.99,.1,1.98-.34,2.91Z" style="fill:#414d50;" />
                    <path d="M174.58,249.71c-.56,.5-1.13,.99-1.69,1.49,.05-.98,.67-1.46,1.26-1.95,.25,.05,.39,.21,.44,.46h0Z" style="fill:#7a6746;" />
                    <path d="M174.58,249.71c-.14-.15-.29-.31-.44-.46,.25-.27,.5-.55,.75-.82,.16,.16,.32,.31,.48,.47-.13,.4-.49,.58-.79,.81h0Z" style="fill:#605c4b;" />
                    <path d="M187.78,222.86h-.18s-.18,0-.18,0c.05-.3-.24-.81,.26-.83,.23-.01,.13,.53,.1,.82h0Z" style="fill:#9d7840;" />
                    <path d="M174.58,351.95c.13-.14,.26-.27,.39-.41,1.27-.18,2.41,.37,3.62,.61-1.35,.06-2.71,.54-4.01-.2h0Z" style="fill:#021f3b;" />
                    <path d="M191.35,267.51c-.51-1.5-.17-3.05-.31-4.57,.73,1.48,.26,3.04,.31,4.57Z" style="fill:#df9634;" />
                    <path d="M191.75,275.12c-.57-1.35-.19-2.77-.3-4.16,.75,1.34,.23,2.77,.3,4.16Z" style="fill:#df9634;" />
                    <path d="M218.38,123.73c.73,.01,1.33,.67,1.36,1.45,.02,.56-.25,1.03-.49,1.5-1.21,2.39-1.3,4.82-.39,7.33,.41,1.12,.76,2.27,1.04,3.43,.3,1.22,0,1.89-.8,2.17s-1.48-.13-1.88-1.29c-1-2.95-2.2-5.86-1.73-9.1,.23-1.6,.7-3.1,1.48-4.51,.32-.57,.78-.89,1.41-.98h0Z" style="fill:#fefcfc;" />
                    <path d="M238.22,131.8c-2.22,.02-3.77-1.06-4.87-2.91-.89-1.5-1.27-3.2-1.8-4.83-.3-.93,.25-1.82,1.13-1.99,.86-.16,1.35,.37,1.64,1.1,.49,1.23,.69,2.55,1.28,3.76,.69,1.41,1.66,2.19,3.27,2.06,.26-.02,.53-.01,.79,.01,.77,.07,1.23,.52,1.27,1.27,.05,.77-.41,1.26-1.13,1.41-.51,.11-1.06,.08-1.58,.11h0Z" style="fill:#fefbfb;" />
                    <path d="M222.78,87.74c-1.06,.13-2.2-.25-3.32-.69-.37-.14-.74-.32-1.05-.55-.61-.47-.68-1.13-.44-1.8,.22-.61,.79-.62,1.34-.63,.76-.01,1.42,.34,2.11,.55,2.61,.8,3.8,.22,4.78-2.35,.21-.54,.25-1.18,.97-1.39,1.25-.37,2.15,.56,1.85,1.95-.64,3.06-2.96,4.93-6.25,4.9h0Z" style="fill:#fefbfb;" />
                    <path d="M207.51,131.81c.04-2.27,.57-4.47,1.11-6.66,.08-.32,.21-.64,.39-.91,.44-.63,1.08-.74,1.77-.52,.64,.2,.68,.74,.68,1.3,0,.54-.1,1.05-.21,1.57-.35,1.69-.67,3.38-.93,5.09-.15,1,.01,1.96,.49,2.9,.45,.89,.16,1.73-.52,2.04-.71,.32-1.57,.03-2.06-.75-.79-1.25-.76-2.66-.73-4.07h0Z" style="fill:#fefafa;" />
                    <path d="M224.09,126.66c-.09-2.07,.25-2.78,1.11-2.87,.82-.08,1.38,.63,1.64,2.08,.21,1.17,.4,2.34,.55,3.52,.22,1.62,.63,3.09,2.09,4.1,.75,.52,.79,1.47,.26,2.1-.57,.66-1.2,.68-1.94,.22-1.31-.81-2.13-1.97-2.63-3.39-.71-2.02-.93-4.12-1.07-5.77Z" style="fill:#fefbfb;" />
                    <path d="M307.13,115.8c.02,.13,.03,.26,.04,.39-.21-.02-.5,.12-.51-.24,0-.05,.31-.1,.47-.15Z" style="fill:#e46266;" />
                </g>
                <g id="fh">
                    <path d="M383.08,23.81h100.76l1.68,42.88h-5.78c-2.57-14.74-6.17-24.34-10.8-28.81-5.31-5.19-12.87-7.79-22.7-7.79h-19.85c-1.96,0-3.38,.46-4.27,1.38-.89,.92-1.34,2.3-1.34,4.15v44.81h10.64c5.92,0,10.64-1.95,14.15-5.86,3.52-3.91,5.42-9.94,5.7-18.09h6.03v55.87h-6.03c-.39-9.27-2.29-15.89-5.7-19.85-3.41-3.96-8.12-5.95-14.15-5.95h-10.64v47.24c0,2.79,.67,4.76,2.01,5.9,1.34,1.15,3.66,1.72,6.95,1.72h12.9v6.28h-59.55v-6.28h12.73c2.57,0,4.4-.52,5.49-1.55,1.09-1.03,1.63-2.72,1.63-5.07V36.29c0-2.23-.57-3.82-1.72-4.77-1.15-.95-3.14-1.42-5.99-1.42h-12.14v-6.28Z" style="fill:#000;" />
                    <path d="M527.28,23.81h63.99c12.4,0,22.01,2.82,28.85,8.46,6.84,5.64,10.26,13.04,10.26,22.2,0,5.47-1.35,10.34-4.06,14.62-2.71,4.27-6.62,7.68-11.73,10.22-5.11,2.54-13.14,4.37-24.08,5.49,7.96,2.18,13.45,4.31,16.46,6.41,3.01,2.09,5.42,4.8,7.23,8.12,1.81,3.32,3.9,10.09,6.27,20.31,1.69,7.32,3.49,12.31,5.39,14.99,1.45,2.07,3.02,3.1,4.69,3.1s3.34-1.27,4.82-3.81c1.48-2.54,2.39-7.02,2.72-13.44h5.7c-1.06,18.99-8.07,28.48-21.02,28.48-4.08,0-7.61-1.03-10.6-3.1-2.99-2.06-5.23-5.11-6.74-9.13-1.17-3.02-2.49-10.92-3.94-23.7-.84-7.43-2.16-12.7-3.98-15.83-1.81-3.13-4.65-5.64-8.5-7.54-3.85-1.9-8.46-2.85-13.82-2.85h-10.13v48.66c0,2.07,.53,3.52,1.59,4.36,1.34,1.06,3.43,1.59,6.28,1.59h11.89v6.28h-57.54v-6.28h12.14c2.68,0,4.65-.53,5.9-1.59,1.26-1.06,1.88-2.51,1.88-4.36V36.46c0-2.12-.64-3.71-1.93-4.77-1.29-1.06-3.24-1.59-5.86-1.59h-12.14v-6.28Zm37.77,56.95h14.66c9.83,0,17.52-1.85,23.08-5.56,5.56-3.71,8.33-10.3,8.33-19.78,0-6.52-1.05-11.68-3.14-15.47-2.09-3.79-4.84-6.38-8.25-7.78-3.41-1.39-10.11-2.09-20.1-2.09-6.59,0-10.67,.46-12.23,1.38-1.56,.92-2.35,2.49-2.35,4.72v44.57Z" style="fill:#000;" />
                    <path d="M692.33,23.81h98.16l2.35,41.79h-5.61c-2.23-13.18-5.76-22.39-10.58-27.64-4.82-5.25-11.11-7.87-18.86-7.87h-24.26c-1.95,0-3.43,.53-4.43,1.59-1,1.06-1.51,2.68-1.51,4.86v44.14h10.61c5.68,0,10.29-2.04,13.82-6.11,3.54-4.08,5.44-10.33,5.72-18.76h5.78v57.37h-5.78c-.28-9.49-2.21-16.29-5.81-20.4-3.59-4.1-8.48-6.16-14.66-6.16h-9.69v49.25c0,1.79,.6,3.16,1.8,4.1,1.2,.95,3.19,1.42,5.98,1.42h19.16c20.8,0,32.79-13.29,35.97-39.87h6.03l-2.09,46.15h-102.1v-6.28h8.46c3.29,0,5.61-.57,6.95-1.72,1.34-1.14,2.01-2.81,2.01-4.98V36.54c0-2.18-.59-3.8-1.76-4.86-1.17-1.06-3.02-1.59-5.53-1.59h-10.13v-6.28Z" style="fill:#000;" />
                    <path d="M845.65,23.81h57.12v6.28h-9.8c-4.36,0-7.11,.54-8.25,1.63-1.15,1.09-1.72,3.39-1.72,6.91v93.81c0,3.74,.5,6.09,1.51,7.04,1.34,1.29,3.77,1.93,7.29,1.93h10.97v6.28h-57.12v-6.28h10.8c3.8,0,6.25-.56,7.37-1.68,1.12-1.12,1.67-3.54,1.67-7.29V38.64c0-3.57-.5-5.78-1.51-6.62-1.56-1.28-4.44-1.93-8.63-1.93h-9.72v-6.28Z" style="fill:#000;" />
                    <path d="M954.67,23.81h98.16l2.34,41.79h-5.61c-2.23-13.18-5.76-22.39-10.58-27.64-4.82-5.25-11.11-7.87-18.86-7.87h-24.26c-1.95,0-3.43,.53-4.43,1.59-1,1.06-1.5,2.68-1.5,4.86v44.14h10.61c5.68,0,10.29-2.04,13.82-6.11,3.54-4.08,5.44-10.33,5.72-18.76h5.78v57.37h-5.78c-.28-9.49-2.21-16.29-5.81-20.4-3.59-4.1-8.48-6.16-14.66-6.16h-9.69v49.25c0,1.79,.6,3.16,1.8,4.1,1.2,.95,3.19,1.42,5.98,1.42h19.16c20.8,0,32.79-13.29,35.97-39.87h6.03l-2.09,46.15h-102.1v-6.28h8.46c3.29,0,5.61-.57,6.95-1.72,1.34-1.14,2.01-2.81,2.01-4.98V36.54c0-2.18-.59-3.8-1.76-4.86-1.17-1.06-3.01-1.59-5.53-1.59h-10.13v-6.28Z" style="fill:#000;" />
                    <path d="M1191.64,23.81h52.93v6.28h-9.72c-3.41,0-5.63,.6-6.66,1.8-1.03,1.2-1.55,4.12-1.55,8.75v38.19h56.62V40.65c0-4.8-.49-7.76-1.47-8.88-.98-1.12-3.28-1.68-6.91-1.68h-9.05v-6.28h52.6v6.28h-9.63c-2.4,0-4.09,.31-5.07,.92-.98,.62-1.77,1.68-2.39,3.18-.33,.95-.5,3.43-.5,7.45v89.12c0,4.3,.36,6.9,1.09,7.79,1.45,1.9,4.1,2.85,7.96,2.85h8.54v6.28h-52.6v-6.28h9.05c3.57,0,6-.98,7.29-2.93,.72-1.06,1.09-3.85,1.09-8.38v-43.05h-56.62v43.72c0,4.36,.33,6.98,1,7.87,1.4,1.84,3.8,2.76,7.2,2.76h9.72v6.28h-52.93v-6.28h10.13c1.79,0,3.21-.36,4.27-1.09,1.06-.73,1.84-1.76,2.34-3.1,.28-.89,.42-3.27,.42-7.12V40.65c0-4.63-.52-7.55-1.55-8.75-1.03-1.2-3.28-1.8-6.74-1.8h-8.88v-6.28Z" style="fill:#000;" />
                    <path d="M1420.73,21.22h5.61l44.42,113.32c1.79,4.58,5.44,6.87,10.97,6.87h3.85v6.28h-49.42v-6.28h3.94c5.14,0,8.77-.56,10.89-1.68,1.45-.78,2.18-1.98,2.18-3.6,0-.95-.2-1.95-.59-3.02l-10.25-26.3h-49l-5.75,15.08c-1.51,4.02-2.26,7.2-2.26,9.55,0,2.74,1.27,5.08,3.81,7.04,2.54,1.96,6.24,2.93,11.1,2.93h4.19v6.28h-44.89v-6.28c4.8,0,8.6-1.05,11.39-3.14,2.79-2.09,5.36-6.07,7.7-11.94l42.11-105.11Zm-2.44,23.87l-22.24,55.2h43.75l-21.51-55.2Z" style="fill:#000;" />
                    <path d="M1526.01,23.81h29.57l74.71,97.33V50.7c0-7.09-1.62-12.34-4.86-15.75-2.96-3.13-7.37-4.75-13.23-4.86v-6.28h43.39v6.28c-4.43,0-7.74,.62-9.93,1.84-2.19,1.23-4.09,3.46-5.72,6.7-1.63,3.24-2.44,6.78-2.44,10.64v98.41h-7.87l-77.31-101.33V121.13c0,6.76,1.7,11.8,5.11,15.13,3.41,3.32,8.68,5.04,15.83,5.15v6.28h-47.24v-6.28c5.92-.22,10.61-2.16,14.07-5.81,3.46-3.65,5.19-8.13,5.19-13.42V38.28l-1.47-1.62c-2.79-3.04-4.77-4.84-5.94-5.4-1.62-.78-3.7-1.17-6.26-1.17h-5.59v-6.28Z" style="fill:#000;" />
                    <path d="M1772.39,33.19l7.54-10.8h6.11l.5,45.15h-5.53c-3.46-13.85-8.58-23.95-15.37-30.32-6.78-6.37-14.59-9.55-23.41-9.55-7.37,0-13.4,2.21-18.09,6.62s-7.04,9.81-7.04,16.18c0,3.97,.89,7.45,2.68,10.44,1.79,2.99,4.45,5.42,7.99,7.29,3.54,1.87,10.23,3.86,20.06,5.98,13.8,3,23.47,6.04,29.03,9.11,5.56,3.07,9.7,6.94,12.44,11.6,2.74,4.66,4.1,10.12,4.1,16.37,0,11.17-3.76,20.42-11.27,27.77-7.51,7.34-17.21,11.01-29.11,11.01-12.79,0-23.9-4.13-33.33-12.4l-9.46,11.56h-6.03v-47.99h6.03c2.35,13.23,7.59,23.68,15.75,31.32,8.15,7.65,17.34,11.48,27.56,11.48,8.26,0,15-2.44,20.23-7.33,5.22-4.89,7.83-10.82,7.83-17.8,0-3.96-.98-7.44-2.93-10.43-1.96-2.99-5.05-5.5-9.3-7.54-4.24-2.04-12.59-4.51-25.04-7.41-13.68-3.24-23-6.87-27.97-10.89-6.98-5.7-10.47-13.76-10.47-24.21s3.48-19.81,10.43-26.76c6.95-6.95,15.84-10.43,26.68-10.43,5.19,0,10.13,.92,14.83,2.76s9.55,4.91,14.57,9.21Z" style="fill:#000;" />
                    <path d="M1846.65,23.81h98.16l2.34,41.79h-5.61c-2.23-13.18-5.76-22.39-10.58-27.64-4.82-5.25-11.11-7.87-18.86-7.87h-24.26c-1.95,0-3.43,.53-4.43,1.59-1,1.06-1.5,2.68-1.5,4.86v44.14h10.61c5.68,0,10.29-2.04,13.82-6.11,3.54-4.08,5.44-10.33,5.72-18.76h5.78v57.37h-5.78c-.28-9.49-2.21-16.29-5.81-20.4-3.59-4.1-8.48-6.16-14.66-6.16h-9.69v49.25c0,1.79,.6,3.16,1.8,4.1,1.2,.95,3.19,1.42,5.98,1.42h19.16c20.8,0,32.79-13.29,35.97-39.87h6.03l-2.09,46.15h-102.1v-6.28h8.46c3.29,0,5.61-.57,6.95-1.72,1.34-1.14,2.01-2.81,2.01-4.98V36.54c0-2.18-.59-3.8-1.76-4.86-1.17-1.06-3.01-1.59-5.53-1.59h-10.13v-6.28Z" style="fill:#000;" />
                    <path d="M2072.93,33.19l7.54-10.8h6.11l.5,45.15h-5.53c-3.46-13.85-8.58-23.95-15.37-30.32-6.78-6.37-14.59-9.55-23.41-9.55-7.37,0-13.4,2.21-18.09,6.62s-7.04,9.81-7.04,16.18c0,3.97,.89,7.45,2.68,10.44,1.79,2.99,4.45,5.42,7.99,7.29,3.54,1.87,10.23,3.86,20.06,5.98,13.8,3,23.47,6.04,29.03,9.11,5.56,3.07,9.7,6.94,12.44,11.6,2.74,4.66,4.1,10.12,4.1,16.37,0,11.17-3.76,20.42-11.27,27.77-7.51,7.34-17.21,11.01-29.11,11.01-12.79,0-23.9-4.13-33.33-12.4l-9.46,11.56h-6.03v-47.99h6.03c2.35,13.23,7.59,23.68,15.75,31.32,8.15,7.65,17.34,11.48,27.56,11.48,8.26,0,15-2.44,20.23-7.33,5.22-4.89,7.83-10.82,7.83-17.8,0-3.96-.98-7.44-2.93-10.43-1.96-2.99-5.05-5.5-9.3-7.54-4.24-2.04-12.59-4.51-25.04-7.41-13.68-3.24-23-6.87-27.97-10.89-6.98-5.7-10.47-13.76-10.47-24.21s3.48-19.81,10.43-26.76c6.95-6.95,15.84-10.43,26.68-10.43,5.19,0,10.13,.92,14.83,2.76s9.55,4.91,14.57,9.21Z" style="fill:#000;" />
                    <path d="M2143.5,23.81h100.59l2.43,46.82h-5.78c-3.81-27.53-13.77-41.29-29.88-41.29-3.25,0-5.47,.56-6.67,1.68-1.2,1.12-1.8,2.96-1.8,5.53v99c0,2.01,.59,3.49,1.76,4.44,1.17,.95,3.1,1.42,5.78,1.42h12.48v6.28h-57.54v-6.28h14.24c1.9,0,3.35-.47,4.36-1.42,1-.95,1.51-2.21,1.51-3.77V35.79c0-2.12-.64-3.73-1.93-4.82-1.29-1.09-3.35-1.63-6.2-1.63-8.1,0-14.85,3.83-20.27,11.48-4.47,6.31-7.68,16.25-9.63,29.82h-6.11l2.68-46.82Z" style="fill:#000;" />
                    <path d="M2335.16,21.22h5.61l44.42,113.32c1.79,4.58,5.44,6.87,10.97,6.87h3.85v6.28h-49.42v-6.28h3.94c5.14,0,8.77-.56,10.89-1.68,1.45-.78,2.18-1.98,2.18-3.6,0-.95-.2-1.95-.59-3.02l-10.25-26.3h-49l-5.75,15.08c-1.51,4.02-2.26,7.2-2.26,9.55,0,2.74,1.27,5.08,3.81,7.04,2.54,1.96,6.24,2.93,11.1,2.93h4.19v6.28h-44.89v-6.28c4.8,0,8.6-1.05,11.39-3.14,2.79-2.09,5.36-6.07,7.7-11.94l42.11-105.11Zm-2.44,23.87l-22.24,55.2h43.75l-21.51-55.2Z" style="fill:#000;" />
                    <path d="M2439.26,23.81h63.99c9.49,0,17.56,1.68,24.21,5.03,8.26,4.24,15.29,11.31,21.07,21.19s8.67,21.58,8.67,35.09c0,11.17-1.73,21.22-5.19,30.15-2.62,6.87-6.66,12.87-12.1,18.01-5.44,5.14-12.08,8.99-19.89,11.56-5.86,1.9-13.68,2.85-23.45,2.85h-57.29v-6.28h11.73c2.96,0,5.01-.47,6.16-1.42,1.14-.95,1.72-2.43,1.72-4.44V35.79c0-1.73-.62-3.11-1.84-4.15-1.23-1.03-2.93-1.55-5.11-1.55h-12.65v-6.28Zm44.89,6.28c-2.51,0-4.37,.53-5.57,1.59-1.2,1.06-1.8,2.65-1.8,4.77v98.83c0,2.01,.7,3.53,2.09,4.56,1.4,1.03,3.77,1.55,7.12,1.55h10.55c9.44,0,17.7-2.43,24.79-7.29,4.63-3.24,8.15-7.79,10.55-13.65,3.29-7.93,4.94-18.87,4.94-32.83,0-20.66-3.6-35.79-10.8-45.4-6.14-8.1-15.41-12.15-27.81-12.15h-14.07Z" style="fill:#000;" />
                    <path d="M2608.67,23.81h100.59l2.43,46.82h-5.78c-3.81-27.53-13.77-41.29-29.88-41.29-3.25,0-5.47,.56-6.67,1.68-1.2,1.12-1.8,2.96-1.8,5.53v99c0,2.01,.59,3.49,1.76,4.44,1.17,.95,3.1,1.42,5.78,1.42h12.48v6.28h-57.54v-6.28h14.24c1.9,0,3.35-.47,4.36-1.42,1-.95,1.51-2.21,1.51-3.77V35.79c0-2.12-.64-3.73-1.93-4.82-1.29-1.09-3.35-1.63-6.2-1.63-8.1,0-14.85,3.83-20.27,11.48-4.47,6.31-7.68,16.25-9.63,29.82h-6.11l2.68-46.82Z" style="fill:#000;" />
                </g>
                <g id="sb">
                    <path d="M506.71,226.87l13.51-19.37h10.96l.9,80.92h-9.91c-6.21-24.82-15.39-42.94-27.55-54.35-12.16-11.41-26.15-17.11-41.96-17.11-13.21,0-24.02,3.96-32.43,11.87-8.41,7.91-12.61,17.58-12.61,29,0,7.11,1.6,13.35,4.8,18.71,3.2,5.36,7.98,9.72,14.33,13.07,6.35,3.36,18.34,6.93,35.96,10.71,24.73,5.38,42.07,10.82,52.03,16.32,9.96,5.5,17.39,12.44,22.29,20.79,4.9,8.36,7.36,18.14,7.36,29.35,0,20.02-6.73,36.61-20.19,49.77-13.46,13.16-30.85,19.74-52.17,19.74-22.92,0-42.84-7.41-59.75-22.22l-16.96,20.72h-10.81v-86.02h10.81c4.2,23.72,13.61,42.44,28.22,56.15,14.61,13.71,31.08,20.57,49.39,20.57,14.81,0,26.9-4.38,36.26-13.14,9.36-8.76,14.04-19.39,14.04-31.9,0-7.11-1.75-13.34-5.25-18.69-3.5-5.35-9.06-9.86-16.66-13.51-7.61-3.65-22.57-8.08-44.89-13.29-24.52-5.8-41.24-12.31-50.14-19.52-12.51-10.21-18.77-24.67-18.77-43.39s6.23-35.5,18.69-47.97c12.46-12.46,28.4-18.69,47.82-18.69,9.31,0,18.17,1.65,26.57,4.95s17.11,8.81,26.12,16.51Z" style="fill:#000;" />
                    <path d="M625.52,210.05h180.3l4.35,83.92h-10.36c-6.82-49.34-24.68-74.01-53.56-74.01-5.82,0-9.8,1-11.96,3-2.16,2-3.23,5.31-3.23,9.91v177.45c0,3.6,1.05,6.26,3.15,7.96,2.1,1.7,5.55,2.55,10.36,2.55h22.37v11.26h-103.14v-11.26h25.52c3.4,0,6-.85,7.81-2.55,1.8-1.7,2.7-3.95,2.7-6.76V231.52c0-3.8-1.15-6.68-3.45-8.63-2.3-1.95-6-2.93-11.11-2.93-14.51,0-26.62,6.86-36.33,20.57-8.01,11.31-13.76,29.12-17.26,53.45h-10.96l4.8-83.92Z" style="fill:#000;" />
                    <path d="M891,210.05h175.95l4.2,74.91h-10.06c-4-23.62-10.32-40.13-18.97-49.54-8.65-9.41-19.91-14.11-33.8-14.11h-43.48c-3.5,0-6.15,.95-7.95,2.85-1.8,1.9-2.7,4.8-2.7,8.71v79.12h19.01c10.18,0,18.44-3.65,24.78-10.96,6.34-7.3,9.76-18.51,10.26-33.63h10.36v102.84h-10.36c-.5-17.01-3.97-29.2-10.41-36.56-6.44-7.36-15.2-11.03-26.27-11.03h-17.37v88.27c0,3.2,1.07,5.66,3.22,7.36,2.15,1.7,5.72,2.55,10.72,2.55h34.33c37.28,0,58.77-23.82,64.47-71.46h10.81l-3.75,82.72h-183v-11.26h15.16c5.9,0,10.06-1.02,12.46-3.08,2.4-2.05,3.6-5.03,3.6-8.93V232.87c0-3.9-1.05-6.81-3.15-8.71-2.1-1.9-5.4-2.85-9.91-2.85h-18.17v-11.26Z" style="fill:#000;" />
                    <path d="M1160.24,210.05h180.3l4.35,83.92h-10.36c-6.82-49.34-24.68-74.01-53.56-74.01-5.82,0-9.8,1-11.96,3-2.16,2-3.23,5.31-3.23,9.91v177.45c0,3.6,1.05,6.26,3.15,7.96,2.1,1.7,5.55,2.55,10.36,2.55h22.37v11.26h-103.14v-11.26h25.52c3.4,0,6-.85,7.81-2.55,1.8-1.7,2.7-3.95,2.7-6.76V231.52c0-3.8-1.15-6.68-3.45-8.63-2.3-1.95-6-2.93-11.11-2.93-14.51,0-26.62,6.86-36.33,20.57-8.01,11.31-13.76,29.12-17.26,53.45h-10.96l4.8-83.92Z" style="fill:#000;" />
                    <path d="M1419.12,210.05h180.3l4.35,83.92h-10.36c-6.82-49.34-24.68-74.01-53.56-74.01-5.82,0-9.8,1-11.96,3-2.16,2-3.23,5.31-3.23,9.91v177.45c0,3.6,1.05,6.26,3.15,7.96,2.1,1.7,5.55,2.55,10.36,2.55h22.37v11.26h-103.14v-11.26h25.52c3.4,0,6-.85,7.81-2.55,1.8-1.7,2.7-3.95,2.7-6.76V231.52c0-3.8-1.15-6.68-3.45-8.63-2.3-1.95-6-2.93-11.11-2.93-14.51,0-26.62,6.86-36.33,20.57-8.01,11.31-13.76,29.12-17.26,53.45h-10.96l4.8-83.92Z" style="fill:#000;" />
                    <path d="M1679.05,210.05h115.75c22.72,0,40.11,5,52.17,15,12.06,10,18.09,22.55,18.09,37.65,0,12.6-4.2,23.3-12.61,32.1s-21.07,15.75-37.98,20.85c20.92,4.41,36.61,11.34,47.06,20.81,10.46,9.47,15.69,21.41,15.69,35.84,0,11.02-2.98,20.79-8.93,29.3-5.96,8.52-15.86,15.73-29.72,21.64-13.86,5.91-29.6,8.86-47.21,8.86h-112.29v-11.26h18.32c5.5,0,9.53-.9,12.08-2.7s3.83-3.85,3.83-6.16V234.07c0-4.2-1.25-7.38-3.75-9.53-2.5-2.15-6.16-3.23-10.96-3.23h-19.52v-11.26Zm66.36,101.94h42.04c12.91,0,23.19-3.79,30.85-11.39,7.66-7.59,11.49-20.03,11.49-37.32,0-10.69-1.83-19.03-5.48-25.03-3.65-6-8.96-10.37-15.91-13.12-6.96-2.75-18.99-4.12-36.11-4.12-12.11,0-19.62,.88-22.52,2.62-2.9,1.75-4.35,4.82-4.35,9.22v79.14Zm0,11.26v85.57c0,4,1.18,7.01,3.53,9.01,2.35,2,6.58,3,12.69,3h26.87c17.81,0,30.98-3.93,39.48-11.79,8.51-7.86,12.76-20.09,12.76-36.71,0-15.21-4.41-27.2-13.21-35.96-8.81-8.76-20.97-13.14-36.48-13.14h-45.64Z" style="fill:#000;" />
                    <path d="M1960.45,210.05h175.95l4.2,74.91h-10.06c-4-23.62-10.32-40.13-18.97-49.54-8.65-9.41-19.91-14.11-33.8-14.11h-43.48c-3.5,0-6.15,.95-7.95,2.85-1.8,1.9-2.7,4.8-2.7,8.71v79.12h19.01c10.18,0,18.44-3.65,24.78-10.96,6.34-7.3,9.76-18.51,10.26-33.63h10.36v102.84h-10.36c-.5-17.01-3.97-29.2-10.41-36.56-6.44-7.36-15.2-11.03-26.27-11.03h-17.37v88.27c0,3.2,1.07,5.66,3.22,7.36,2.15,1.7,5.72,2.55,10.72,2.55h34.33c37.28,0,58.77-23.82,64.47-71.46h10.81l-3.75,82.72h-183v-11.26h15.16c5.9,0,10.06-1.02,12.46-3.08,2.4-2.05,3.6-5.03,3.6-8.93V232.87c0-3.9-1.05-6.81-3.15-8.71-2.1-1.9-5.4-2.85-9.91-2.85h-18.17v-11.26Z" style="fill:#000;" />
                    <path d="M2394.07,227.32l10.36-21.47h9.46l3.9,89.33h-10.06c-6.61-25.12-14.91-43.63-24.92-55.55-12.41-14.91-27.22-22.37-44.44-22.37-18.62,0-33.78,8.31-45.49,24.92-11.71,16.61-17.56,44.74-17.56,84.37,0,33.33,6.7,58.75,20.12,76.26,11.41,14.81,27.42,22.22,48.04,22.22,18.01,0,33.13-5.78,45.34-17.34,12.21-11.56,20.17-29,23.87-52.32h10.66c-3.1,26.42-12.16,46.74-27.17,60.95-15.01,14.21-33.93,21.32-56.75,21.32-18.52,0-35.5-4.65-50.97-13.96s-27.83-22.69-37.08-40.16c-9.26-17.46-13.89-36.6-13.89-57.42,0-22.12,4.78-42.88,14.34-62.3,9.56-19.42,21.79-34,36.71-43.76,14.91-9.76,30.98-14.64,48.19-14.64,9.61,0,18.92,1.65,27.92,4.95s18.81,8.96,29.42,16.96Z" style="fill:#000;" />
                    <path d="M2504.48,210.05h101.49v11.26h-21.62c-4.6,0-8.16,1.2-10.66,3.6-2.5,2.4-3.75,5.45-3.75,9.16v103.11l83.62-89.29c4.8-5.2,7.21-10.11,7.21-14.71,0-3.5-1.65-6.35-4.95-8.56-3.3-2.2-8.71-3.3-16.21-3.3h-6.16v-11.26h91.13v11.26h-7.06c-6.11,0-12.81,1.8-20.12,5.41-7.31,3.6-14.06,8.61-20.27,15.01l-51.41,53.71,73.63,108.58c4.1,6.11,8.36,10.43,12.76,12.99,4.4,2.55,9.61,3.83,15.61,3.83h8.11v11.26h-100.13v-11.26h11.41c8.31,0,13.83-.7,16.59-2.1,2.75-1.4,4.13-3.15,4.13-5.25,0-1.9-.6-3.8-1.8-5.71l-61.21-90-34.87,37.3v54.65c0,3.4,1,5.91,3,7.51,3.2,2.4,7,3.6,11.41,3.6h21.62v11.26h-101.49v-11.26h19.52c5,0,8.71-1.02,11.11-3.08,2.4-2.05,3.6-4.73,3.6-8.03V232.42c0-3.5-1.35-6.23-4.05-8.18-2.7-1.95-7.26-2.93-13.66-2.93h-16.51v-11.26Z" style="fill:#000;" />
                </g>
            </svg>
        </a>
    </div>
    <div class="container shadow rounded-3 position-relative bg-light mb-3" style="margin-top:-50px;z-index:10" id="mainpageContainer">
        <?php include '../../assets/php/admin-nav-v2.php' ?>
        <!-- ------------ -->
        <!-- PAGE CONTENT -->
        <!-- ------------ -->
        <div class="container">
            <div class="row">
                <div class="col mb-5">
                    <hr class="text-light my-3">
                    <h1 class="mb-3">Mitarbeiterprofil</h1>
                    <?php
                    $panelakten = mysqli_query($conn, "SELECT id, username, fullname, aktenid FROM cirs_users WHERE aktenid = '$openedID'") or die(mysqli_error($conn));
                    $num = mysqli_num_rows($panelakten);
                    $panelakte = mysqli_fetch_assoc($panelakten);

                    if ($num != 0) {
                    ?>
                        <div class="alert alert-info" role="alert">
                            <h5 class="fw-bold">Achtung!</h5>
                            Dieses Mitarbeiterprofil gehört einem Funktionsträger - dieser besitzt ein registriertes Benutzerkonto im Intranet.<br>
                            <?php if ($admincheck || $usedit) { ?>
                                <strong>Name u. Benutzername:</strong> <a href="/admin/users/user<?= $panelakte['id'] ?>" class="text-decoration-none"><?= $panelakte['fullname'] ?> (<?= $panelakte['username'] ?>)</a>
                            <?php } else { ?>
                                <strong>Name u. Benutzername:</strong> <?= $panelakte['fullname'] ?> (<?= $panelakte['username'] ?>)
                            <?php } ?>
                        </div>
                    <?php
                    }
                    include('../../assets/php/personal-checks.php') ?>
                    <div class="row">
                        <div class="col-5 p-3 shadow-sm border ma-basedata">
                            <form id="profil" method="post">
                                <?php if (!isset($_GET['edit']) && $canEdit) { ?>
                                    <a href="?id=<?= $_GET['id'] . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '') ?>&edit" class="btn btn-dark btn-sm" id="personal-edit"><i class="fa-solid fa-edit"></i></a>
                                <?php } elseif (isset($_GET['edit']) && $canEdit) { ?>
                                    <a href="#" class="btn btn-success btn-sm" id="personal-save" onclick="document.getElementById('profil').submit()"><i class="fa-solid fa-floppy-disk"></i></a>
                                    <a href="<?php echo removeEditParamFromURL(); ?>" class="btn btn-dark btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
                                    <?php if ($admincheck || $perdelete) { ?>
                                        <div class="btn btn-danger btn-sm" id="personal-delete" data-bs-toggle="modal" data-bs-target="#modalPersoDelete"><i class="fa-solid fa-trash"></i></div>
                                <?php }
                                } ?>
                                <?php
                                // Function to remove the 'edit' parameter from the URL
                                function removeEditParamFromURL()
                                {
                                    $currentURL = $_SERVER['REQUEST_URI'];
                                    $parsedURL = parse_url($currentURL);
                                    parse_str($parsedURL['query'], $queryParams);
                                    unset($queryParams['edit']);
                                    $newQuery = http_build_query($queryParams);
                                    $modifiedURL = $parsedURL['path'] . '?' . $newQuery;
                                    return $modifiedURL;
                                }

                                $dienstgradMapping = array(
                                    16 => "Ehrenamtliche/-r",
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
                                    15 => "Brandratanwärter/in",
                                    11 => "Brandrat/rätin",
                                    12 => "Oberbrandrat/rätin",
                                    13 => "Branddirektor/-in",
                                    14 => "Leitende/-r Branddirektor/-in",
                                );

                                $rankIcons = [
                                    1 => '/assets/img/dienstgrade/bf/1.png',
                                    2 => '/assets/img/dienstgrade/bf/2.png',
                                    3 => '/assets/img/dienstgrade/bf/3.png',
                                    4 => '/assets/img/dienstgrade/bf/4.png',
                                    5 => '/assets/img/dienstgrade/bf/5.png',
                                    6 => '/assets/img/dienstgrade/bf/6.png',
                                    7 => '/assets/img/dienstgrade/bf/7.png',
                                    8 => '/assets/img/dienstgrade/bf/8.png',
                                    9 => '/assets/img/dienstgrade/bf/9.png',
                                    10 => '/assets/img/dienstgrade/bf/10.png',
                                    15 => '/assets/img/dienstgrade/bf/15.png',
                                    11 => '/assets/img/dienstgrade/bf/11.png',
                                    12 => '/assets/img/dienstgrade/bf/12.png',
                                    13 => '/assets/img/dienstgrade/bf/13.png',
                                    14 => '/assets/img/dienstgrade/bf/14.png',
                                ];

                                ?>
                                <div class="w-100 text-center">
                                    <i class="fa-solid fa-user-circle" style="font-size:94px"></i>
                                    <?php if (!isset($_GET['edit']) || !$canEdit) { ?>
                                        <p class="mt-3" style="text-transform:uppercase">
                                            <?php
                                            $rank = $row['dienstgrad'];
                                            if (isset($rankIcons[$rank])) {
                                            ?>
                                                <img src="<?= $rankIcons[$rank] ?>" height='16px' width='auto' alt='Dienstgrad' />
                                            <?php } ?>
                                            <?= $dienstgrad ?>
                                            <?php if ($row['qualird'] > 0) { ?>
                                                <br><span style="text-transform:none" class="badge bg-warning"><?= $rdqualtext ?></span>
                                            <?php } ?>
                                        </p>
                                    <?php } else {
                                        include('../../assets/php/personal-dg-select.php');
                                    } ?>
                                    <hr class="my-3">
                                    <?php if (!isset($_GET['edit']) || !$canEdit) { ?>
                                        <table class="mx-auto">
                                            <tbody class="text-start">
                                                <tr>
                                                    <td class="fw-bold">Vor- und Zuname</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $row['fullname'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Geburtsdatum</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $geburtstag ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Charakter-ID</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $row['charakterid'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Foren-Profil</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><a href="https://nordnetzwerk.eu/app/user/<?= $row['forumprofil'] ?>">Zum Profil</a></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Discord-Tag</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $row['discordtag'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Telefonnummer</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $row['telefonnr'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Dienstnummer</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $row['dienstnr'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Einstellungsdatum</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><?= $einstellungsdatum ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } elseif (isset($_GET['edit']) && $canEdit) { ?>
                                        <input type="hidden" name="id" id="id" value="<?= $_GET['id'] ?>" />
                                        <input type="hidden" name="new" value="1" />
                                        <table class="mx-auto">
                                            <tbody class="text-start">
                                                <tr>
                                                    <td class="fw-bold">Vor- und Zuname</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="text" name="fullname" id="fullname" value="<?= $row['fullname'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Geburtsdatum</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="date" name="gebdatum" id="gebdatum" value="<?= $row['gebdatum'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Charakter-ID</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="text" name="charakterid" id="charakterid" value="<?= $row['charakterid'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Foren-Profil</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="number" name="forumprofil" id="forumprofil" value="<?= $row['forumprofil'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Discord-Tag</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="text" name="discordtag" id="discordtag" value="<?= $row['discordtag'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Telefonnummer</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="text" name="telefonnr" id="telefonnr" value="<?= $row['telefonnr'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Dienstnummer</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="number" name="dienstnr" id="dienstnr" value="<?= $row['dienstnr'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold">Einstellungsdatum</td>
                                                    <td><span class="mx-1"></span></td>
                                                    <td><input class="form-control" type="date" name="einstdatum" id="einstdatum" value="<?= $row['einstdatum'] ?>" readonly disabled></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                    <hr class="my-3">
                                    <div class="btn btn-secondary mb-2" style="border-radius:0;max-width:80%;width:80%" data-bs-toggle="modal" data-bs-target="#modalFWQuali">FW Qualifikationen einsehen</div>
                                    <div class="btn btn-secondary mb-2" style="border-radius:0;max-width:80%;width:80%" data-bs-toggle="modal" data-bs-target="#modalRDQuali">RD Qualifikationen einsehen</div>
                                    <div class="btn btn-secondary mb-2" style="border-radius:0;max-width:80%;width:80%" data-bs-toggle="modal" data-bs-target="#modalFDQuali">Fachdienste einsehen</div>

                                    <?php if ($canView) { ?>
                                        <div class="btn btn-primary mb-2" style="border-radius:0;max-width:80%;width:80%" data-bs-toggle="modal" data-bs-target="#modalNewComment">Notiz anlegen</div>
                                    <?php } ?>
                                    <?php if ($admincheck || $perdoku) { ?>
                                        <div class="btn btn-primary mb-2" style="border-radius:0;max-width:80%;width:80%" data-bs-toggle="modal" data-bs-target="#modalDokuCreate">Dokument erstellen</div>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                        <div class="col ms-4 p-3 shadow-sm border ma-comments">
                            <div class="comment-settings mb-3">
                                <h4>Kommentare/Notizen</h4>
                            </div>
                            <div class="comment-container">
                                <?php include('../../assets/php/personal-comments.php') ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mb-4">
                        <div class="col p-3 shadow-sm border ma-documents">
                            <h4>Dokumente</h4>
                            <?php include('../../assets/php/personal-documents.php') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('../../assets/php/personal-modals.php') ?>
    <div class="floating-button">
        <button id="dark-mode-toggle" class="btn btn-primary">
            <i id="mode-icon" class="fa-solid fa-lightbulb"></i>
        </button>
    </div>
    <script>
        // Function to toggle dark mode
        function toggleDarkMode() {
            const html = document.querySelector('html');
            const isDarkMode = html.getAttribute('data-bs-theme') === 'dark';

            if (isDarkMode) {
                html.setAttribute('data-bs-theme', 'light');
                localStorage.setItem('darkMode', 'false');
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('darkMode', 'true');
            }
        }

        // Function to check and set the theme based on user preference
        function checkThemePreference() {
            const savedDarkMode = localStorage.getItem('darkMode');
            const html = document.querySelector('html');

            if (savedDarkMode === 'true') {
                html.setAttribute('data-bs-theme', 'dark');
            } else {
                html.setAttribute('data-bs-theme', 'light');
            }
        }

        // Event listener for dark mode toggle
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        darkModeToggle.addEventListener('click', toggleDarkMode);

        // Initialize theme preference
        checkThemePreference();
    </script>
</body>

</html>