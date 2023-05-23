$(function () {
    if (sessionStorage.getItem("order_id") !== null) {
        sessionStorage.removeItem("order_id");
    }

    $.ajax({
        url: "../php/profile.php",
        dataType: "json",
        type: "GET",
        data: {id: sessionStorage.getItem("user_id")},
        success: data => {
            $("#userName").text(`Имя: ${data["user"]["name"]}`);
            $("#userEmail").text(`Возраст: ${data["user"]["email"]}`);
            sessionStorage.setItem("balance", data["user"]["balance"]);
            $("#bonuses").addClass("strong").text(`${data["user"]["balance"]}%`);
            if (data["user"]["gender"] === "male") {
                $("#userGender").text("Пол: мужской");
            } else {
                $("#userGender").text("Пол: женский");
            }

            if (data["orders"] !== null) {
                var table = document.getElementById("ordersTable");

                for (const order of data["orders"]) {
                    var row = document.createElement("tr");
                    var el = document.createElement("td");

                    el.innerText = table.rows.length.toString();
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["id"];
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["name"];
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["product_name"];
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["amount"];
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["price"];
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["pickup"];
                    row.appendChild(el);

                    el = document.createElement("td");
                    el.innerText = order["discount"] === "true" ? "Да" : "Нет";
                    row.appendChild(el);

                    el = document.createElement("td");
                    var button = document.createElement("button");
                    button.innerText = "Изменить";
                    button.className = "putButton";
                    button.id = `put${order["id"]}`;
                    el.appendChild(button);
                    button = document.createElement("button");
                    button.innerText = "Удалить";
                    button.className = "deleteButton";
                    button.id = `delete${order["id"]}`;
                    el.appendChild(button);
                    row.appendChild(el);

                    table.appendChild(row);
                }
            }
        }
    });
});

$(document).on("click", "#logoutNav", function (event) {
    sessionStorage.clear();
    location.replace("../index.html");
});

$(document).on("click", "#addOrder", function (event) {
    location.replace("addorder.html");
});

$(document).on("click", ".putButton", function (event) {
    sessionStorage.setItem("order_id", event.target.id.slice(3));
    location.replace("addorder.html");
});

$(document).on("click", ".deleteButton", function (event) {
    $.ajax({
        url: "../php/profile.php",
        dataType: "json",
        type: "POST",
        data: {
            "id": event.target.id.slice(6)
        }
    });
    location.reload();
});