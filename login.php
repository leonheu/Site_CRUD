<?php

include($_SERVER['DOCUMENT_ROOT'] . "/host.php");
$errors = array();

if (isset($_POST['login'])) {
    if (!empty($_POST['user_mail']) && !empty($_POST['user_mdp'])) {
        $req = $db->prepare('SELECT * FROM users
    NATURAL JOIN roles WHERE user_mail = ?
');
        $req->execute([strtolower($_POST['user_mail'])]);
        $user = $req->fetch();
        if ($user) {
            if (password_verify($_POST['user_mdp'], $user['user_mdp'])) {
                echo 'coucou';
                $_SESSION['auth'] = $user;

                echo "<script language='javascript'>
                        document.location.replace('../index.php');
                        </script>
                    ";
            } else {
                $errors['user_mdp'] = "Le mot de pass est invalide.";
            }
        }
    }
}



include($_SERVER['DOCUMENT_ROOT'] . "/_blocks/doctype.php");


?>


</head>

<body class="vh-100 d-flex flex-column justify-content-center align-items-center bg-light">


    <?php
    if (!empty($errors)) {
    ?>
        <div id="zoneErreur">
            <div id="danger" class="alert alert-danger" role="alert">
                <p>Connexion impossible</p>
                <ul>
                    <?php
                    foreach ($errors as $error) :
                    ?>
                        <li><?php echo $error; ?></li>
                    <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="container w-25 shadow p-3 mb-5 bg-white rounded border">
        <h1>Connectez-vous !</h1>

        <form action="" method="post">

            <div class="mb-3">
                <label for="email" class="form-label">Email: </label>
                <input type="email" class="form-control" id="email" placeholder="Votre email" name="user_mail">
            </div>

            <div class="mb-3">
                <label for="mdp" class="form-label">Mot de passe: </label>
                <input type="password" class="form-control" id="mdp" placeholder="Votre mot de passe" name="user_mdp">
            </div>

            <div class="mb-3 text-center">
                <input type="submit" value="Log in" name="login" class="btn btn-primary">
            </div>


        </form>
    </div>
</body>

</html>