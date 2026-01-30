<!doctype html>
<?php
include 'class/include.php';
include './auth.php';
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Supplier Payment History | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>
</head>

<body data-layout="horizontal" data-topbar="colored">

    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    

                    <section>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">Supplier Payment History</h4>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="supplierFilter" class="form-label">Supplier</label>
                                                <select id="supplierFilter" class="form-select">
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
                                                <label for="startDate" class="form-label">Start Date</label>
                                                <input type="date" id="startDate" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="endDate" class="form-label">End Date</label>
                                                <input type="date" id="endDate" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">&nbsp;</label>
                                                <div>
                                                    <button id="filterBtn" class="btn btn-primary w-100">
                                                        <i class="uil uil-filter"></i> Apply Filter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table id="paymentHistoryTable" class="table table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>Payment No</th>
                                                        <th>Supplier Code</th>
                                                        <th>Supplier Name</th>
                                                        <th>Entry Date</th>
                                                        <th>Cash Amount</th>
                                                        <th>Cheque Amount</th>
                                                        <th>Total Amount</th>
                                                        <th>Remark</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="paymentTableBody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

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

            <?php include 'footer.php' ?>

            <div class="rightbar-overlay"></div>

            <?php include 'main-js.php' ?>

            <script>
                jQuery(document).ready(function ($) {
                    let dataTable;

                    function loadPayments() {
                        const supplierId = $('#supplierFilter').val();
                        const startDate = $('#startDate').val();
                        const endDate = $('#endDate').val();

                        if (dataTable) {
                            dataTable.destroy();
                        }

                        $('#paymentTableBody').html('<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></td></tr>');

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
                                    if (response.data.length > 0) {
                                        response.data.forEach((payment, index) => {
                                            html += `
                                                <tr>
                                                    <td>${index + 1}</td>
                                                    <td>${payment.payment_no}</td>
                                                    <td>${payment.supplier_code}</td>
                                                    <td>${payment.supplier_name}</td>
                                                    <td>${payment.entry_date}</td>
                                                    <td class="text-end">${parseFloat(payment.cash_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                    <td class="text-end">${parseFloat(payment.cheque_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                    <td class="text-end fw-bold">${parseFloat(payment.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                                    <td>${payment.remark || ''}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info view-details" 
                                                            data-id="${payment.id}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detailsModal">
                                                            <i class="uil uil-eye"></i> View
                                                        </button>
                                                    </td>
                                                </tr>
                                            `;
                                        });
                                    } else {
                                        html = '<tr><td colspan="10" class="text-center">No payments found</td></tr>';
                                    }
                                    $('#paymentTableBody').html(html);

                                    dataTable = $('#paymentHistoryTable').DataTable({
                                        order: [[0, 'desc']],
                                        pageLength: 50
                                    });
                                } else {
                                    $('#paymentTableBody').html('<tr><td colspan="10" class="text-center text-danger">Error loading payments</td></tr>');
                                }
                            },
                            error: function() {
                                $('#paymentTableBody').html('<tr><td colspan="10" class="text-center text-danger">Error loading payments</td></tr>');
                            }
                        });
                    }

                    loadPayments();

                    $('#filterBtn').on('click', function() {
                        loadPayments();
                    });

                    $('#supplierFilter, #startDate, #endDate').on('keypress', function(e) {
                        if (e.which === 13) {
                            loadPayments();
                        }
                    });

                    $(document).on('click', '.view-details', function() {
                        const paymentId = $(this).data('id');
                        
                        $('#modalContent').html(`
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        `);

                        $.ajax({
                            url: 'ajax/php/supplier-payment-new.php',
                            type: 'POST',
                            data: { 
                                get_payment_history: true,
                                supplier_id: null
                            },
                            success: function(response) {
                                if (response.success) {
                                    const payment = response.data.find(p => p.id == paymentId);
                                    
                                    if (payment) {
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
                                                        <td class="text-end">${parseFloat(detail.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
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
                                                                <h5 class="text-success">${parseFloat(payment.cash_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <strong>Cheque Amount:</strong><br>
                                                                <h5 class="text-info">${parseFloat(payment.cheque_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <strong>Total Amount:</strong><br>
                                                                <h5 class="text-primary">${parseFloat(payment.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;

                                        $('#modalContent').html(detailsHtml);
                                    }
                                }
                            },
                            error: function() {
                                $('#modalContent').html('<div class="alert alert-danger">Failed to load payment details</div>');
                            }
                        });
                    });
                });
            </script>
</body>

</html>
