<?php
    require "requires/config.php";

    if(isset($_SESSION['username']) && $_SESSION['username'] == "Test"){
        session_destroy();
    }

    if ($_SESSION['loggedin']) {
        header("Location: dashboard");
        exit;
    } else {
        header("Location: login");
        exit;
    }
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
                            <span class="title">Welkom K. Mulder</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-button" href="#">
                            <button class="btn btn-outline-light btn-logout my-2 my-sm-0" type="button">LOG UIT</button>
                        </a>
                    </li>
                </ul>

                <!-- Right menu -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">DASHBOARD <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        OPZOEKEN
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="reports">PERSONEN</a>
                            <a class="dropdown-item" href="vehicles">VOERTUIGEN</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">ARRESTATIEBEVELEN</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-report" href="#">NIEUW RAPPORT</a>
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
                    <div class="panel-list">
                        <div class="panel-item">
                            <h5 class="panel-title">#0001 Report Title</h5>
                            <p class="panel-author">door: K. Mulder</p>
                        </div>
                        <div class="panel-item">
                            <h5 class="panel-title">#0001 Report Title</h5>
                            <p class="panel-author">door: K. Mulder</p>
                        </div>
                        <div class="panel-item">
                            <h5 class="panel-title">#0001 Report Title</h5>
                            <p class="panel-author">door: K. Mulder</p>
                        </div>
                        <div class="panel-item">
                            <h5 class="panel-title">#0001 Report Title</h5>
                            <p class="panel-author">door: K. Mulder</p>
                        </div>
                        <div class="panel-item">
                            <h5 class="panel-title">#0001 Report Title</h5>
                            <p class="panel-author">door: K. Mulder</p>
                        </div>
                        <div class="panel-item">
                            <h5 class="panel-title">#0001 Report Title</h5>
                            <p class="panel-author">door: K. Mulder</p>
                        </div>
                    </div>
                </div>  
                <!-- Right Container -->
                <div class="right-panel-container">
                    <h5 class="panel-container-title">Opgezochte Personen</h5>
                    <div class="panel-list">
                        <div class="panel-item">
                            <h5 class="panel-title">Ricky de Wilde</h5>
                            <p class="panel-author">BSN: ELK384E39</p>
                        </div>
                        <div class="panel-item">
                            <h5 class="panel-title">Ricky de Wilde</h5>
                            <p class="panel-author">BSN: ELK384E39</p>
                        </div><div class="panel-item">
                            <h5 class="panel-title">Ricky de Wilde</h5>
                            <p class="panel-author">BSN: ELK384E39</p>
                        </div><div class="panel-item">
                            <h5 class="panel-title">Ricky de Wilde</h5>
                            <p class="panel-author">BSN: ELK384E39</p>
                        </div><div class="panel-item">
                            <h5 class="panel-title">Ricky de Wilde</h5>
                            <p class="panel-author">BSN: ELK384E39</p>
                        </div><div class="panel-item">
                            <h5 class="panel-title">Ricky de Wilde</h5>
                            <p class="panel-author">BSN: ELK384E39</p>
                        </div>
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
