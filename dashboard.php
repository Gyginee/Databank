<?php
    require "requires/config.php";
    if (!$_SESSION['loggedin']) {
        header("Location: login");
        exit;
    }
    $profiles = $con->prepare("SELECT * FROM profiles ORDER BY lastsearch DESC LIMIT 6");
    $profiles->execute();
    $recentsearch_array = $profiles->fetchAll(PDO::FETCH_ASSOC);
    $reports = $con->prepare("SELECT * FROM reports ORDER BY created DESC LIMIT 6");
    $reports->execute();
    $recentreports_array = $reports->fetchAll(PDO::FETCH_ASSOC);
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
                <h3>Welkom bij de Politie Databank</h3>
                <p class="lead">Zoek personen, voertuigen en andere informatie op die je kunt gebruiken tijdens je dienst. <br />Ook kun je hier alle rapportages lezen, aanmaken, bijwerken en verwijderen. <br /><strong>Zorg ervoor dat alle documentatie goed wordt opgenomen en alle bewijzen erin worden meegenomen.</strong></p>
            </div>
            <div class="dashboard-container">
                <!-- Left Container -->
                <div class="left-panel-container">
                    <h5 class="panel-container-title">Laatste rapportages</h5>
                    <?php if(!empty($recentreports_array)) { ?>
                        <?php foreach($recentreports_array as $report) {?>
                            <form method="post" action="reports">
                                <input type="hidden" name="type" value="show">
                                <input type="hidden" name="reportid" value="<?php echo $report['id']; ?>">
                                <button type="submit" class="btn btn-panel panel-item" style="text-align:left!important;">
                                    <h5 class="panel-title">#<?php echo $report['id']; ?> <?php echo $report['title']; ?></h5>
                                    <p class="panel-author">door: <?php echo $report['author']; ?></p>
                                </button>
                            </form>
                        <?php }?>
                    <?php } else { ?>
                            <p>Geen personen opgezocht..</p>
                    <?php } ?>
                </div>  
                <!-- Right Container -->
                <div class="right-panel-container">
                    <h5 class="panel-container-title">Laatst opgezocht</h5>
                    <div class="panel-list">
                    <?php if(!empty($recentsearch_array)) { ?>
                        <?php foreach($recentsearch_array as $person) {?>
                            <form method="post" action="profiles">
                                <input type="hidden" name="type" value="show">
                                <input type="hidden" name="personid" value="<?php echo $person['id']; ?>">
                                <button type="submit" class="btn btn-panel panel-item" style="text-align:left!important;">
                                    <h5 class="panel-title"><?php echo $person['fullname']; ?></h5>
                                    <p class="panel-author">BSN: <?php echo $person['citizenid']; ?></p>
                                </button>
                            </form>
                        <?php }?>
                    <?php } else { ?>
                            <p>Geen personen opgezocht..</p>
                    <?php } ?>
                    </div>
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
