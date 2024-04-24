<section class="teesorten">
    <h1>Kontaktieren Sie uns</h1>
    <div class="kontakt_wrapper">
        <div class="flex">
            Tea & Co<br>
            Tastystraße 22/3<br>
            1010 Wien<br>
            <br>
            Telefon: <a href="tel:01 / 23 456 42">01 / 23 456 42</a><br>
            E-Mail: <a href="mailto:tea@buy.shop">tea@buy.shop</a><br>
        </div>
        <div class="flex">

            <form id="contactForm">
                <label for="Vorname">Vorname: </label> <br>
                <input id="Vorname" name="Vorname" minlength="3" type="text" required=""><br>
                <label for="Nachname">Nachname: </label> <br>
                <input id="Nachname" name="Nachname" minlength="3" type="text" required=""><br>
                <label for="Telefonnummer">Telefonnummer: </label> <br>
                <input id="Telefonnummer" name="Telefonnummer" type="text"><br>
                <label for="E-Mail">E-Mail: </label><br>
                <input id="E-Mail" name="E-Mail" type="email"><br> <!-- Changed type to email -->
                <textarea id="Nachricht" placeholder="Was Sie uns sagen möchten"></textarea>
                <button class="cta" type="submit">Formular absenden</button>
            </form>

            <script>
                document.getElementById("contactForm").onsubmit = function(event) {
                    event.preventDefault(); // 

                    // Collect form data
                    var vorname = document.getElementById("Vorname").value;
                    var nachname = document.getElementById("Nachname").value;
                    var telefonnummer = document.getElementById("Telefonnummer").value;
                    var email = document.getElementById("E-Mail").value;
                    var nachricht = document.getElementById("Nachricht").value;

                    // Format the email content
                    var emailBody = `Vorname: ${vorname}\nNachname: ${nachname}\nTelefonnummer: ${telefonnummer}\nE-Mail: ${email}\nNachricht: ${nachricht}`;

                    // Trigger mail client
                    window.location.href = `mailto:tea@buy.shop?subject=Anfrage Webseite&body=${encodeURIComponent(emailBody)}`;
                };
            </script>

        </div>
    </div>
</section>
<?php include 'uspContent.php'; ?>