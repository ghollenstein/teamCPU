<?php
$controller = new Controller();
if (!$controller->login->isUserLoggedIn()) {
    $this->addFeedback("Um eine Bestellung zu tätigen, erstellen Sie zuerst ein Konto oder melden Sie sich an!", "success");
    header("Location: index.php?page=meinkonto");
}

function formatAddress($address)
{
    // Initialize an array to hold parts of the formatted address
    $formattedAddress = [];

    // Append the name or company if available
    if (!empty($address['company'])) {
        $formattedAddress[] = $address['company'];
    } elseif (!empty($address['firstname']) && !empty($address['lastname'])) {
        $formattedAddress[] = $address['firstname'] . ' ' . $address['lastname'];
    } elseif (!empty($address['name'])) {
        $formattedAddress[] = $address['name'];
    }

    // Append the street
    if (!empty($address['street'])) {
        $formattedAddress[] = $address['street'];
    }

    // Append the city, state, and postal code in one line if available
    $cityStatePostal = [];
    if (!empty($address['postal_code'])) {
        $cityStatePostal[] = $address['postal_code'];
    }
    if (!empty($address['city'])) {
        $cityStatePostal[] = $address['city'];
    }

    if (!empty($cityStatePostal)) {
        $formattedAddress[] = implode(' ', $cityStatePostal);
    }

    // Append the country
    if (!empty($address['country'])) {
        $formattedAddress[] = $address['country'];
    }

    // Combine all parts into a single string separated by line breaks for HTML display
    return implode("<br>", $formattedAddress);
}


?>
<section id="checkoutProcess" class="teesorten">
    <form id="payment-form" action="index.php?page=checkout" method="post">
        <h1>Kasse</h1>
        <?php
        if ($controller->login->isUserLoggedIn() == true) :
            $userData = $controller->account->getUserData()[0];
            $addresses = $controller->getAddresses()['data'];
        ?>

            <b>Versand & Rechnungsdaten</b>
            
            <div class="form_group">
                <div class="flex">
                    <label for="checkout_delivery">Lieferadresse&nbsp;<span>*</span></label>
                    <select required name="delivery" id="checkout_delivery" onchange="updateAddressDisplay(this, '#delivery_address_display')">
                        <?php foreach ($addresses as $address) : ?>
                            <option value="<?= htmlspecialchars($address['address_id']) ?>" data-address="<?= formatAddress($address) ?>">
                                <?= htmlspecialchars($address['street'] . ', ' . $address['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="delivery_address_display" class="checkoutAddress whiteBox"></div> <!-- Display für die gewählte Lieferadresse -->

                </div>
                <div class="flex">
                    <label for="checkout_billing">Rechnungsadresse&nbsp;<span>*</span></label>
                    <select required name="billing" id="checkout_billing" onchange="updateAddressDisplay(this, '#billing_address_display')">
                        <?php foreach ($addresses as $address) : ?>
                            <option value="<?= htmlspecialchars($address['address_id']) ?>" data-address="<?= formatAddress($address) ?>">
                                <?= htmlspecialchars($address['street'] . ', ' . $address['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="billing_address_display" class="checkoutAddress whiteBox"></div> <!-- Display für die gewählte Rechnungsadresse -->
                </div>
            </div>
            <script>
                function updateAddressDisplay(selectElement, displayElementSelector) {
                    var address = selectElement.selectedOptions[0].getAttribute('data-address');
                    document.querySelector(displayElementSelector).innerHTML = address;
                }
                document.addEventListener('DOMContentLoaded', function() {
                    updateAddressDisplay(document.getElementById('checkout_delivery'), '#delivery_address_display');
                    updateAddressDisplay(document.getElementById('checkout_billing'), '#billing_address_display');
                });
            </script>


        <?php else : ?>


            <b>Rechnungsdetails</b>
            <div class="form_group">
                <div class="flex">
                    <label for="checkout_vorname">Vorname&nbsp;<span>*</span></label>
                    <input type="text" name="vorname" id="checkout_vorname" value="">
                </div>
                <div class="flex">
                    <label for="checkout_nachname">Nachname&nbsp;<span>*</span></label>
                    <input type="text" name="nachname" id="checkout_nachname" value="">
                </div>
            </div>

            <div class="form_group">
                <div class="flex">
                    <label for="checkout_strasse">Straße&nbsp;<span>*</span></label>
                    <input type="text" name="strasse" id="checkout_strasse" value="">
                </div>
                <div class="flex">
                    <label for="checkout_strassenr">Hausnummer&nbsp;<span>*</span></label>
                    <input type="text" name="strassenr" id="checkout_strassenr" value="">
                </div>
            </div>

            <div class="form_group">
                <div class="flex">
                    <label for="checkout_postleitzahl">Postleitzahl&nbsp;<span>*</span></label>
                    <input type="text" name="postleitzahl" id="checkout_postleitzahl" value="">
                </div>
                <div class="flex">
                    <label for="checkout_ort">Ort&nbsp;<span>*</span></label>
                    <input type="text" name="ort" id="checkout_ort" value="">
                </div>
            </div>

            <p>
                <label for="checkout_email">E-Mail-Adresse&nbsp;<span>*</span></label>
                <input type="email" name="email" id="checkout_email" autocomplete="email" value="">
            </p>


        <?php endif; ?>
        <b>Deine Bestellung</b>
        <div id='checkout_warenkorb' class='whiteBox'></div>
        <!-- dynamically rendered -->


        <b>Zahlungsmethode</b> 
        <div class="form-group">
            <div class="flex " >

                <label for="card-element">
                    Kredit- oder Debitkarte - (Demodaten: 4242424242424242 | 10/25 | 123 | 68500)
                </label>
                <div id="card-element" class='whiteBox'>
                    <!-- A Stripe Element will be inserted here. -->
                </div>

                <!-- Used to display form errors. -->
                <div id="card-errors" role="alert"></div>
            </div>
        </div>
        <input type="hidden" name="action" value="processCheckout">
        <input id="cartData" type="hidden" name="cartData" value="">
        <input name="agb" id="agb" type="checkbox" value="1" required="">
        <label for="agb">Ich bin mit der Verarbeitung meiner angegebenen Daten einverstanden und akzeptiere die AGBs.</label>
        <button class="cta">Kostenpflichtig bestellen</button>
    </form>

    <script>
        // Set your publishable key.
        var stripe = Stripe('pk_test_mY9dmGCWnQgKn04SrNtHCjNJ');
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '16px',
                color: '#000000',
                border: '1px solid #ff0000',
                backgroundColor: '#ffffff',
                padding: '0.5em',
                lineHeight: '1.4'
            },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {
            style: style
        });

        // Add an instance of the card Element into the `card-element` div.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        // Submit the form with the token ID.
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);


            // Check if the item exists in localStorage
            const CART_KEY = 'warenkorb';
            if (localStorage.getItem(CART_KEY) !== null) {
                // Item exists, remove it
                localStorage.removeItem(CART_KEY);
                // Submit the form
                form.submit();
            } else {
                // Item does not exist, log a message
                console.info("Item '" + CART_KEY + "' does not exist in localStorage.");
                alert("es befinden sich keine Artikel im Warenkorb!");
            }



        }
    </script>


</section>
<?php include 'uspContent.php'; ?>