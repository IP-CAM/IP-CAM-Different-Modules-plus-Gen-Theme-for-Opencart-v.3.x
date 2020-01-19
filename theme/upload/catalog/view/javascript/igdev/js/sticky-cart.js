/* Sticky product cart*/
$(document).ready(function () {
    if ($(window).width() < 768) {
        var myCartHeight = $('#cart').height();

        $('#cart').on('click', function () {
            $('#cart').parent().toggleClass(' sticky-cart-open');
        });
    }

    var cartOffsetTop = $('#cart').offset().top;

    $(window).on('scroll', function () {

        if ($(window).scrollTop() >= cartOffsetTop) {
            $('#cart').parent().addClass(' sticky-cart');
            if ($(window).width() < 768) {
                $('header').css('padding-bottom', myCartHeight);
            }
        } else {
            $('#cart').parent().removeClass(' sticky-cart');
            if ($(window).width() < 768) {
                $('header').css('padding-bottom', 0);
            }
        }
    });
});
