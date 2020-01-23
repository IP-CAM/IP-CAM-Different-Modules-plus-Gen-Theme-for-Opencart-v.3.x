$(document).ready(function () {
    const cart = $("#cart");
    let cartOffsetTop = cart.offset().top;

    if ($(window).width() < 768) {
        cart.find("button").on("click", function () {
            cart.find(".dropdown-menu li div").css({
                "min-width": ($(window).width() - 30)
            })
        });
    }

    $(window).on("scroll", function () {

        if ($(window).scrollTop() >= cartOffsetTop) {
            cart.parent().addClass("sticky-cart");
        } else {
            cart.parent().removeClass("sticky-cart");
        }
    });
});
