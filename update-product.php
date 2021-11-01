<?php
include_once './Product.php';

$products_object = new Product();
$product_query = $products_object->getUpdate($_GET["id"])[0];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_update = $products_object->postUpdate($_GET["id"]);
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
        <h2>Add product</h2>
    
        <div class="main">
            <div class="success-message"><?php echo isset($product_update['sucessMessage']) ? $product_update['sucessMessage'] : "" ?></div>
            <form id="updateProduct" method="post" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" enctype="multipart/form-data">
                <table class="ui celled table ui form">
                    <tr>
                        <td>SKU*</td>
                        <td>
                            <input type="text" name="sku" value="<?php echo isset($_POST['sku']) ? $_POST['sku'] : $product_query['sku'] ?>">
                            <p class="error-field"><?php echo isset($product_update['sku']) ? $product_update['sku'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Title*</td>
                        <td>
                            <input type="text" name="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : $product_query['title'] ?>">
                            <p class="error-field"><?php echo isset($product_update['title']) ? $product_update['title'] : "" ?></p>
                        </td>
                    <tr>
                        <td>Price*</td>
                        <td>
                            <div class="ui icon input">
                                <input type="number" name="price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : $product_query['price'] ?>">
                                <i class="circular icon icofont-dollar"></i>
                            </div>
                            <p class="error-field"><?php echo isset($product_update['price']) ? $product_update['price'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Sale price</td>
                        <td>
                            <div class="ui icon input">
                                <input type="number" name="salePrice" value="<?php echo isset($_POST['salePrice']) ? $_POST['salePrice'] : $product_query['sale_price'] ?>">
                                <i class="circular icon icofont-dollar"></i>
                            </div>
                            <p class="error-field"><?php echo isset($product_update['salePrice']) ? $product_update['salePrice'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Feature Image*</td>
                        <td>
                            <input type="file" name="featuredImage" value="">
                            <input hidden name="oldFeaturedImage" value="<?php echo $product_query['featured_image'] ?>">
                            <img src="assets/images/<?php echo isset($_POST['featuredImage']) ? $_POST['featuredImage'] : $product_query['featured_image'] ?>" alt="Image not found">
                            <p class="error-field"><?php echo isset($product_update['featuredImage']) ? $product_update['featuredImage'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Gallery</td>
                        <td>
                            <input type="file" name="gallery[]" multiple>
                            <?php
                            $galleries = $product_query['gallery'];
                            $gallery = strtok($galleries, ",");
                            ?>
                            <div>
                                <div class="field-galleries">
                                    <?php while ($gallery !== false) { ?>
                                        <img src="assets/images/<?php echo $gallery ?>">
                                        <?php $gallery = strtok(","); ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <p class="error-field"><?php echo isset($product_update['gallery']) ? $product_update['gallery'] : "" ?></p>
                        </td>
                    </tr>
                    <?php
                    $terms = $products_object->getTerms($_GET["id"]);
                    ?>
                    <tr>
                        <td>Categories</td>
                        <td>
                            <p class="suggestion">Each category id seperated by " , "</p>
                            <?php
                            $str_categories = "";
                            foreach ($terms as $term) {
                                if ($term['type'] == 1) {
                                    $str_categories .= $term['name'] . ", ";
                                }
                            }
                            ?>
                            <input type="text" name="categories" value="<?php echo isset($_POST['categories']) ? $_POST['categories'] : chop($str_categories, ", ") ?>">
                            <p class="error-field"><?php echo isset($product_update['categories']) ? $product_update['categories'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Tags</td>
                        <td>
                            <p class="suggestion">Each tag id seperated by " , "</p>
                            <?php
                            $str_tags = "";
                            foreach ($terms as $term) {
                                if ($term['type'] == 2) {
                                    $str_tags .= $term['name'] . ', ';
                                }
                            }
                            ?>
                            <input type="text" name="tags" value="<?php echo isset($_POST['tags']) ? $_POST['tags'] : chop($str_tags, ", ") ?>">
                            <p class="error-field"><?php echo isset($product_update['tags']) ? $product_update['tags'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Description*</td>
                        <td>
                            <textarea name="description"><?php echo isset($_POST['description']) ? $_POST['description'] : $product_query['description'] ?></textarea>
                            <p class="error-field"><?php echo isset($product_update['description']) ? $product_update['description'] : "" ?></p>
                        </td>
                    </tr>
                </table>
                <a class="ui button" href="./">Back</a>
                <button class="ui button" form="updateProduct" name="submit">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>