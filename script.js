$(document).ready(function () {
    /*
    document.addEventListener('DOMContentLoaded', function () {
        const menuIcon = document.querySelector('.hamburger-menu');
        const navLinks = document.querySelector('.nav-links');
    
        menuIcon.addEventListener('click', function () {
            navLinks.classList.toggle('show');
        });
    }); */

    // BURGER
    $(".nav-opener").click(function () {
        $('body').toggleClass('nav-active');
        $('header .nav_inner').slideToggle();
    });

    // CART
    $(".warenkorb a, #weiterShoppen").click(function () {
        $('.warenkorb_wrapper').slideToggle();
        $('body').toggleClass('cart-active');
    });

    // ADD TO CART
    $("body").on('click', '#teelist button', function () {
        $('.warenkorb_wrapper').slideDown();
        $('body').addClass('cart-active');
    });

    // SCROLL TO
    $("nav .anker").click(function (e) {
        // Extrahiere nur den Anker-Teil der URL
        var anker = $(this).attr('href').split('#')[1];

        // Stelle sicher, dass das Ziel-Element existiert
        if ($('#' + anker).length) {
            $('html, body').animate({
                scrollTop: $('#' + anker).offset().top - 120
            }, 500);
        } else {
            console.error('Element mit ID ' + anker + ' nicht gefunden.');
        }
    });


    //Checkout
    $('#zurKassa').click(function (e) {
        e.preventDefault(); // Verhindere das Standardverhalten des Browsers

        // Daten aus dem Local Storage holen
        var cartData = localStorage.getItem('warenkorb');

        // Dynamisch ein Formular erstellen
        var form = $('<form>', {
            'action': 'index.php?page=checkout', // Ziel-URL
            'method': 'POST'
        }).append($('<input>', {
            'type': 'hidden',
            'name': 'action',
            'value': 'processCheckout' // Die Aktion, die das Backend erwartet für Checkout
        })).append($('<input>', {
            'type': 'hidden',
            'name': 'cartData',
            'value': cartData // Die Daten aus dem Local Storage als Wert
        }));

        // Füge das Formular zum Body hinzu und reiche es ein
        $(document.body).append(form);
        form.submit();
    });

    // Funktion zum Aktivieren eines Tabs
    function activateTab(anker, animate) {
        var $tab = $('.mein_konto_wrapper nav a[href="' + anker + '"]');
        var $content = $(anker + "_content");

        // Entferne die aktive Klasse von allen Tabs und füge sie dem ausgewählten Tab hinzu
        $('.mein_konto_wrapper nav a').removeClass('btn_active');
        $tab.addClass('btn_active');

        // Schließe alle Inhalte und öffne den entsprechenden Inhalt
        $('.meinkonto_content_wrapper .meinkonto_list_element').stop(true, true).slideUp(animate ? 400 : 0);
        $content.stop(true, true).slideDown(animate ? 400 : 0);
    }

    // Event-Handler für das Klicken auf einen Tab
    $('.mein_konto_wrapper nav a').click(function (e) {
        var anker = $(this).attr('href');
        activateTab(anker, true);
    });

    // Prüfe beim Laden der Seite, ob ein Hash in der URL vorhanden ist
    var urlHash = window.location.hash;
    if (urlHash) {
        switch (urlHash) {
            case '#teesorten':
                $('html, body').animate({
                    scrollTop: $(urlHash).offset().top - 120
                }, 500);
                break;
            default:
                activateTab(urlHash, false);
                break;
        }
    }
});