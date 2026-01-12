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
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <?php $pageQuery = isset($_GET['page_id']) ? '?page_id=' . (int) $_GET['page_id'] : ''; ?>
                            <a href="supplier-payment-new.php<?php echo $pageQuery; ?>" id="new-supplier-payment" class="btn btn-success">
                                <i class="uil uil-plus me-1"></i> New Payment
                            </a>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Supplier Payment History</li>
                            </ol>
                        </div>
                    </div>

                    <section>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">Supplier Payment History</h4>
                                        
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
                                                <tbody>
                                                    <?php
                                                    $SUPPLIER_PAYMENT = new SupplierPaymentNew(null);
                                                    $payments = $SUPPLIER_PAYMENT->all();
                                                    
                                                    foreach ($payments as $key => $payment) {
                                                        $key++;
                                                        $SUPPLIER = new CustomerMaster($payment['supplier_id']);
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $key; ?></td>
                                                            <td><?php echo htmlspecialchars($payment['payment_no']); ?></td>
                                                            <td><?php echo htmlspecialchars($SUPPLIER->code); ?></td>
                                                            <td><?php echo htmlspecialchars($SUPPLIER->name); ?></td>
                                                            <td><?php echo htmlspecialchars($payment['entry_date']); ?></td>
                                                            <td class="text-end"><?php echo number_format($payment['cash_amount'], 2); ?></td>
                                                            <td class="text-end"><?php echo number_format($payment['cheque_amount'], 2); ?></td>
                                                            <td class="text-end fw-bold"><?php echo number_format($payment['total_amount'], 2); ?></td>
                                                            <td><?php echo htmlspecialchars($payment['remark']); ?></td>
                                                            <td>
                                                                <button class="btn btn-sm btn-info view-details" 
                                                                    data-id="<?php echo $payment['id']; ?>"
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#detailsModal">
                                                                    <i class="uil uil-eye"></i> View
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
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
                    $('#paymentHistoryTable').DataTable({
                        order: [[0, 'desc']],
                        pageLength: 50
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
