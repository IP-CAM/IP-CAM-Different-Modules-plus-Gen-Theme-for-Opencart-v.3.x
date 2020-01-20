function getURLVar(key) {
    let value = [];

    let query = String(document.location).split("?");

    if (query[1]) {
        let part = query[1].split("&");

        for (i = 0; i < part.length; i++) {
            let data = part[i].split("=");

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }

        if (value[key]) {
            return value[key];
        } else {
            return "";
        }
    }
}

$(document).ready(function () {
    // Highlight any found errors
    $(".text-danger").each(function () {
        let element = $(this).parent().parent();

        if (element.hasClass("form-group")) {
            element.addClass("has-error");
        }
    });

    // Currency
    $("#form-currency").find(".currency-select").on("click", function (e) {
        e.preventDefault();

        $("#form-currency").find("input[name=\"code\"]").val($(this).attr("name"));

        $("#form-currency").trigger("submit");
    });

    // Language
    $("#form-language").find(".language-select").on("click", function (e) {
        e.preventDefault();

        $("#form-language").find("input[name=\"code\"]").val($(this).attr("name"));

        $("#form-language").trigger("submit");
    });

    /* Search */
    $("#search input[name=\"search\"]").parent().find("button").on("click", function () {
        let url = $("base").attr("href") + "index.php?route=product/search";

        let value = $("header #search input[name=\"search\"]").val();

        if (value) {
            url += "&search=" + encodeURIComponent(value);
        }

        location = url;
    });

    $("#search input[name=\"search\"]").on("keydown", function (e) {
        if (e.keyCode == 13) {
            $("header #search input[name=\"search\"]").parent().find("button").trigger("click");
        }
    });

    // Menu
    $("#menu .dropdown-menu").each(function () {
        let menu = $("#menu").offset();
        let dropdown = $(this).parent().offset();

        let i = (dropdown.left + $(this).outerWidth()) - (menu.left + $("#menu").outerWidth());

        if (i > 0) {
            $(this).css("margin-left", "-" + (i + 10) + "px");
        }
    });

    // Product List
    $("#list-view").on("click", function () {
        $("#content .product-grid > .clearfix").remove();

        $("#content .row > .product-grid").attr("class", "product-layout product-list col-xs-12");
        $("#grid-view").removeClass("active");
        $("#list-view").addClass("active");

        localStorage.setItem("display", "list");
    });

    // Product Grid
    $("#grid-view").on("click", function () {
        // What a shame bootstrap does not take into account dynamically loaded columns
        let cols = $("#column-right, #column-left").length;

        if (cols === 2) {
            $("#content .product-list").attr("class", "product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12");
        } else if (cols === 1) {
            $("#content .product-list").attr("class", "product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12");
        } else {
            $("#content .product-list").attr("class", "product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12");
        }

        $("#list-view").removeClass("active");
        $("#grid-view").addClass("active");

        localStorage.setItem("display", "grid");
    });

    if (localStorage.getItem("display") == "list") {
        $("#list-view").trigger("click");
        $("#list-view").addClass("active");
    } else {
        $("#grid-view").trigger("click");
        $("#grid-view").addClass("active");
    }

    // Checkout
    $(document).on("keydown", "#collapse-checkout-option input[name=\"email\"], #collapse-checkout-option input[name=\"password\"]", function (e) {
        if (e.keyCode == 13) {
            $("#collapse-checkout-option #button-login").trigger("click");
        }
    });

    // tooltips on hover
    $("[data-toggle=\"tooltip\"]").tooltip({container: "body"});

    // Makes tooltips work on ajax generated content
    $(document).ajaxStop(function () {
        $("[data-toggle=\"tooltip\"]").tooltip({container: "body"});
    });

    // Menu left
    if ($(window).width() < 768) {
        $("#menu-left #mobile-menu-open").on("click", function () {
            $("#menu-left .menu-left-container").toggle("slow");
        });
        $("#menu-left #mobile-menu-close").on("click", function () {
            $("#menu-left .menu-left-container").toggle("slow");
        });
        $("#menu-left .menu-left-container .top-category").on("click", function (e) {
            e.preventDefault();
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(this).next(".menu-content").hide();
            } else {
                $(this).addClass("active");
                $(this).next(".menu-content").show();
            }

        });
    } else {
        $("#menu-left").on("mouseover", function () {
            $("#content").css({opacity: "0.4", filter: "blur(0.5px)"});
        }).on("mouseleave", function () {
            $("#content").css({opacity: "1", filter: "blur(0)"});
        });
    }

    // Scroll to top button
    $(window).on("scroll", function () {

        if ($(window).scrollTop() > 0 && !$("#scroll-to-top").hasClass("hidden")) {
            $("#scroll-to-top").show();
        } else {
            $("#scroll-to-top").hide();
        }
    });

    $("#scroll-to-top").on("click", function () {
        $("html, body").animate({scrollTop: 0}, "slow");
    });
});

// Cart add remove functions
const cart = {
    "add": function (product_id, quantity) {
        $.ajax({
            url: "index.php?route=checkout/cart/add",
            type: "post",
            data: "product_id=" + product_id + "&quantity=" + (typeof (quantity) != "undefined" ? quantity : 1),
            dataType: "json",
            beforeSend: function () {
                $("#cart > button").button("loading");
            },
            complete: function () {
                $("#cart > button").button("reset");
            },
            success: function (json) {
                $(".alert-dismissible, .text-danger").remove();

                if (json["redirect"]) {
                    location = json["redirect"];
                }

                if (json["success"]) {
                    $("#content").parent().before("<div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> " + json["success"] + " <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button></div>");

                    // Need to set timeout otherwise it wont update the total
                    setTimeout(function () {
                        $("#cart > button").html("<span id=\"cart-total\"><i class=\"fa fa-shopping-cart\"></i> " + json["total"] + "</span>");
                    }, 100);

                    // $("html, body").animate({ scrollTop: 0 }, "slow");

                    $("#cart > ul").load("index.php?route=common/cart/info ul li");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    "update": function (key, quantity) {
        $.ajax({
            url: "index.php?route=checkout/cart/edit",
            type: "post",
            data: "key=" + key + "&quantity=" + (typeof (quantity) != "undefined" ? quantity : 1),
            dataType: "json",
            beforeSend: function () {
                $("#cart > button").button("loading");
            },
            complete: function () {
                $("#cart > button").button("reset");
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $("#cart > button").html("<span id=\"cart-total\"><i class=\"fa fa-shopping-cart\"></i> " + json["total"] + "</span>");
                }, 100);

                if (getURLVar("route") == "checkout/cart" || getURLVar("route") == "checkout/checkout") {
                    location = "index.php?route=checkout/cart";
                } else {
                    $("#cart > ul").load("index.php?route=common/cart/info ul li");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    "remove": function (key) {
        $.ajax({
            url: "index.php?route=checkout/cart/remove",
            type: "post",
            data: "key=" + key,
            dataType: "json",
            beforeSend: function () {
                $("#cart > button").button("loading");
            },
            complete: function () {
                $("#cart > button").button("reset");
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $("#cart > button").html("<span id=\"cart-total\"><i class=\"fa fa-shopping-cart\"></i> " + json["total"] + "</span>");
                }, 100);

                if (getURLVar("route") == "checkout/cart" || getURLVar("route") == "checkout/checkout") {
                    location = "index.php?route=checkout/cart";
                } else {
                    $("#cart > ul").load("index.php?route=common/cart/info ul li");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
};

const voucher = {
    "add": function () {

    },
    "remove": function (key) {
        $.ajax({
            url: "index.php?route=checkout/cart/remove",
            type: "post",
            data: "key=" + key,
            dataType: "json",
            beforeSend: function () {
                $("#cart > button").button("loading");
            },
            complete: function () {
                $("#cart > button").button("reset");
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $("#cart > button").html("<span id=\"cart-total\"><i class=\"fa fa-shopping-cart\"></i> " + json["total"] + "</span>");
                }, 100);

                if (getURLVar("route") == "checkout/cart" || getURLVar("route") == "checkout/checkout") {
                    location = "index.php?route=checkout/cart";
                } else {
                    $("#cart > ul").load("index.php?route=common/cart/info ul li");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
};

const wishlist = {
    "add": function (product_id) {
        $.ajax({
            url: "index.php?route=account/wishlist/add",
            type: "post",
            data: "product_id=" + product_id,
            dataType: "json",
            success: function (json) {
                $(".alert-dismissible").remove();

                if (json["redirect"]) {
                    location = json["redirect"];
                }

                if (json["success"]) {
                    $("#content").parent().before("<div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> " + json["success"] + " <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button></div>");
                }

                $("#wishlist-total span").html(json["total"]);
                $("#wishlist-total").attr("title", json["total"]);

                // $("html, body").animate({scrollTop: 0}, "slow");
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    "remove": function () {

    }
};

const compare = {
    "add": function (product_id) {
        $.ajax({
            url: "index.php?route=product/compare/add",
            type: "post",
            data: "product_id=" + product_id,
            dataType: "json",
            success: function (json) {
                $(".alert-dismissible").remove();

                if (json["success"]) {
                    $("#content").parent().before("<div class=\"alert alert-success alert-dismissible\"><i class=\"fa fa-check-circle\"></i> " + json["success"] + " <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button></div>");

                    $("#compare-total").html(json["total"]);

                    // $("html, body").animate({scrollTop: 0}, "slow");
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    "remove": function () {

    }
};

/* Agree to Terms */
$(document).delegate(".agree", "click", function (e) {
    e.preventDefault();

    $("#modal-agree").remove();

    let element = this;

    $.ajax({
        url: $(element).attr("href"),
        type: "get",
        dataType: "html",
        success: function (data) {
            html = "<div id=\"modal-agree\" class=\"modal\">";
            html += "  <div class=\"modal-dialog\">";
            html += "    <div class=\"modal-content\">";
            html += "      <div class=\"modal-header\">";
            html += "        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>";
            html += "        <h4 class=\"modal-title\">" + $(element).text() + "</h4>";
            html += "      </div>";
            html += "      <div class=\"modal-body\">" + data + "</div>";
            html += "    </div>";
            html += "  </div>";
            html += "</div>";

            $("body").append(html);

            $("#modal-agree").modal("show");
        }
    });
});

// Autocomplete */
(function ($) {
    $.fn.autocomplete = function (option) {
        return this.each(function () {
            this.timer = null;
            this.items = new Array();

            $.extend(this, option);

            $(this).attr("autocomplete", "off");

            // Focus
            $(this).on("focus", function () {
                this.request();
            });

            // Blur
            $(this).on("blur", function () {
                setTimeout(function (object) {
                    object.hide();
                }, 200, this);
            });

            // Keydown
            $(this).on("keydown", function (event) {
                switch (event.keyCode) {
                    case 27: // escape
                        this.hide();
                        break;
                    default:
                        this.request();
                        break;
                }
            });

            // Click
            this.click = function (event) {
                event.preventDefault();

                value = $(event.target).parent().attr("data-value");

                if (value && this.items[value]) {
                    this.select(this.items[value]);
                }
            };

            // Show
            this.show = function () {
                let pos = $(this).position();

                $(this).siblings("ul.dropdown-menu").css({
                    top: pos.top + $(this).outerHeight(),
                    left: pos.left
                });

                $(this).siblings("ul.dropdown-menu").show();
            };

            // Hide
            this.hide = function () {
                $(this).siblings("ul.dropdown-menu").hide();
            };

            // Request
            this.request = function () {
                clearTimeout(this.timer);

                this.timer = setTimeout(function (object) {
                    object.source($(object).val(), $.proxy(object.response, object));
                }, 200, this);
            };

            // Response
            this.response = function (json) {
                html = "";

                if (json.length) {
                    for (i = 0; i < json.length; i++) {
                        this.items[json[i]["value"]] = json[i];
                    }

                    for (i = 0; i < json.length; i++) {
                        if (!json[i]["category"]) {
                            html += "<li data-value=\"" + json[i]["value"] + "\"><a href=\"#\">" + json[i]["label"] + "</a></li>";
                        }
                    }

                    // Get all the ones with a categories
                    let category = new Array();

                    for (i = 0; i < json.length; i++) {
                        if (json[i]["category"]) {
                            if (!category[json[i]["category"]]) {
                                category[json[i]["category"]] = new Array();
                                category[json[i]["category"]]["name"] = json[i]["category"];
                                category[json[i]["category"]]["item"] = new Array();
                            }

                            category[json[i]["category"]]["item"].push(json[i]);
                        }
                    }

                    for (i in category) {
                        html += "<li class=\"dropdown-header\">" + category[i]["name"] + "</li>";

                        for (j = 0; j < category[i]["item"].length; j++) {
                            html += "<li data-value=\"" + category[i]["item"][j]["value"] + "\"><a href=\"#\">&nbsp;&nbsp;&nbsp;" + category[i]["item"][j]["label"] + "</a></li>";
                        }
                    }
                }

                if (html) {
                    this.show();
                } else {
                    this.hide();
                }

                $(this).siblings("ul.dropdown-menu").html(html);
            };

            $(this).after("<ul class=\"dropdown-menu\"></ul>");
            $(this).siblings("ul.dropdown-menu").delegate("a", "click", $.proxy(this.click, this));

        });
    }
})(window.jQuery);
