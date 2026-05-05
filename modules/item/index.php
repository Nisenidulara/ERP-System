<?php
require_once '../../config/db.php';

$pageTitle = 'Items';
$activePage = 'item';
$rootPath = '../../';

$conn = getDBConnection();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM items WHERE id = $id");
    header('Location: index.php?msg=deleted');
    exit;
}

$search = isset($_GET['q']) ? trim($conn->real_escape_string($_GET['q'])) : '';
$where = $search ? "WHERE i.item_name LIKE '%$search%' OR i.item_code LIKE '%$search%'" : '';

$items = $conn->query("
    SELECT i.*, ic.name AS category_name, sc.name AS subcategory_name
    FROM items i
    JOIN item_categories ic ON ic.id = i.category_id
    JOIN item_subcategories sc ON sc.id = i.subcategory_id
    $where
    ORDER BY i.created_at DESC
");

$msg = $_GET['msg'] ?? '';
include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Items</h1>
        <p>Manage inventory items</p>
    </div>
    <a href="create.php" class="btn btn-primary">
        <i class="bi bi-plus-square-fill"></i> Add Item
    </a>
</div>

<?php if ($msg === 'created'): ?>
<div class="alert alert-success alert-auto-dismiss"><i class="bi bi-check-circle-fill"></i> Item added successfully.</div>
<?php elseif ($msg === 'updated'): ?>
<div class="alert alert-success alert-auto-dismiss"><i class="bi bi-check-circle-fill"></i> Item updated successfully.</div>
<?php elseif ($msg === 'deleted'): ?>
<div class="alert alert-danger alert-auto-dismiss"><i class="bi bi-trash-fill"></i> Item deleted.</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-box-seam-fill"></i> Item List</h6>
        <form class="search-bar" method="GET">
            <input type="text" class="form-control" name="q" placeholder="Search by name or code..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
            <?php if ($search): ?><a href="index.php" class="btn btn-outline-secondary">Clear</a><?php endif; ?>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Sub Category</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items->num_rows > 0):
                    $i = 1; while ($row = $items->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted mono"><?php echo $i++; ?></td>
                    <td><span class="badge bg-primary-light text-primary mono"><?php echo htmlspecialchars($row['item_code']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['subcategory_name']); ?></td>
                    <td class="text-end mono">
                        <span class="badge <?php echo $row['quantity'] < 10 ? 'bg-danger' : 'bg-success'; ?> text-white">
                            <?php echo number_format($row['quantity']); ?>
                        </span>
                    </td>
                    <td class="text-end mono">Rs.<?php echo number_format($row['unit_price'], 2); ?></td>
                    <td class="text-center">
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil-fill"></i>
                        </a>
                        <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger btn-delete">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8">
                    <div class="empty-state">
                        <i class="bi bi-box-seam"></i>
                        <p>No items found<?php echo $search ? ' matching "'.htmlspecialchars($search).'"' : ''; ?>.</p>
                        <a href="create.php" class="btn btn-primary btn-sm">Add First Item</a>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); include '../../includes/footer.php'; ?>
