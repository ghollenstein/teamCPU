<?php
function is_user_logged_in() {
    session_start();

    // Dummy-Benutzerdaten
    $benutzername = 'benutzer';
    $passwort = 'passwort';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Überprüfe, ob Benutzername und Passwort gesendet wurden
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Überprüfe, ob Benutzername und Passwort korrekt sind
            if ($_POST['username'] === $benutzername && $_POST['password'] === $passwort) {
                // Benutzer erfolgreich eingeloggt
                $_SESSION['loggedin'] = true;
                return true;
            } else {
                $fehlermeldung = 'Benutzername oder Passwort ist falsch.';
            }
        } else {
            $fehlermeldung = 'Bitte füllen Sie alle Felder aus.';
        }
    }
    echo $fehlermeldung;
}


?>