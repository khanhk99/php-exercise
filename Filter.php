<?php
include_once './DBconnect.php';

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
        $products = $this->executeParam($sql, $param, PDO::PARAM_INT);
        return array("products" => $products, "total_page" => $total_page);
    }
}
