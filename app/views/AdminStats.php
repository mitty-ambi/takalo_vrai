<?php


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>ca va ve zala eaaaaa </h1>
    <p>nombre d'utilisateur inscrits : <?= count($statsParJour) ?></p>
    <table border="1px">
        <tr></tr>
        <tr>
            <td>id user</td>
            <td>nom</td>
            <td>prenom</td>
            <td>email</td>
            <td>type d'utilisateur</td>
            <td>Date creation</td>
        </tr>
        <?php foreach ($statsParJour as $stats) { ?>
            <tr>
                <td><?= $stats['id_user'] ?></td>
                <td><?= $stats['nom'] ?></td>
                <td><?= $stats['prenom'] ?></td>
                <td><?= $stats['email'] ?></td>
                <td><?= $stats['type_user'] ?></td>
                <td><?= $stats['date_creation'] ?></td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>