# Csquare Fintech ERP System

A full-stack ERP web application built with **PHP**, **MySQL**, **Bootstrap 5**, and **JavaScript** for managing customers, inventory items, and invoice reports.

---

## Features

- **Customer Management** — Register, view, edit, delete customers with form validation
- **Item Management** — Register items with dynamic category/subcategory selection, view list
- **Invoice Report** — Filter by date range; shows invoice number, date, customer, district, item count, and total
- **Invoice Item Report** — Detailed line-item report filtered by date range
- **Item Report** — Inventory summary grouped by category (no duplicate item names)
- Responsive sidebar layout with Bootstrap 5
- Print-friendly report pages

---

## Assumptions

1. The system uses **Sri Lanka's 25 districts** as the district options for customers.
2. Item categories and sub-categories are **pre-seeded** in the database; new ones can be added directly via SQL.
3. Invoices and invoice items are already in the database (via `database.sql` sample data). The system does **not include an invoice creation UI** — it focuses on reporting as per the assignment scope.
4. Item codes must be **unique** across all items.
5. Quantities below **10** are flagged as "critical stock" (red badge), 10–49 as "low stock" (yellow), 50+ as healthy (green).
6. The `unit_price` stored in `invoice_items` is the price **at time of invoicing** and may differ from the current item price.
7. Authentication/login is **not included** as it was not part of the assignment requirements.

---

## Tech Stack

| Layer      | Technology                     |
|------------|-------------------------------|
| Backend    | PHP 8.x                        |
| Database   | MySQL 8.x / MariaDB 10.x       |
| Frontend   | Bootstrap 5.3, Vanilla JS      |
| Fonts      | Google Fonts (Sora, JetBrains Mono) |
| Icons      | Bootstrap Icons 1.11           |

---

## Local Setup Guide

### Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 / MariaDB 10.6 or higher
- A local server: **XAMPP**, **Laragon**, or **WAMP** (recommended)

---

### Step 1 — Clone / Copy Project

```bash
# If using Git
git clone https://github.com/YOUR_USERNAME/csquare-erp.git

# Or extract the ZIP into your web root
# XAMPP: C:\xampp\htdocs\csquare-erp\
# Laragon: C:\laragon\www\csquare-erp\
```

---

### Step 2 — Import the Database

1. Start MySQL (via XAMPP Control Panel or your server).
2. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
3. Click **Import** → Choose file → Select `database.sql`
4. Click **Go**

Or via command line:
```bash
mysql -u root -p < database.sql
```

This will create the `csquare_erp` database with all tables and sample data.

---

### Step 3 — Configure Database Connection

Open `config/db.php` and update the credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Your MySQL username
define('DB_PASS', '');           // Your MySQL password
define('DB_NAME', 'csquare_erp');
```

---

### Step 4 — Run the Application

Start Apache and MySQL in XAMPP, then open:

```
http://localhost/csquare-erp/
```

---

## Project Structure

```
csquare-erp/
├── index.php                    # Dashboard
├── database.sql                 # Database schema + seed data
├── README.md
├── config/
│   └── db.php                   # Database connection
├── includes/
│   ├── header.php               # Sidebar + topbar layout
│   └── footer.php               # Scripts + closing tags
├── assets/
│   ├── css/
│   │   └── style.css            # Custom stylesheet
│   └── js/
│       └── app.js               # Custom JavaScript
└── modules/
    ├── customer/
    │   ├── index.php            # Customer list
    │   ├── create.php           # Add customer
    │   └── edit.php             # Edit customer
    ├── item/
    │   ├── index.php            # Item list
    │   ├── create.php           # Add item (with dynamic subcategory)
    │   └── edit.php             # Edit item
    └── reports/
        ├── invoice_report.php       # Invoice report (date filter)
        ├── invoice_item_report.php  # Invoice item report (date filter)
        └── item_report.php          # Item inventory report
```

---

## GitHub Repository

[https://github.com/YOUR_USERNAME/csquare-erp](https://github.com/YOUR_USERNAME/csquare-erp)

---

## Contact

Submitted for: **Software Intern — Full Stack Developer**
Company: **Csquare Fintech (Pvt) Ltd**
