<?php
require_once '../../config/db.php';

$pageTitle = 'Edit Customer';
$activePage = 'customer';
$rootPath = '../../';

$conn = getDBConnection();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) { header('Location: index.php'); exit; }

$districts = $conn->query("SELECT * FROM districts ORDER BY name");
$customer  = $conn->query("SELECT * FROM customers WHERE id=$id")->fetch_assoc();
if (!$customer) { header('Location: index.php'); exit; }

$errors = [];
$data = $customer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['title']          = trim($_POST['title'] ?? '');
    $data['first_name']     = trim($_POST['first_name'] ?? '');
    $data['last_name']      = trim($_POST['last_name'] ?? '');
    $data['contact_number'] = trim($_POST['contact_number'] ?? '');
    $data['district_id']    = (int)($_POST['district_id'] ?? 0);

    if (!in_array($data['title'], ['Mr','Mrs','Miss','Dr'])) $errors['title'] = 'Please select a title.';
    if (empty($data['first_name'])) $errors['first_name'] = 'First name is required.';
    elseif (!preg_match('/^[A-Za-z\s]+$/', $data['first_name'])) $errors['first_name'] = 'First name must contain only letters.';
    if (empty($data['last_name'])) $errors['last_name'] = 'Last name is required.';
    elseif (!preg_match('/^[A-Za-z\s]+$/', $data['last_name'])) $errors['last_name'] = 'Last name must contain only letters.';
    if (empty($data['contact_number'])) $errors['contact_number'] = 'Contact number is required.';
    elseif (!preg_match('/^[0-9+\-\s]{9,15}$/', $data['contact_number'])) $errors['contact_number'] = 'Enter a valid contact number (9-15 digits).';
    if ($data['district_id'] <= 0) $errors['district_id'] = 'Please select a district.';

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE customers SET title=?, first_name=?, last_name=?, contact_number=?, district_id=? WHERE id=?");
        $stmt->bind_param('ssssii', $data['title'], $data['first_name'], $data['last_name'], $data['contact_number'], $data['district_id'], $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: index.php?msg=updated');
        exit;
    }
}

include '../../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Edit Customer</h1>
        <p>Update customer record</p>
    </div>
    <a href="index.php" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-pencil-fill"></i> Edit: <?php echo htmlspecialchars($customer['title'].' '.$customer['first_name'].' '.$customer['last_name']); ?></h6>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-triangle-fill"></i> Please fix the errors below before submitting.
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <select name="title" class="form-select <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>">
                    <option value="">-- Select Title --</option>
                    <?php foreach (['Mr','Mrs','Miss','Dr'] as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo $data['title']===$t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?php echo $errors['title']; ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>"
                       value="<?php echo htmlspecialchars($data['first_name']); ?>" required>
                <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?php echo $errors['first_name']; ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>"
                       value="<?php echo htmlspecialchars($data['last_name']); ?>" required>
                <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?php echo $errors['last_name']; ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                <input type="text" name="contact_number" class="form-control <?php echo isset($errors['contact_number']) ? 'is-invalid' : ''; ?>"
                       value="<?php echo htmlspecialchars($data['contact_number']); ?>" data-type="phone" maxlength="15" required>
                <?php if (isset($errors['contact_number'])): ?><div class="invalid-feedback"><?php echo $errors['contact_number']; ?></div><?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label">District <span class="text-danger">*</span></label>
                <select name="district_id" class="form-select <?php echo isset($errors['district_id']) ? 'is-invalid' : ''; ?>">
                    <option value="">-- Select District --</option>
                    <?php $districts->data_seek(0); while ($d = $districts->fetch_assoc()): ?>
                    <option value="<?php echo $d['id']; ?>" <?php echo $data['district_id']==$d['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($d['name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <?php if (isset($errors['district_id'])): ?><div class="invalid-feedback"><?php echo $errors['district_id']; ?></div><?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Customer</button>
                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?php $conn->close(); include '../../includes/footer.php'; ?>
