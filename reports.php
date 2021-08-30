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
            $result = $con->prepare("SELECT * FROM reports WHERE concat(' ', title, ' ') LIKE :searchTitle OR concat(' ', report, ' ') LIKE :searchReport ORDER BY created DESC");
            $result->bindParam(":searchTitle", $search);
            $result->bindParam(":searchReport", $search);
            $result->execute();
            $search_array = $result->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($_POST['type'] == "show" || isset($_SESSION["reportid"]) && $_SESSION["reportid"] != NULL) {
            if (isset($_SESSION["reportid"]) && $_SESSION["reportid"] != NULL) {
                $reportId = $_SESSION["reportid"];
            } else {
                $reportId = $_POST['reportid'];
            }
            $query = $con->prepare("SELECT * FROM reports WHERE id = :reportId;");
            $query->bindParam(":reportId", $reportId);
            $query->execute();
            $selectedreport = $query->fetchAll(PDO::FETCH_ASSOC)[0];
            $lawids = json_decode($selectedreport["laws"], true);

            if($selectedreport['profileid'] == null){?>
                <script>window.location = '/databank/dashboard';</script><?php
            }

            $profile = $con->prepare("SELECT * FROM profiles WHERE id = :profileId;");
            $profile->bindParam(":profileId", $selectedreport["profileid"]);
            $profile->execute();
            $profiledata = $profile->fetchAll(PDO::FETCH_ASSOC)[0];
            $lawdata = [];
            if (!empty($lawids)) {
                foreach($lawids as $lawid) {
                    $result = $con->prepare("SELECT * FROM laws WHERE id = :lawId;");
                    $result->bindParam(":lawId", $lawid);
                    $result->execute();
                    $lawdata[] = $result->fetchAll(PDO::FETCH_ASSOC)[0];
                }
            }
            $_SESSION["reportid"] = NULL;
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
                <h3>Reports</h3>
                <p class="lead">Hier kun je reportages opzoeken, lezen en bijwerken. <br/> Gelieve niet een report bij te werken van iemand anders mits je toestemming heb gekregen!</p>
            </div>
            <div class="profile-container">
                <div class="profile-search">
                    <?php if ($_SERVER['REQUEST_METHOD'] != "POST" || $_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] != "show") { ?>
                        <a href="createreport" class="btn btn-pol btn-md my-0 ml-sm-2">MAAK NIEUW REPORTAGE</a>
                    <?php } else { ?>
                        <form method="post" action="createreport" id="createreportform">
                            <input type="hidden" name="type" value="edit">
                            <input type="hidden" name="reportid" value="<?php echo $selectedreport['id']; ?>">
                            <button type="submit" name="issabutn" class="btn btn-pol btn-md my-0 ml-sm-2">PAS REPORT AAN</button>



                            <?php    
                            if($_SESSION['rank'] == "Korpsleiding" || $_SESSION['rank'] == "Inspecteur" || $_SESSION['rank'] == "Hoofdinspecteur"  || $_SESSION['username'] == "HermanB" || $_SESSION['username'] == "FrankB"){ ?>
                                <a href="createreport?delete=<?php echo $selectedreport['id']; ?>" style="float:right;background:#980c0c!important" class="btn btn-pol btn-md my-0 ml-sm-2">VERWIJDER RAPPORT</a>
                            <?php } ?>
                        </form>
                        
                    <?php } ?>
                    <br /><br />
                    <form method="post" class="form-inline ml-auto">
                        <input type="hidden" name="type" value="search">
                        <div class="md-form my-0">
                            <input class="form-control" name="search" type="text" placeholder="Zoek een report.." aria-label="Search">
                        </div>
                        <button type="submit" name="issabutn" class="btn btn-pol btn-md my-0 ml-sm-2">ZOEK</button>
                    </form>
                </div>
                <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "search") { ?>
                    <div class="search-panel">
                        <h5 class="panel-container-title">Gevonden reports..</h5>
                        <div class="panel-list">
                            <?php if (empty($search_array)) { ?>
                                <p>Geen reportages gevonden..</p>
                            <?php } else { ?>
                                <?php foreach($search_array as $report) {?>
                                    <form method="post">
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
                <?php } ?>
                <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "show" && !empty($selectedreport)) { ?>
                    <div class="report-show">
                        <h4 class="report-title"><?php echo $selectedreport["title"]; ?></h4>
                        <?php if ($profiledata != NULL) {?>
                            <p>Betfreft: <?php echo $profiledata["fullname"]; ?> (<?php echo $profiledata["citizenid"]; ?>)
                        <?php } ?>
                        <hr>
                        <strong>Reportage:</strong>
                        <p class="report-description"><?php echo $selectedreport["report"]; ?></p>
                        <p class="report-author"><i>Geschreven door: <?php echo $selectedreport["author"]; ?></i></p>
                    </div>
                    <div class="laws-added">
                        <?php if ($lawdata != NULL) {?>
                            <h5 class="report-laws-title">Voorgelegde Straffen:</h5>
                            <?php foreach($lawdata as $law){?>
                                <div class="law-item" data-toggle="tooltip" data-html="true" title="<?php echo $law['description']; ?>">
                                    <h5 class="lawlist-title"><?php echo $law['name']; ?></h5>
                                    <p class="lawlist-fine">Boete: €<?php echo $law['fine']; ?></p>
                                    <p class="lawlist-months">Cel: <?php echo $law['months']; ?> maanden</p>
                                </div>
                            <?php }?>
                        <?php } ?>
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
