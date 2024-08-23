<?php

include($_SERVER['DOCUMENT_ROOT'] . "/host.php");

$selectAllRoles = $db->prepare('SELECT * FROM roles ORDER BY role_level asc');
$selectAllRoles->execute();

include($_SERVER['DOCUMENT_ROOT'] . "/_blocks/doctype.php");

if (isset($_POST['addUser'])) {
    $errors = array();

    if (empty($_POST['user_firstname']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['user_firstname'])) {
        $errors['user_firstname'] = "Le champs 'Prénom' n'est pas valide.";
    }
    if (empty($_POST['user_lastname']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['user_lastname'])) {
        $errors['user_lastname'] = "Le champs 'Nom' n'est pas valide.";
    }
    if (empty($_POST['user_mail']) || !filter_var($_POST['user_mail'], FILTER_VALIDATE_EMAIL)) {
        $errors['user_mail'] = "Il ne s'agit pas d'un mail.";
    } else {
        $req = $db->prepare("SELECT id_user FROM users WHERE user_mail = ?");
        $req->execute([$_POST['user_mail']]);
        $email = $req->fetch(PDO::FETCH_OBJ);
        if ($email) {
            $errors['user_mail'] = "Cet email est déjà utilisé pas un compte.";
        }
    }

    if (empty($_POST['user_mdp']) || $_POST['user_mdp'] != $_POST['confmdp']) {
        $errors['user_mdp'] = "Vos mots de passe sont vides ou ne sont pas identiques.";
    }

    if (empty($errors)) {

        $insertUser = $db->prepare('
        INSERT INTO users SET 
        user_firstname = ? ,
        user_lastname = ? ,
        user_mail = ? ,
        id_role = ? ,
        user_mdp = ?
        ');

        $password = password_hash($_POST['user_mdp'], PASSWORD_BCRYPT);
        $insertUser->execute([
            strtolower($_POST['user_firstname']),
            strtolower($_POST['user_lastname']),
            strtolower($_POST['user_mail']),
            $_POST['id_role'],
            $password
        ]);
        echo "<script language='javascript'>
            document.location.replace('../index.php');
            </script>
        ";
    }
}


?>

</head>

<body class="vh-100 d-flex flex-column justify-content-center align-items-center bg-light">



    <div class="container">
        <div class="container mb-3 text-center"><a class="btn btn-primary" href="../index.php">Retour à la liste</a></div>
        <h1>Insertion d'un nouvelle utilisateur</h1>

        <?php
        if (!empty($errors)) {
        ?>
            <div id="zoneErreur">
                <div id="danger" class="alert alert-danger" role="alert">
                    <p>Le formulaire n'est pas correctement renseigné:</p>
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

        <form action="" method="post">

            <div class="mb-3">
                <label for="firstname" class="form-label">Prénom: </label>
                <input type="text" class="form-control" id="firstname" placeholder="Votre prénom" name="user_firstname">
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Nom: </label>
                <input type="text" class="form-control" id="lastname" placeholder="Votre nom" name="user_lastname">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email: </label>
                <input type="email" class="form-control" id="email" placeholder="Votre email" name="user_mail">
            </div>

            <div class="mb-3">
                <label for="id_role">Role de l'utilisateur:</label>
                <select class="form-select" id="id_role" name="id_role" aria-label="Default select example">

                    <?php
                    while ($sAR = $selectAllRoles->fetch(PDO::FETCH_OBJ)) {

                    ?>

                        <option value="<?php echo $sAR->id_role; ?>"><?php echo ucfirst($sAR->role_name); ?></option>

                    <?php
                    }
                    ?>

                </select>
            </div>

            <div class="mb-3">
                <label for="mdp" class="form-label">Mot de passe: </label>
                <input type="password" class="form-control" id="mdp" placeholder="Votre mot de passe" name="user_mdp">
            </div>
            <div class="mb-3">
                <label for="confmdp" class="form-label">Confirmer votre mot de passe: </label>
                <input type="password" class="form-control" id="confmdp" placeholder="Votre mot de passe" name="confmdp">
            </div>

            <div class="mb-3 text-center">
                <input type="submit" value="Ajouter" name="addUser" class="btn btn-primary">
            </div>

        </form>
    </div>
</body>

</html>