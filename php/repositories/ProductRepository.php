<?php

class ProductRepository
{
    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getProducts(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }

    public function getProductById($id): ?array
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            if ($product["id"] === $id)
                return $product;
        }

        return null;
    }

    public function getProductByName($name): ?array
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            if ($product["name"] === $name)
                return $product;
        }

        return null;
    }
}