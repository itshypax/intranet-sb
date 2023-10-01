<?php
session_start();

include '../assets/php/mysql-con.php';
date_default_timezone_set('Europe/Berlin');
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

<body style="background-color: #293241; overflow-x:hidden;">
    <button onclick="playSound();" id="soundBtn">Play</button>
    <script type="text/javascript">
        document.getElementById('soundBtn').style.visibility = 'hidden';

        function performSound() {
            var soundButton = document.getElementById("soundBtn");
            soundButton.click();
        }

        function playSound() {
            const audio = new Audio("/assets/sfx/ding2.mp3");
            audio.play();
        }
    </script>
    <div class="row" style="background-color: #181D26;">
        <div class="col text-center text-light pt-3 pb-2">
            <h4>Patienten-Anmeldungen durch Rettungsdienst</h4>
        </div>
    </div>
    <div class="row mx-4 mt-4" id="card-container">
        <div class="col">
            <div class="card mb-3 shadow baseCard">
                <div class="card-body">
                    <h4 class="card-title text-center fw-bold">Organisationseinheit - UKSB</h4>
                </div>
            </div>

            <?php
            $zielobjtypValues = array(
                "1" => "ZNA",
                "2" => "ZNA/Schockraum",
                "3" => "Intensivstation",
                "4" => "OP"
            );

            $currentTime = time();

            $result = mysqli_query($conn, "SELECT * FROM klinik_anmeldungen WHERE zielklinik = 1");
            $resultnumber = mysqli_num_rows($result);
            // check table klinik_row_count if the number is equal
            // if it is not update it
            $result2 = mysqli_query($conn, "SELECT * FROM klinik_row_count WHERE id = 1");
            $row2 = mysqli_fetch_array($result2);
            if (!$result2) {
                die("Feher in der Datenbankabfrage: " . mysqli_error($conn));
            }
            echo $row2['rows'];
            echo $resultnumber;
            if ($row2['rows'] != $resultnumber) {
                $result2 = mysqli_query($conn, "UPDATE klinik_row_count SET `rows` = " . $resultnumber . " WHERE id = 1");
                if (!$result2) {
                    die("Feher in der Datenbankabfrage: " . mysqli_error($conn));
                }
            ?>
                <script>
                    performSound()
                </script>
            <?php
            }

            while ($row = mysqli_fetch_array($result)) {
                $timestamp = strtotime($row['timestamp']);

                if ($timestamp < strtotime("-45 minutes")) {
                    continue;
                }

                $result2 = mysqli_query($conn, "SELECT * FROM cirs_rd_protokolle WHERE enr = '" . $row['enr'] . "'");
                $row2 = mysqli_fetch_array($result2);
                $row2_count = mysqli_num_rows($result2);

                if ($row2_count > 0) {
                    $row['enr'] = "<a href='/edivi/khview.php?enr=" . $row['enr'] . "' target='_blank'>" . $row['enr'] . "</a>";
                }

                if ($row['kat_1'] == 1) {
                    $border_type = "border-danger";
                } else if ($row['kat_2'] == 1) {
                    $border_type = "border-warning";
                } else if ($row['kat_3'] == 1) {
                    $border_type = "border-success";
                } else {
                    $border_type = "border-primary";
                }
            ?>
                <div class="card shadow mb-3 <?= $border_type ?> border-5 border-top-0 border-end-0 border-bottom-0 clickableCard" data-id="<?= $row['id'] ?>">
                    <div class="card-body">
                        <h2 class="card-title"><?= $row['diagnose'] ?></h2>
                        <h3 class="card-subtitle mb-2 text-body-secondary"><?= $row['estichwort'] ?></h3>
                        <p class="card-text"><span data-bs-toggle="tooltip" data-bs-title="Geschätzte Eintreffzeit"><i class="fa-regular fa-clock"></i> <?= $row['ankunftzeit'] ?></span> <span class="mx-3">//</span> <span data-bs-toggle="tooltip" data-bs-title="Notfallprotokoll einsehen (insofern verfügbar)"><i class="fa-regular fa-paperclip"></i> <?= $row['enr'] ?></span> <span class="mx-3">//</span> <span data-bs-toggle="tooltip" data-bs-title="Transportziel"><i class="fa-regular fa-building"></i> <?= $zielobjtypValues[$row['zielobjtyp']] ?></span></p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
       
            <script>
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
            </script>
            <script>
                const clickableCards = document.querySelectorAll('.clickableCard');
                clickableCards.forEach((card) => {
                    card.addEventListener('click', () => {
                        const cardId = card.getAttribute('data-id');
                        const currentUrl = new URL(window.location.href);

                        if (cardId) {
                            if (currentUrl.searchParams.has('card')) {
                                currentUrl.searchParams.set('card', cardId);
                            } else {
                                currentUrl.searchParams.append('card', cardId);
                            }
                        }

                        window.location.href = currentUrl.toString();
                    });
                });

                const resetCard = document.querySelector('.baseCard');
                resetCard.addEventListener('click', () => {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('card');
                    window.location.href = currentUrl.toString();
                });
            </script>
            <script>
                function refreshPage() {
                    // Get the current query parameters
                    var queryParams = window.location.search;

                    // Reload the page with the same query parameters
                    window.location.href = window.location.pathname + queryParams;
                }
                setInterval(refreshPage, 15000); // Refresh every 15 seconds
            </script>
</body>

</html>