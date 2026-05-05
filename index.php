<?php
require_once 'config/db.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$rootPath = '';

$conn = getDBConnection();

// Stats
$customers = $conn->query("SELECT COUNT(*) as cnt FROM customers")->fetch_assoc()['cnt'];
$items     = $conn->query("SELECT COUNT(*) as cnt FROM items")->fetch_assoc()['cnt'];
$invoices  = $conn->query("SELECT COUNT(*) as cnt FROM invoices")->fetch_assoc()['cnt'];
$revenue   = $conn->query("SELECT COALESCE(SUM(total_amount),0) as total FROM invoices")->fetch_assoc()['total'];

// Recent invoices
$recentInvoices = $conn->query("
    SELECT i.invoice_number, i.invoice_date, i.total_amount,
           CONCAT(c.title, ' ', c.first_name, ' ', c.last_name) AS customer
    FROM invoices i
    JOIN customers c ON c.id = i.customer_id
    ORDER BY i.created_at DESC LIMIT 5
");
$conn->close();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back to Csquare ERP System</p>
    </div>
    <div class="topbar-user" style="font-size:13px;color:var(--text-muted);">
        <i class="bi bi-calendar3"></i>
        <?php echo date('l, d F Y'); ?>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo number_format($customers); ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo number_format($items); ?></div>
                <div class="stat-label">Total Items</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-receipt"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?php echo number_format($invoices); ?></div>
                <div class="stat-label">Total Invoices</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-currency-exchange"></i></div>
            <div class="stat-info">
                <div class="stat-value" style="font-size:18px;">Rs.<?php echo number_format($revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links + Recent Invoices -->
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-lightning-fill"></i> Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="modules/customer/create.php" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus-fill"></i> Add New Customer
                    </a>
                    <a href="modules/item/create.php" class="btn btn-outline-primary">
                        <i class="bi bi-plus-square-fill"></i> Add New Item
                    </a>
                    <a href="modules/reports/invoice_report.php" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Invoice Report
                    </a>
                    <a href="modules/reports/item_report.php" class="btn btn-outline-primary">
                        <i class="bi bi-bar-chart-fill"></i> Item Report
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-clock-history"></i> Recent Invoices</h6>
                <a href="modules/reports/invoice_report.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentInvoices->num_rows > 0): while ($row = $recentInvoices->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge bg-primary-light text-primary mono"><?php echo htmlspecialchars($row['invoice_number']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['customer']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['invoice_date'])); ?></td>
                            <td class="text-end mono fw-500">Rs.<?php echo number_format($row['total_amount'], 2); ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No invoices found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
