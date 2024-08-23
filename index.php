<?php

include($_SERVER['DOCUMENT_ROOT'] . "/host.php");

$selectAllUsers = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles
');
$selectAllUsers->execute();



include($_SERVER['DOCUMENT_ROOT'] . "/_blocks/doctype.php");

?>


</head>

<body class="vh-100 d-flex flex-column justify-content-center align-items-center bg-light">

    <?php
    if (isset($_SESSION['auth'])) {
    ?>

        <div class="container d-flex flex-column justify-content-center align-items-center">
            <h1>Liste des utilisateurs</h1>

            <div class="container d-flex mb-3 justify-content-between bg-secondary">
                <a href="./insert_user.php" class="btn btn-primary m-3">Nouvel utilisateur</a>
                <a href="./logout.php" class="btn btn-light m-3">Se déconnecter</a>
            </div>

            <div class="container">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Prénom</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Role</th>
                            <th scope="col">Date de création</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        while ($sAU = $selectAllUsers->fetch(PDO::FETCH_OBJ)) {

                        ?>

                            <tr>
                                <th scope="row"><?php echo $sAU->id_user; ?></th>
                                <td><?php echo ucfirst($sAU->user_firstname); ?></td>
                                <td><?php echo strtoupper($sAU->user_lastname); ?></td>
                                <td><?php echo strtoupper($sAU->role_name); ?></td>
                                <td><?php echo $sAU->user_insert_date; ?></td>
                                <td>
                                    <a class="btn btn-primary m-1" href="../user.php?id=<?php echo $sAU->id_user; ?>">Voir</a>

                                    <?php
                                    if ($_SESSION['auth']['role_level'] > 99) {
                                    ?>
                                        <a class="btn btn-danger m-1" href="../delete.php?id=<?php echo $sAU->id_user; ?>">Supprimer</a>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>

                        <?php
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>

    <?php   } else {
        echo "<script language='javascript'>
        document.location.replace('../login.php');
        </script>
    ";
    }

    ?>
</body>

</html>