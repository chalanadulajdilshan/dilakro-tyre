<!doctype html>
<?php
include 'class/include.php';
include './auth.php';

$SUPPLIER_PAYMENT_NEW = new SupplierPaymentNew(null);
$lastId = $SUPPLIER_PAYMENT_NEW->getLastID();
$payment_no = $COMPANY_PROFILE_DETAILS->company_code . '/SPY/00/0' . ($lastId + 1);

?>

<html lang="en">

<head>

    <meta charset="utf-8" />
    <title> Supplier Payment | <?php echo $COMPANY_PROFILE_DETAILS->name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <?php include 'main-css.php' ?>

    <style>
        .btn-danger {
            color: #fff;
            background-color: #f46a6a !important;
            border-color: #f46a6a;
            padding: 6px !important;
            margin: 4px !important;
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="colored" class="someBlock">

    <div id="layout-wrapper">

        <?php include 'navigation.php' ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-8 d-flex align-items-center flex-wrap gap-2">
                            <a href="#" class="btn btn-success" id="new">
                                <i class="uil uil-plus me-1"></i> New
                            </a>

                            <?php if ($PERMISSIONS['add_page']): ?>
                                <a href="#" class="btn btn-primary" id="create">
                                    <i class="uil uil-save me-1"></i> Save
                                </a>
                            <?php endif; ?>

                            <a href="supplier-payment-history.php" class="btn btn-info">
                                <i class="uil uil-history me-1"></i> Payment History
                            </a>
                        </div>

                        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
                            <ol class="breadcrumb m-0 justify-content-md-end">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active"> Supplier Payment </li>
                            </ol>
                        </div>
                    </div>

                    <section>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="p-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-xs">
                                                    <div
                                                        class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        01
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="font-size-16 mb-1">Supplier Payment </h5>
                                                <p class="text-muted text-truncate mb-0">Fill all information below to
                                                    make supplier payment </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <i class="mdi mdi-chevron-up accor-down-icon font-size-24"></i>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="p-4">
                                        <form id="form-data" autocomplete="off">
                                            <div class="row">
                                                <input type="hidden" id="supplier_id">

                                                <div class="col-md-3">
                                                    <label for="payment_no" class="form-label">Payment No</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="payment_no" name="payment_no"
                                                            value="<?php echo $payment_no ?>"
                                                            class="form-control" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="supplierCode" class="form-label">Supplier Code</label>
                                                    <div class="input-group mb-3">
                                                        <input id="supplier_code" name="supplier_code" type="text"
                                                            placeholder="Supplier code" class="form-control" readonly>
                                                        <button class="btn btn-info" type="button" id="supplierModalBtn"
                                                            data-bs-toggle="modal" data-bs-target="#supplierModal">
                                                            <i class="uil uil-search me-1"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="supplierName" class="form-label">Supplier Name</label>
                                                    <div class="input-group mb-3">
                                                        <input id="supplier_name" name="supplier_name" type="text"
                                                            class="form-control" placeholder="Enter Supplier Name"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="entry_date" class="form-label">Entry Date</label>
                                                    <div class="input-group" id="datepicker2">
                                                        <input type="text" class="form-control date-picker"
                                                            id="entry_date" name="entry_date"> <span
                                                            class="input-group-text"><i
                                                                class="mdi mdi-calendar"></i></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="remark" class="form-label"># Enter Remark</label>
                                                    <div class="input-group mb-3">
                                                        <input id="remark" name="remark" type="text"
                                                            class="form-control" placeholder="Enter Remark">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="cash_amount" class="form-label text-danger fw-bold">Cash Amount</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control border-danger text-danger" id="cash_amount"
                                                            placeholder="Enter Cash Amount" name="cash_amount" min="0"
                                                            step="0.01" value="0">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <label for="total_amount" class="form-label text-success fw-bold">Total Amount</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control border-success text-success fw-bold" id="total_amount"
                                                            placeholder="Total Amount" name="total_amount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="card w-100 h-100">
                                    <div class="p-4">
                                        <form id="form-data-cheque" autocomplete="off">
                                            <div class="row">
                                                <div class="row align-items-center mb-3">
                                                    <div class="col-md-6">
                                                        <h5 class="mb-0">Add Cheque Details</h5>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <div class="d-inline-flex align-items-center">
                                                            <label for="cheque_total"
                                                                class="form-label me-2 mb-0 text-danger"
                                                                style="white-space: nowrap;">Cheque Total:</label>
                                                            <input id="cheque_total" name="cheque_total" type="text"
                                                                placeholder="Cheque Total Amount" class="form-control"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row align-items-end">
                                                    <div class="col-md-2">
                                                        <label for="cheque_no" class="form-label">Cheque
                                                            No</label>
                                                        <div class="input-group">
                                                            <input id="cheque_no" type="text" class="form-control"
                                                                placeholder="No">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label for="cheque_date" class="form-label">Cheque
                                                            Date</label>
                                                        <div class="input-group" id="datepicker2">
                                                            <input type="text" class="form-control date-picker"
                                                                id="cheque_date" name="cheque_date"
                                                                placeholder="Cheque Date">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="bank_branch" class="form-label">Bank &
                                                            Branch</label>
                                                        <div class="input-group">
                                                            <input type="hidden" id="bank_branch">
                                                            <input type="hidden" id="bank_id">
                                                            <input id="bank_branch_name" type="text"
                                                                class="form-control" placeholder="Bank & Branch"
                                                                readonly>
                                                            <button class="btn btn-info" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#branch_master">
                                                                <i class="uil uil-search me-1"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" id="amount" class="form-control"
                                                            placeholder="Amount" step="0.01" min="0">
                                                    </div>

                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-success  "
                                                            id="add_cheque">Add</button>
                                                    </div>
                                                </div>

                                                <div class="table-responsive mt-4">
                                                    <table class="table table-bordered" id="chequeBody">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Cheque No</th>
                                                                <th>Cheque Date</th>
                                                                <th>Bank & Branch</th>
                                                                <th>Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="chequeBody">
                                                            <tr id="noItemRow">
                                                                <td colspan="5" class="text-center text-muted">No
                                                                    items added</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>

            <div class="modal fade " id="branch_master" tabindex="-1" role="dialog"
                aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myExtraLargeModalLabel">Manage Bank Branches</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">

                                    <table id="datatable" class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#id</th>
                                                <th>Bank</th>
                                                <th>Branch</th>
                                                <th>Address</th>
                                                <th>Phone Number</th>
                                                <th>City</th>
                                                <th>Status</th>

                                            </tr>
                                        </thead>


                                        <tbody>
                                            <?php
                                            $BRANCH = new Branch(null);
                                            foreach ($BRANCH->getByStatus(1) as $key => $branch) {
                                                $key++;
                                                $BANK = new Bank($branch['bank_id']);
                                            ?>
                                                <tr class="select-branch" data-id="<?php echo $branch['id']; ?>"
                                                    data-bankid="<?php echo $branch['bank_id']; ?>"
                                                    data-code="<?php echo htmlspecialchars($branch['code']); ?>"
                                                    data-name="<?php echo htmlspecialchars($branch['name']); ?>"
                                                    data-address="<?php echo htmlspecialchars($branch['address']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($branch['phone_number']); ?>"
                                                    data-city="<?php echo htmlspecialchars($branch['city']); ?>"
                                                    data-active="<?php echo $branch['active_status']; ?>">

                                                    <td><?php echo $key; ?></td>
                                                    <td><?php echo htmlspecialchars($BANK->code . ' - ' . $BANK->name); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($branch['code'] . ' - ' . $branch['name']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($branch['address']); ?></td>
                                                    <td><?php echo htmlspecialchars($branch['phone_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($branch['city']); ?></td>
                                                    <td>
                                                        <?php if ($branch['active_status'] == 1): ?>
                                                            <span class="badge bg-soft-success font-size-12">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-soft-danger font-size-12">Inactive</span>
                                                        <?php endif; ?>
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
            </div>

            <div id="supplierModal" class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalLabel">Select Supplier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <table id="supplierTable" class="table table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#ID</th>
                                                <th>Code</th>
                                                <th>Name</th>
                                                <th>Mobile Number</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'footer.php' ?>

            <div class="rightbar-overlay"></div>

            <?php include 'main-js.php' ?>

            <script src="ajax/js/supplier-payment-new.js"></script>
</body>

</html>
