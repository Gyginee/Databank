<?php
    require "requires/config.php";
    if (!$_SESSION['loggedin']) {
        header("Location: login");
        exit;
    }
    $respone = false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if ($_POST['type'] == "create") {
            $note = nl2br($_POST["note"]);
            $time = time();
            $insert = $con->prepare("INSERT INTO profiles (citizenid,fullname,avatar,fingerprint,dnacode,note,lastsearch) 
                                                VALUES(:citizenId, :fullname, :avatar, :fingerprint, :dnacode, :note, :time);");
            $insert->bindParam(":citizenId",$_POST['citizenid']);
            $insert->bindParam(":fullname",$_POST['fullname']);
            $insert->bindParam(":avatar",$_POST['avatar']);
            $insert->bindParam(":fingerprint",$_POST['fingerprint']);
            $insert->bindParam(":dnacode",$_POST['dnacode']);
            $insert->bindParam(":note",$note);
            $insert->bindParam(":time",$time);

            if ($insert->execute()) {
                $_SESSION["personid"] = $con->lastInsertId();
                $respone = true;
                header('Location: profiles');
                exit;
            } else {
                print_r($insert->errorInfo());
                print_r($insert->debugDumpParams());
            }
        } elseif ($_POST['type'] == "edit") {
            $query = $con->prepare("SELECT * FROM profiles WHERE id = :profileId;");
            $query->bindParam(":profileId", $_POST['profileid']);
            $query->execute();
            $selectedprofile = $query->fetchAll(PDO::FETCH_ASSOC)[0];
        } elseif ($_POST['type'] == "realedit") {
            $note = nl2br($_POST["note"]);
            $update = $con->prepare("UPDATE profiles SET citizenid = :citizenId, fullname = :fullname, avatar = :avatar, fingerprint = :fingerprint, dnacode = :dnacode, note = :note WHERE id = :profileId;");
            $update->bindParam(":citizenId", $_POST['citizenid']);
            $update->bindParam(":fullname", $_POST['fullname']);
            $update->bindParam(":avatar", $_POST['avatar']);
            $update->bindParam(":fingerprint", $_POST['fingerprint']);
            $update->bindParam(":dnacode", $_POST['dnacode']);
            $update->bindParam(":note", $note);
            $update->bindParam(":profileId", $_POST['profileid']);

            if ($update->execute()) {
                $_SESSION["personid"] = $_POST['profileid'];
                $respone = true;
                header('Location: profiles');
                exit;
            } else {
                $response = false;
                print_r($update->errorInfo());
                print_r($update->debugDumpParams());
            }
        }
    }
    $name = explode(" ", $_SESSION["name"]);
    $firstname = $name[0];
    $last_word_start = strrpos($_SESSION["name"], ' ') + 1;
    $lastname = substr($_SESSION["name"], $last_word_start);
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
        <link href="assets/css/profiles.css" rel="stylesheet">
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
                <h3>Profiel Maken</h3>
                <p class="lead">Hier maak je een profiel aan als een nieuwe crimineel in wordt gebracht.<br />Zorg ervoor dat alle gegevens kloppen en dat er een juiste foto is geplaatst!</p>
            </div>
            <div class="createprofile-container">
            <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "edit" && !empty($selectedprofile)) { ?>
                <form method="post">
                    <input type="hidden" name="type" value="realedit">
                    <input type="hidden" name="profileid" value="<?php echo $selectedprofile["id"]; ?>">
                    <div class="input-group mb-3">
                        <input type="text" name="citizenid" class="form-control login-user" value="<?php echo $selectedprofile["citizenid"]; ?>" placeholder="bsn" required>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" name="fullname" class="form-control login-pass" value="<?php echo $selectedprofile["fullname"]; ?>" placeholder="volledige naam" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="avatar" class="form-control login-user" value="<?php echo $selectedprofile["avatar"]; ?>" placeholder="profiel foto (imgur URL vb. https://i.imgur.com/zKDjdhe.png)" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="fingerprint" class="form-control login-user" value="<?php echo $selectedprofile["fingerprint"]; ?>" placeholder="vinger patroon">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="dnacode" class="form-control login-user" value="<?php echo $selectedprofile["dnacode"]; ?>" placeholder="dna code">
                    </div>
                    <?php $notes = str_replace( "<br />", '', $selectedprofile["note"]); ?>
                    <div class="input-group mb-2">
                        <textarea name="note" class="form-control" value="<?php echo $notes; ?>" placeholder="notitie" required><?php echo $notes; ?></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="create" class="btn btn-primary btn-police">Pas Aan</button>
                    </div>
                </form>
            <?php } else { ?>
                <form method="post">
                    <input type="hidden" name="type" value="create">
                    <div class="input-group mb-3">
                        <input type="text" name="citizenid" class="form-control login-user" value="" placeholder="bsn" required>
                    </div>
                    <div class="input-group mb-2">
                        <input type="text" name="fullname" class="form-control login-pass" value="" placeholder="volledige naam" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="avatar" class="form-control login-user" value="" placeholder="profiel foto (imgur URL vb. https://i.imgur.com/zKDjdhe.png)">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="fingerprint" class="form-control login-user" value="" placeholder="vinger patroon">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="dnacode" class="form-control login-user" value="" placeholder="dna code">
                    </div>
                    <div class="input-group mb-2">
                        <textarea name="note" class="form-control" value="" placeholder="notitie"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="create" class="btn btn-primary btn-police">Voeg toe</button>
                    </div>
                </form>
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
