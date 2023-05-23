$(document).on("submit", "#signupForm", function (event) {
    event.preventDefault();
    if (sessionStorage.getItem("user_id") === null) {
        $("#formError").text("");
        for (var item of $(".errorMessage")) {
            item.innerText = "";
        }

        $.ajax({
            url: "../php/signup.php",
            dataType: "json",
            type: "POST",
            data: $("#signupForm").serialize(),
            success: data => {
                if ("user_id" in data) {
                    sessionStorage.setItem("user_id", data["user_id"]);
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
    } else {
        alert("Вы уже вошли в аккаунт");
    }
});