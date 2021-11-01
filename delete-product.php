<?php 
include_once './Product.php';

$products_object = new Product();
$delete_product = $products_object->delete($_GET['id']);
