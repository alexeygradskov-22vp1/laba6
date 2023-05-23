<?php
require "repositories/UserRepository.php";
require "repositories/OrderRepository.php";
require "repositories/ProductRepository.php";

$urepository = new UserRepository("../json/users.json");
$orepository = new OrderRepository("../json/orders.json");
$prepository = new ProductRepository("../json/products.json");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $response = array("user" => $urepository->getUserById(intval($_GET["id"])));

    $orders = $orepository->getOrdersOfUser(intval($_GET["id"]));
    if ($orders !== null) {
        for ($i = 0; $i < count($orders); $i++) {
            $product = $prepository->getProductById($orders[$i]["product_id"]);
            $orders[$i]["product_name"] = $product["name"];
            $discount = (($product["price"] * $orders[$i]["amount"])
                    *(int)$response["user"]["balance"])/100;
            $orders[$i]["price"] = $product["price"] * $orders[$i]["amount"]-$discount;
        }
    }
    $response["orders"] = $orders;

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} else {
    $orepository->deleteOrder(intval($_POST["id"]));
}