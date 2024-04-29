# teamCPU
FH-Wien Team CPU Abgabe 4 Web-Programmierung (BB) SS 2024 DKM Wien

## Installation
1. Zugang zu DB konfigurieren: class/DatabaseExample.php kopieren zu class/Database.php und die Zugangsdaten anpassen
2. Datenbank anlegen über Script: 00_db/install.php

## Testbenutzer
E-Mail: teamCpu@test.at  
PW: 1teamCpu@test.at

## Hinweis zu XAMPP
1. Navigiere zu C:\xampp\htdocs
2. Starte eine Konsole (git muss installiert sein! https://github.com/git-guides/install-git)
3. git clone https://github.com/ghollenstein/teamCPU
4. Aufruf über http://localhost/teamCpu/00_db/install.php --> PW ist: FH-Wien-TeamCPU

## Testdaten für Stripe
Kartennummer: 4242424242424242

## Debuggen in PHP
1. http://localhost/teamCpu/00_db/info.php
2. Input hier eintragen: https://xdebug.org/wizard
3. Datei herunterladen nach C:\xampp\php\ext, unbenennen zu php_xdebug.dll
4. Datei C:\xampp\php\php.ini folgenden Inhalt hinzufügen

    [PHP]
    zend_extension = xdebug

    [XDebug]
    xdebug.mode = debug
    xdebug.start_with_request = yes

    
