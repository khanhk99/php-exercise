<?php
include_once './Product.php';

$products_object = new Product();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_add = $products_object->add();
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
            <div class="success-message"><?php echo isset($product_add['sucessMessage']) ? $product_add['sucessMessage'] : "" ?></div>
            <form id="addProduct" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <table class="ui celled table ui form">
                    <tr>
                        <td>SKU*</td>
                        <td>
                            <input type="text" name="sku" value="<?php echo isset($_POST['sku']) ? $_POST['sku'] : "" ?>">
                            <p class="error-field"><?php echo isset($product_add['sku']) ? $product_add['sku'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Title*</td>
                        <td>
                            <input type="text" name="title" value="<?php echo isset($_POST['title']) ? $_POST['title'] : "" ?>">
                            <p class="error-field"><?php echo isset($product_add['title']) ? $product_add['title'] : "" ?></p>
                        </td>
                    <tr>
                        <td>Price*</td>
                        <td>
                            <div class="ui icon input">
                                <input type="number" name="price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : 50 ?>">
                                <i class="circular icon icofont-dollar"></i>
                            </div>
                            <p class="error-field"><?php echo isset($product_add['price']) ? $product_add['price'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Sale price</td>
                        <td>
                            <div class="ui icon input">
                                <input type="number" name="salePrice" value="<?php echo isset($_POST['salePrice']) ? $_POST['salePrice'] : "" ?>">
                                <i class="circular icon icofont-dollar"></i>
                            </div>
                            <p class="error-field"><?php echo isset($product_add['salePrice']) ? $product_add['salePrice'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Feature Image*</td>
                        <td>
                            <input type="file" name="featuredImage" value="">
                            <p class="error-field"><?php echo isset($product_add['featuredImage']) ? $product_add['featuredImage'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Gallery</td>
                        <td>
                            <input type="file" name="gallery[]" multiple>
                            <p class="error-field"><?php echo isset($product_add['gallery']) ? $product_add['gallery'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Categories</td>
                        <td>
                            <p class="suggestion">Each category id seperated by " , "</p>
                            <input type="text" name="categories" value="<?php echo isset($_POST['categories']) ? $_POST['categories'] : "plugin" ?>">
                            <p class="error-field"><?php echo isset($product_add['categories']) ? $product_add['categories'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Tags</td>
                        <td>
                            <p class="suggestion">Each tag id seperated by " , "</p>
                            <input type="text" name="tags" value="<?php echo isset($_POST['tags']) ? $_POST['tags'] : "plugin, ecommerce, woocommerce" ?>">
                            <p class="error-field"><?php echo isset($product_add['tags']) ? $product_add['tags'] : "" ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>Description*</td>
                        <td>
                            <textarea name="description"><?php echo isset($_POST['description']) ? $_POST['description'] : "New product" ?></textarea>
                            <p class="error-field"><?php echo isset($product_add['description']) ? $product_add['description'] : "" ?></p>
                        </td>
                    </tr>
                </table>
                <a class="ui button" href="./">Back</a>
                <button class="ui button" form="addProduct" name="submit">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>