<?php
    require "requires/config.php";
    if (!$_SESSION['loggedin']) {
        header("Location: login");
        exit;
    }
    $respone = false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if ($_POST['type'] == "search") {
            $search = "%" . $_POST['search'] . "%";
            $result = $con->prepare("SELECT * FROM profiles WHERE concat(' ', fullname, ' ') LIKE :search OR citizenid = :citizenId OR dnacode = :dnaCode OR fingerprint = :fingerprint;");
            $result->bindParam(":search", $search);
            $result->bindParam(":citizenId", $_POST['search']);
            $result->bindParam(":dnaCode", $_POST['search']);
            $result->bindParam(":dnaCode", $_POST['search']);
            $result->bindParam(":fingerprint", $_POST['search']);
            $result->execute();
            $search_array = $result->fetchAll(PDO::FETCH_ASSOC);
        }elseif ($_POST['type'] == "show" || isset($_SESSION["personid"]) && $_SESSION["personid"] != NULL) {
            if (isset($_SESSION["personid"]) && $_SESSION["personid"] != NULL) {
                $personId = $_SESSION["personid"];
            } else {
                $personId = $_POST['personid'];
            }
            $team = time();
            $query = $con->prepare("SELECT * FROM profiles WHERE id = :personId;");
            $query->bindParam(":personId", $personId);
            $query->execute();
            $selectedprofile = $query->fetchAll(PDO::FETCH_ASSOC)[0];
            $result = $con->prepare("SELECT * FROM reports WHERE profileid = :personId ORDER BY created DESC");
            $result->bindParam(":personId",$personId);
            $result->execute();
            $update = $con->prepare("UPDATE profiles SET lastsearch = :lastsearch WHERE id = :personId;");
            $update->bindParam(":personId", $personId);
            $update->bindParam(":lastsearch", $time);
            $result->execute();
            $reports_array = $result->fetchAll(PDO::FETCH_ASSOC);
            $_SESSION["personid"] = NULL;
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
                <h3>Personen</h3>
                <p class="lead">Hier kun je personen opzoeken om informatie in te zien, een rapportage aan te maken of profielen aanmaken. <br />Het zou kunnen zijn dat een persoon niet bestaat, maak hiervoor een profiel aan en voer de juiste gegevens in.</p>
            </div>
            <div class="profile-container">
                <div class="profile-search">
                    <?php if ($_SERVER['REQUEST_METHOD'] != "POST" || $_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] != "show") { ?>
                        <a href="createprofile" class="btn btn-pol btn-md my-0 ml-sm-2">MAAK NIEUW PROFIEL</a>
                    <?php } else { ?>
                        <form method="post" action="createprofile">
                            <input type="hidden" name="type" value="edit">
                            <input type="hidden" name="profileid" value="<?php echo $selectedprofile['id']; ?>">
                            <button type="submit" name="issabutn" class="btn btn-pol btn-md my-0 ml-sm-2">PAS PROFIEL AAN</button>
                        </form>
                    <?php } ?>
                    <br /><br />
                    <form method="post" class="form-inline ml-auto">
                        <input type="hidden" name="type" value="search">
                        <div class="md-form my-0">
                            <input class="form-control" name="search" type="text" placeholder="Zoek een persoon.." aria-label="Search">
                        </div>
                        <button type="submit" name="issabutn" class="btn btn-pol btn-md my-0 ml-sm-2">ZOEK</button>
                    </form>
                </div>
                <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "search") { ?>
                    <div class="search-panel">
                        <h5 class="panel-container-title">Gevonden personen..</h5>
                        <div class="panel-list">
                            <?php if (empty($search_array)) { ?>
                                <p>Geen persoon persoon gevonden.. Maak een profiel aan.</p>
                            <?php } else { ?>
                                <?php foreach($search_array as $person) {?>
                                    <form method="post">
                                        <input type="hidden" name="type" value="show">
                                        <input type="hidden" name="personid" value="<?php echo $person['id']; ?>">
                                        <button type="submit" class="btn btn-panel panel-item">
                                            <h5 class="panel-title"><?php echo $person['fullname']; ?></h5>
                                            <p class="panel-author">BSN: <?php echo $person['citizenid']; ?></p>
                                        </button>
                                    </form>
                                <?php }?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "show" && !empty($selectedprofile)) { ?>
                    <div class="profile-panel">
                        <div class="profile-avatar">
                            <img src="<?php echo $selectedprofile["avatar"]; ?>" alt="profile-pic" width="150" height="150" />
                        </div>
                        <div class="profile-information">
                            <p><strong>Naam:</strong><br /><?php echo $selectedprofile["fullname"]; ?></p>
                            <p><strong>BSN:</strong><br /><?php echo $selectedprofile["citizenid"]; ?></p>
                            <p><strong>Vinger Patroon:</strong><br /><?php echo $selectedprofile["fingerprint"]; ?></p>
                            <p><strong>DNA Code:</strong><br /><?php echo $selectedprofile["dnacode"]; ?></p>
                            <p><strong>Notitie:</strong><br /><?php echo $selectedprofile["note"]; ?></p>
                        </div>
                    </div>
                    <div class="profile-reports-panel">
                        <div class="profile-lastincidents">
                            <form method="post" action="createreport" style="float:right; margin-left: 1vw;">
                                <input type="hidden" name="type" value="createnew">
                                <input type="hidden" name="profileid" value="<?php echo $selectedprofile['id']; ?>">
                                <button type="submit" name="issabutn" style="margin-left:0!important;" class="btn btn-success btn-md my-0 ml-sm-2">NIEUW RAPPORT</button>
                            </form>
                            <form method="post" action="createwarrant" style="float:right;">
                                <input type="hidden" name="type" value="create">
                                <input type="hidden" name="profileid" value="<?php echo $selectedprofile['id']; ?>">
                                <button type="submit" name="issabutn" style="margin-left:0!important;" class="btn btn-danger btn-md my-0 ml-sm-2">NIEUW BEVEL</button>
                            </form>
                            <br />
                            <h5 class="panel-container-title">Laatste rapportages</h5>
                            <div class="panel-list">
                                <?php if (empty($reports_array)) { ?>
                                    <p>Geen reportages gevonden bij deze persoon..</p>
                                <?php } else { ?>
                                    <?php foreach($reports_array as $report) {?>
                                        <form method="post" action="reports">
                                            <input type="hidden" name="type" value="show">
                                            <input type="hidden" name="reportid" value="<?php echo $report['id']; ?>">
                                            <button type="submit" class="btn btn-panel panel-item">
                                                <h5 class="panel-title">#<?php echo $report['id']; ?> <?php echo $report['title']; ?></h5>
                                                <p class="panel-author">door: <?php echo $report['author']; ?></p>
                                            </button>
                                        </form>
                                    <?php }?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <!---->
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
