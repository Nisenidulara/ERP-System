<?php
require_once '../../config/db.php';

$pageTitle = 'Invoice Item Report';
$activePage = 'rpt_invoice_item';
$rootPath = '../../';

$conn = getDBConnection();

$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo   = $_GET['date_to']   ?? date('Y-m-d');
$searched = isset($_GET['search']);

$results = null;

if ($searched) {
    $from = $conn->real_escape_string($dateFrom);
    $to   = $conn->real_escape_string($dateTo);

    $results = $conn->query("
        SELECT
            inv.invoice_number,
            inv.invoice_date,
            CONCAT(c.title, ' ', c.first_name, ' ', c.last_name) AS customer_name,
            CONCAT(it.item_name, ' (', it.item_code, ')') AS item_detail,
            ic.name AS item_category,
            ii.unit_price
        FROM invoice_items ii
        JOIN invoices inv ON inv.id = ii.invoice_id
        JOIN customers c ON c.id  = inv.customer_id
        JOIN items it ON it.id = ii.item_id
        JOIN item_categories ic ON ic.id = it.category_id
        WHERE inv.invoice_date BETWEEN '$from' AND '$to'
        ORDER BY inv.invoice_date DESC, inv.invoice_number, it.item_name
    ");
}

include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Invoice Item Report</h1>
        <p>Detailed line-item view per invoice</p>
    </div>
    <?php if ($results && $results->num_rows > 0): ?>
    <button class="btn btn-outline-primary btn-print no-print"><i class="bi bi-printer-fill"></i> Print</button>
    <?php endif; ?>
</div>

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
        <h6 class="card-title"><i class="bi bi-card-list"></i> Invoice Item Details
            <span style="font-size:12px;font-weight:400;color:var(--text-muted);margin-left:8px;">
                <?php echo date('d M Y', strtotime($dateFrom)); ?> &mdash; <?php echo date('d M Y', strtotime($dateTo)); ?>
            </span>
        </h6>
        <?php if ($results && $results->num_rows > 0): ?>
        <span class="badge bg-primary-light text-primary"><?php echo $results->num_rows; ?> records</span>
        <?php endif; ?>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Customer Name</th>
                    <th>Item (Code)</th>
                    <th>Category</th>
                    <th class="text-end">Unit Price</th>
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
                    <td><?php echo htmlspecialchars($row['item_detail']); ?></td>
                    <td><span class="badge" style="background:var(--primary-light);color:var(--primary);"><?php echo htmlspecialchars($row['item_category']); ?></span></td>
                    <td class="text-end mono">Rs.<?php echo number_format($row['unit_price'], 2); ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-card-list"></i>
                        <p>No invoice items found for the selected date range.</p>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $conn->close(); include '../../includes/footer.php'; ?>
