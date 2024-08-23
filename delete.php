<?php

include($_SERVER['DOCUMENT_ROOT'] . "/host.php");

$id = $_GET['id'];

$selectUser = $db->prepare('SELECT * FROM users
NATURAL JOIN roles
where id_user = ?
');
$selectUser->execute([$id]);
$user = $selectUser->fetch(PDO::FETCH_OBJ);

$selectAllUsers = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles
');
$selectAllUsers->execute();
$return = count($selectAllUsers->fetchALL());

if (isset($_POST['yes'])) {
    $deleteUser = $db->prepare('DELETE FROM users WHERE id_user = ?');
    $deleteUser->execute([$id]);

    echo "<script language='javascript'>
            document.location.replace('../index.php');
            </script>
        ";
}

if (isset($_POST['no'])) {
    echo "<script language='javascript'>
            document.location.replace('../index.php');
            </script>
        ";
}

include($_SERVER['DOCUMENT_ROOT'] . "/_blocks/doctype.php");

?>


</head>

<body class="vh-100 d-flex flex-column justify-content-center align-items-center bg-light">

    <div class="container text-center">
        <h1>Voulez-vous supprimer <?php echo ucfirst($user->user_firstname) . ' ' . strtoupper($user->user_lastname); ?>?</h1>

        <form action="" method="post" class="m-5">

            <?php

            if ($return > 1) {

            ?>

                <button class="btn btn-success" type="submit" name="yes">Oui</button>
                <button class="btn btn-danger" type="submit" name="no">Non</button>

            <?php
            } else {
            ?>
                <h2>Vous ne pouvez plus supprimer.</h2>
                <a class="btn btn-primary" href="../index">Retour à la liste</a>
            <?php
            }
            ?>

        </form>
    </div>
</body>

</html>