<?php
include 'class/include.php';
include 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Supplier Payment Report | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <style>
        .payment-summary-card {
            border-left: 4px solid #556ee6;
        }
        
        .summary-value {
            font-size: 1.5rem;
            font-weight: 600;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #receiptContent, #receiptContent * {
                visibility: visible;
            }
            #receiptContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .modal-footer, .btn-close {
                display: none !important;
            }
        }

        .receipt-container {
            padding: 20px;
            background: white;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-section {
            margin-bottom: 20px;
        }

        .receipt-section-title {
            font-weight: bold;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .receipt-table th,
        .receipt-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .receipt-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .receipt-total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }

        .receipt-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <div id="layout-wrapper">
        <?php include 'navigation.php'; ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0">Supplier Payment Report</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form id="reportForm">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="supplierFilter" class="form-label">Supplier</label>
                                                <select id="supplierFilter" name="supplier_id" class="form-select">
                                                    <option value="">All Suppliers</option>
                                                    <?php
                                                    $CUSTOMER = new CustomerMaster(null);
                                                    $suppliers = $CUSTOMER->all();
                                                    foreach ($suppliers as $supplier) {
                                                        echo '<option value="' . $supplier['id'] . '">' . htmlspecialchars($supplier['code'] . ' - ' . $supplier['name']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <label for="startDate" class="form-label">From Date</label>
                                                <input type="date" id="startDate" name="start_date" class="form-control">
                                            </div>

                                            <div class="col-md-3">
                                                <label for="endDate" class="form-label">To Date</label>
                                                <input type="date" id="endDate" name="end_date" class="form-control">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-grid gap-2">
                                                    <button type="button" class="btn btn-primary" id="searchBtn">
                                                        <i class="mdi mdi-magnify me-1"></i> Search
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="resetBtn">
                                                    <i class="mdi mdi-refresh me-1"></i> Reset
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="setTodayBtn">
                                                    <i class="mdi mdi-calendar-today me-1"></i> Today
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info" id="setThisMonthBtn">
                                                    <i class="mdi mdi-calendar-month me-1"></i> This Month
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="summaryCards" style="display: none;">
                        <div class="col-md-4">
                            <div class="card payment-summary-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Cash Payments</h6>
                                    <div class="summary-value text-success" id="totalCash">0.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card payment-summary-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Cheque Payments</h6>
                                    <div class="summary-value text-info" id="totalCheque">0.00</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card payment-summary-card">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Payments</h6>
                                    <div class="summary-value text-primary" id="totalPayments">0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="reportTable" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Payment No</th>
                                                    <th>Type</th>
                                                    <th>Supplier Code</th>
                                                    <th>Supplier Name</th>
                                                    <th>Entry Date</th>
                                                    <th class="text-end">Cash Amount</th>
                                                    <th class="text-end">Cheque Amount</th>
                                                    <th class="text-end">Total Amount</th>
                                                    <th>Remark</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="reportTableBody">
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" class="text-end">Total:</th>
                                                    <td id="footerCash" class="text-end fw-bold text-success">0.00</td>
                                                    <td id="footerCheque" class="text-end fw-bold text-info">0.00</td>
                                                    <td id="footerTotal" class="text-end fw-bold text-primary">0.00</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'footer.php'; ?>
        </div>
    </div>

    <div id="detailsModal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="receiptModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="receiptContent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="printReceiptBtn">
                        <i class="mdi mdi-printer"></i> Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'main-js.php'; ?>
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        jQuery(document).ready(function ($) {
            let dataTable;

            function formatNumber(num) {
                return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            function loadPayments() {
                const supplierId = $('#supplierFilter').val();
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();

                if ($.fn.dataTable.isDataTable('#reportTable')) {
                    $('#reportTable').DataTable().clear().destroy();
                }
                dataTable = null;

                $('#reportTableBody').html('<tr><td colspan="11" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');

                $.ajax({
                    url: 'ajax/php/supplier-payment-new.php',
                    type: 'POST',
                    data: {
                        get_filtered_payments: true,
                        supplier_id: supplierId,
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        if (response.success) {
                            let html = '';
                            let totalCash = 0;
                            let totalCheque = 0;
                            let totalAmount = 0;

                            if (response.data.length > 0) {
                                response.data.forEach((payment, index) => {
                                    totalCash += parseFloat(payment.cash_amount);
                                    totalCheque += parseFloat(payment.cheque_amount);
                                    totalAmount += parseFloat(payment.total_amount);

                                    const paymentTypeBadge = payment.payment_type === 'payment_receipt' 
                                        ? '<span class="badge bg-success">Receipt</span>' 
                                        : '<span class="badge bg-primary">Payment</span>';

                                    html += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${payment.payment_no}</td>
                                            <td>${paymentTypeBadge}</td>
                                            <td>${payment.supplier_code}</td>
                                            <td>${payment.supplier_name}</td>
                                            <td>${payment.entry_date}</td>
                                            <td class="text-end">${formatNumber(payment.cash_amount)}</td>
                                            <td class="text-end">${formatNumber(payment.cheque_amount)}</td>
                                            <td class="text-end fw-bold">${formatNumber(payment.total_amount)}</td>
                                            <td>${payment.remark || ''}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info view-details me-1" 
                                                    data-id="${payment.id}"
                                                    data-payment-type="${payment.payment_type}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailsModal">
                                                    <i class="uil uil-eye"></i> View
                                                </button>
                                                <button class="btn btn-sm btn-primary print-receipt" 
                                                    data-id="${payment.id}"
                                                    data-payment-type="${payment.payment_type}">
                                                    <i class="mdi mdi-printer"></i> Receipt
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                });

                                $('#summaryCards').show();
                                $('#totalCash').text(formatNumber(totalCash));
                                $('#totalCheque').text(formatNumber(totalCheque));
                                $('#totalPayments').text(formatNumber(totalAmount));

                                $('#footerCash').text(formatNumber(totalCash));
                                $('#footerCheque').text(formatNumber(totalCheque));
                                $('#footerTotal').text(formatNumber(totalAmount));
                            } else {
                                html = '<tr><td colspan="11" class="text-center">No payments found</td></tr>';
                                $('#summaryCards').hide();
                                $('#footerCash').text('0.00');
                                $('#footerCheque').text('0.00');
                                $('#footerTotal').text('0.00');
                            }
                            
                            $('#reportTableBody').html(html);

                            dataTable = $('#reportTable').DataTable({
                                order: [[0, 'desc']],
                                pageLength: 50,
                                dom: 'Bfrtip',
                                buttons: [
                                    'copy', 'csv', 'excel', 'pdf', 'print'
                                ],
                                destroy: true
                            });
                        } else {
                            $('#reportTableBody').html('<tr><td colspan="11" class="text-center text-danger">Error loading payments</td></tr>');
                        }
                    },
                    error: function() {
                        $('#reportTableBody').html('<tr><td colspan="11" class="text-center text-danger">Error loading payments</td></tr>');
                    }
                });
            }

            $('#searchBtn').on('click', function() {
                loadPayments();
            });

            $('#resetBtn').on('click', function() {
                $('#supplierFilter').val('');
                $('#startDate').val('');
                $('#endDate').val('');
                $('#reportTableBody').empty();
                $('#summaryCards').hide();
                if (dataTable) {
                    dataTable.destroy();
                }
            });

            $('#setTodayBtn').on('click', function() {
                const today = new Date().toISOString().split('T')[0];
                $('#startDate').val(today);
                $('#endDate').val(today);
            });

            $('#setThisMonthBtn').on('click', function() {
                const today = new Date();
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                
                $('#startDate').val(firstDay.toISOString().split('T')[0]);
                $('#endDate').val(lastDay.toISOString().split('T')[0]);
            });

            $('#supplierFilter, #startDate, #endDate').on('keypress', function(e) {
                if (e.which === 13) {
                    loadPayments();
                }
            });

            function renderDetails(payment) {
                let detailsHtml = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Payment No:</strong> ${payment.payment_no}
                        </div>
                        <div class="col-md-6">
                            <strong>Entry Date:</strong> ${payment.entry_date}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Supplier Code:</strong> ${payment.supplier_code}
                        </div>
                        <div class="col-md-6">
                            <strong>Supplier Name:</strong> ${payment.supplier_name}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Remark:</strong> ${payment.remark || 'N/A'}
                        </div>
                    </div>
                    <hr>
                    <h5 class="mb-3">Payment Breakdown</h5>
                `;

                if (payment.details && payment.details.length > 0) {
                    detailsHtml += `
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Cheque No</th>
                                    <th>Cheque Date</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    payment.details.forEach(detail => {
                        detailsHtml += `
                            <tr>
                                <td><span class="badge ${detail.payment_type === 'cash' ? 'bg-success' : 'bg-info'}">${detail.payment_type.toUpperCase()}</span></td>
                                <td class="text-end">${formatNumber(detail.amount)}</td>
                                <td>${detail.cheque_no || '-'}</td>
                                <td>${detail.cheque_date || '-'}</td>
                            </tr>
                        `;
                    });

                    detailsHtml += `
                            </tbody>
                        </table>
                    `;
                }

                detailsHtml += `
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Cash Amount:</strong><br>
                                        <h5 class="text-success">${formatNumber(payment.cash_amount)}</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Cheque Amount:</strong><br>
                                        <h5 class="text-info">${formatNumber(payment.cheque_amount)}</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Total Amount:</strong><br>
                                        <h5 class="text-primary">${formatNumber(payment.total_amount)}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#modalContent').html(detailsHtml);
            }

            $(document).on('click', '.view-details', function() {
                const paymentIdRaw = $(this).data('id');
                const paymentType = $(this).data('payment-type');

                $('#modalContent').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `);

                if (paymentType === 'payment_receipt') {
                    const receiptId = String(paymentIdRaw).replace('receipt_', '');
                    $.ajax({
                        url: 'ajax/php/supplier-payment-new.php',
                        type: 'POST',
                        data: {
                            get_receipt_details: true,
                            receipt_id: receiptId
                        },
                        success: function(resp) {
                            if (resp.success && resp.data) {
                                renderDetails(resp.data);
                            } else {
                                $('#modalContent').html('<div class="alert alert-danger">Failed to load receipt details</div>');
                            }
                        },
                        error: function() {
                            $('#modalContent').html('<div class="alert alert-danger">Failed to load receipt details</div>');
                        }
                    });
                } else {
                    $.ajax({
                        url: 'ajax/php/supplier-payment-new.php',
                        type: 'POST',
                        data: { 
                            get_payment_history: true,
                            supplier_id: null
                        },
                        success: function(response) {
                            if (response.success) {
                                const payment = response.data.find(p => String(p.id) === String(paymentIdRaw));
                                if (payment) {
                                    renderDetails(payment);
                                } else {
                                    $('#modalContent').html('<div class="alert alert-danger">Payment not found</div>');
                                }
                            } else {
                                $('#modalContent').html('<div class="alert alert-danger">Failed to load payment details</div>');
                            }
                        },
                        error: function() {
                            $('#modalContent').html('<div class="alert alert-danger">Failed to load payment details</div>');
                        }
                    });
                }
            });

            $(document).on('click', '.print-receipt', function() {
                const paymentIdRaw = $(this).data('id');
                const paymentType = $(this).data('payment-type');
                
                $('#receiptContent').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `);

                $('#receiptModal').modal('show');

                const buildReceipt = (payment, supplier) => {
                    let supplierDetails = '';
                    if (supplier) {
                        supplierDetails = `
                            <div class="receipt-section">
                                <div class="receipt-section-title">Supplier Details</div>
                                <div class="receipt-info-row">
                                    <span><strong>Supplier Code:</strong></span>
                                    <span>${supplier.code || 'N/A'}</span>
                                </div>
                                <div class="receipt-info-row">
                                    <span><strong>Supplier Name:</strong></span>
                                    <span>${supplier.name || 'N/A'}</span>
                                </div>
                                <div class="receipt-info-row">
                                    <span><strong>Address:</strong></span>
                                    <span>${supplier.address || 'N/A'}</span>
                                </div>
                                <div class="receipt-info-row">
                                    <span><strong>Mobile:</strong></span>
                                    <span>${supplier.mobile_number || 'N/A'}</span>
                                </div>
                                ${supplier.email ? `
                                <div class="receipt-info-row">
                                    <span><strong>Email:</strong></span>
                                    <span>${supplier.email}</span>
                                </div>
                                ` : ''}
                            </div>
                        `;
                    }

                    let receiptHtml = `
                        <div class="receipt-container">
                            <div class="receipt-header">
                                <div class="receipt-title"><?php echo $COMPANY_PROFILE_DETAILS->name ?></div>
                                <div><?php echo $COMPANY_PROFILE_DETAILS->address ?? '' ?></div>
                                <div>Tel: <?php echo $COMPANY_PROFILE_DETAILS->mobile_number ?? '' ?></div>
                                <div style="margin-top: 10px; font-size: 18px; font-weight: bold;">PAYMENT RECEIPT</div>
                            </div>

                            ${supplierDetails}

                            <div class="receipt-section">
                                <div class="receipt-section-title">Payment Information</div>
                                <div class="receipt-info-row">
                                    <span><strong>Payment No:</strong></span>
                                    <span>${payment.payment_no}</span>
                                </div>
                                <div class="receipt-info-row">
                                    <span><strong>Payment Date:</strong></span>
                                    <span>${payment.entry_date}</span>
                                </div>
                                ${payment.remark ? `
                                <div class="receipt-info-row">
                                    <span><strong>Remark:</strong></span>
                                    <span>${payment.remark}</span>
                                </div>
                                ` : ''}
                            </div>

                            <div class="receipt-section">
                                <div class="receipt-section-title">Payment Details</div>
                    `;

                    if (payment.details && payment.details.length > 0) {
                        receiptHtml += `
                            <table class="receipt-table">
                                <thead>
                                    <tr>
                                        <th>Payment Type</th>
                                        <th>Amount</th>
                                        <th>Cheque No</th>
                                        <th>Cheque Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        payment.details.forEach(detail => {
                            receiptHtml += `
                                <tr>
                                    <td>${detail.payment_type.toUpperCase()}</td>
                                    <td style="text-align: right;">${formatNumber(detail.amount)}</td>
                                    <td>${detail.cheque_no || '-'}</td>
                                    <td>${detail.cheque_date || '-'}</td>
                                </tr>
                            `;
                        });

                        receiptHtml += `
                                </tbody>
                            </table>
                        `;
                    }

                    receiptHtml += `
                            </div>

                            <div class="receipt-section">
                                <table class="receipt-table">
                                    <tr>
                                        <td><strong>Cash Amount:</strong></td>
                                        <td style="text-align: right;">${formatNumber(payment.cash_amount)}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cheque Amount:</strong></td>
                                        <td style="text-align: right;">${formatNumber(payment.cheque_amount)}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="receipt-total">
                                Total Amount Paid: ${formatNumber(payment.total_amount)}
                            </div>

                            <div class="receipt-footer">
                                <p>Thank you for your business!</p>
                                <p>This is a computer-generated receipt.</p>
                                <p>Printed on: ${new Date().toLocaleString()}</p>
                            </div>
                        </div>
                    `;

                    $('#receiptContent').html(receiptHtml);
                };

                const loadSupplierAndRender = (payment) => {
                    $.ajax({
                        url: 'ajax/php/supplier-payment-new.php',
                        type: 'POST',
                        data: {
                            get_supplier_details: true,
                            supplier_id: payment.supplier_id || null
                        },
                        success: function(supplierResponse) {
                            const supplier = supplierResponse.success ? supplierResponse.data : null;
                            buildReceipt(payment, supplier);
                        },
                        error: function() {
                            buildReceipt(payment, null);
                        }
                    });
                };

                if (paymentType === 'payment_receipt') {
                    const receiptId = String(paymentIdRaw).replace('receipt_', '');
                    $.ajax({
                        url: 'ajax/php/supplier-payment-new.php',
                        type: 'POST',
                        data: {
                            get_receipt_details: true,
                            receipt_id: receiptId
                        },
                        success: function(resp) {
                            if (resp.success && resp.data) {
                                loadSupplierAndRender(resp.data);
                            } else {
                                $('#receiptContent').html('<div class="alert alert-danger">Failed to load receipt details</div>');
                            }
                        },
                        error: function() {
                            $('#receiptContent').html('<div class="alert alert-danger">Failed to load receipt details</div>');
                        }
                    });
                } else {
                    $.ajax({
                        url: 'ajax/php/supplier-payment-new.php',
                        type: 'POST',
                        data: { 
                            get_payment_history: true,
                            supplier_id: null
                        },
                        success: function(response) {
                            if (response.success) {
                                const payment = response.data.find(p => String(p.id) === String(paymentIdRaw));
                                if (payment) {
                                    loadSupplierAndRender(payment);
                                } else {
                                    $('#receiptContent').html('<div class="alert alert-danger">Payment not found</div>');
                                }
                            } else {
                                $('#receiptContent').html('<div class="alert alert-danger">Failed to load payment details</div>');
                            }
                        },
                        error: function() {
                            $('#receiptContent').html('<div class="alert alert-danger">Failed to load payment details</div>');
                        }
                    });
                }
            });

            $('#printReceiptBtn').on('click', function() {
                window.print();
            });
        });
    </script>
</body>

</html>
