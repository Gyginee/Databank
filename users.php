<?php
    require "requires/config.php";
    if (!$_SESSION['loggedin']) {
        header("Location: login");
        exit;
    }
    if ($_SESSION["role"] != "admin") {
        header("Location: dashboard");
        exit;
    }
    $respone = false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (trim($_POST['type']) == NULL) {
            header("Location:dashboard");
            exit;
        }
        if ($_POST['type'] == "create") {
            $last_login = date('Y-m-d');
            $password = password_hash($_POST['password'],PASSWORD_BCRYPT);
            $insert = $con->prepare("INSERT INTO users (username,password,name,role,rank,last_login) VALUES(:username, :password, :fullname, 'user', :rank, :last_login);");
            $insert->bindParam(":username", $_POST['username']);
            $insert->bindParam(":password", $password);
            $insert->bindParam(":fullname", $_POST['fullname']);
            $insert->bindParam(":rank", $_POST['rank']);
            $insert->bindParam(":last_login", $last_login);
            if ($insert->execute()) {
                $respone = true;
            }
        } elseif ($_POST['type'] == "delete") {
            $sql = $con->prepare("DELETE FROM users WHERE id = :userId;");
            $sql->bindParam(":userId", $_POST['deleteuser']);
            if ($sql->execute()) {
                $respone = true;
            } else {
                echo "Error deleting record: " . $sql->errorInfo();
                exit();
            }
        } elseif ($_POST['type'] == "edit") {
            $query = $con->prepare("SELECT * FROM users WHERE id = :userId;");
            $query->bindParam(":userId", $_POST['edituser']);
            $query->execute();
            $selecteduser = $query->fetchAll(PDO::FETCH_ASSOC)[0];
        } elseif ($_POST['type'] == "realedit") {
            $update = $con->prepare("UPDATE users SET username = :username, name = :fullname, rank = :rank WHERE id = :userId;");
            $update->bindParam(":username", $_POST['username']);
            $update->bindParam(":fullname", $_POST['fullname']);
            $update->bindParam(":rank", $_POST['rank']);
            $update->bindParam(":userId", $_POST['userid']);
            if ($update->execute()) {
                $respone = true;
            } else {
                $response = false;
            }
        }
    }
    $name = explode(" ", $_SESSION["name"]);
    $firstname = $name[0];
    $last_word_start = strrpos($_SESSION["name"], ' ') + 1;
    $lastname = substr($_SESSION["name"], $last_word_start);

    $result = $con->prepare("SELECT * FROM users WHERE role = 'user'");
    $result->execute();
    $user_array = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="https://www.politie.nl/politie2018/assets/images/icons/favicon.ico" type="image/x-icon" />
        <link rel="icon" type="image/png" sizes="16x16" href="https://www.politie.nl/politie2018/assets/images/icons/favicon-16.png">
        <link rel="icon" type="image/png" sizes="32x32" href="https://www.politie.nl/politie2018/assets/images/icons/favicon-32.png">
        <link rel="icon" type="image/png" sizes="64x64" href="https://www.politie.nl/politie2018/assets/images/icons/favicon-64.png">

        <title>Politie Databank</title>

        <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/starter-template/">

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <!-- Custom styles for this template -->
        <link href="assets/css/main.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar fixed-top navbar-expand-lg navbar-custom bg-custom">
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">

                <!-- Left menu -->
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-label" href="#">
                            <img src="assets/images/icon.png" width="22" height="22" alt="">
                            <span class="title">
                                Welkom <?php echo $_SESSION["rank"] . " " . $firstname . " " . substr($lastname, 0, 1); ?>.
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-button" href="logout">
                            <button class="btn btn-outline-light btn-logout my-2 my-sm-0" type="button">LOG UIT</button>
                        </a>
                    </li>
                </ul>

                <!-- Right menu -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard">DASHBOARD</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        OPZOEKEN
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="profiles">PERSONEN</a>
                            <a class="dropdown-item" href="reports">REPORTS</a>
                            <a class="dropdown-item" href="vehicles">VOERTUIGEN</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warrants">ARRESTATIEBEVELEN</a>
                    </li>
                    <?php if ($_SESSION["role"] == "admin") { ?>
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        ADMIN
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="laws">STRAFFEN</a>
                            <a class="dropdown-item" href="users">GEBRUIKERS</a>
                        </div>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link-report" href="createreport">NIEUW RAPPORT</a>
                    </li>
                </ul>
            </div>
        </nav>

        <main role="main" class="container">
            <div class="content-introduction">
                <h3>Gebruikers Instellingen</h3>
                <p class="lead">Hier kun je gebruikers aanmaken, bewerken en verwijderen. <br /><strong>Wanneer gebruikers verwijderd worden kan het niet meer ongedaan worden gemaakt!</strong></p>
            </div>
            <div class="users-container">
                <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "edit") { ?>
                    <div class="left-panel-container">
                    <h5 class="panel-container-title">Pas gebruiker aan</h5>
                    <form method="post">
                        <input type="hidden" name="type" value="realedit">
                        <input type="hidden" name="userid" value="<?php echo $selecteduser['id']; ?>">
                        <div class="input-group mb-3">
                            <input type="text" name="username" class="form-control login-user" value="<?php echo $selecteduser['username']; ?>" placeholder="gebruikersnaam">
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="fullname" class="form-control login-user" value="<?php echo $selecteduser['name']; ?>" placeholder="volledige naam">
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="rank" class="form-control login-user" value="<?php echo $selecteduser['rank']; ?>" placeholder="rank">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="create" class="btn btn-primary btn-police">Pas aan</button>
                        </div>
                    </form>
                </div> 
                <?php } else { ?>
                <!-- Left Container -->
                <div class="left-panel-container">
                    <h5 class="panel-container-title">Pas gebruiker aan</h5>
                    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "realedit" && $respone) {?>
                        <?php echo "<p style='color: #13ba2c;'>Gebruiker aangepast!</p>"; ?>
                    <?php } ?>
                    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "realedit" && !$respone) {?>
                        <?php echo "<p style='color:#9f1010;'>Gebruiker niet aangepast!</p>"; ?>
                    <?php } ?>
                    <form method="post">
                        <input type="hidden" name="type" value="edit">
                        <div class="form-group">
                            <label for="userselect">Gebruiker</label>
                            <select class="form-control" name="edituser">
                            <?php foreach($user_array as $user){?>
                                <option value="<?php echo $user["id"] ?>"><?php echo $user['name']; ?></option>
                            <?php }?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="edit" class="btn btn-primary btn-police">Pas aan</button>
                        </div>
                    </form>
                </div>  
                <!-- Right Container -->
                <div class="right-panel-container">
                    <h5 class="panel-container-title">Verwijder gebruiker</h5>
                    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "delete" && $respone) {?>
                        <?php echo "<p style='color: #13ba2c;'>Gebruiker verwijderd!</p>"; ?>
                    <?php } ?>
                    <form method="post">
                        <input type="hidden" name="type" value="delete">
                        <div class="form-group">
                            <label for="userselect">Gebruiker</label>
                            <select class="form-control" name="deleteuser">
                            <?php foreach($user_array as $user){?>
                                <option value="<?php echo $user["id"] ?>"><?php echo $user['name']; ?></option>
                            <?php }?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="delete" class="btn btn-primary btn-police">Verwijder</button>
                        </div>
                    </form>
                </div> 
                <div class="left-panel-container">
                    <h5 class="panel-container-title">Voeg gebruiker toe</h5>
                    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "create" && $respone) {?>
                        <?php echo "<p style='color: #13ba2c;'>Gebruiker toegevoegd!</p>"; ?>
                    <?php } ?>
                    <form method="post">
                        <input type="hidden" name="type" value="create">
                        <div class="input-group mb-3">
                            <input type="text" name="username" class="form-control login-user" value="" placeholder="gebruikersnaam" required>
                        </div>
                        <div class="input-group mb-2">
                            <input type="password" name="password" class="form-control login-pass" value="" placeholder="wacthtwoord" required>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="fullname" class="form-control login-user" value="" placeholder="volledige naam" required>
                        </div>
                        <select class="form-control" style="margin-bottom:2vh;" name="rank" required>
                            <option value="Aspirant">Aspirant</option>
                            <option value="Surveillant">Surveillant</option>
                            <option value="Agent">Agent</option>
                            <option value="Hoofdagent">Hoofdagent</option>
                            <option value="Brigadier">Brigadier</option>
                            <option value="Inspecteur">Inspecteur</option>
                            <option value="Hoofdinspecteur">Hoofdinspecteur</option>
                        </select>
                        <div class="form-group">
                            <button type="submit" name="create" class="btn btn-primary btn-police">Voeg toe</button>
                        </div>
                    </form>
                </div> 
                <?php } ?>
            </div>
        </main><!-- /.container -->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="assets/js/main.js"></script>
    </body>
</html>
