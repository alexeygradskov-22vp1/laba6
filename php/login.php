<?php
require "repositories/UserRepository.php";

$repository = new UserRepository("../json/users.json");

if (!empty($_POST["username"]) && !empty($_POST["password"])) {
    $response = $repository->getUserByUsername($_POST["username"]);
    if ($response === null || !password_verify($_POST["password"], $response["password"])) {
        $response = array("error_message" => "Неверный логин и/или пароль");
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