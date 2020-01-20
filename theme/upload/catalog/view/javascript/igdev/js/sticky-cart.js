$(document).ready(function () {
    if ($(window).width() < 768) {

        $("#cart").on("click", function () {
            $("#cart").parent().toggleClass(" sticky-cart-open");
        });
    }

    let cartOffsetTop = $("#cart").offset().top;

    $(window).on("scroll", function () {

        if ($(window).scrollTop() >= cartOffsetTop) {
            $("#cart").parent().addClass(" sticky-cart");
            if ($(window).width() < 768) {
                $("header").css("padding-bottom", $("#cart").height());
            }
        } else {
            $("#cart").parent().removeClass(" sticky-cart");
            if ($(window).width() < 768) {
                $("header").css("padding-bottom", 0);
            }
        }
    });
});
