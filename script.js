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
    $("nav .anker").click(function () {
        var anker = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(anker).offset().top - 120
        }, 500);
    });

    // MEIN KONTO
    $('.mein_konto_wrapper nav a').click(function () {
        var anker = $(this).attr('href');

        $('.mein_konto_wrapper nav a').removeClass('btn_active');
        $(this).addClass('btn_active');

        if ($(anker + "_content").is(":hidden")) {
            $('.meinkonto_content_wrapper .meinkonto_list_element').slideUp();
            $(anker + "_content").slideDown();
        }
    });
    //Checkout
    $('#zurKassa').click(function () {
        // Leite den Benutzer zur Checkout-Seite um
        window.location.href = 'index.php?page=checkout';
    });
});