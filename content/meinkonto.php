<?php
$controller = new Controller();
$orders = $controller->getOrderHistory();
$addresses = $controller->getAddresses();
?>
<section class="teesorten">
    <h1>Mein Konto</h1>
    <?php
    if ($controller->login->isUserLoggedIn() == true) :
        $userData = $controller->account->getUserData()[0];
    ?>
        <div class="mein_konto_wrapper">
            <nav>
                <ul>
                    <li><a class="btn_active" href="#orders">Bestellungen</a></li>
                    <li><a href="#adresses">Adressen</a></li>
                    <li><a href="#profil_edit">Kontodetails</a></li>
                    <li><a href="index.php?action=logout">Logout</a></li>
                </ul>
            </nav>

            <div class="meinkonto_content_wrapper">
                <ol>
                    <li id="orders_content" class="meinkonto_list_element">
                        <?php
                        $currentOrderId = null;
                        $totalBrutto = 0;

                        echo '<table border="1" style="width:100%; border-collapse: collapse;">';
                        echo '<thead>';
                        echo '<tr><th>Bestellnummer</th><th>Datum</th><th>Produkt</th><th>Brutto</th><th>Menge</th><th>Gesamt</th></tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        foreach ($orders['data'] as $order) {
                            if ($currentOrderId !== $order['order_id']) {
                                if ($currentOrderId !== null) {
                                    // Display the total for the previous order
                                    echo "<tr class='summe'><td colspan='5'>Gesamt für Bestellung #$currentOrderId</td><td class='right'>" . number_format($totalBrutto, 2) . " €</td></tr>";
                                }
                                // Reset total and update current order ID
                                $totalBrutto = 0;
                                $currentOrderId = $order['order_id'];
                            }

                            $totalBrutto += $order['quantity'] * $order['brutto'];

                            echo "<tr>";
                            echo "<td>#" . htmlspecialchars($order['order_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['createdDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($order['name']) . "</td>";
                            echo "<td class='right'>" . number_format($order['brutto'], 2) . " €</td>";
                            echo "<td class='right'>" . htmlspecialchars($order['quantity']) . "</td>";
                            echo "<td class='right'>" . number_format($order['quantity'] * $order['brutto'], 2) . " €</td>";
                            echo "</tr>";
                        }
                        if ($currentOrderId !== null) {
                            // Display the total for the last order
                            echo "<tr class='summe'><td colspan='5'>Gesamt für Bestellung #$currentOrderId</td><td class='right'>" . number_format($totalBrutto, 2) . " €</td></tr>";
                        }

                        echo '</tbody>';
                        echo '</table>';
                        ?>
                    </li>
                    <li id="adresses_content" style="display:none" class="meinkonto_list_element">
                        <ol class="adresse_list">
                            <?php foreach ($addresses['data'] as $address) {
                                echo "<li>";
                                echo "<div>";
                                echo "<span><b>" . htmlspecialchars($address['name']) . "</b></span>";
                                echo "<span>" . htmlspecialchars($address['firstname']) . " " . htmlspecialchars($address['lastname']) . "</span>";
                                echo "<span>" . htmlspecialchars($address['street']) . "</span>";
                                echo "<span>" . htmlspecialchars($address['postal_code']) . " " . htmlspecialchars($address['city']) . "</span>";
                                echo '<a href="#' . htmlspecialchars($address['address_id']) . '"><i class="fa fa-pencil" aria-hidden="true"></i> Bearbeiten</a>';
                                echo "</div>";
                                echo "</li>";
                            } ?>


                           
                        </ol>
                    </li>
                    <li id="profil_edit_content" style="display:none" class="meinkonto_list_element">
                        <form method="post" class="ajax_call konto_form">
                            <p>
                                <label for="profil_vorname">Vorname&nbsp;<span>*</span></label>
                                <input type="text" name="firstname" id="profil_vorname" value="<?php echo htmlspecialchars($userData['firstname']); ?>">

                                <label for="profil_nachname">Nachname&nbsp;<span>*</span></label>
                                <input type="text" name="lastname" id="profil_nachname" value="<?php echo htmlspecialchars($userData['lastname']); ?>">
                            </p>

                            <p>
                                <label for="profil_email">E-Mail-Adresse&nbsp;<span>*</span></label>
                                <input type="email" name="email" id="profil_email" autocomplete="email" value="<?php echo htmlspecialchars($userData['email']); ?>">
                            </p>

                            <p>
                                <b>Passwort ändern</b>
                                <label for="profil_new_password">Neues Passwort (leer lassen für keine Änderung)&nbsp;<span>*</span></label>
                                <span>
                                    <input autocomplete="none" type="password" name="password1" id="profil_new_password">
                                    <span></span>
                                </span>
                                <br>
                                <label for="profil_new_password_2">Neues Passwort bestätigen&nbsp;<span>*</span></label>
                                <span>
                                    <input autocomplete="none" type="password" name="password2" id="profil_new_password_2">
                                    <span></span>
                                </span>
                            </p>
                            <p>
                                <button type="submit" name="profil_update" value="profil_update" class="cta">Änderungen speichern</button>
                                <input type="hidden" name="action" value="accountUpdate">
                            </p>
                        </form>
                    </li>
                </ol>
            </div>
        </div>
    <?php else : ?>
        <div class="register_wrapper">
            <div class="flex">
                <form method="post" class="konto_form">
                    <p>
                        <label for="username">E-Mail-Adresse&nbsp;<span class="required">*</span></label>
                        <input required type="text" name="username" id="username" autocomplete="username" value="<?php $controller->getPostVar("username") ?>">
                    </p>
                    <p>
                        <label for="password">Passwort&nbsp;<span class="required">*</span></label>
                        <span><input required type="password" name="password" id="password" autocomplete="current-password"></span>
                    </p>
                    <button class="cta" type="submit">Annmelden</button>
                    <input type="hidden" name="action" value="login">
                </form>
            </div>
            <div class="flex">
                <form method="post" class="konto_form">
                    <p>
                        <label for="reg_firstname">Vorname</span></label>
                        <input type="text" name="firstname" id="reg_firstname" value="<?php $controller->getPostVar("firstname") ?>">
                    </p>
                    <p>
                        <label for="reg_lastname">Nachname</span></label>
                        <input type="text" name="lastname" id="reg_lastname" value="<?php $controller->getPostVar("lastname") ?>">
                    </p>
                    <p>
                        <label for="reg_email">E-Mail-Adresse&nbsp;<span>*</span></label>
                        <input required type="email" name="email" id="reg_email" autocomplete="email" value="<?php $controller->getPostVar("email") ?>">
                    </p>

                    <p>
                        <label for="reg_password">Passwort&nbsp;<span>*</span></label>
                        <span>
                            <input required type="password" name="password" id="reg_password" autocomplete="new-password">
                            <span></span>
                        </span>
                    </p>
                    <p><label for="reg_password_2">Passwort bestätigen&nbsp;<span>*</span></label>
                        <span>
                            <input required type="password" name="reg_password_2" id="reg_password_2">
                            <span></span>
                        </span>
                    </p>
                    <div>
                        <p>Ihre persönlichen Daten werden verwendet, um Ihr Erlebnis auf dieser Website zu unterstützen, den Zugriff auf Ihr Konto zu verwalten und für andere in unserem beschriebene Zwecke <a href="#" target="_blank">Datenschutzerklärung</a>.</p>
                    </div>
                    <p>
                        <button type="submit" name="register" value="Registrieren" class="cta">Registrieren</button>
                        <input type="hidden" name="action" value="register">
                    </p>
                </form>
            </div>
        <?php endif; ?>
        </div>
</section>
<?php include 'uspContent.php'; ?>