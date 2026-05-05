<?php
require_once '../../config/db.php';

$pageTitle = 'Customers';
$activePage = 'customer';
$rootPath = '../../';

$conn = getDBConnection();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM customers WHERE id = $id");
    header('Location: index.php?msg=deleted');
    exit;
}

// Search
$search = isset($_GET['q']) ? trim($conn->real_escape_string($_GET['q'])) : '';
$where = $search ? "WHERE c.first_name LIKE '%$search%' OR c.last_name LIKE '%$search%' OR c.contact_number LIKE '%$search%'" : '';

$customers = $conn->query("
    SELECT c.*, d.name AS district_name
    FROM customers c
    JOIN districts d ON d.id = c.district_id
    $where
    ORDER BY c.created_at DESC
");

$msg = $_GET['msg'] ?? '';
include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Customers</h1>
        <p>Manage your customer records</p>
    </div>
    <a href="create.php" class="btn btn-primary">
        <i class="bi bi-person-plus-fill"></i> Add Customer
    </a>
</div>

<?php if ($msg === 'created'): ?>
<div class="alert alert-success alert-auto-dismiss"><i class="bi bi-check-circle-fill"></i> Customer added successfully.</div>
<?php elseif ($msg === 'updated'): ?>
<div class="alert alert-success alert-auto-dismiss"><i class="bi bi-check-circle-fill"></i> Customer updated successfully.</div>
<?php elseif ($msg === 'deleted'): ?>
<div class="alert alert-danger alert-auto-dismiss"><i class="bi bi-trash-fill"></i> Customer deleted.</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-people-fill"></i> Customer List</h6>
        <form class="search-bar" method="GET" action="">
            <input type="text" class="form-control" name="q" placeholder="Search by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
            <?php if ($search): ?>
            <a href="index.php" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Contact</th>
                    <th>District</th>
                    <th>Registered</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($customers->num_rows > 0):
                    $i = 1; while ($row = $customers->fetch_assoc()): ?>
                <tr>
                    <td class="text-muted mono"><?php echo $i++; ?></td>
                    <td><span class="badge bg-primary-light text-primary"><?php echo htmlspecialchars($row['title']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td class="mono"><?php echo htmlspecialchars($row['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                    <td class="text-muted"><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
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
                        <i class="bi bi-people"></i>
                        <p>No customers found<?php echo $search ? ' matching "' . htmlspecialchars($search) . '"' : ''; ?>.</p>
                        <a href="create.php" class="btn btn-primary btn-sm">Add First Customer</a>
                    </div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); include '../../includes/footer.php'; ?>
