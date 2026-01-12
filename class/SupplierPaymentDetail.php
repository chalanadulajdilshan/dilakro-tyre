<?php

class SupplierPaymentDetail
{
    public $id;
    public $payment_id;
    public $payment_type;
    public $amount;
    public $cheque_no;
    public $cheque_date;
    public $bank_id;
    public $branch_id;

    public function __construct($id = null)
    {
        if ($id) {
            $query = "SELECT `id`, `payment_id`, `payment_type`, `amount`, `cheque_no`, `cheque_date`, `bank_id`, `branch_id`
                      FROM `supplier_payment_detail`
                      WHERE `id` = " . (int) $id;

            $db = Database::getInstance();
            $result = mysqli_fetch_array($db->readQuery($query));

            if ($result) {
                $this->id = $result['id'];
                $this->payment_id = $result['payment_id'];
                $this->payment_type = $result['payment_type'];
                $this->amount = $result['amount'];
                $this->cheque_no = $result['cheque_no'];
                $this->cheque_date = $result['cheque_date'];
                $this->bank_id = $result['bank_id'];
                $this->branch_id = $result['branch_id'];
            }
        }
    }

    public function create()
    {
        $query = "INSERT INTO `supplier_payment_detail` (`payment_id`, `payment_type`, `amount`, `cheque_no`, `cheque_date`, `bank_id`, `branch_id`) 
                  VALUES (
                    '{$this->payment_id}', 
                    '{$this->payment_type}', 
                    '{$this->amount}', 
                    " . ($this->cheque_no ? "'{$this->cheque_no}'" : "NULL") . ", 
                    " . ($this->cheque_date ? "'{$this->cheque_date}'" : "NULL") . ", 
                    " . ($this->bank_id ? "'{$this->bank_id}'" : "NULL") . ", 
                    " . ($this->branch_id ? "'{$this->branch_id}'" : "NULL") . "
                  )";

        $db = Database::getInstance();
        return $db->readQuery($query) ? mysqli_insert_id($db->DB_CON) : false;
    }

    public function getByPaymentId($paymentId)
    {
        $query = "SELECT `id`, `payment_id`, `payment_type`, `amount`, `cheque_no`, `cheque_date`, `bank_id`, `branch_id`
                  FROM `supplier_payment_detail`
                  WHERE `payment_id` = '" . (int)$paymentId . "'
                  ORDER BY `id` ASC";

        $db = Database::getInstance();
        $result = $db->readQuery($query);
        $array = [];

        while ($row = mysqli_fetch_array($result)) {
            array_push($array, $row);
        }

        return $array;
    }

    public function deleteByPaymentId($paymentId)
    {
        $query = "DELETE FROM `supplier_payment_detail` WHERE `payment_id` = '" . (int)$paymentId . "'";
        $db = Database::getInstance();
        return $db->readQuery($query);
    }
}
