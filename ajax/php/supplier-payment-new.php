<?php
include '../../class/include.php';
header('Content-Type: application/json');

if (isset($_POST['create'])) {
    
    $SUPPLIER_PAYMENT = new SupplierPaymentNew(NULL);

    $SUPPLIER_PAYMENT->payment_no   = $_POST['payment_no'];
    $SUPPLIER_PAYMENT->supplier_id  = $_POST['supplier_id'];
    $SUPPLIER_PAYMENT->entry_date   = $_POST['entry_date'];
    $SUPPLIER_PAYMENT->cash_amount  = floatval($_POST['cash_amount']);
    $SUPPLIER_PAYMENT->cheque_amount = floatval($_POST['cheque_amount']);
    $SUPPLIER_PAYMENT->total_amount = floatval($_POST['total_amount']);
    $SUPPLIER_PAYMENT->remark       = $_POST['remark'];

    $res = $SUPPLIER_PAYMENT->create();

    if ($res) {
        if (isset($_POST['cheque_details']) && !empty($_POST['cheque_details'])) {
            $chequeDetails = json_decode($_POST['cheque_details'], true);

            if (is_array($chequeDetails)) {
                foreach ($chequeDetails as $cheque) {
                    $PAYMENT_DETAIL = new SupplierPaymentDetail(null);

                    $PAYMENT_DETAIL->payment_id = $res;
                    $PAYMENT_DETAIL->payment_type = 'cheque';
                    $PAYMENT_DETAIL->amount = floatval($cheque['amount']);
                    $PAYMENT_DETAIL->cheque_no = $cheque['cheque_no'];
                    $PAYMENT_DETAIL->cheque_date = $cheque['cheque_date'];
                    $PAYMENT_DETAIL->bank_id = $cheque['bank_id'];
                    $PAYMENT_DETAIL->branch_id = $cheque['branch_id'];

                    $PAYMENT_DETAIL->create();
                }
            }
        }

        if ($SUPPLIER_PAYMENT->cash_amount > 0) {
            $PAYMENT_DETAIL = new SupplierPaymentDetail(null);
            $PAYMENT_DETAIL->payment_id = $res;
            $PAYMENT_DETAIL->payment_type = 'cash';
            $PAYMENT_DETAIL->amount = $SUPPLIER_PAYMENT->cash_amount;
            $PAYMENT_DETAIL->create();

            $SUPPLIER = new CustomerMaster($SUPPLIER_PAYMENT->supplier_id);
            
            $CASHBOOK = new Cashbook(null);
            $CASHBOOK->ref_no = $SUPPLIER_PAYMENT->payment_no;
            $CASHBOOK->transaction_type = 'withdrawal';
            $CASHBOOK->bank_id = 0;
            $CASHBOOK->branch_id = 0;
            $CASHBOOK->amount = $SUPPLIER_PAYMENT->cash_amount;
            $CASHBOOK->remark = 'Supplier Payment - ' . $SUPPLIER->name . ' - ' . $SUPPLIER_PAYMENT->payment_no . ($SUPPLIER_PAYMENT->remark ? ' - ' . $SUPPLIER_PAYMENT->remark : '');
            $CASHBOOK->create();
        }

        echo json_encode(["status" => 'success', "id" => $res]);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}

if (isset($_POST['get_payment_history'])) {
    $supplierId = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
    
    $SUPPLIER_PAYMENT = new SupplierPaymentNew(null);
    
    if ($supplierId) {
        $payments = $SUPPLIER_PAYMENT->getBySupplier($supplierId);
    } else {
        $payments = $SUPPLIER_PAYMENT->all();
    }

    $result = [];
    foreach ($payments as $payment) {
        $CUSTOMER_MASTER = new CustomerMaster($payment['supplier_id']);
        $PAYMENT_DETAIL = new SupplierPaymentDetail(null);
        $details = $PAYMENT_DETAIL->getByPaymentId($payment['id']);
        
        $result[] = [
            'id' => $payment['id'],
            'payment_no' => $payment['payment_no'],
            'supplier_code' => $CUSTOMER_MASTER->code,
            'supplier_name' => $CUSTOMER_MASTER->name,
            'entry_date' => $payment['entry_date'],
            'cash_amount' => $payment['cash_amount'],
            'cheque_amount' => $payment['cheque_amount'],
            'total_amount' => $payment['total_amount'],
            'remark' => $payment['remark'],
            'details' => $details
        ];
    }

    echo json_encode(['success' => true, 'data' => $result]);
    exit();
}

if (isset($_POST['delete']) && isset($_POST['id'])) {
    $SUPPLIER_PAYMENT = new SupplierPaymentNew($_POST['id']);
    
    if ($SUPPLIER_PAYMENT->cash_amount > 0) {
        $db = Database::getInstance();
        $query = "DELETE FROM `cashbook_transactions` WHERE `ref_no` = '{$SUPPLIER_PAYMENT->payment_no}'";
        $db->readQuery($query);
    }
    
    $PAYMENT_DETAIL = new SupplierPaymentDetail(null);
    $PAYMENT_DETAIL->deleteByPaymentId($_POST['id']);
    
    $res = $SUPPLIER_PAYMENT->delete();

    if ($res) {
        echo json_encode(["status" => 'success']);
    } else {
        echo json_encode(["status" => 'error']);
    }
    exit();
}
