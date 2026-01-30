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

if (isset($_POST['get_receipt_details'])) {
    $receiptId = isset($_POST['receipt_id']) && $_POST['receipt_id'] !== '' ? (int)$_POST['receipt_id'] : null;

    if ($receiptId) {
        $PAYMENT_RECEIPT = new PaymentReceiptSupplier($receiptId);

        if ($PAYMENT_RECEIPT->id) {
            $CUSTOMER_MASTER = new CustomerMaster($PAYMENT_RECEIPT->customer_id);
            $PAYMENT_METHOD = new PaymentReceiptMethodSupplier(null);
            $methods = $PAYMENT_METHOD->getByReceipt($receiptId);

            $details = [];
            $cashAmount = 0;
            $chequeAmount = 0;

            foreach ($methods as $method) {
                if ($method['payment_type_id'] == 1) {
                    $cashAmount += floatval($method['amount']);
                } else if ($method['payment_type_id'] == 2) {
                    $chequeAmount += floatval($method['amount']);
                }

                $details[] = [
                    'payment_type' => $method['payment_type_id'] == 1 ? 'cash' : 'cheque',
                    'amount' => $method['amount'],
                    'cheque_no' => $method['cheq_no'],
                    'cheque_date' => $method['cheq_date']
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $PAYMENT_RECEIPT->id,
                    'payment_no' => $PAYMENT_RECEIPT->receipt_no,
                    'supplier_id' => $PAYMENT_RECEIPT->customer_id,
                    'supplier_code' => $CUSTOMER_MASTER->code,
                    'supplier_name' => $CUSTOMER_MASTER->name,
                    'entry_date' => $PAYMENT_RECEIPT->entry_date,
                    'cash_amount' => $cashAmount,
                    'cheque_amount' => $chequeAmount,
                    'total_amount' => $PAYMENT_RECEIPT->amount_paid,
                    'remark' => $PAYMENT_RECEIPT->remark,
                    'details' => $details,
                    'supplier_address' => $CUSTOMER_MASTER->address,
                    'supplier_mobile' => $CUSTOMER_MASTER->mobile_number,
                    'supplier_email' => $CUSTOMER_MASTER->email
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Receipt not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Receipt ID required']);
    }
    exit();
}

if (isset($_POST['get_filtered_payments'])) {
    $supplierId = isset($_POST['supplier_id']) && $_POST['supplier_id'] !== '' ? (int)$_POST['supplier_id'] : null;
    $startDate = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : null;
    $endDate = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : null;
    
    $result = [];
    
    // Get SupplierPaymentNew records
    $SUPPLIER_PAYMENT = new SupplierPaymentNew(null);
    $payments = $SUPPLIER_PAYMENT->getFiltered($supplierId, $startDate, $endDate);

    foreach ($payments as $payment) {
        $CUSTOMER_MASTER = new CustomerMaster($payment['supplier_id']);
        $PAYMENT_DETAIL = new SupplierPaymentDetail(null);
        $details = $PAYMENT_DETAIL->getByPaymentId($payment['id']);
        
        $result[] = [
            'id' => $payment['id'],
            'payment_no' => $payment['payment_no'],
            'supplier_id' => $payment['supplier_id'],
            'supplier_code' => $CUSTOMER_MASTER->code,
            'supplier_name' => $CUSTOMER_MASTER->name,
            'entry_date' => $payment['entry_date'],
            'cash_amount' => $payment['cash_amount'],
            'cheque_amount' => $payment['cheque_amount'],
            'total_amount' => $payment['total_amount'],
            'remark' => $payment['remark'],
            'details' => $details,
            'payment_type' => 'payment_new'
        ];
    }

    // Get PaymentReceiptSupplier records
    $PAYMENT_RECEIPT_SUPPLIER = new PaymentReceiptSupplier(null);
    $receipts = $PAYMENT_RECEIPT_SUPPLIER->getFiltered($supplierId, $startDate, $endDate);

    foreach ($receipts as $receipt) {
        $CUSTOMER_MASTER = new CustomerMaster($receipt['customer_id']);
        $PAYMENT_METHOD = new PaymentReceiptMethodSupplier(null);
        $methods = $PAYMENT_METHOD->getByReceipt($receipt['id']);
        
        // Calculate cash and cheque amounts
        $cashAmount = 0;
        $chequeAmount = 0;
        $details = [];
        
        foreach ($methods as $method) {
            if ($method['payment_type_id'] == 1) { // Cash
                $cashAmount += floatval($method['amount']);
            } else if ($method['payment_type_id'] == 2) { // Cheque
                $chequeAmount += floatval($method['amount']);
            }
            
            $details[] = [
                'payment_type' => $method['payment_type_id'] == 1 ? 'cash' : 'cheque',
                'amount' => $method['amount'],
                'cheque_no' => $method['cheq_no'],
                'cheque_date' => $method['cheq_date']
            ];
        }
        
        $result[] = [
            'id' => 'receipt_' . $receipt['id'],
            'payment_no' => $receipt['receipt_no'],
            'supplier_id' => $receipt['customer_id'],
            'supplier_code' => $CUSTOMER_MASTER->code,
            'supplier_name' => $CUSTOMER_MASTER->name,
            'entry_date' => $receipt['entry_date'],
            'cash_amount' => $cashAmount,
            'cheque_amount' => $chequeAmount,
            'total_amount' => $receipt['amount_paid'],
            'remark' => $receipt['remark'],
            'details' => $details,
            'payment_type' => 'payment_receipt'
        ];
    }

    // Sort by date descending
    usort($result, function($a, $b) {
        return strtotime($b['entry_date']) - strtotime($a['entry_date']);
    });

    echo json_encode(['success' => true, 'data' => $result]);
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

if (isset($_POST['get_supplier_details'])) {
    $supplierId = isset($_POST['supplier_id']) && $_POST['supplier_id'] !== '' ? (int)$_POST['supplier_id'] : null;
    
    if ($supplierId) {
        $SUPPLIER = new CustomerMaster($supplierId);
        
        if ($SUPPLIER->id) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $SUPPLIER->id,
                    'code' => $SUPPLIER->code,
                    'name' => $SUPPLIER->name,
                    'address' => $SUPPLIER->address,
                    'mobile_number' => $SUPPLIER->mobile_number,
                    'mobile_number_2' => $SUPPLIER->mobile_number_2,
                    'email' => $SUPPLIER->email,
                    'contact_person' => $SUPPLIER->contact_person,
                    'contact_person_number' => $SUPPLIER->contact_person_number
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Supplier not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Supplier ID required']);
    }
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
