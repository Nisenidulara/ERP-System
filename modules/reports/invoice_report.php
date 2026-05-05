<?php
require_once '../../config/db.php';

$pageTitle = 'Invoice Report';
$activePage = 'rpt_invoice';
$rootPath = '../../';

$conn = getDBConnection();

$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo   = $_GET['date_to']   ?? date('Y-m-d');
$searched = isset($_GET['search']);

$results = null;
$totalAmount = 0;

if ($searched) {
    $from = $conn->real_escape_string($dateFrom);
    $to   = $conn->real_escape_string($dateTo);

    $results = $conn->query("
        SELECT
            i.invoice_number,
            i.invoice_date,
            CONCAT(c.title, ' ', c.first_name, ' ', c.last_name) AS customer_name,
            d.name AS customer_district,
            COUNT(ii.id) AS item_count,
            i.total_amount
        FROM invoices i
        JOIN customers c ON c.id = i.customer_id
        JOIN districts d ON d.id  = c.district_id
        LEFT JOIN invoice_items ii ON ii.invoice_id = i.id
        WHERE i.invoice_date BETWEEN '$from' AND '$to'
        GROUP BY i.id
        ORDER BY i.invoice_date DESC, i.invoice_number
    ");

    $sumRes  = $conn->query("SELECT SUM(total_amount) as total FROM invoices WHERE invoice_date BETWEEN '$from' AND '$to'");
    $totalAmount = $sumRes->fetch_assoc()['total'] ?? 0;
}

include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Invoice Report</h1>
        <p>Filter invoices by date range</p>
    </div>
    <?php if ($results && $results->num_rows > 0): ?>
    <button class="btn btn-outline-primary btn-print no-print"><i class="bi bi-printer-fill"></i> Print</button>
    <?php endif; ?>
</div>

<!-- Filter Form -->
<div class="card mb-4 no-print">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-funnel-fill"></i> Search Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($dateFrom); ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($dateTo); ?>" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="search" value="1" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($searched): ?>
<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-receipt"></i> Results
            <span style="font-size:12px;font-weight:400;color:var(--text-muted);margin-left:8px;">
                <?php echo date('d M Y', strtotime($dateFrom)); ?> &mdash; <?php echo date('d M Y', strtotime($dateTo)); ?>
            </span>
        </h6>
        <?php if ($results && $results->num_rows > 0): ?>
        <div class="mono" style="font-size:14px;font-weight:600;color:var(--primary);">
            Total: Rs.<?php echo number_format($totalAmount, 2); ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>District</th>
                    <th class="text-center">Item Count</th>
                    <th class="text-end">Invoice Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results && $results->num_rows > 0):
                    $i = 1; while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted"><?php echo $i++; ?></td>
                    <td><span class="badge bg-primary-light text-primary mono"><?php echo htmlspecialchars($row['invoice_number']); ?></span></td>
                    <td><?php echo date('d M Y', strtotime($row['invoice_date'])); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['customer_district']); ?></td>
                    <td class="text-center">
                        <span class="badge bg-success text-white"><?php echo (int)$row['item_count']; ?></span>
                    </td>
                    <td class="text-end mono fw-600">Rs.<?php echo number_format($row['invoice_amount'] ?? $row['total_amount'], 2); ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-receipt"></i>
                        <p>No invoices found for the selected date range.</p>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
            <?php if ($results && $results->num_rows > 0): ?>
            <tfoot>
                <tr style="background:var(--page-bg);">
                    <td colspan="6" class="text-end fw-600 py-3">Grand Total:</td>
                    <td class="text-end mono fw-700 py-3" style="color:var(--primary);">Rs.<?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $conn->close(); include '../../includes/footer.php'; ?>
