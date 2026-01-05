<?php
header('Content-Type: application/json; charset=UTF-8');

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
    
    // Connect to company_erp database and get project start date
    $erpDb = CompanyErpDatabase::getInstance();
    $projectStartDate = $erpDb->getProjectStartDate($customerId);
    
    if (!$projectStartDate) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Project start date not found for customer ID: ' . $customerId
        ]);
        exit;
    }
    
    // Calculate days until next payment
    $daysUntilPayment = $erpDb->getDaysUntilPayment($projectStartDate);
    $nextDueDate = $erpDb->getNextPaymentDueDate($projectStartDate);
    
    // Determine if we should show warning (10 days or less)
    $showWarning = ($daysUntilPayment !== null && $daysUntilPayment <= 10);
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'customer_id' => $customerId,
            'project_start_date' => $projectStartDate,
            'next_due_date' => $nextDueDate ? $nextDueDate->format('Y-m-d') : null,
            'next_due_date_formatted' => $nextDueDate ? $nextDueDate->format('F j, Y') : null,
            'days_until_payment' => $daysUntilPayment,
            'show_warning' => $showWarning
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
