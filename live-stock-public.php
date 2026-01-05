<!doctype html>
<?php
// Public live stock view (no auth)
include 'class/include.php';

// Get active company to drive theming and title
$company = new CompanyProfile();
$activeCompany = $company->getActiveCompany();
$companyId = !empty($activeCompany[0]['id']) ? (int)$activeCompany[0]['id'] : 1;
$COMPANY_PROFILE_DETAILS = new CompanyProfile($companyId);
// Expose to main-css include which expects $COMPANY_PROFILE
$COMPANY_PROFILE = $COMPANY_PROFILE_DETAILS;

// Fetch departments for the filter
$DEPARTMENT_MASTER = new DepartmentMaster();
$departments = $DEPARTMENT_MASTER->all();
?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Live Stocks | <?php echo $COMPANY_PROFILE_DETAILS->name ?> </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="<?php echo $COMPANY_PROFILE_DETAILS->name ?>" name="author" />
    <!-- include main CSS -->
    <?php include 'main-css.php' ?>
    <!-- Select2 CSS -->
    <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
</head>

<body data-layout="horizontal" data-topbar="colored">

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Live Stock</h3>
                <small class="text-muted">View current stock without logging in</small>
            </div>
            <a href="login.php" class="btn btn-outline-primary">
                <i class="mdi mdi-login me-1"></i> Back to Login
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4" id="summary-cards">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Cost</h5>
                        <h3 class="card-text" id="total-cost">Loading...</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Invoice</h5>
                        <h3 class="card-text" id="total-invoice">Loading...</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Profit %</h5>
                        <h3 class="card-text" id="profit-percentage">Loading...</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Department Filter -->
                <div class="row mb-3 align-items-end">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center" style="height: 100%;">
                            <div class="me-2">
                                <button id="exportToExcel" class="btn btn-primary me-2">
                                    <i class="fas fa-file-excel me-1"></i> Export to Excel
                                </button>
                                <button id="exportToPdf" class="btn btn-warning">
                                    <i class="fas fa-file-pdf me-1"></i> Export to PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="filter_department_id" class="form-label">Filter by Department</label>
                            <div class="input-group">
                                <select class="form-control select2" id="filter_department_id" name="filter_department_id">
                                    <option value="all" selected>Show All Departments</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department['id']; ?>">
                                            <?php echo htmlspecialchars($department['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap" id="stockTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:30px;"></th>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>Department</th>
                                <th>Category</th>
                                <th>Group</th>
                                <th>List Price</th>
                                <th>Selling Price</th>
                                <th>Quantity</th>
                                <th>Stock Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="assets/libs/select2/js/select2.min.js"></script>
    <!-- Datatables -->
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- include main js  -->
    <?php include 'main-js.php' ?>

    <!-- Public page: disable Item Master redirect by setting page id to 0 -->
    <script>
        window.ITEM_MASTER_PAGE_ID = 0;
    </script>

    <!-- Live Stock JS -->
    <script src="ajax/js/live-stock.js"></script>
    <!-- Public view overrides -->
    <script src="ajax/js/live-stock-public-view.js"></script>

</body>

</html>
