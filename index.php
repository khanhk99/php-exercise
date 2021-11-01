<?php
include_once './Product.php';
include_once './Filter.php';
include_once './Term.php';

$products_object = new Product();
$get_all_products = $products_object->getAll();
$products = $get_all_products["products"];
$total_page = $get_all_products["total_page"];
$stt = 1;
$digit_currency = "$";

$term_object = new Term();
$categories = $term_object->getCategory();
$tags = $term_object->getTag();

$filter_object = new Filter();
if (($_SERVER["REQUEST_METHOD"] == "GET") && (isset($_GET['sortValue']))) {
    $sort_query = $filter_object->sortSearch();
    $products = $sort_query["products"];
    $total_page = $sort_query["total_page"];
}
if (isset($_GET['filterSearchSubmit'])) {
    $filter_query = $filter_object->filterSearch();
    $products = $filter_query["products"];
    $total_page = $filter_query["total_page"];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP1</title>
    <link rel="stylesheet" href="assets/css/semantic.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="ui container">
        <a href="./">
            <h2>PHP1</h2>
        </a>
        <div class="header">
            <div class="feature">
                <div class="ui two columns stackable grid">
                    <div class="column">
                        <div class="pages">
                            <a class="ui button" href="./add-product.php">Add product</a>
                            <a class="ui button" href="./add-property.php">Add property</a>
                            <a class="ui button">Sync from VillaTheme</a>
                        </div>
                    </div>
                    <div class="column">
                        <div class="ui icon input" id="searchProduct">
                            <input type="text" placeholder="Search product...">
                            <i class="inverted circular icon icofont-search-1"></i>
                            <div class="list-product-search" id="resultSearchProduct">
                                <ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- filter -->
            <div class="search">
                <div>
                    <form id="sortSearch" method="GET">
                        <?php
                        $sort_value = isset($_GET['sortValue']) ? $_GET['sortValue'] : "";
                        $sort_date = "";
                        $sort_price = "";
                        $sort_title = "";
                        switch ($sort_value) {
                            case "created_date":
                                $sort_date = "selected";
                                break;
                            case "price":
                                $sort_price = "selected";
                                break;
                            case "title":
                                $sort_title = "selected";
                                break;
                            default:
                        }
                        ?>
                        <select name="sortValue" id="sortDate">
                            <option value="created_date" <?php echo $sort_date ?>>Date</option>
                            <option value="price" <?php echo $sort_price ?>>Price</option>
                            <option value="title" <?php echo $sort_title ?>>Product name</option>
                        </select>
                        <?php
                        $sort_type = isset($_GET['sortType']) ? $_GET['sortType'] : "";
                        $sort_asc = "";
                        $sort_desc = "";
                        switch ($sort_type) {
                            case "asc":
                                $sort_asc = "selected";
                                break;
                            case "desc":
                                $sort_desc = "selected";
                                break;
                            default:
                        }
                        ?>
                        <select name="sortType" id="sortType">
                            <option value="asc" <?php echo $sort_asc ?>>ASC</option>
                            <option value="desc" <?php echo $sort_desc ?>>DESC</option>
                        </select>
                    </form>
                </div>
                <div class="filter">
                    <form id="filterSearch" method="GET">
                        <select name="categoryFilter">
                            <?php if ($categories) {
                                foreach ($categories as $category) { ?>
                                    <option value="<?php echo $category["ID"] ?>"><?php echo $category["name"] ?></option>
                            <?php }
                            } ?>
                        </select>
                        <select name="tagFilter">
                            <?php if ($tags) {
                                foreach ($tags as $tag) { ?>
                                    <option value="<?php echo $tag["ID"] ?>"><?php echo $tag["name"] ?></option>
                            <?php }
                            } ?>
                        </select>
                        <input type="date" name="startDateFilter" value="" min="1970-01-01">
                        <input type="date" name="endDateFilter" value="" min="1970-01-01">
                    </form>
                    <button class="ui button" form="filterSearch" type="submit" name="filterSearchSubmit">Filter</button>
                </div>
            </div>
            <!-- filter -->
        </div>

        <div class="main">
            <div class="success-message"><?php echo isset($product_add['sucessMessage']) ? $product_add['sucessMessage'] : "" ?></div>
            <div class="datas">
                <table class="ui celled table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Date</th>
                            <th>Product name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Feature Image</th>
                            <th>Gallary</th>
                            <th>Categories</th>
                            <th>Tags</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if ($products) {
                            foreach ($products as $product) { ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo $stt;
                                        $stt++;
                                        ?>
                                    </td>
                                    <td><?php echo date("d/m/Y", strtotime($product['created_date'])) ?></td>
                                    <td><?php echo $product['title'] ?></td>
                                    <td><?php echo $product['sku'] ?></td>
                                    <td><?php echo $product['price'] . $digit_currency ?></td>
                                    <td>
                                        <img src="assets/images/<?php echo $product['featured_image'] ?>">
                                    </td>
                                    <?php
                                    $galleries = $product['gallery'];
                                    $gallery = strtok($galleries, ",");
                                    ?>
                                    <td>
                                        <div class="data-galleries">
                                            <?php while ($gallery !== false) { ?>
                                                <img src="assets/images/<?php echo $gallery ?>">
                                                <?php $gallery = strtok(","); ?>
                                            <?php } ?>
                                        </div>
                                        <a href="#" class="toggle-galleries">more+</a>
                                    </td>
                                    <?php
                                    $terms = $products_object->getTerms($product['ID']);
                                    ?>
                                    <td>
                                        <?php
                                        $str_categories = "";
                                        foreach ($terms as $term) {
                                            if ($term['type'] == 1) {
                                                $str_categories .= $term['name'] . ", ";
                                            }
                                        }
                                        echo chop($str_categories, ", ");
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $str_tags = "";
                                        foreach ($terms as $term) {
                                            if ($term['type'] == 2) {
                                                $str_tags .= '<a href="#">' . $term['name'] . '</a>, ';
                                            }
                                        }
                                        echo chop($str_tags, ", ");
                                        ?>
                                    </td>
                                    <td>
                                        <a href="./update-product.php?id=<?php echo $product['ID'] ?>">
                                            <i class="icofont-edit"></i>
                                        </a>
                                        <?php $link_delete = "./delete-product.php?id=" . $product['ID'] ?>
                                        <a href="#" onclick="if(confirm('Are you sure delete this product')) location.href='<?php echo $link_delete ?>'">
                                            <i class="icofont-ui-delete"></i>
                                        </a>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php if (isset($_GET['page']) && $_GET['page'] > 1 && ($total_page != 0)) { ?>
                    <a class="ui basic button" href="index.php?page=<?php echo $_GET['page'] - 1 ?>">
                        <i class="icofont-arrow-left"></i>
                    </a>
                <?php } ?>
                <?php for ($i = 1; $i <= $total_page; $i++) { ?>
                    <a class="ui basic button" href="index.php?page=<?php echo $i ?>"><?php echo $i ?></a>
                <?php } ?>
                <?php if ((isset($_GET['page']) && $_GET['page'] < $total_page) || !isset($_GET['page']) && ($total_page != 0)) { ?>
                    <a class="ui basic button" href="index.php?page=<?php echo isset($_GET['page']) ? ($_GET['page'] + 1) : 2 ?>">
                        <i class="icofont-arrow-right"></i>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/semantic.min.js"></script>
<script>
    $("#sortDate").on("change", function() {
        $("#sortSearch").submit();
    });

    $("#sortType").on("change", function() {
        $("#sortSearch").submit();
    });

    $(".toggle-galleries").click(function() {
        event.preventDefault();
        if ($(this).prev(".data-galleries").css("overflow") == "hidden") {
            // console.log("show more");
            $(this).prev(".data-galleries").css({
                "overflow": "unset",
                "max-height": "unset"
            });
            $(this).html("hide-");
        } else {
            // console.log("hide");
            $(this).prev(".data-galleries").css({
                "overflow": "hidden",
                "max-height": "100px"
            });
            $(this).html("more+");
        }
    });

    $("#searchProduct input").on("input", function() {
        var value = this.value;
        if (value == "") {
            $("#resultSearchProduct").html("");
        } else {
            $.ajax({
                
            })
        }
    });
</script>

</html>