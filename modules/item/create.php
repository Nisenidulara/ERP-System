<?php
require_once '../../config/db.php';

$pageTitle = 'Add Item';
$activePage = 'item';
$rootPath = '../../';

$conn = getDBConnection();
$categories = $conn->query("SELECT * FROM item_categories ORDER BY name");
$subcategories = $conn->query("SELECT * FROM item_subcategories ORDER BY name");

$errors = [];
$data = ['item_code'=>'','item_name'=>'','category_id'=>'','subcategory_id'=>'','quantity'=>'','unit_price'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['item_code']      = trim($_POST['item_code'] ?? '');
    $data['item_name']      = trim($_POST['item_name'] ?? '');
    $data['category_id']    = (int)($_POST['category_id'] ?? 0);
    $data['subcategory_id'] = (int)($_POST['subcategory_id'] ?? 0);
    $data['quantity']       = $_POST['quantity'] ?? '';
    $data['unit_price']     = $_POST['unit_price'] ?? '';

    if (empty($data['item_code'])) $errors['item_code'] = 'Item code is required.';
    else {
        $esc = $conn->real_escape_string($data['item_code']);
        $dup = $conn->query("SELECT id FROM items WHERE item_code='$esc'");
        if ($dup->num_rows > 0) $errors['item_code'] = 'Item code already exists.';
    }
    if (empty($data['item_name'])) $errors['item_name'] = 'Item name is required.';
    if ($data['category_id'] <= 0) $errors['category_id'] = 'Please select a category.';
    if ($data['subcategory_id'] <= 0) $errors['subcategory_id'] = 'Please select a sub-category.';
    if ($data['quantity'] === '' || !is_numeric($data['quantity']) || (int)$data['quantity'] < 0)
        $errors['quantity'] = 'Enter a valid quantity (0 or more).';
    if ($data['unit_price'] === '' || !is_numeric($data['unit_price']) || (float)$data['unit_price'] <= 0)
        $errors['unit_price'] = 'Enter a valid unit price greater than 0.';

    if (empty($errors)) {
        $qty   = (int)$data['quantity'];
        $price = (float)$data['unit_price'];
        $stmt  = $conn->prepare("INSERT INTO items (item_code, item_name, category_id, subcategory_id, quantity, unit_price) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('ssiiid', $data['item_code'], $data['item_name'], $data['category_id'], $data['subcategory_id'], $qty, $price);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: index.php?msg=created');
        exit;
    }
}

// Build subcategories JSON for JS
$allSubs = [];
$subcategories->data_seek(0);
while ($s = $subcategories->fetch_assoc()) $allSubs[] = $s;

include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Add Item</h1>
        <p>Register a new inventory item</p>
    </div>
    <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to List</a>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-plus-square-fill"></i> Item Information</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3"><i class="bi bi-exclamation-triangle-fill"></i> Please fix the errors below.</div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="mb-3">
                <label class="form-label">Item Code <span class="text-danger">*</span></label>
                <input type="text" name="item_code" class="form-control mono <?php echo isset($errors['item_code']) ? 'is-invalid' : ''; ?>"
                       value="<?php echo htmlspecialchars($data['item_code']); ?>" placeholder="e.g. ITM-001" required>
                <?php if (isset($errors['item_code'])): ?><div class="invalid-feedback"><?php echo $errors['item_code']; ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" name="item_name" class="form-control <?php echo isset($errors['item_name']) ? 'is-invalid' : ''; ?>"
                       value="<?php echo htmlspecialchars($data['item_name']); ?>" placeholder="Enter item name" required>
                <?php if (isset($errors['item_name'])): ?><div class="invalid-feedback"><?php echo $errors['item_name']; ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Item Category <span class="text-danger">*</span></label>
                <select name="category_id" id="categorySelect" class="form-select <?php echo isset($errors['category_id']) ? 'is-invalid' : ''; ?>" required>
                    <option value="">-- Select Category --</option>
                    <?php $categories->data_seek(0); while ($c = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $data['category_id']==$c['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <?php if (isset($errors['category_id'])): ?><div class="invalid-feedback"><?php echo $errors['category_id']; ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Item Sub Category <span class="text-danger">*</span></label>
                <select name="subcategory_id" id="subcategorySelect" class="form-select <?php echo isset($errors['subcategory_id']) ? 'is-invalid' : ''; ?>" required>
                    <option value="">-- Select Sub Category --</option>
                    <?php foreach ($allSubs as $s): ?>
                    <option value="<?php echo $s['id']; ?>"
                            data-cat="<?php echo $s['category_id']; ?>"
                            <?php echo $data['subcategory_id']==$s['id'] ? 'selected' : ''; ?>
                            style="display:none;">
                        <?php echo htmlspecialchars($s['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['subcategory_id'])): ?><div class="invalid-feedback"><?php echo $errors['subcategory_id']; ?></div><?php endif; ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control <?php echo isset($errors['quantity']) ? 'is-invalid' : ''; ?>"
                           value="<?php echo htmlspecialchars($data['quantity']); ?>" min="0" placeholder="0" required>
                    <?php if (isset($errors['quantity'])): ?><div class="invalid-feedback"><?php echo $errors['quantity']; ?></div><?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Unit Price (Rs.) <span class="text-danger">*</span></label>
                    <input type="number" name="unit_price" class="form-control <?php echo isset($errors['unit_price']) ? 'is-invalid' : ''; ?>"
                           value="<?php echo htmlspecialchars($data['unit_price']); ?>" min="0.01" step="0.01" placeholder="0.00" required>
                    <?php if (isset($errors['unit_price'])): ?><div class="invalid-feedback"><?php echo $errors['unit_price']; ?></div><?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-box-seam-fill"></i> Save Item</button>
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php
$extraJs = "<script>
const catSel = document.getElementById('categorySelect');
const subSel = document.getElementById('subcategorySelect');
const allOpts = Array.from(subSel.querySelectorAll('option[data-cat]'));

function filterSubs(catId) {
    allOpts.forEach(opt => {
        const show = opt.dataset.cat == catId;
        opt.style.display = show ? '' : 'none';
        if (!show && opt.selected) opt.selected = false;
    });
    if (!catId) { subSel.value = ''; }
}

catSel.addEventListener('change', () => filterSubs(catSel.value));
// On load if category was pre-selected (validation re-render)
if (catSel.value) filterSubs(catSel.value);
</script>";
$conn->close();
include '../../includes/footer.php';
?>
