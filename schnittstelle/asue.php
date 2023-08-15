Copy<!DOCTYPE html>
<html>
<head>
    <title>Atemschutztableau</title>
    <style>
        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>
<body>
    <?php
    $atemschutzgeraete = array(
        array("Gerätebezeichnung", "Anzahl"),
        array("Halbmaske", 5),
        array("Vollmaske", 3),
        array("Filtergerät", 2)
    );
    ?>

    <h1>Atemschutztableau</h1>

    <table>
        <?php foreach ($atemschutzgeraete as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                    <?php if ($row[0] == $cell): ?>
                        <th><?php echo $cell; ?></th>
                    <?php else: ?>
                        <td><?php echo $cell; ?></td>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
