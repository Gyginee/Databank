<?php
    require "requires/config.php";
    if (!$_SESSION['loggedin']) {
        header("Location: login");
        exit;
    }
    $response = false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if ($_POST['type'] == "show") {
            $query = $con->prepare("SELECT * FROM warrants WHERE id = :warrentId;");
            $query->bindParam(":warrentId", $_POST['warrantid']);
            $query->execute();
            $selectedwarrant = $query->fetchAll(PDO::FETCH_ASSOC)[0];
            $profile = $con->prepare("SELECT * FROM profiles WHERE citizenid = :citizenId;");
            $profile->bindParam(":citizenId", $selectedwarrant["citizenid"]);
            $profile->execute();
            $profiledata = $profile->fetchAll(PDO::FETCH_ASSOC)[0];
        } elseif ($_POST['type'] == "delete") {
            $deleteWarrent = $con->prepare("DELETE FROM warrants WHERE id = :warrentId;");
            $deleteWarrent->bindParam(":warrentId", $_POST['warrantid']);
            if ($deleteWarrent->execute()) {
                $response = true;
            } else {
                echo "Error deleting record: " . $deleteWarrent->errorInfo();
                exit();
            }
        }
    }
    $result = $con->prepare("SELECT * FROM warrants ORDER BY created DESC");
    $result->execute();
    $warrant_array = [];
    foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $key => $warrent) {
        $profile = $con->prepare("SELECT * FROM profiles WHERE citizenid = :citizenId");
        $profile->bindParam(":citizenId", $warrent["citizenid"]);
        $profile->execute();
        $profiledata = $profile->fetchAll(PDO::FETCH_ASSOC)[0];
        $warrent["fullname"] = $profiledata["fullname"];
        $warrant_array[] = $warrent;
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
                <h3>Arrestatiebevelen</h3>
                <p class="lead">Hier vind je alle arrestatiebevelen die zijn ingedeeld.<br/>Je kunt ook nieuwe arrestatiebevelen maken, deze mag je alleen aanmaken als je toestemming heb gekregen van de korpsleiding en/of HOVJ</p>
            </div>
            <div class="warrants-container">
                <div class="warrants-list">
                    <h5 class="panel-container-title">Gezochte personen</h5>
                    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST["type"] == "delete" && $response) { ?>
                        <p style='color: #13ba2c;'>Bevel verwijderd!</p>
                    <?php } ?>
                    <?php if (empty($warrant_array)) { ?>
                        <p>Geen arrestatiebevelen..</p>
                    <?php } else { ?>
                        <?php foreach($warrant_array as $warrant) {?>
                            <form method="post">
                                <input type="hidden" name="type" value="show">
                                <input type="hidden" name="warrantid" value="<?php echo $warrant["id"]; ?>">
                                <button type="submit" class="btn warrant-item">
                                    <h5 class="warrant-title"><?php echo $warrant["title"]; ?> - <?php echo $warrant["fullname"]; ?></h5>
                                    <p class="warrant-author">door: <?php echo $warrant["author"]; ?></p>
                                    <?php 
                                        $datetime = new DateTime($warrant["created"]);
                                        echo '<p class="warrant-author">Aangemaakt: '.$datetime->format('d/m/y H:i').'</p>';
                                    ?>
                                </button>
                            </form>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="warrant-report">
                    <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST["type"] == "show") { ?>
                        <div class="report-show">
                            <h4 class="report-title"><?php echo $selectedwarrant["title"]; ?></h4>
                            <p>Betfreft: <?php echo $profiledata["fullname"]; ?> (<?php echo $profiledata["citizenid"]; ?>)</p>
                            <hr>
                            <strong>Omschrijving:</strong>
                            <p class="report-description"><?php echo $selectedwarrant["description"]; ?></p>
                            <p class="report-author"><i>Geschreven door: <?php echo $selectedwarrant["author"]; ?></i></p>
                        </div>
                        <form method="post">
                            <input type="hidden" name="type" value="delete">
                            <input type="hidden" name="warrantid" value="<?php echo $warrant["id"]; ?>">
                            <div class="form-group">
                                <button type="submit" style="margin-top: 1vh; float: right;" name="create" class="btn btn-danger">VERWIJDER</button>
                            </div>
                        </form> 
                    <?php } ?>
                </div>
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
