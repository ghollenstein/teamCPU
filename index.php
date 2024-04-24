<?php
include 'class/Controller.php';
$controller = new Controller();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tee-Shop</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico?v=1712474485779">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="font.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <script src="assets/res/jquery.min.js"></script>
    <script src="assets/res/lordicon.js"></script>
    <script defer src="script.js"></script>
    <link rel="stylesheet" href="assets/res/font-awesome-4.7.0/css/font-awesome.min.css" />
</head>

<body>
    <header>
        <div class="top_bar">
            <b>FH-WIEN24 - AKTION</b>
        </div>
        <div class="nav_wrapper container">
            <div class="flex">
                <button class="nav-opener" aria-label="menu mobile opener">
                    <span></span>
                </button>
            </div>
            <div class="flex">
                <a href="./" class="logo"><i class="fa fa-coffee fa-2x" aria-hidden="true"></i> TEE & CO
                </a>
            </div>
            <div class="flex">
                <div class="warenkorb">
                    <a href="#">
                        <span class="warenkorb-icon">
                            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                        </span>
                    </a>
                </div>
                <aside class="warenkorb_wrapper" style="display: none">
                    <b>Dein Warenkorb</b>
                    <div id="warenkorb"></div>
                    <button class="buttonEckig" id="weiterShoppen">
                        weiter einkaufen
                    </button>
                </aside>
            </div>
            <div class="nav_inner" style="display: none">
                <nav>
                    <ul class="nav_links">
                        <li><a href="./">Home</a></li>
                        <li><a href="./#teesorten" class="anker">Teesorten</a></li>
                        <li><a href="index.php?page=kontakt">Kontakt</a></li>
                        <li><a href="index.php?page=impressum">Impressum</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <section class="video_wrapper">
        <div class="video_content">
            <h1>Best Vienna Tea</h1>
            <sub>handmade in Vienna</sub>
        </div>
        <video width="100%" height="auto" autoplay muted loop>
            <source src="assets/tee-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </section>    
    <main class="content container">
        <?php $controller->route(); ?>
    </main>
    <footer>
        <div class="container">
            <section class="newsletter">
                <form>
                    <input type="email" id="email" name="email" placeholder="E-Mail Adresse eingeben" required>
                    <button type="submit">Newsletter abonnieren</button>
                    <fieldset>
                        <input name="dsgvo" id="dsgvo" type="checkbox" value="1" required>
                        <label for="dsgvo">Ich bin mit der Verarbeitung meiner angegebenen Daten einverstanden. Weitere
                            Informationen zum <a href="#" target="_blank">Datenschutz</a>.</label>
                    </fieldset>
                </form>
            </section>
            <hr>
            <div class="footer_adresse">
                <strong>Tea & Co</strong> &nbsp; | &nbsp;Tastystraße 22/3 &nbsp;| &nbsp; 1010 Wien | &nbsp;
                Telefon: 01 / 23 456 42 &nbsp; | &nbsp;E-Mail: <a href="mailto:tea@buy.shop">tea@buy.shop</a>
            </div>
            <div class="copyright">
                <p>© 2024 | FH-Wien</p>
            </div>
        </div>
    </footer>
    <script src="app.js"></script>
</body>

</html>