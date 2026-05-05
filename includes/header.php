<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>Csquare ERP</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $rootPath ?? ''; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
        <div class="brand-text">
            <span class="brand-name">Csquare</span>
            <span class="brand-sub">ERP System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main Menu</div>

        <a href="<?php echo $rootPath ?? ''; ?>index.php" class="nav-item <?php echo ($activePage ?? '') === 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-section-label">Modules</div>

        <a href="<?php echo $rootPath ?? ''; ?>modules/customer/index.php" class="nav-item <?php echo ($activePage ?? '') === 'customer' ? 'active' : ''; ?>">
            <i class="bi bi-people-fill"></i>
            <span>Customers</span>
        </a>

        <a href="<?php echo $rootPath ?? ''; ?>modules/item/index.php" class="nav-item <?php echo ($activePage ?? '') === 'item' ? 'active' : ''; ?>">
            <i class="bi bi-box-seam-fill"></i>
            <span>Items</span>
        </a>

        <div class="nav-section-label">Reports</div>

        <a href="<?php echo $rootPath ?? ''; ?>modules/reports/invoice_report.php" class="nav-item <?php echo ($activePage ?? '') === 'rpt_invoice' ? 'active' : ''; ?>">
            <i class="bi bi-receipt"></i>
            <span>Invoice Report</span>
        </a>

        <a href="<?php echo $rootPath ?? ''; ?>modules/reports/invoice_item_report.php" class="nav-item <?php echo ($activePage ?? '') === 'rpt_invoice_item' ? 'active' : ''; ?>">
            <i class="bi bi-card-list"></i>
            <span>Invoice Items</span>
        </a>

        <a href="<?php echo $rootPath ?? ''; ?>modules/reports/item_report.php" class="nav-item <?php echo ($activePage ?? '') === 'rpt_item' ? 'active' : ''; ?>">
            <i class="bi bi-bar-chart-fill"></i>
            <span>Item Report</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="company-info">
            <i class="bi bi-building"></i>
            <span>Csquare Fintech (Pvt) Ltd</span>
        </div>
    </div>
</div>

<!-- Top Bar -->
<div class="main-content" id="mainContent">
    <div class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title"><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard'; ?></div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="user-avatar"><i class="bi bi-person-fill"></i></div>
                <span>Admin</span>
            </div>
        </div>
    </div>
    <div class="page-content">
