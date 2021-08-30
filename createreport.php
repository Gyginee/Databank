<?php
    require "requires/config.php";
    if (!$_SESSION['loggedin']) {
        header("Location: login");
        exit;
    }
    $result = $con->prepare("SELECT * FROM laws ORDER BY months ASC");
    $result->execute();
    $laws_array = $result->fetchAll(PDO::FETCH_ASSOC);
    $respone = false;
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if ($_POST['type'] == "createnew") {
            $query = $con->prepare("SELECT * FROM profiles WHERE id = :id");
            $query->bindParam(":id", $_POST["profileid"]);
            $query->execute();
            $selectedprofile = $query->fetchAll(PDO::FETCH_ASSOC)[0];
        } elseif ($_POST['type'] == "create") {
            $profileid = NULL;
            $lawids = array_map('intval', explode(',', $_POST["laws"]));
            array_shift($lawids);

            if (isset($_POST["citizenid"]) && $_POST["citizenid"] != "") {
                $query = $con->prepare("SELECT * FROM profiles WHERE citizenid = :citizenId;");
                $query->bindParam(":citizenId", $_POST["citizenid"]);
                $query->execute();
                $profile = $query->fetchAll(PDO::FETCH_ASSOC)[0];
                if ($profile != NULL) {
                    $profileid = $profile["id"];
                }
            }

            if($profileid == ""){ $profileid = 0; }
            
            $reportnote = $_POST["report"];
            $json_lawIds = json_encode($lawids);
            $created = time();

            $insert = $con->prepare("INSERT INTO reports (title,author,profileid,report,laws,created) 
                VALUES(:title, :author, :profileId, :report, :laws, :created);");
            $insert->bindParam(":title", $_POST['title']);
            $insert->bindParam(":author", $_POST['author']);
            $insert->bindParam(":profileId", $profileid);
            $insert->bindParam(":report", $reportnote);
            $insert->bindParam(":laws", $json_lawIds);
            $insert->bindParam(":created", $created);

            if ($insert->execute()) {
                $_SESSION["reportid"] = $con->lastInsertId();
                $respone = true;
                header('Location: reports');
                exit;
            }
        } elseif ($_POST["type"] == "edit") {
            $query = $con->prepare("SELECT * FROM reports WHERE id = :reportId");
            $query->bindParam(":reportId", $_POST['reportid']);
            $query->execute();
            $selectedreport = $query->fetchAll(PDO::FETCH_ASSOC)[0];
            $laws = json_decode($selectedreport["laws"], true);
            $lawsedit_array = [];
            $totalprice = 0;
            $totalmonths = 0;
            if (!empty($laws)) {
                foreach($laws as $lawid) {
                    $law = $con->prepare("SELECT * FROM laws WHERE id = :id;");
                    $law->bindParam(":id", $lawid);
                    $law->execute();
                    $selectedlaw = $law->fetchAll(PDO::FETCH_ASSOC)[0];
                    $totalmonths = $totalmonths + $selectedlaw["months"];
                    $totalprice = $totalprice + $selectedlaw["fine"];
                    $lawsedit_array[] = $selectedlaw;
                }
            }
            $profile = $con->prepare("SELECT * FROM profiles WHERE id = :profileId;");
            $profile->bindParam(":profileId", $selectedreport['profileid']);
            $profile->execute();
            $profiledata = $profile->fetchAll(PDO::FETCH_ASSOC)[0];
        } elseif ($_POST["type"] == "realedit") {
            $report = $_POST["report"];
            $created = time();
            $profile = $con->prepare("SELECT * FROM profiles WHERE citizenid = :citizenId;");
            $profile->bindParam(":citizenId", $_POST['citizenid']);
            $profile->execute();
            $profiles = $profile->fetchAll(PDO::FETCH_ASSOC);
            $profileid = 0;
            if (count($profiles) > 0) {
                $profiledata = $profiles[0];
                $profileid = $profiledata['id'];
            }
            $update = $con->prepare("UPDATE reports SET title = :title, author = :author, profileid = :profileId, report = :report, created = :created WHERE id = :reportId");
            $update->bindParam(":title", $_POST['title']);
            $update->bindParam(":author", $_POST['author']);
            $update->bindParam(":profileId", $profileid);
            $update->bindParam(":report", $report);
            $update->bindParam(":created", $created);
            $update->bindParam(":reportId", $_POST['reportid']);

            if ($update->execute()) {
                $_SESSION["reportid"] = $_POST['reportid'];
                $respone = true;
                header('Location: reports');
                exit;
            } else {
                $response = false;
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
        <link href="assets/css/laws.css" rel="stylesheet">
        <script src="https://cdn.tiny.cloud/1/w4xes0tfjp36lkwexohju35ewmp1fhyehu31o5reuj8pkm80/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
        <script>     
            var today = new Date();   
            var naam = '<?=$_SESSION["name"]?>';
            var rank = '<?=$_SESSION["rank"]?>';
            tinymce.init({
                selector: 'textarea',
                content_css: '//www.tiny.cloud/css/codepen.min.css',
                width: '100%',
                selector: 'textarea',
                menubar: false,
                force_br_newlines : true,
                force_p_newlines : true,
                forced_root_block : '',
                remove_linebreaks : false,
                convert_newlines_to_br: true,
                toolbar: [
                    {
                        name: 'history', items: [ 'undo', 'redo' ]
                    },
                    {
                        name: 'styles', items: [ 'styleselect' ]
                    },
                    {
                        name: 'formatting', items: [ 'bold', 'italic']
                    },
                    {
                        name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright', 'alignjustify' ]
                    },
                    {
                        name: 'indentation', items: [ 'outdent', 'indent', 'pv' ]
                    }
                ],
                setup: function (editor) {
                    editor.ui.registry.addMenuButton('pv', {
                    text: '+',
                        fetch: function (callback) {
                            var items = [
                            {
                                type: 'menuitem',
                                text: 'Aangifte',
                                onAction: function () {
                                editor.insertContent('<img style="display: block; margin-left: auto; margin-right: auto;" src="assets/images/Politie.png" alt="" width="376" height="138" /><br>EENHEID LOS SANTOS<br>BASISTEAM MISSION ROW<br><br>Proces-verbaalnummer: (pv nummer)<br><br>P R O C E S - V E R B A A L - A A N G F I T E<br><br>Feit:<br>Plaatsdelict:<br>Pleegdatum/tijd:<br><br>Ik, verbalisant, <b>'+naam+'</b>, <b>'+rank+'</b> van Politie Eenheid Los<br>Santos, verklaar het volgende.<br>Op '+(today.getDate() < 10 ? '0'+today.getDate() : today.getDate())+'-'+((today.getMonth()+1) < 10 ? '0'+(today.getMonth()+1) : (today.getMonth()+1))+'-'+today.getFullYear()+', '+(today.getHours() < 10 ? '0'+today.getHours() : today.getHours())+':'+(today.getMinutes() < 10 ? '0'+today.getMinutes() : today.getMinutes())+' uur, verscheen voor mij, in het<br>politiebureau, Mission Row, Sinner Street, Los Santos,<br>een persoon, de aangever die mij opgaf te zijn:<br><br><b>Achternaam:</b><br><b>Voornamen:</b><br><b>Geboren:</b><br><b>Geboorteplaats:</b><br><b>Geslacht:</b><br><b>Nationaliteit:</b><br><b>Adres:</b><br><br>Hij/Zij deed aangifte en verklaarde het volgende over het<br>in de aanhef vermelde incident, dat plaatsvond op de<br>locatie genoemd bij plaats delict, op de genoemde<br>pleegdatum/tijd.<br><br>BEVINDINGEN<br><br>Aan niemand werd het recht of de toestemming geven tot<br>het plegen van dit feit.<br>De verbalisant,<br>NAAM<br><br>Ik, NAAM AANGEVER, verklaar dat ik dit proces-verbaal heb<br>gelezen. Ik verklaar dat ik de waarheid heb verteld. Ik<br>verklaar dat mijn verhaal goed is weergegeven in het<br>proces-verbaal. Ik weet dat het doen van een valse<br>aangifte strafbaar is.<br>De aangever,<br>NAAM AANGEVER<br><br>Eventuele opmerkingen verbalisant<br><br>Waarvan door mij is opgemaakt dit proces-verbaal, dat ik<br>sloot en ondertekende te Los Santos op DATUM/TIJD<br>NAAM');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Proces Verbaal',
                                onAction: function () {
                                editor.insertContent('<img style="display: block; margin-left: auto; margin-right: auto;" src="assets/images/Politie.png" alt="" width="376" height="138" /><br>EENHEID LOS SANTOS<br>BASISTEAM MISSION ROW<br>Proces-verbaalnummer: (pv nummer)<br><br>P R O C E S - V E R B A A L<br>Aanhouding<br><br>Ik, verbalisant, <b>'+naam+'</b>('+rank+'), Agent van Politie Eenheid Los Santos, verklaar het volgende.<br>Op '+today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear()+', omstreeks '+(today.getHours() < 10 ? '0'+today.getHours() : today.getHours())+':'+(today.getMinutes() < 10 ? '0'+today.getMinutes() : today.getMinutes())+' uur, bevond ik mij in uniform gekleed en met algemene politie taak belast op de openbare weg.<br><br>Identiteitsfouillering:<br>Ja/Nee<br><br>Veiligheisfouillering:<br>Ja/Nee<br><br>Inbeslagneming:<br>Ja/Nee, Zo ja wat?<br><br>Gebruik transportboeien:<br>Ja/Nee<br><br>Gebruik geweld:<br>Ja/Nee<br><br>Rechtsbijstand:<br>Ja/Nee<br><br><strong>BEVINDINGEN</strong> (schrijf hier op wat je hebt gezien / gedaan etc):<br><br><br>Ik heb de verdachte tijdens aanhouding verteld dat hij/zij zich mag beroepen op zijn zwijgrecht.<br><br>Voorgeleiding:<br><br>Op genoemd bureau werdt de verdachte ten spoedigste voorgeleid voor de hulpofficier van justitie. Deze gaf op te '+(today.getHours() < 10 ? '0'+today.getHours() : today.getHours())+':'+(today.getMinutes() < 10 ? '0'+today.getMinutes() : today.getMinutes())+' uur het bevel de verdachte op te houden voor onderzoek.<br><br>Waarvan door mij, Voor achternaam, op ambtseed is opgemaakt, dit proces-verbaal te Los Santos op '+(today.getDate() < 10 ? '0'+today.getDate() : today.getDate())+'-'+((today.getMonth()+1) < 10 ? '0'+(today.getMonth()+1) : (today.getMonth()+1))+'-'+today.getFullYear()+'<br><br>Strafeis:<br>Gekregen straf:');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Bewijsmateriaal',
                                onAction: function () {
                                editor.insertContent('<img style="display: block; margin-left: auto; margin-right: auto;" src="assets/images/Politie.png" alt="" width="376" height="138" /><br>EENHEID LOS SANTOS<br>BASISTEAM MISSION ROW<br><br>Proces-verbaalnummer: (pv nummer)<br><br>P R O C E S - V E R B A A L<br>BEWIJSMATERIAAL<br><br>Ik, verbalisant, <b>'+naam+'</b>, <b>'+rank+'</b> van Politie Eenheid Los<br>Santos, verklaar het volgende.<br><br>BEVINDINGEN<br><br>Adres Bedrijf/Winkel:<br>Datum/tijd:<br>Bewijs:');
                                }
                            },
                            {
                                type: 'menuitem',
                                text: 'Mini Proces Verbaal',
                                onAction: function () {
                                editor.insertContent('<img style="display: block; margin-left: auto; margin-right: auto;" src="assets/images/Politie.png" alt="" width="376" height="138" /><br>EENHEID LOS SANTOS<br>BASISTEAM Politie Los Santos<br><br>Proces-verbaalnummer: (pv nummer)<br><br>M I N I - P R O C E S - V E R B A A L - BESCHIKKING<br>Ik, verbalisant, <b>'+naam+'</b>, <b>'+rank+'</b> van Politie Eenheid Los<br>Santos<br>Op '+today.getDate()+'-'+(today.getMonth()+1)+'-'+today.getFullYear()+', omstreeks '+today.getHours()+':'+today.getMinutes()+' uur, bevond ik mij in uniform<br>gekleed en met algemene politietaak belast op de openbare<br>weg,<br><br>BEVINDINGEN<br><br>Locatie:<br>Gepleegde overtreding:<br>Feitcode:<br>Boetebedrag:<br>Verklaring:<br><br>Indien Snelheidsovertreding<br>Gemeten snelheid:<br>Toegestane snelheid:<br>Correctie: - 10%<br>Uiteindelijke snelheid:');
                                }
                            },
        
                            ];
                            callback(items);
                        }
                    });
                }
            });
        </script>
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
                <h3>Report Maken</h3>
                <p class="lead">Hier kun je een nieuw reportage aanmaken.<br />Je kunt een BSN koppelen aan een reportage (Hiervoor MOET er een profiel bestaan) of je kan het leeg laten en later toevoegen.<br />Je kunt ook straffen toevoegen (wanneer nodig) onderaan de pagina.</br>Om een straf weg te halen kun je klikken op dezelfde straf bij "Geselecteerde Straffen"</p>
            </div>
            <div class="createreport-container">
                <div class="createreport-left">
                <?php if (isset($_GET['delete']) && $_GET['delete'] != "") { 
                    $deleted = $con->prepare("DELETE FROM reports WHERE id = :reportId");
                    $deleted->bindParam(":reportId", $_GET["delete"]);
                    $deleted->execute();
                ?>
                <script>window.location = '/databank/dashboard';</script>
                <?php
                } else if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "edit" && !empty($selectedreport)) { ?>
                    <form method="post">
                        <input type="hidden" name="type" value="realedit">
                        <input type="hidden" name="author" class="form-control login-pass" value="<?php echo $_SESSION["name"]; ?>" placeholder="" required>
                        <input type="hidden" name="reportid" class="form-control login-pass" value="<?php echo $selectedreport["id"]; ?>" placeholder="" required>
                        <div class="input-group mb-3">
                            <input type="text" name="title" class="form-control login-user" value="<?php echo $selectedreport["title"]; ?>" placeholder="titel" required>
                        </div>
                        <?php if (!empty($profiledata)) { ?>
                            <div class="input-group mb-3">
                                <input type="text" name="citizenid" class="form-control login-user" value="<?php echo $profiledata["citizenid"]; ?>" placeholder="koppel bsn (MOET INGEVULD ZIJN)">
                            </div>
                        <?php } else {?>
                            <div class="input-group mb-3">
                                <input type="text" name="citizenid" class="form-control login-user" value="" placeholder="koppel bsn (MOET INGEVULD ZIJN)">
                            </div>
                        <?php } ?>
                        <?php $report = $selectedreport["report"]; ?>
                        <div class="input-group mb-2">
                            <textarea name="report" id="editreport" class="form-control" value="" placeholder="reportage.."><?=$report?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="create" class="btn btn-primary btn-police">Bewerk reportage</button>
                        </div>
                    </form>
                <?php } else { ?>
                    <form method="post">
                        <input type="hidden" name="type" value="create">
                        <input type="hidden" name="laws" class="report-law-punishments" value="">
                        <input type="hidden" name="author" class="form-control login-pass" value="<?php echo $_SESSION["name"]; ?>" placeholder="" required>
                        <div class="input-group mb-3">
                            <input type="text" name="title" class="form-control login-user" value="" placeholder="titel" required>
                        </div>
                        <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "createnew") { ?>
                            <div class="input-group mb-3">
                                <input type="text" name="citizenid" class="form-control login-user" value="<?php echo $selectedprofile["citizenid"]; ?>" placeholder="koppel bsn (MOET INGEVULD ZIJN)">
                            </div>
                        <?php } else {?>
                            <div class="input-group mb-3">
                                <input type="text" name="citizenid" class="form-control login-user" value="" placeholder="koppel bsn (MOET INGEVULD ZIJN)">
                            </div>
                        <?php } ?>
                        <div class="input-group mb-2">
                            <textarea name="report" class="form-control" value="" placeholder="reportage.."></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="create" class="btn btn-primary btn-police">Maak reportage</button>
                        </div>
                    </form>
                <?php } ?>
                </div>
                <?php if ($_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] == "edit" && !empty($selectedreport)) { ?>
                    <div class="createreport-right">
                        <h5>Geselecteerde Straffen</h5>
                        <p class="total-punishment">Totaal: €<?php echo $totalprice; ?> - <?php echo $totalmonths; ?> maanden</p>
                        <div class="added-laws">
                        <?php if (!empty($lawsedit_array)) { ?>
                            <?php foreach($lawsedit_array as $issalaw) { ?>
                                <div class="report-law-item" data-toggle="tooltip" data-html="true" title="<?php echo $issalaw["description"]; ?>">
                                    <h5 class="lawlist-title"><?php echo $issalaw["name"]; ?></h5>
                                    <p class="lawlist-fine">Boete: €<span class="fine-amount"><?php echo $issalaw["fine"]; ?></span></p>
                                    <p class="lawlist-months">Cel: <span class="months-amount"><?php echo $issalaw["months"]; ?></span> maanden</p>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="createreport-right">
                        <h5>Geselecteerde Straffen</h5>
                        <p class="total-punishment">Totaal: €0 - 0 maanden</p>
                        <div class="added-laws">
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if ($_SERVER['REQUEST_METHOD'] != "POST" || $_SERVER['REQUEST_METHOD'] == "POST" && $_POST['type'] != "edit") { ?>
                <button type="button" class="btn btn-primary btn-police" id="togglelaws" style="margin-bottom:2vh!important;">TOGGLE STRAFFEN</button>
                <div class="laws">
                    <div class="lawlist-search">
                        <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Zoeken</span>
                            </div>
                            <input type="text" class="lawsearch form-control" aria-label="Zoeken" aria-describedby="inputGroup-sizing-sm">
                        </div>
                    </div>
                    <?php foreach($laws_array as $law){?>
                        <div class="report-law-item-tab" data-toggle="tooltip" data-html="true" title="<?php echo $law['description']; ?>">
                            <input type="hidden" class="lawlist-id" value="<?php echo $law['id']; ?>">
                            <h5 class="lawlist-title"><?php echo $law['name']; ?></h5>
                            <p class="lawlist-fine">Boete: €<span class="fine-amount"><?php echo $law['fine']; ?></span></p>
                            <p class="lawlist-months">Cel: <span class="months-amount"><?php echo $law['months']; ?></span> maanden</p>
                        </div>
                    <?php }?>
                    </div>
                </div>
            <?php } ?>
        </main><!-- /.container -->

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="assets/js/main.js"></script>
    </body>
</html>
