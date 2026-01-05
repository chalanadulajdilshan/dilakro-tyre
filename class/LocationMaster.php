<?php

class LocationMaster
{

    public $id;
    public $code;
    public $name;
    public $is_active;

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT * FROM `location_master` WHERE `id` = " . (int) $id;
            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id = $result['id'];
                $this->code = $result['code'];
                $this->name = $result['name'];
                $this->is_active = $result['is_active'];
            }
        }
    }

    public function create()
    {
        $query = "INSERT INTO `location_master` (
            `code`, `name`,`is_active`
        ) VALUES (
            '$this->code', '$this->name','$this->is_active'
        )";

        $db = Database::getInstance();
        $result = $db->readQuery($query);

        if ($result) {
            return mysqli_insert_id($db->DB_CON);
        } else {
            return false;
        }
    }

    public function update()
    {
        $query = "UPDATE `location_master` SET 
            `code` = '$this->code', 
            `name` = '$this->name',  
            `is_active` = '$this->is_active'
            WHERE `id` = '$this->id'";
 

        $db = Database::getInstance();
        $result = $db->readQuery($query);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delete()
    {
        $query = "DELETE FROM `location_master` WHERE `id` = '$this->id'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    public function all()
    {
        $query = "SELECT * FROM `location_master` ORDER BY name ASC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);

        $array_res = array();
        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }

        return $array_res;
    }

    public function getLastID()
    {
        $query = "SELECT * FROM `location_master` ORDER BY `id` DESC LIMIT 1";
        $db = Database::getInstance();
        $result = mysqli_fetch_array($db->readQuery($query));
        return $result['id'];
    }

    public function getActiveLocation()
    {
        $query = "SELECT * FROM `location_master` WHERE `is_active` = 1 ORDER BY `id` ASC";
        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array = [];

        while ($row = mysqli_fetch_array($result)) {
            array_push($array, $row);
        }

        return $array;
    }

}

?>
