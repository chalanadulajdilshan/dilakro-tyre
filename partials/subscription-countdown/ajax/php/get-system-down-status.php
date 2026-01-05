<?php
// Set content type first to prevent any output before headers
header('Content-Type: application/json; charset=UTF-8');

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Include main database for company_profile
require_once __DIR__ . '/../../../../class/include.php';

// Include the CompanyErpDatabase class
require_once __DIR__ . '/../../class/CompanyErpDatabase.php';

try {
    // Get the active company profile
    $mainDb = Database::getInstance();
    $query = "SELECT customer_id FROM company_profile WHERE is_active = 1 LIMIT 1";
    $result = $mainDb->readQuery($query);

    if (!$result || mysqli_num_rows($result) === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No active company profile found'
        ]);
        exit;
    }

    $companyProfile = mysqli_fetch_assoc($result);
    $customerId = $companyProfile['customer_id'];

    if (empty($customerId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No customer ID configured in company profile'
        ]);
        exit;
    }

    // Connect to company_erp database and get system down status
    $erpDb = CompanyErpDatabase::getInstance();
    $systemDownStatus = $erpDb->getSystemDownStatus($customerId);

    if ($systemDownStatus === null) {
        echo json_encode([
            'status' => 'error',
            'message' => 'System down status not found for customer ID: ' . $customerId
        ]);
        exit;
    }

    // Save system down status in session with type casting
    $_SESSION['system_down_status'] = (int)$systemDownStatus;
    $_SESSION['system_down_last_updated'] = date('Y-m-d H:i:s');

    // Ensure session is saved before sending response
    session_write_close();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'customer_id' => $customerId,
            'system_down' => $systemDownStatus,
            'session_saved' => true
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
