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

<body style="background-color: #293241;">
    <div class="row" style="background-color: #181D26;">
        <div class="col text-center text-light pt-3 pb-2">
            <h4>Patienten-Anmeldungen durch Rettungsdienst</h4>
        </div>
    </div>
    <div class="row mx-4 mt-4" id="card-container">
        <div class="col-3">
            <div class="card mb-3 shadow">
                <div class="card-body">
                    <h4 class="card-title text-center fw-bold">Organisationseinheit - ZNA</h4>
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

            $result = mysqli_query($conn, "SELECT * FROM klinik_anmeldungen");
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
                        <h5 class="card-title"><?= $row['diagnose'] ?></h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary"><?= $row['estichwort'] ?></h6>
                        <p class="card-text"><span data-bs-toggle="tooltip" data-bs-title="Geschätzte Eintreffzeit"><i class="fa-regular fa-clock"></i> <?= $row['ankunftzeit'] ?></span> <span class="mx-3">//</span> <span data-bs-toggle="tooltip" data-bs-title="Notfallprotokoll einsehen (insofern verfügbar)"><i class="fa-regular fa-paperclip"></i> <?= $row['enr'] ?></span> <span class="mx-3">//</span> <span data-bs-toggle="tooltip" data-bs-title="Transportziel"><i class="fa-regular fa-building"></i> <?= $zielobjtypValues[$row['zielobjtyp']] ?></span></p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="col-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Mehr Informationen</h5>
                    <?php if (!isset($_GET['card'])) { ?>
                        <p class="card-text">Bitte eine Anmeldung anwählen</p>
                    <?php } else {
                        $result = mysqli_query($conn, "SELECT * FROM klinik_anmeldungen WHERE id = '" . $_GET['card'] . "'");
                        $row = mysqli_fetch_array($result);
                    ?>
                        <h6 class="card-subtitle mb-2 text-body-secondary">1. Notfallkategorie</h6>
                        <?php
                        if ($row['kat_1'] == 1) {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Sofort</h4>
                                <p>Der Patient muss sofort behandelt werden!</p>
                            </div>
                        <?php }
                        if ($row['kat_2'] == 1) {
                        ?>
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">Dringend</h4>
                                <p>Der Patient muss dringend behandelt werden!</p>
                            </div>
                        <?php }
                        if ($row['kat_3'] == 1) {
                        ?>
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">Normal</h4>
                                <p>Der Patient kann normal behandelt werden!</p>
                            </div>
                        <?php }
                        if ($row['kat_1'] == 0 && $row['kat_2'] == 0 && $row['kat_3'] == 0) {
                        ?>
                            <div class="alert alert-primary" role="alert">
                                <h4 class="alert-heading">Keine Kategorie</h4>
                                <p>Der Patient hat keine Kategorie!</p>
                            </div>
                        <?php }
                        ?>
                        <h6 class="card-subtitle mb-2 text-body-secondary">2. Atemwege</h6>
                        <div class="row py-1 px-2 mb-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="a_1" name="a_1" value="1" autocomplete="off" <?php echo ($row['a_1'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="a_1">Intubiert</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="a_2" name="a_2" value="1" autocomplete="off" <?php echo ($row['a_2'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="a_2">Koniotomie</label>
                            </div>
                        </div>
                        <h6 class="card-subtitle mb-2 text-body-secondary">3. Atmung</h6>
                        <div class="row py-1 px-2 mb-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="b_1" name="b_1" value="1" autocomplete="off" <?php echo ($row['b_1'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="b_1">Beatmet</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="b_2" name="b_2" value="1" autocomplete="off" <?php echo ($row['b_2'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="b_2">Entlastungspunktion</label>
                            </div>
                        </div>
                        <h6 class="card-subtitle mb-2 text-body-secondary">4. Kreislauf</h6>
                        <div class="row py-1 px-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="c_1" name="c_1" value="1" autocomplete="off" <?php echo ($row['c_1'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="b_1">STEMI Verdacht</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="c_2" name="c_2" value="1" autocomplete="off" <?php echo ($row['c_2'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="c_2">Lebensbedrohliche Blutung</label>
                            </div>
                        </div>
                        <div class="row py-1 px-2 mb-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="c_3" name="c_3" value="1" autocomplete="off" <?php echo ($row['c_3'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="c_3">Laufende Reanimation</label>
                            </div>
                            <div class="col"></div>
                        </div>
                        <h6 class="card-subtitle mb-2 text-body-secondary">5. Neurologie</h6>
                        <div class="row py-1 px-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="d_1" name="d_1" value="1" autocomplete="off" <?php echo ($row['d_1'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="d_1">STROKE Verdacht</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="d_2" name="d_2" value="1" autocomplete="off" <?php echo ($row['d_2'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="d_2">Narkose</label>
                            </div>
                        </div>
                        <div class="row py-1 px-2 mb-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="d_3" name="d_3" value="1" autocomplete="off" <?php echo ($row['d_3'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="d_3">Betäubungsmittel Verdacht</label>
                            </div>
                            <div class="col"></div>
                        </div>
                        <h6 class="card-subtitle mb-2 text-body-secondary">6. Sonstiges</h6>
                        <div class="row py-1 px-2 mb-2">
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="s_1" name="s_1" value="1" autocomplete="off" <?php echo ($row['s_1'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="s_1">Transport liegend</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" class="btn-check" id="s_2" name="s_2" value="1" autocomplete="off" <?php echo ($row['s_2'] == 1 ? 'checked' : '') ?> disabled>
                                <label class="btn btn-ss-blue w-100" for="s_2">Transport sitzend</label>
                            </div>
                        </div>
                    <?php
                    } ?>
                </div>
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
            </script>

</body>

</html>