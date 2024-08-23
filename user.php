<?php

include($_SERVER['DOCUMENT_ROOT'] . "/host.php");

$id = $_GET['id'];

$selectUser = $db->prepare('SELECT * FROM users
NATURAL JOIN roles
where id_user = ?
');
$selectUser->execute([$id]);
$user = $selectUser->fetch(PDO::FETCH_OBJ);

$id_role = $user->id_role;
$mail = $user->user_mail;

$selectAllRoles = $db->prepare('SELECT * FROM roles 
WHERE id_role != ?
ORDER BY role_level asc');
$selectAllRoles->execute([$id_role]);

if (isset($_POST['updateUser'])) {
    $errors = array();

    if (empty($_POST['user_firstname']) || !preg_match('/^[a-zA-Z ]+$/', $_POST['user_firstname'])) {
        $errors['user_firstname'] = "Le champs 'Préom' n'est pas valide.";
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
            if ($email->id_user != $id) {
                $errors['user_mail'] = "Cet email est déjà utilisé pas un compte.";
            }
        }
    }

    if (empty($errors)) {

        $updateUser = $db->prepare('
        UPDATE users SET 
        user_firstname = ? ,
        user_lastname = ? ,
        user_mail = ? ,
        id_role = ? 
        WHERE id_user = ?
        ');

        $updateUser->execute([
            strtolower($_POST['user_firstname']),
            strtolower($_POST['user_lastname']),
            strtolower($_POST['user_mail']),
            $_POST['id_role'],
            $id
        ]);

        echo "<script language='javascript'>
            document.location.replace('../index.php');
            </script>
        ";
    }
}


include($_SERVER['DOCUMENT_ROOT'] . "/_blocks/doctype.php");

?>


</head>

<body class="vh-100 d-flex flex-column justify-content-center align-items-center bg-light">
    <div class="container mb-3 text-center"><a class="btn btn-primary" href="../index.php">Retour à la liste</a></div>

    <h1>Vue de l'utilisateur.</h1>

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

    <div class="container">
        <form action="" method="post">

            <div class="mb-3">
                <label for="firstname" class="form-label">Prénom: </label>
                <input type="text" class="form-control" id="firstname" placeholder="Votre prénom" name="user_firstname" value="<?php echo $user->user_firstname ?>">
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Nom: </label>
                <input type="text" class="form-control" id="lastname" placeholder="Votre nom" name="user_lastname" value="<?php echo $user->user_lastname ?>">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email: </label>
                <input type="email" class="form-control" id="email" placeholder="Votre email" name="user_mail" value="<?php echo $user->user_mail ?>">
            </div>

            <div class="mb-3">
                <label for="id_role">Role de l'utilisateur:</label>
                <select class="form-select" id="id_role" name="id_role" aria-label="Default select example">
                    <option value="<?php echo $user->id_role ?>" selected><?php echo ucfirst($user->role_name) ?></option>
                    <?php
                    while ($sAR = $selectAllRoles->fetch(PDO::FETCH_OBJ)) {

                    ?>

                        <option value="<?php echo $sAR->id_role; ?>"><?php echo ucfirst($sAR->role_name); ?></option>

                    <?php
                    }
                    ?>

                </select>
            </div>

            <div class="mb-3 text-center">
                <input type="submit" value="Modifier" name="updateUser" class="btn btn-primary">
            </div>

        </form>
    </div>
</body>

</html>