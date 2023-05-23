$(document).on("submit", "#loginForm", function (event) {
    event.preventDefault();
    if (sessionStorage.getItem("user_id") === null) {
        $("#formError").text("");
        for (var item of $(".errorMessage")) {
            item.innerText = "";
        }
        
        $.ajax({
            url: "../php/login.php",
            dataType: "json",
            type: "POST",
            data: $("#loginForm").serialize(),
            success: data => {
                if ("id" in data) {
                    sessionStorage.setItem("user_id", data["id"]);
                    location.replace("html/profile.html");
                } else {
                    $("#formError").text(data["error_message"]);
                    for (var field of data["fields"]) {
                        $(`#${field}Error`).text("Поле не заполнено");
                    }
                }
            }
        });
    } else {
        alert("Вы уже вошли в аккаунт");
    }
});