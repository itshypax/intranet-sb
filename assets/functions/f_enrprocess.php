<?php

include("../php/mysql-con.php");

// Database connection code here

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"];
    $enr = $_POST["enr"];

    // check if there is a existing enr with this name.
    $sql = "SELECT * FROM cirs_rd_protokolle WHERE enr = :enr";
    $stmt = $mysql->prepare($sql);
    $stmt->bindParam(":enr", $enr);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // get amount of results
    $count = $stmt->rowCount();

    if ($action === "openOrCreate") {
        if ($count > 0) {
            $redirectUrl = "/edivi/protokoll.php?enr=$enr";
            echo $redirectUrl;
            exit();
        } else {
            try {
                $sql2 = "INSERT INTO cirs_rd_protokolle (enr) VALUES (:enr)";

                $stmt2 = $mysql->prepare($sql2);
                $stmt2->bindParam(":enr", $enr);
                $stmt2->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage(); // Output the error message for debugging
            }

            $redirectUrl = "/edivi/protokoll.php?enr=$enr";
            echo $redirectUrl;
            exit();
        }
    }
}
