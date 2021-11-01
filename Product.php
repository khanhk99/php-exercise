<?php
include_once './Validate.php';

class Product extends Validate
{
    public function getAll()
    {
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

        $sql = 'SELECT * FROM products LIMIT :start_record, :record_per_page';
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

    public function getTerms($product_id)
    {
        $sql = "SELECT type, name FROM terms
        INNER JOIN term_relationship ON terms.ID = term_relationship.foreign_id
        WHERE term_relationship.product_id = :product_id";
        $params = array(
            ":product_id" => array(
                "data" => $product_id,
                "type" => PDO::PARAM_INT,
            )
        );
        $terms = $this->executeParam($sql, $params, PDO::PARAM_INT);

        return $terms;
    }

    public function add()
    {
        $validate = new Validate;
        $message = array();
        $sql_unique_sku = "SELECT COUNT(id) AS count, `ID` FROM `products` WHERE `sku` = :data";
        $count_gallery = count($_FILES['gallery']['name']);

        if (empty($_POST['sku'])) {
            $message['sku'] = "SKU is required";
        } else if ($validate->regexTextValidate($_POST['sku'])) {
            $message['sku'] = "SKU can not contain special characters";
        } else if ($validate->uniqueValidate($_POST['sku'], $sql_unique_sku)) {
            $message['sku'] = "SKU existed";
        }
        if (empty($_POST['title'])) {
            $message['title'] = "Title is required";
        } else if ($validate->regexTextValidate($_POST['title'])) {
            $message['title'] = "Title can not contain special characters";
        }
        if (empty($_POST['price'])) {
            $message['price'] = "Price is required";
        }
        if ($_FILES['featuredImage']['size'] == 0) {
            $message['featuredImage'] = "Feature image is required";
        } else if ($img_message = $validate->imageValidate($_FILES['featuredImage'])) {
            $message['featuredImage'] = $img_message;
        }
        if (empty($_POST['description'])) {
            $message['description'] = "Description is required";
        }
        if ($validate->regexTextValidate($_POST['categories'])) {
            $message['categories'] = "Categories can not contain special characters";
        }
        if ($validate->regexTextValidate($_POST['tags'])) {
            $message['tags'] = "Tags can not contain special characters";
        }

        $image_type_allow = array('jpeg', 'jpg', 'png', 'gif');
        $gallery_str = "";
        if (!empty($_FILES['gallery']['name'][0])) {
            for ($i = 0; $i < $count_gallery; $i++) {
                if (!in_array(pathinfo($_FILES['gallery']['name'][$i])['extension'], $image_type_allow)) {
                    $message['gallery'] = "You have to upload image";
                } else if ($_FILES['gallery']['size'][$i] > 2000000) {
                    $message['gallery'] = "Image can't be larger than 2MB";
                } else {
                    $gallery_str .= $_FILES['gallery']['name'][$i] . ",";
                    $target_file = "assets/images/" . ($_FILES["gallery"]["name"][$i]);
                    move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_file);
                }
            }
        }

        if (!$message) {
            $sku = $_POST['sku'];
            $title = $_POST['title'];
            $price = $_POST['price'];
            $featuredImage = $_FILES['featuredImage']['name'];
            $description = $_POST['description'];
            $categories_arr = explode(",", $_POST['categories']);
            $tags_arr = explode(",", $_POST['tags']);

            $target_file = "assets/images/" . ($_FILES["featuredImage"]["name"]);
            move_uploaded_file($_FILES['featuredImage']['tmp_name'], $target_file);


            $sale_price = !empty($_POST['salePrice']) ? $_POST['salePrice'] : NULL;

            $params_query = array(
                ":sku" => array(
                    "data" => $sku,
                    "type" => PDO::PARAM_STR
                ),
                ":title" => array(
                    "data" => $title,
                    "type" => PDO::PARAM_STR
                ),
                ":price" => array(
                    "data" => $price,
                    "type" => PDO::PARAM_INT
                ),
                ":sale_price" => array(
                    "data" => $sale_price,
                    "type" => PDO::PARAM_INT
                ),
                ":featured_image" => array(
                    "data" => $featuredImage,
                    "type" => PDO::PARAM_STR
                ),
                ":gallery" => array(
                    "data" => rtrim($gallery_str, ","),
                    "type" => PDO::PARAM_STR
                ),
                ":description" => array(
                    "data" => $description,
                    "type" => PDO::PARAM_STR
                )
            );
            $sql = "INSERT INTO `products` 
            (`sku`, `title`, `price`, `sale_price`, `featured_image`, `gallery`, `description`)
            VALUES (:sku, :title, :price, :sale_price, :featured_image, :gallery, :description)";
            if ($query_product = $this->modifyData($sql, $params_query)) {
                $last_product_id = $query_product->lastInsertId();

                // add categories
                $this->addTermRelationship($categories_arr, $last_product_id, 1);
                // add categories
                // add tags
                $this->addTermRelationship($tags_arr, $last_product_id, 2);
                // add tags
                $message['sucessMessage'] = "<p>Add new product successful</p>";
            }
            return $message;
        } else {
            return $message;
        }
    }

    public function addTermRelationship($str_arr, $product_id, $type)
    {
        $sql_unique = "SELECT COUNT(id) as count, `ID` FROM `terms` WHERE (`type` = $type) AND (`name` = :data)";
        $validate = new Validate;

        foreach ($str_arr as $item) {
            if ($sql_unique_query = $validate->uniqueValidate(trim($item), $sql_unique)) {
                // term existed
                $insert_category = "INSERT INTO `term_relationship` (`product_id`, `foreign_id`) VALUES(:product_id, :foreign_id)";
                $param_term_relationship = array(
                    ":product_id" => array(
                        "data" => $product_id,
                        "type" => PDO::PARAM_INT
                    ),
                    ":foreign_id" => array(
                        "data" => $sql_unique_query,
                        "type" => PDO::PARAM_INT
                    )
                );
                $this->modifyData($insert_category, $param_term_relationship);
            } else {
                // term doesn't exist
                $param_term = array(
                    ":name" => array(
                        "data" => trim($item),
                        "type" => PDO::PARAM_STR
                    )
                );
                if ($insert_term = $this->modifyData("INSERT INTO `terms` (`type`, `name`) VALUES($type, :name)", $param_term)) {
                    $insert_category = "INSERT INTO `term_relationship` (`product_id`, `foreign_id`) VALUES(:product_id, :foreign_id)";
                    $param_term_relationship = array(
                        ":product_id" => array(
                            "data" => $product_id,
                            "type" => PDO::PARAM_INT
                        ),
                        ":foreign_id" => array(
                            "data" => $insert_term->lastInsertId(),
                            "type" => PDO::PARAM_INT
                        )
                    );
                    $this->modifyData($insert_category, $param_term_relationship);
                }
            }
        }
    }

    public function deleteTermRelationship($str_arr, $product_id, $type)
    {
        $sql_unique = "DELETE `term_relationship` FROM `term_relationship` INNER JOIN  `terms` 
        ON `term_relationship`.`foreign_id` = `terms`.`id`
        WHERE (`term_relationship`.`product_id` = $product_id) AND (`terms`.`name` = :name) AND (`terms`.`type` = $type)";
        foreach ($str_arr as $item) {
            $param = array(
                ":name" => array(
                    "data" => $item,
                    "type" => PDO::PARAM_STR
                )
            );
            $this->modifyData($sql_unique, $param);
        }
    }

    public function getUpdate($id)
    {
        $sql = "SELECT * FROM `products` WHERE id = :id";
        $param = array(
            ":id" => array(
                "data" => $id,
                "type" => PDO::PARAM_INT
            )
        );
        return $this->executeParam($sql, $param);
    }

    public function postUpdate($id)
    {
        $validate = new Validate;
        $message = array();
        $sql_unique_sku = "SELECT COUNT(ID) AS count, `ID`
        FROM `products` WHERE `sku` = :data 
        AND ID NOT IN( 
        SELECT ID FROM `products` WHERE `ID` = $id)";

        $featuredImage = $_POST['oldFeaturedImage'];
        $count_gallery = count($_FILES['gallery']['name']);

        //validate
        if (empty($_POST['sku'])) {
            $message['sku'] = "SKU is required";
        } else if ($validate->regexTextValidate($_POST['sku'])) {
            $message['sku'] = "SKU can not contain special characters";
        } else if ($validate->uniqueValidate($_POST['sku'], $sql_unique_sku)) {
            $message['sku'] = "SKU existed";
        }
        if (empty($_POST['title'])) {
            $message['title'] = "Title is required";
        } else if ($validate->regexTextValidate($_POST['title'])) {
            $message['title'] = "Title can not contain special characters";
        }
        if (empty($_POST['price'])) {
            $message['price'] = "Price is required";
        }
        if ($_FILES['featuredImage']['size'] > 0) {
            if ($img_message = $validate->imageValidate($_FILES['featuredImage'])) {
                $message['featuredImage'] = $img_message;
            } else {
                $featuredImage = $_FILES['featuredImage']['name'];
                $target_file = "assets/images/" . ($_FILES["featuredImage"]["name"]);
                move_uploaded_file($_FILES['featuredImage']['tmp_name'], $target_file);
            }
        }
        if (empty($_POST['description'])) {
            $message['description'] = "Description is required";
        }

        if ($validate->regexTextValidate($_POST['categories'])) {
            $message['categories'] = "Categories can not contain special characters";
        }
        if ($validate->regexTextValidate($_POST['tags'])) {
            $message['tags'] = "Tags can not contain special characters";
        }

        //gallery
        $image_type_allow = array('jpeg', 'jpg', 'png', 'gif');
        $gallery_str = "";
        if (!empty($_FILES['gallery']['name'][0])) {
            for ($i = 0; $i < $count_gallery; $i++) {
                if (!in_array(pathinfo($_FILES['gallery']['name'][$i])['extension'], $image_type_allow)) {
                    $message['gallery'] = "You have to upload image";
                } else if ($_FILES['gallery']['size'][$i] > 2000000) {
                    $message['gallery'] = "Image can't be larger than 2MB";
                } else {
                    $gallery_str .= $_FILES['gallery']['name'][$i] . ",";
                    $target_file = "assets/images/" . ($_FILES["gallery"]["name"][$i]);
                    move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_file);
                }
            }
        }
        //
        //

        if (!$message) {
            $sku = $_POST['sku'];
            $title = $_POST['title'];
            $price = $_POST['price'];

            $description = $_POST['description'];
            $sale_price = !empty($_POST['salePrice']) ? $_POST['salePrice'] : NULL;

            $categories_arr = array_map("trim", explode(",", $_POST['categories']));
            $tags_arr = array_map("trim", explode(",", $_POST['tags']));

            //params of table Products
            $params_query = array(
                ":sku" => array(
                    "data" => $sku,
                    "type" => PDO::PARAM_STR
                ),
                ":title" => array(
                    "data" => $title,
                    "type" => PDO::PARAM_STR
                ),
                ":price" => array(
                    "data" => $price,
                    "type" => PDO::PARAM_INT
                ),
                ":sale_price" => array(
                    "data" => $sale_price,
                    "type" => PDO::PARAM_INT
                ),
                ":featured_image" => array(
                    "data" => $featuredImage,
                    "type" => PDO::PARAM_STR
                ),
                ":gallery" => array(
                    "data" => rtrim($gallery_str, ","),
                    "type" => PDO::PARAM_STR
                ),
                ":description" => array(
                    "data" => $description,
                    "type" => PDO::PARAM_STR
                ),
                ":id" => array(
                    "data" => $id,
                    "type" => PDO::PARAM_INT
                )
            );
            //

            $sql = "UPDATE `products` 
            SET `sku` = :sku , `title` = :title, `price` = :price, `sale_price` = :sale_price, `featured_image` = :featured_image, `gallery` = :gallery, `description` = :description
            WHERE `id` = :id";
            if ($this->modifyData($sql, $params_query)) {
                
                //get terms of this product then compare with present terms. If exist the different, its will be add new to DB
                $terms = $this->getTerms($id);
                $categories_current = array();
                $tags_current = array();

                foreach ($terms as $term) {
                    if ($term["type"] == 1) {
                        array_push($categories_current, $term["name"]);
                    }
                    if ($term["type"] == 2) {
                        array_push($tags_current, $term["name"]);
                    }
                }

                $compare_categories_current_w_new = array_diff($categories_current, $categories_arr);
                $compare_categories_new_w_current = array_diff($categories_arr, $categories_current);
                $compare_tags_current_w_new = array_diff($tags_current, $tags_arr);
                $compare_tags_new_w_current = array_diff($tags_arr, $tags_current);
                //
                // add categories
                if (!empty($compare_categories_new_w_current)) {
                    $this->addTermRelationship($compare_categories_new_w_current, $id, 1);
                }
                // add tags
                if (!empty($compare_tags_new_w_current)) {
                    $this->addTermRelationship($compare_tags_new_w_current, $id, 2);
                }
                // delete categories
                if (!empty($compare_categories_current_w_new)) {
                    $this->deleteTermRelationship($compare_categories_current_w_new, $id, 1);
                }
                // delete tags
                if (!empty($compare_tags_current_w_new)) {
                    $this->deleteTermRelationship($compare_tags_current_w_new, $id, 2);
                }

                $message['sucessMessage'] = "<p>Update product successful</p>";
            }
            return $message;
        } else {
            return $message;
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM `products` WHERE id = :id";
        $param = array(
            ":id" => array(
                "data" => $id,
                "type" => PDO::PARAM_INT
            )
        );
        $this->modifyData($sql, $param);

        return header('Location: ./');
    }
}
