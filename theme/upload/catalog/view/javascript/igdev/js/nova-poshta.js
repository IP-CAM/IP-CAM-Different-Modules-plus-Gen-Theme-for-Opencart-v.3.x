$(document).ready(function () {

    let npCities, npDepartments, novaPoshtaSelected = false;

    // Get nova poshta cities
    $.ajax({
        url: "https://api.novaposhta.ua/v2.0/json/",
        method: "POST",
        timeout: 0,
        headers: {
            "Content-Type": "application/json"
        },
        data: JSON.stringify({
            "apiKey": $("#accordion #collapse-payment-address").attr("data-np-api-key"),
            "modelName": "Address",
            "calledMethod": "getCities",
            "methodProperties": {
                "Warehouse": "1"
            }
        }),

    }).done(function (json) {
        if (json["success"]) {
            npCities = json.data;
        }
    });

    // Listen to shipping method
    $(document).delegate("input[name=\"shipping_method\"]", "change", function () {

        if ($("input[name=\"shipping_method\"]:checked").val() === "nova_poshta.nova_poshta") {
            novaPoshtaSelected = true;
        } else {
            novaPoshtaSelected = false;
        }
    });

    // Listen to first upload and dom changes
    $(document).on("DOMSubtreeModified", function () {
        if ($("input[name=\"shipping_method\"]:checked").val() === "nova_poshta.nova_poshta") {
            novaPoshtaSelected = true;
        } else {
            novaPoshtaSelected = false;
        }
    });

    // Construct list of data
    function npCitiesList(json, ref, text) {
        let regEx = new RegExp("^" + text, "i");

        if (ref) {

            let html = "<ul class=\"list-group np-cities-list\">";

            $.each(json, function (k, val) {
                if (val["Area"] === ref) {
                    if (text.length) {
                        if (val["Description"].match(regEx)) {
                            html += "<li data-ref=\"" + val["Ref"] + "\" class=\"list-group-item\">" + val["Description"] + "</li>";
                        }
                    } else {
                        html += "<li data-ref=\"" + val["Ref"] + "\" class=\"list-group-item\">" + val["Description"] + "</li>";
                    }

                }
            });

            html += "</ul>";

            return html;
        } else {
            return "";
        }

    }

    // Insert city name to input and get departments
    $(document).delegate(".np-cities-list li", "click", function () {

        $(this).parent().prev("input").val($(this).text());

        getDepartment($(this).attr("data-ref"));

        $(".np-cities-list").remove();
    });

    // Get list of cities and filter on input
    $(document).delegate("#input-payment-city", "click input", function () {

        if (novaPoshtaSelected) {


            let selectedZone = $("select#input-payment-zone");
            let inputCity = $(this);

            if (selectedZone.val()) {

                let zoneId = selectedZone.val(), zoneRef;

                selectedZone.children("option").each(function () {
                    if ($(this).val() === zoneId) {
                        zoneRef = $(this).attr("data-zone-code");
                    }
                });

                inputCity.parent().css({position: "relative"});

                if ($(".np-cities-list")) {
                    $(".np-cities-list").remove();
                }
                inputCity.after(npCitiesList(npCities, zoneRef, inputCity.val()));

            }
        }
    });

    // Remove cities list on lost focus
    $(document).delegate("#input-payment-city", "focusout", function () {
        setTimeout(function () {
            if ($(".np-cities-list")) {
                $(".np-cities-list").remove();
            }
        }, 500);
    });

    // Get nova poshta department
    function getDepartment(ref) {

        $.ajax({
            url: "https://api.novaposhta.ua/v2.0/json/",
            method: "POST",
            timeout: 0,
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                "apiKey": $("#accordion #collapse-payment-address").attr("data-np-api-key"),
                "modelName": "AddressGeneral",
                "calledMethod": "getWarehouses",
                "methodProperties": {
                    "CityRef": ref
                }
            })
        }).done(function (response) {
            if (response["success"]) {
                npDepartments = response["data"];
            }
        });
    }

    // Construct department list
    function npDepartmentList(json, text) {

        let html = "<ul class=\"list-group np-departments-list\">";

        $.each(json, function (k, val) {

            if (text.length) {

                let regEx = new RegExp(text, "gi");


                if (val["Description"].match(regEx)) {
                    html += "<li class=\"list-group-item\">" + val["Description"] + "</li>";
                }

            } else {

                html += "<li class=\"list-group-item\">" + val["Description"] + "</li>";
            }
        });

        html += "</ul>";

        return html;
    }

    // Insert department name to input
    $(document).delegate(".np-departments-list li", "click", function () {

        $(this).parent().prev("input").val($(this).text());

        $(".np-departments-list").remove();

        npDepartments = undefined;
    });

    // Get department list and filter if needed
    $(document).delegate("#input-payment-address-1", "click input", function () {
        if (npDepartments) {

            let inputAddress = $(this);

            inputAddress.parent().css({position: "relative"});

            if ($(".np-departments-list")) {
                $(".np-departments-list").remove();
            }

            inputAddress.after(npDepartmentList(npDepartments, inputAddress.val()))

        }
    });

    // Remove departments list on lost focus
    $(document).delegate("#input-payment-address-1", "focusout", function () {
        setTimeout(function () {
            if ($(".np-departments-list")) {
                $(".np-departments-list").remove();
            }
        }, 500);
    });
});