<?php
include_once './DBconnect.php';
include_once './Term.php';

class Filter extends DBconnection
{
    public function sortSearch()
    {
        $sort_value = $_GET['sortValue'];
        $sort_type = $_GET['sortType'];

        $count_product = $this->executeNoParam("SELECT COUNT(id) AS count_product FROM products")[0]['count_product'];
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $record_per_page = 3;
        $total_page = ceil($count_product / $record_per_page);

        if ($current_page > $total_page) {
            $current_page = $total_page;
        } else if ($current_page < 1) {
            $current_page = 1;
        }

        $start_record = ($current_page - 1) * $record_per_page;

        $sql = "SELECT * FROM `products` ORDER BY $sort_value $sort_type LIMIT :start_record, :record_per_page";
        $param = array(
            ":start_record" => array(
                "data" => $start_record,
                "type" => PDO::PARAM_INT
            ),
            ":record_per_page" => array(
                "data" => $record_per_page,
                "type" => PDO::PARAM_INT
            )
        );
        $products = $this->executeParam($sql, $param);
        return array("products" => $products, "total_page" => $total_page);
    }

    public function filterSearch()
    {
        $category_filter = $_GET['categoryFilter'];
        $tag_filter = $_GET['tagFilter'];
        $start_date_filter = !empty($_GET['startDateFilter']) ? (string)$_GET['startDateFilter'] : "1970-01-01";
        $end_date_filter = !empty($_GET['endDateFilter']) ? (string)$_GET['endDateFilter'] : date("Y-m-d");

        $sql_paginate = "SELECT * FROM `products`
        INNER JOIN `term_relationship` ON `products`.`ID` = `term_relationship`.`product_id` 
        WHERE ((`term_relationship`.`foreign_id` = $category_filter) OR (`term_relationship`.`foreign_id` = $tag_filter)) 
        AND (`products`.`created_date` >= :start_date_filter) 
        AND (`products`.`created_date` <= :end_date_filter) 
        GROUP BY sku
        ";
        $params_paginate =  array(
            ":start_date_filter" => array(
                "data" => $start_date_filter,
                "type" => PDO::PARAM_STR
            ),
            ":end_date_filter" => array(
                "data" => $end_date_filter,
                "type" => PDO::PARAM_STR
            ),
        );
        $products = $this->executeParam($sql_paginate, $params_paginate);

        return array("products" => $products, "total_page" => 0);
    }

    public function searchProduct($title){
        $sql = "SELECT id, title FROM `products` WHERE title LIKE '%$title%'";
        $products = $this->executeNoParam($sql);
        $html = "";
        
        foreach($products as $product){
            $html .= '<li><a href="#">'. $product['title'] . '</a></li>';
        }
        return $html;
    }
}
