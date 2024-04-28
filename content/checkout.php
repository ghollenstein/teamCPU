<?php
$controller = new Controller();
if (!$controller->login->isUserLoggedIn()) {
    $this->addFeedback("Um eine Bestellung zu tätigen, erstellen Sie zuerst ein Konto oder melden Sie sich an!", "success");
    header("Location: index.php?page=meinkonto");
}

?>
<section id="checkoutProcess" class="teesorten">
    <form id="payment-form" action="index.php?page=checkout" method="post">
        <h1>Kasse</h1>
        <?php
        if ($controller->login->isUserLoggedIn() == true) :
            $userData = $controller->account->getUserData()[0];
        ?>

            <b>Versand & Rechnungsdaten</b>
            <div class="form_group">
                <div class="flex">
                    <label for="checkout_billing">Lieferadresse:&nbsp;<span>*</span></label>
                    <input type="text" name="delivery" id="checkout_billing" value="<?php $controller->getPostVar("delivery") ?>">
                </div>
                <div class="flex">
                    <label for="checkout_billing">Rechnungsadresse&nbsp;<span>*</span></label>
                    <input type="text" name="billing" id="checkout_billing" value="<?php $controller->getPostVar("billing") ?>">
                </div>
            </div>
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
        <div id='checkout_warenkorb'></div>
        <!-- dynamically rendered -->


        <b>Zahlungsmethode</b>
        <div class="form_group">
            <div class="flex">

                <label for="card-element">
                    Kredit- oder Debitkarte
                </label>
                <div id="card-element">
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

            // Submit the form
            form.submit();
        }
    </script>


</section>
<?php include 'uspContent.php'; ?>