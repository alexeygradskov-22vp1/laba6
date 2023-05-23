<?php
require "repositories/UserRepository.php";

$repository = new UserRepository("../json/users.json");
if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["name"]) &&
    !empty($_POST["email"]) && !empty($_POST["gender"])) {
    $response = array("errors" => []);


    if ($repository->getUserByUsername($_POST["username"]) !== null) {
        $response["errors"]["username"] = "Пользователь с таким логином уже существует";
    } elseif (strlen($_POST["username"]) < 3 || strlen($_POST["username"]) > 15) {
        $response["errors"]["username"] = "Неверная длина логина";
    }
$email ="/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
    if (strlen($_POST["password"]) < 3 || strlen($_POST["password"]) > 15) {
        $response["errors"]["password"] = "Неверная длина пароля";
    }

    if (strlen($_POST["name"]) < 2 || strlen($_POST["name"]) > 20) {
        $response["errors"]["name"] = "Неверная длина имени";
    } elseif ($_POST["name"][0] === " ") {
        $response["errors"]["name"] = "Имя не должно начинаться с пробела";
    } elseif ($_POST["name"][0] != strtoupper($_POST["name"][0])) {
        $response["errors"]["name"] = "Имя должно начинаться с большой буквы";
    }

    if (!preg_match($email, $_POST["email"])) {
        $response["errors"]["email"] = "Неверный формат электронной почты";
    }

    if ($_POST["gender"] != "male" && $_POST["gender"] != "female") {
        $response["errors"]["gender"] = "Выберите пол";
    }

    if (empty($response["errors"])) {
        $id = $repository->postUser(
            $_POST["username"], $_POST["password"], $_POST["name"], $_POST["email"], $_POST["gender"]);
        $response = array("user_id" => $id);
    }
} else {
    $response = array("error_message" => "Некоторые поля не заполнены");
    $fields = array();

    foreach ($_POST as $key => $value) {
        if (empty($value)) {
            $fields[] = $key;
        }
    }

    $response["fields"] = $fields;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);