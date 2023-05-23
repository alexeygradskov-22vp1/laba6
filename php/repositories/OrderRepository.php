<?php

class OrderRepository
{
    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getOrders(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }

    public function getOrderById($id): ?array
    {
        $orders = $this->getOrders();
        foreach ($orders as $order) {
            if ($order["id"] === $id)
                return $order;
        }

        return null;
    }

    public function getOrdersOfUser($user_id): ?array
    {
        $orders = $this->getOrders();
        $result = [];
        foreach ($orders as $order) {
            if ($order["user_id"] === $user_id)
                $result[] = $order;
        }

        if (empty($result)) return null;
        else return $result;
    }

    public function postOrder($user_id, $product_id, $name, $amount, $pickup, $discount): int
    {
        $orders = $this->getOrders();
        if (empty($orders)) $id = 0;
        else $id = $orders[array_key_last($orders)]["id"] + 1;
        $orders[] = [
            "id" => $id,
            "user_id" => $user_id,
            "product_id" => $product_id,
            "name" => $name,
            "amount" => $amount,
            "pickup" => $pickup,
            "discount" => $discount
        ];
        $orders = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = fopen($this->path, "w");
        fwrite($file, $orders);
        fclose($file);

        return $id;
    }

    public function putOrder($order_id, $user_id, $product_id, $name, $amount, $pickup, $discount): int
    {
        $orders = $this->getOrders();
        for ($i = 0; $i < count($orders); $i++) {
            if ($orders[$i]["id"] === $order_id) {
                $orders[$i] = [
                    "id" => $order_id,
                    "user_id" => $user_id,
                    "product_id" => $product_id,
                    "name" => $name,
                    "amount" => $amount,
                    "pickup" => $pickup,
                    "discount" => $discount
                ];
                break;
            }
        }
        $orders = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = fopen($this->path, "w");
        fwrite($file, $orders);
        fclose($file);

        return $order_id;
    }

    public function deleteOrder($id): void
    {
        $temp = $this->getOrders();
        $orders = [];
        for ($i = 0; $i < count($temp); $i++) {
            if ($temp[$i]["id"] !== $id) {
                $orders[] = $temp[$i];
            }
        }
        $orders = json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = fopen($this->path, "w");
        fwrite($file, $orders);
        fclose($file);
    }
}