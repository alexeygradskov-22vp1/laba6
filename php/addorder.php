<?php
require "repositories/ProductRepository.php";
require "repositories/OrderRepository.php";
require "repositories/UserRepository.php";

$prepository = new ProductRepository("../json/products.json");
$orepository = new OrderRepository("../json/orders.json");
$urepository = new UserRepository("../json/users.json");
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (empty($_GET)) {
        echo json_encode($prepository->getProducts(), JSON_UNESCAPED_UNICODE);
    } elseif (array_key_exists("product_id", $_GET)) {
        $product = $prepository->getProductById(intval($_GET["product_id"]));
        if ($_GET["amount"] < 0) echo 0;
        else {
            if (array_key_exists("balance", $_GET)) {
                $discount = (($product["price"] * $_GET["amount"])*(int)$_GET["balance"])/100;
                echo $product["price"] * $_GET["amount"] - $discount;
            } else {
                echo $product["price"] * $_GET["amount"];}
        }
    } else {
        $response = [];
        $order = $orepository->getOrderById(intval($_GET["order_id"]));
        $product = $prepository->getProductById($order["product_id"]);
        $user = $urepository->getUserById($order["user_id"]);
        $discount = (($product["price"] * $order["amount"])*(int)$user["balance"])/100;
        $response["order"] = $order;
        $response["products"] = $prepository->getProducts();
        $response["price"] = $product["price"] * $order["amount"] - $discount;
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ((!empty($_POST["product"]) || $_POST["product"] === "0") && !empty($_POST["amount"]) &&
        $_POST["product"] !== "null" && !empty($_POST["name"])) {
        $response = array("errors" => []);

        if ($_POST["amount"] < 1) {
            $response["errors"]["amount"] = "Количество товаров должно быть больше 0";
        }

        $orders = $orepository->getOrdersOfUser(intval($_POST["user_id"]));
        if ($orders !== null) {
            for ($i = 0; $i < count($orders); $i++) {
                if ($orders[$i]["product_id"] === intval($_POST["product"]) &&
                    $orders[$i]["amount"] === intval($_POST["amount"]) &&
                    $orders[$i]["name"] === $_POST["name"] &&
                    $orders[$i]["pickup"] === $_POST["pickup"] &&
                    $orders[$i]["discount"] === $_POST["discount"] &&
                    $orders[$i]["id"] !== $_POST["order_id"]) {
                    $response["errors"]["name"] = "Такой заказ (id: {$orders[$i]["id"]}) уже существует";
                    break;
                }
            }
        }

        if (strlen($_POST["name"]) < 3 || strlen($_POST["name"]) > 50) {
            $response["errors"]["name"] = "Неверная длина названия";
        }

        if (empty($response["errors"])) {
            if ($_POST["order_id"] !== "null") {
                $id = $orepository->putOrder(
                    intval($_POST["order_id"]), intval($_POST["user_id"]),
                    intval($_POST["product"]), $_POST["name"], intval($_POST["amount"]),
                    $_POST["pickup"], $_POST["discount"]);
            } else {

                $id = $orepository->postOrder(
                    intval($_POST["user_id"]), intval($_POST["product"]), $_POST["name"],
                    intval($_POST["amount"]), $_POST["pickup"], $_POST["discount"]);
            }
            $response = array("order_id" => $id);
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
}