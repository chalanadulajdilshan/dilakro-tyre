<?php

class SupplierPaymentNew
{
    public $id;
    public $payment_no;
    public $supplier_id;
    public $entry_date;
    public $cash_amount;
    public $cheque_amount;
    public $total_amount;
    public $remark;
    public $created_at;

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT `id`, `payment_no`, `supplier_id`, `entry_date`, `cash_amount`, `cheque_amount`, `total_amount`, `remark`, `created_at`
                      FROM `supplier_payment_new`
                      WHERE `id` = " . (int) $id;

            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id = $result['id'];
                $this->payment_no = $result['payment_no'];
                $this->supplier_id = $result['supplier_id'];
                $this->entry_date = $result['entry_date'];
                $this->cash_amount = $result['cash_amount'];
                $this->cheque_amount = $result['cheque_amount'];
                $this->total_amount = $result['total_amount'];
                $this->remark = $result['remark'];
                $this->created_at = $result['created_at'];
            }
        }
    }

    public function create()
    {
        $query = "INSERT INTO `supplier_payment_new` (`payment_no`, `supplier_id`, `entry_date`, `cash_amount`, `cheque_amount`, `total_amount`, `remark`, `created_at`) 
                  VALUES (
                    '{$this->payment_no}', 
                    '{$this->supplier_id}', 
                    '{$this->entry_date}', 
                    '{$this->cash_amount}', 
                    '{$this->cheque_amount}', 
                    '{$this->total_amount}', 
                    '{$this->remark}', 
                    NOW()
                  )";

        $db = Database::getInstance();
        return $db->readQuery($query) ? mysqli_insert_id($db->DB_CON) : false;
    }

    public function update()
    {
        $query = "UPDATE `supplier_payment_new` 
                  SET 
                    `payment_no` = '{$this->payment_no}', 
                    `supplier_id` = '{$this->supplier_id}', 
                    `entry_date` = '{$this->entry_date}', 
                    `cash_amount` = '{$this->cash_amount}', 
                    `cheque_amount` = '{$this->cheque_amount}', 
                    `total_amount` = '{$this->total_amount}', 
                    `remark` = '{$this->remark}'
                  WHERE `id` = '{$this->id}'";

        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    public function delete()
    {
        $query = "DELETE FROM `supplier_payment_new` WHERE `id` = '{$this->id}'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }

    public function all()
    {
        $query = "SELECT `id`, `payment_no`, `supplier_id`, `entry_date`, `cash_amount`, `cheque_amount`, `total_amount`, `remark`, `created_at`
                  FROM `supplier_payment_new`
                  ORDER BY `entry_date` DESC, `id` DESC";

        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array_res = array();

        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }

        return $array_res;
    }

    public function getBySupplier($supplierId)
    {
        $query = "SELECT `id`, `payment_no`, `supplier_id`, `entry_date`, `cash_amount`, `cheque_amount`, `total_amount`, `remark`, `created_at`
                  FROM `supplier_payment_new`
                  WHERE `supplier_id` = '" . (int)$supplierId . "'
                  ORDER BY `entry_date` DESC, `id` DESC";

        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array = [];

        while ($row = mysqli_fetch_array($result)) {
            array_push($array, $row);
        }

        return $array;
    }

    public function getLastID()
    {
        $query = "SELECT * FROM `supplier_payment_new` ORDER BY `id` DESC LIMIT 1";
        $db = Database::getInstance();
        $result = mysqli_fetch_array($db->readQuery($query));

        if ($result && isset($result['id'])) {
            return $result['id'];
        }
        return 0;
    }

    public function getFiltered($supplierId = null, $startDate = null, $endDate = null)
    {
        $query = "SELECT `id`, `payment_no`, `supplier_id`, `entry_date`, `cash_amount`, `cheque_amount`, `total_amount`, `remark`, `created_at`
                  FROM `supplier_payment_new`
                  WHERE 1=1";

        if ($supplierId && $supplierId != '') {
            $query .= " AND `supplier_id` = '" . (int)$supplierId . "'";
        }

        if ($startDate && $startDate != '') {
            $query .= " AND `entry_date` >= '" . mysqli_real_escape_string(Database::getInstance()->DB_CON, $startDate) . "'";
        }

        if ($endDate && $endDate != '') {
            $query .= " AND `entry_date` <= '" . mysqli_real_escape_string(Database::getInstance()->DB_CON, $endDate) . "'";
        }

        $query .= " ORDER BY `entry_date` DESC, `id` DESC";

        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array_res = array();

        while ($row = mysqli_fetch_array($result)) {
            array_push($array_res, $row);
        }

        return $array_res;
    }
}
