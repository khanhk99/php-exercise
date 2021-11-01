<?php
include_once './Validate.php';

class Term extends Validate
{
    public function add()
    {
        $validate = new Validate();
        $message = array();

        if (isset($_POST['tagField'])) {
            foreach ($_POST['tagField'] as $item) {
                if (empty($item)) {
                    $message['errorMessage'] = "<p>Tag is required</p>";
                } else if ($validate->regexTextValidate($item)) {
                    $message['errorMessage'] = "<p>Tag can not contain special characters</p>";
                }
            }
        }
        if (isset($_POST['categoryField'])) {
            foreach ($_POST['categoryField'] as $item) {
                if (empty($item)) {
                    $message['errorMessage'] = "<p>Category is required</p>";
                } else if ($validate->regexTextValidate($item)) {
                    $message['errorMessage'] = "<p>Category can not contain special characters</p>";
                }
            }
        }

        if (!$message) {
            $sql = "INSERT INTO `terms` (`type`, `name`) VALUES(:type, :name)";
            if (isset($_POST['tagField'])) {
                $sql_unique_tag = "SELECT COUNT(id) AS count FROM `terms` WHERE (`type` = 2) AND (`name` = :data)";
                foreach ($_POST['tagField'] as $item) {
                    $param = array(
                        ":type" => array(
                            "data" => 2,
                            "type" => PDO::PARAM_INT
                        ),
                        ":name" => array(
                            "data" => $item,
                            "type" => PDO::PARAM_STR
                        )
                    );
                    if ($this->uniqueValidate($item, $sql_unique_tag)) {
                        continue;
                    } else {
                        $this->modifyData($sql, $param);
                    }
                }
            }
            if (isset($_POST['categoryField'])) {
                $sql_unique_category = "SELECT COUNT(id) AS count FROM `terms` WHERE (`type` = 1) AND (`name` = :data)";
                foreach ($_POST['categoryField'] as $item) {
                    $param = array(
                        ":type" => array(
                            "data" => 1,
                            "type" => PDO::PARAM_INT
                        ),
                        ":name" => array(
                            "data" => $item,
                            "type" => PDO::PARAM_STR
                        )
                    );
                    if ($this->uniqueValidate($item, $sql_unique_category)) {
                        continue;
                    } else {
                        $this->modifyData($sql, $param);
                    }
                }
            }

            $message['sucessMessage'] = "<p>Add new successful</p>";
            return $message;
        } else {
            return $message;
        }
    }
    
    public function getCategory(){

    }

    public function getTag(){

    }
}
