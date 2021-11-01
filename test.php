<?php 
    $arr = ["plugin", "woocommerce"];
    $arr1 = ["plugin", "woocommerce", "theme"];
    $fullDiff = array_diff($arr, $arr1);
    print_r($fullDiff);
?>