<?php
require_once '../../config/db.php';

$pageTitle = 'Item Report';
$activePage = 'rpt_item';
$rootPath = '../../';

$conn = getDBConnection();

// Item report: unique item names, category, subcategory, total quantity
$results = $conn->query("
    SELECT
        it.item_name,
        ic.name AS category_name,
        sc.name AS subcategory_name,
        SUM(it.quantity) AS total_quantity
    FROM items it
    JOIN item_categories ic ON ic.id = it.category_id
    JOIN item_subcategories sc ON sc.id = it.subcategory_id
    GROUP BY it.item_name, ic.name, sc.name
    ORDER BY ic.name, it.item_name
");

include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Item Report</h1>
        <p>Current inventory status — grouped by item name</p>
    </div>
    <button class="btn btn-outline-primary btn-print no-print"><i class="bi bi-printer-fill"></i> Print</button>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-bar-chart-fill"></i> Item Inventory Summary</h6>
        <span class="badge bg-primary-light text-primary"><?php echo $results->num_rows; ?> unique items</span>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Item Category</th>
                    <th>Item Sub Category</th>
                    <th class="text-end">Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results->num_rows > 0):
                    $i = 1;
                    $currentCat = null;
                    $rows = $results->fetch_all(MYSQLI_ASSOC);
                    foreach ($rows as $row):
                        if ($currentCat !== $row['category_name']):
                            $currentCat = $row['category_name'];
                ?>
                <tr style="background:var(--page-bg);">
                    <td colspan="5" style="font-weight:600;font-size:12px;letter-spacing:0.5px;color:var(--primary);padding:10px 16px;">
                        <i class="bi bi-folder-fill me-1"></i><?php echo htmlspecialchars($currentCat); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted"><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['subcategory_name']); ?></td>
                    <td class="text-end">
                        <span class="badge mono <?php echo $row['total_quantity'] < 10 ? 'bg-danger' : ($row['total_quantity'] < 50 ? 'bg-warning text-dark' : 'bg-success'); ?> text-white">
                            <?php echo number_format($row['total_quantity']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        <p>No items in inventory.</p>
                        <a href="../item/create.php" class="btn btn-primary btn-sm">Add First Item</a>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock level legend -->
<div class="mt-3 d-flex gap-3 no-print" style="font-size:12px;">
    <span><span class="badge bg-success me-1">■</span> Healthy stock (&ge;50)</span>
    <span><span class="badge bg-warning text-dark me-1">■</span> Low stock (10–49)</span>
    <span><span class="badge bg-danger me-1">■</span> Critical stock (&lt;10)</span>
</div>

<?php $conn->close(); include '../../includes/footer.php'; ?>
