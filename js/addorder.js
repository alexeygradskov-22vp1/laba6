$(function () {
    $("#bonuses").html(sessionStorage.getItem("balance") + "%");
    if (sessionStorage.getItem("order_id") === null) {
        $.ajax({
            url: "../php/addorder.php",
            dataType: "json",
            type: "GET",
            success: data => {
                var select = document.getElementById("product");
                for (const product of data) {
                    var option = document.createElement("option");
                    option.value = product["id"];
                    option.innerText = `${product["name"]} | ${product["price"]} руб.`;
                    select.appendChild(option);
                }
            }
        });
    } else {
        $.ajax({
            url: "../php/addorder.php",
            dataType: "json",
            type: "GET",
            data: {
                "order_id": sessionStorage.getItem("order_id")
            },
            success: data => {
                var select = document.getElementById("product");
                for (const product of data["products"]) {
                    var option = document.createElement("option");
                    if (product["id"] === data["order"]["product_id"]) {
                        option.selected = true;
                    }
                    option.value = product["id"];
                    option.innerText = `${product["name"]} | ${product["price"]} руб.`;
                    select.appendChild(option);
                }
                if (data["order"]['pickup'] == "Самовывоз") $('input[name="pickup"][value="pickup"]').prop('checked', true);
                else {
                    $(".delivery").toggle("slow");
                    $("#deliveryField").val(data["order"]['pickup']);
                    $('input[name="pickup"][value="delivery"]').prop('checked', true);
                }
                $("#amount").val(data["order"]["amount"]);
                $("#price").text(`Цена: ${data["price"]} руб.`);
                $("#name").val(data["order"]["name"]);
                $("#discount").prop("checked", data["order"]["discount"] === "true");
                $("#submit").val("Изменить");
            }
        });
    }

});
$(document).on('change', ".radio", function (event) {
    if (this.value == 'delivery') {
        $(".delivery").toggle("slow");
    } else {
        $(".delivery").toggle("hide");
    }
});


$(document).on("change", "#product, #amount, #discount", function (event) {
    var select = $("#product");
    var number = $("#amount");
    var balance = $("#bonuses");
    var discount = $("#discount");
    if (select.val() && select.val() !== "null" && number.val()) {
        $.ajax({
            url: "../php/addorder.php",
            dataType: "json",
            type: "GET",
            data: {
                "product_id": select.val(),
                "amount": number.val(),
            },
            success: data => {
                $("#price").text(`Цена: ${data} руб.`);
            }
        });
    }
    if (select.val() && select.val() !== "null" && number.val() && discount.is(':checked')) {
        $.ajax({
            url: "../php/addorder.php",
            dataType: "json",
            type: "GET",
            data: {
                "product_id": select.val(),
                "amount": number.val(),
                "balance": sessionStorage.getItem("balance")
            },
            success: data => {
                $("#price").text(`Цена: ${data} руб.`);
            }
        });
    }

});

$(document).on("submit", "#orderForm", function (event) {
    event.preventDefault();
    $("#formError").text("");
    for (var item of $(".errorMessage")) {
        item.innerText = "";
    }

    $.ajax({
        url: "../php/addorder.php",
        dataType: "json",
        type: "POST",
        data: {
            "user_id": sessionStorage.getItem("user_id"),
            "product": $("#product").val(),
            "name": $("#name").val(),
            "amount": $("#amount").val(),
            "order_id":
                sessionStorage.getItem("order_id") !== null ? sessionStorage.getItem("order_id") : "null",
            "pickup": $('input[name="pickup"]:checked').val() == 'delivery'
                ? $("#deliveryField").val() : 'Самовывоз',
            "discount": $("#discount").prop("checked")
        },
        success: data => {
            if ("order_id" in data) {
                location.replace("profile.html");
            } else if ("error_message" in data) {
                $("#formError").text(data["error_message"]);
                for (var field of data["fields"]) {
                    $(`#${field}Error`).text("Поле не заполнено");
                }
            } else {
                $("#formError").text("Исправьте ошибки в полях");
                for (const [key, value] of Object.entries(data["errors"])) {
                    $(`#${key}Error`).text(value);
                }
            }
        }
    });


});