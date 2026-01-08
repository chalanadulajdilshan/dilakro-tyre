<div class="modal fade" id="dagModel" tabindex="-1" role="dialog" aria-labelledby="dagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dagModalLabel">Select DAG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table id="dagTable" class="table table-bordered table-hover dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ref No</th>
                            <th>Customer</th>
                            <th>Tyre Size</th>
                            <th>Brand</th>
                            <th>Serial No</th>
                            <th>Received Date</th>
                            <th>Customer Request</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $DAG = new DAG(null);
                        foreach ($DAG->printStatus(0) as $key => $dag) {
                            $key++;
                            $CUSTOMER = new CustomerMaster($dag['customer_id']);
                            
                            // Get first DAG item for size, brand, and serial number
                            $DAG_ITEM_MODEL = new DagItem(null);
                            $dag_items = $DAG_ITEM_MODEL->getByDagId($dag['id']);
                            $first_item = !empty($dag_items) ? $dag_items[0] : null;
                            
                            $tyre_size = '';
                            $brand_name = '';
                            $serial_no = '';
                            
                            if ($first_item) {
                                if (!empty($first_item['size_id'])) {
                                    $SIZE = new Sizes($first_item['size_id']);
                                    $tyre_size = $SIZE->name ?? '';
                                }
                                if (!empty($first_item['brand_id'])) {
                                    $BRAND = new Brand($first_item['brand_id']);
                                    $brand_name = $BRAND->name ?? '';
                                }
                                $serial_no = $first_item['serial_number'] ?? '';
                            }
                        
                            ?>

                            <tr class="select-dag" data-id="<?= $dag['id'] ?>"
                                data-ref_no="<?= htmlspecialchars($dag['ref_no']) ?>"
                                data-department_id="<?= $dag['department_id'] ?>"
                                data-customer_id="<?= $dag['customer_id'] ?>" data-customer_code="<?= $CUSTOMER->code ?>"
                                data-customer_name="<?= $CUSTOMER->name ?>"
                                data-vehicle_no="<?= htmlspecialchars($dag['vehicle_no'] ?? '') ?>"
                                data-received_date="<?= $dag['received_date'] ?>"
                                data-delivery_date="<?= $dag['delivery_date'] ?>"
                                data-customer_request_date="<?= $dag['customer_request_date'] ?>"
                                data-dag_company_id="<?= $dag['dag_company_id'] ?? '' ?>"
                                data-company_issued_date="<?= $dag['company_issued_date'] ?? '' ?>"
                                data-company_delivery_date="<?= $dag['company_delivery_date'] ?? '' ?>"
                                data-receipt_no="<?= $dag['receipt_no'] ?? '' ?>"
                                data-remark="<?= htmlspecialchars($dag['remark'] ?? '') ?>" data-status="<?= $dag['status'] ?? '' ?>">


                                <td><?= $key ?></td>
                                <td><?= htmlspecialchars($dag['ref_no']) ?></td>
                                <td><?= htmlspecialchars($CUSTOMER->name) ?></td>
                                <td><?= htmlspecialchars($tyre_size) ?></td>
                                <td><?= htmlspecialchars($brand_name) ?></td>
                                <td><?= htmlspecialchars($serial_no) ?></td>
                                <td><?= htmlspecialchars($dag['received_date']) ?></td>
                                <td><?= htmlspecialchars($dag['customer_request_date']) ?></td>


                                <?php
                                $status = htmlspecialchars($dag['status'] ?? '');
                                $label = '';
                                $bgClass = '';

                                switch ($status) {
                                    case 'pending':
                                        $label = 'Pending';
                                        $bgClass = 'bg-soft-warning'; // yellow
                                        break;
                                    case 'assigned':
                                        $label = 'Assigned';
                                        $bgClass = 'bg-soft-primary'; // blue
                                        break;
                                    case 'received':
                                        $label = 'Received';
                                        $bgClass = 'bg-soft-success'; // green
                                        break;
                                    case 'rejected_company':
                                        $label = 'Rejected by Company';
                                        $bgClass = 'bg-soft-danger'; // red
                                        break;
                                    case 'rejected_store':
                                        $label = 'Rejected by Store';
                                        $bgClass = 'bg-soft-danger'; // red
                                        break;
                                    default:
                                        $label = ucfirst($status); // fallback
                                        $bgClass = 'bg-soft-secondary'; // gray
                                        break;
                                }
                                ?>


                                <td>
                                    <span class="badge <?php echo $bgClass; ?> font-size-12">
                                        <?php echo $label; ?>
                                    </span>

                                </td>



                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>