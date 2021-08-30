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
    if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "create") {
        $insert = $con->prepare("INSERT INTO laws (name,description,fine,months) 
                                            VALUES(:name, :description, :fine, :months);");
        $insert->bindParam(":name", $_POST['name']);
        $insert->bindParam(":description", $_POST['description']);
        $insert->bindParam(":fine", $_POST['fine']);
        $insert->bindParam(":months", $_POST['months']);
        if ($insert->execute()) {
            $respone = true;
            $result = $con->prepare("SELECT * FROM laws ORDER BY months ASC");
            $result->execute();
            $laws_array = $result->fetchAll(PDO::FETCH_ASSOC);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "edit") {
        $update = $con->prepare("UPDATE laws SET name = :name, description = :description, fine = :fine, months = :months WHERE id = :lawId");
        $update->bindParam(":name", $_POST['name']);
        $update->bindParam(":description", $_POST['description']);
        $update->bindParam(":fine", $_POST['fine']);
        $update->bindParam(":months", $_POST['months']);
        $update->bindParam(":lawId", $_POST['lawid']);
        if ($update->execute()) {
            $respone = true;
        } else {
            $response = false;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "delete") {
        $sql = $con->prepare("DELETE FROM laws WHERE id = :lawId");
        $sql->bindParam(":lawId", $_POST['lawid']);
        if ($sql->execute()) {
            $respone = true;
        } else {
            echo "Error deleting record: " . $sql->errorInfo();
            exit();
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "editlaw") {
        $query = $con->prepare("SELECT * FROM laws WHERE id = :lawId");
        $query->bindParam(":lawId", $_POST['lawid']);
        $query->execute();
        $selectedlaw = $query->fetchAll(PDO::FETCH_ASSOC)[0];
    } elseif  ($_SERVER['REQUEST_METHOD'] != "POST" || $_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] != "addlaw" && $_POST['type'] != "editlaw"){
        $result = $con->prepare("SELECT * FROM laws ORDER BY months ASC");
        $result->execute();
        $laws_array = $result->fetchAll(PDO::FETCH_ASSOC);
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
        <link href="assets/css/laws.css" rel="stylesheet">
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
                <h3>Straffen</h3>
                <p class="lead">Hier kun je als admin straffen toevoegen, verwijderen en aanpassen. Zorg ervoor dat er een juiste tijd (in minuten) en boeten (prijs zonder komma) worden geplaatst. <br />Zorg er ook voor dat er een duidelijke beschrijving bij staat zodat andere collega's die kunnen nalezen.</p>
            </div>
            <div class="law-container">
                <?php 
                    if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "editlaw") {
                ?>
                    <div class="law-add">
                        <h5 class="panel-container-title">Wijzig straf</h5>
                        <form method="post">
                            <input type="hidden" name="type" value="edit">
                            <input type="hidden" name="lawid" value="<?php echo $selectedlaw['id']; ?>">
                            <div class="input-group mb-3">
                                <input type="text" name="name" class="form-control login-user" value="<?php echo $selectedlaw['name']; ?>" placeholder="naam" required>
                            </div>
                            <?php $description = str_replace( "<br />", '', $selectedlaw['description']); ?>
                            <div class="input-group mb-2">
                                <textarea name="description" class="form-control" placeholder="omschrijving" required><?php echo $description ?></textarea>
                            </div>
                            <div class="input-group mb-3">
                                <input type="number" min="0" name="fine" class="form-control login-user" value="<?php echo $selectedlaw['fine']; ?>" placeholder="boete" required>
                            </div>
                            <div class="input-group mb-3">
                                <input type="number" min="0" name="months" class="form-control login-user" value="<?php echo $selectedlaw['months']; ?>" placeholder="celstraf" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="create" class="btn btn-primary btn-police">WIJZIG</button>
                            </div>
                        </form>
                        <form method="post">
                            <input type="hidden" name="type" value="delete">
                            <input type="hidden" name="lawid" value="<?php echo $selectedlaw['id']; ?>">
                            <div class="form-group">
                                <button type="submit" name="create" style="width:100%!important" class="btn btn-danger">STRAF VERWIJDEREN</button>
                            </div>
                        </form>
                    </div>
                <?php
                    } elseif ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "addlaw") {
                ?>
                    <div class="law-add">
                        <h5 class="panel-container-title">Voeg straf toe</h5>
                        <form method="post">
                            <input type="hidden" name="type" value="create">
                            <div class="input-group mb-3">
                                <input type="text" name="name" class="form-control login-user" value="" placeholder="naam" required>
                            </div>
                            <div class="input-group mb-2">
                                <textarea name="description" class="form-control" value="" placeholder="omschrijving" required></textarea>
                            </div>
                            <div class="input-group mb-3">
                                <input type="number" min="0" name="fine" class="form-control login-user" value="" placeholder="boete" required>
                            </div>
                            <div class="input-group mb-3">
                                <input type="number" min="0" name="months" class="form-control login-user" value="" placeholder="celstraf" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="create" class="btn btn-primary btn-police">VOEG TOE</button>
                            </div>
                        </form>
                    </div>
                <?php
                    } else {
                ?>
                    <div class="law-list">
                        <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "create" && $respone) {?>
                            <?php echo "<p style='color: #13ba2c;'>Straf toegevoegd!</p>"; ?>
                        <?php } ?>
                        <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "edit" && $respone) {?>
                            <?php echo "<p style='color: #13ba2c;'>Straf gewijzigd!</p>"; ?>
                        <?php } ?>
                        <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "delete" && $respone) {?>
                            <?php echo "<p style='color: #13ba2c;'>Straf verwijderd!</p>"; ?>
                        <?php } ?>
                        <div class="lawlist-search">
                            <form method="post">
                                <input type="hidden" name="type" value="addlaw">
                                <div class="form-group">
                                    <button type="submit" name="addlaw" class="btn btn-primary">STRAF TOEVOEGEN</button>
                                </div>
                            </form>
                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-sm">Zoeken</span>
                                </div>
                                <input type="text" class="lawsearch form-control" aria-label="Zoeken" aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <?php foreach($laws_array as $law){?>
                            <form method="post">
                                <input type="hidden" name="type" value="editlaw">
                                <input type="hidden" name="lawid" value="<?php echo $law['id']; ?>">
                                <button type="submit" class="law-item" data-toggle="tooltip" data-html="true" title="<?php echo $law['description']; ?>">
                                    <h5 class="lawlist-title"><?php echo $law['name']; ?></h5>
                                    <p class="lawlist-fine">Boete: €<?php echo $law['fine']; ?></p>
                                    <p class="lawlist-months">Cel: <?php echo $law['months']; ?> maanden</p>
                                </button>
                            </form>
                        <?php }?>
                    </div>
                <?php
                    }
                ?>
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
