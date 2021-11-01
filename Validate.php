<?php
include_once './DBconnect.php';

class Validate extends DBconnection
{
    public function imageValidate($data)
    {
        $image_type_allow = array('jpeg', 'jpg', 'png', 'gif');
        if(!in_array(pathinfo($data['name'])['extension'], $image_type_allow)){
            return "You have to upload image";
        }
        if ($data['size'] > 2000000) {
            return "Image can't be larger than 2MB";
        }
    }

    public function uniqueValidate($data, $sql)
    {
        $params = array(
            ":data" => array(
                "data" => $data,
                "type" => PDO::PARAM_STR
            )
        );
        
        $count_data = $this->executeParam($sql, $params);
        if ($count_data[0]['count'] > 0) {
            return $count_data[0]['ID'];
        } else {
            return false;
        }
    }

    public function regexTextValidate($data)
    {
        if (!preg_match('/[A-Za-z0-9-]+$/', $data) && !empty($data)) {
            return true;
        } else {
            return false;
        }
    }
}
