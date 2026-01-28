<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'filter') {
    $date    = isset($_POST['date']) ? trim($_POST['date']) : null;
    $date_to = isset($_POST['date_to']) ? trim($_POST['date_to']) : null;
    $checkType = isset($_POST['check_type']) ? trim($_POST['check_type']) : 'all';

    $customerChecks = [];
    $supplierChecks = [];

    if ($checkType === 'all' || $checkType === 'customer') {
        $PAYMENT_RECEIPT = new PaymentReceiptMethod(null);
        $customerChecks = $PAYMENT_RECEIPT->getByDateRange($date, $date_to);
        foreach ($customerChecks as $key => $check) {
            $customerChecks[$key]['type'] = 'Customer';
        }
    }

    if ($checkType === 'all' || $checkType === 'supplier') {
        $PAYMENT_RECEIPT_SUPPLIER = new PaymentReceiptMethodSupplier(null);
        $supplierChecks = $PAYMENT_RECEIPT_SUPPLIER->getByDateRange($date, $date_to);
    }

    $checks = array_merge($customerChecks, $supplierChecks);

    // Sort checks by date (descending)
    usort($checks, function($a, $b) {
        return strtotime($b['cheq_date']) - strtotime($a['cheq_date']);
    });



    echo json_encode([
        'status'  => 'success',
        'date'    => $date,
        'date_to' => $date_to,
        'checks'  => $checks,
    ]);
    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Invalid request',
]);
exit;

?>