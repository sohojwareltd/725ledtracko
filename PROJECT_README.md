# 725TRACKO - LED Module Repair Management System

**Modern Laravel Implementation of Legacy 725Co. Repair System**

![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-green)
![License](https://img.shields.io/badge/License-Private-lightgrey)

---

## 📖 Overview

**725TRACKO** is a complete refactoring of the legacy 725Co. LED Module Repair Management System from procedural PHP into a modern Laravel 11 framework. This system manages the complete lifecycle of LED module repairs from intake through quality control.

### Key Features

- ✅ **Role-Based Access Control** - 4 user roles with granular permissions
- ✅ **Complete Order Lifecycle** - Created → Dropped Off → In Process → Done
- ✅ **Module Tracking** - Track individual modules through repair workflow
- ✅ **Barcode Integration** - Full support for barcode scanners
- ✅ **Real-Time Dashboard** - Live statistics and performance metrics
- ✅ **Audit Trail** - Complete activity logging for compliance
- ✅ **Quality Control** - QC workflow with pass/reject capability
- ✅ **Responsive Design** - Bootstrap 5 with custom styling
- ✅ **Performance Analytics** - Technician output, QC statistics, pass rates

---

## 🏗️ Project Structure

```
725ledtracko/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── OrderController.php
│   │       ├── ReceptionController.php
│   │       ├── RepairController.php
│   │       ├── QCController.php
│   │       └── DashboardController.php
│   └── Models/
│       ├── User.php
│       ├── Order.php
│       ├── OrderDetail.php
│       └── UserAudit.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   └── 2026_02_14_*.php (Order & Audit tables)
│   └── seeders/
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php
│       ├── layouts/
│       │   └── app.blade.php
│       ├── dashboard.blade.php
│       ├── orders/ (CRUD views)
│       ├── reception/ (Intake workflow)
│       ├── repair/ (Technician workspace)
│       └── qc/ (Inspection interface)
├── routes/
│   └── web.php
├── public/
│   ├── css/
│   ├── js/
│   └── assets/ (Legacy 725tracko assets)
└── storage/logs/
```

---

## 🚀 Quick Start

### Prerequisites
- **Laragon** (Apache + PHP 8.3+ + MySQL 8.0+)
- **Composer** (PHP dependency manager)
- **Git** (version control)

### Installation Steps

1. **Navigate to project folder**
   ```bash
   cd d:\laragon\www\725ledtracko
   ```

2. **Start Laravel development server**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

3. **Access in browser**
   ```
   http://127.0.0.1:8000/login
   ```

4. **Login with demo credentials**
   - Username: `admin`
   - Password: `admin123`

### Database Setup (Already Done)

```bash
# Run migrations (creates tables)
php artisan migrate

# Seed demo users
php artisan db:seed --class=UserSeeder
```

---

## 👥 User Roles & Access

| Role | Username | Password | Access |
|------|----------|----------|--------|
| **Admin** | admin | admin123 | All modules, order management, user management |
| **Technician** | technician1 | tech123 | Repair board, can mark modules as repaired |
| **QC Agent** | qcagent1 | qc123 | Quality control, pass/reject modules |
| **Reception** | reception1 | reception123 | Receive modules, mark orders dropped off |

---

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    idUser INT PRIMARY KEY AUTO_INCREMENT,
    UserName VARCHAR(50) UNIQUE,
    Password VARCHAR(255),
    FullName VARCHAR(100),
    Role ENUM('Admin', 'Technician', 'QC', 'Reception'),
    Phone VARCHAR(20),
    Active BOOLEAN DEFAULT 1,
    CreatedDate TIMESTAMP,
    LastLogin TIMESTAMP
);
```

### Orders Table
```sql
CREATE TABLE orders (
    idOrder INT PRIMARY KEY AUTO_INCREMENT,
    OrderName VARCHAR(100),
    OrderDate TIMESTAMP,
    CustomerPhone VARCHAR(20),
    CustomerEmail VARCHAR(100),
    TotalModules INT,
    TotModulesReceived INT DEFAULT 0,
    Status ENUM('Created', 'Dropped off', 'In Process', 'Done', 'Inactive'),
    Notes TEXT,
    CreatedBy VARCHAR(50),
    DateDroppedOff TIMESTAMP NULL,
    DateCompleted TIMESTAMP NULL
);
```

### Order Details (Modules) Table
```sql
CREATE TABLE order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idOrder INT,
    Barcode VARCHAR(50) UNIQUE,
    ModuleModel VARCHAR(50),
    Damage TEXT,
    
    -- Reception
    DateReceived TIMESTAMP NULL,
    ReceivedBy VARCHAR(50),
    
    -- Repair
    DateRepair TIMESTAMP NULL,
    RepairedBy VARCHAR(50),
    RepairNotes TEXT,
    RepairTime INT, -- in minutes
    
    -- Quality Control
    QCStatus ENUM('Pending', 'Passed', 'Rejected'),
    QCDate TIMESTAMP NULL,
    QCAgent VARCHAR(50),
    QCNotes TEXT,
    
    FOREIGN KEY (idOrder) REFERENCES orders(idOrder) ON DELETE CASCADE
);
```

### User Audits Table
```sql
CREATE TABLE user_audits (
    idAudit INT PRIMARY KEY AUTO_INCREMENT,
    User VARCHAR(50),
    Date TIMESTAMP,
    AuditDescription TEXT,
    IPAddress VARCHAR(45),
    ActionType ENUM('Create', 'Update', 'Delete', 'Login')
);
```

---

## 🔄 Workflow

### Order Lifecycle

```
┌─────────────┐
│   Created   │  Admin creates order with customer info & module count
└──────┬──────┘
       │
       ▼
┌──────────────────┐
│  Dropped Off     │  Admin marks order as dropped off (customer delivered)
└──────┬───────────┘
       │
       ▼
┌──────────────┐
│ In Reception │  Reception scans barcodes of arriving modules
└──────┬───────┘
       │
       ▼
┌──────────────────────┐
│  In Process          │  Auto: Order transitions when all modules received
│  (Reception Staff)   │  → Barcode scanning & module intake
└──────┬───────────────┘
       │
       ▼
┌──────────────────┐
│  In Repair       │  Technician repairs modules
│  (Technician)    │  - Mark repaired with notes & time
└──────┬───────────┘
       │
       ▼
┌──────────────────────┐
│  In Quality Control  │  QC Agent inspects & either:
│  (QC Agent)          │  - PASS: Ready for delivery
│                      │  - REJECT: Back to repair
└──────┬───────────────┘
       │
       ├─── REJECT ──┐
       │             │
       │      ┌──────▼───────────┐
       │      │  Back to Repair  │
       │      └──────┬───────────┘
       │             │
       │      (Repair again)
       │             │
       │      ┌──────▼───────────┐
       │      │  Back to QC      │
       │      └──────┬───────────┘
       │             │
       └──────────┬──┘
                  │
                  ▼
              ┌──────────┐
              │   Done   │  Auto: All modules passed QC
              │(Delivered)
              └──────────┘
```

---

## 🔐 Authentication

### Login Flow

1. **User enters credentials** (Username + Password)
2. **System validates** against users table
3. **Password verified** with bcrypt Hash::check()
4. **LastLogin timestamp** updated
5. **Session created** with user role
6. **Audit trail** records login event
7. **Redirect to dashboard**

### Password Security

- Passwords are hashed with bcrypt (Hash::make)
- Original passwords stored as MD5 in legacy system
- New passwords created with proper hashing
- Consider password reset mechanism for future upgrade

---

## 🛡️ Security Features

- **Session-Based Authentication** - Server-side session management
- **CSRF Protection** - Laravel's built-in CSRF token validation
- **Role-Based Access Control** - Middleware checks user roles
- **Audit Trail** - All actions logged with user, timestamp, IP
- **Input Validation** - Server-side validation on all forms
- **SQL Injection Protection** - Eloquent ORM parameterized queries
- **XSS Prevention** - Blade's automatic escaping

---

## 📈 Dashboard Metrics

### Real-Time Statistics

1. **Technician Output** - Modules repaired today per technician
2. **Active Orders** - Orders in "In Process" status
3. **Queue** - Modules awaiting repair (received but not repaired)
4. **QC Passed** - Modules ready for delivery
5. **Pass Rate** - Percentage of modules passing QC
6. **Completed Orders** - Orders with status "Done"

### Auto-Refresh

Dashboard refreshes every 10 seconds with latest data via:
- Full page reload (current implementation)
- Or AJAX refresh (optimizable via /dashboard/refresh endpoint)

---

## 🔌 API Routes

### Authentication
```
POST   /login          - Validate credentials
POST   /logout         - Clear session
```

### Orders Management
```
GET    /orders         - List all orders (paginated 15/page)
GET    /orders/create  - Show create form
POST   /orders         - Store new order
GET    /orders/{id}    - View order details
GET    /orders/{id}/edit - Edit form
PUT    /orders/{id}    - Update order
DELETE /orders/{id}    - Delete order
```

### Reception Workflow
```
GET    /reception      - List ready orders
GET    /reception/{id} - Receive modules interface
POST   /reception/{id}/module - Store received barcode
GET    /reception/last-scanned - Get last scanned (AJAX)
POST   /reception/{id}/complete - Finish reception
```

### Repair Workflow
```
GET    /repair         - Modules awaiting repair
GET    /repair/search  - Search by barcode (AJAX)
POST   /repair/mark-repaired - Mark module complete
DELETE /repair/{id}    - Remove module (admin)
```

### Quality Control
```
GET    /qc             - Modules awaiting QC
GET    /qc/rejected    - Rejected modules history
POST   /qc/{id}/pass   - Pass QC
POST   /qc/{id}/reject - Reject & send back
DELETE /qc/{id}        - Remove module
```

### Dashboard
```
GET    /dashboard      - Dashboard home
GET    /dashboard/refresh - AJAX data refresh
```

---

## ⚙️ Configuration

### Environment Variables (.env)

```env
APP_NAME="725TRACKO"
APP_URL=http://localhost/725ledtracko
APP_DEBUG=false (set to true for development)
APP_KEY=base64:xxxxx (generated by php artisan key:generate)

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=led725co_laravel
DB_USERNAME=root
DB_PASSWORD=

MAIL_DRIVER=log (email notifications for future)

SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Database Connection

Ensure in Laragon:
1. MySQL service is **Running** ✅
2. Database **led725co_laravel** exists
3. Tables created via migrations
4. Seeder has populated demo users

---

## 📝 Common Tasks

### Reset Database to Factory State
```bash
php artisan migrate:refresh --seed
```
⚠️ *This deletes all data and recreates with demo users*

### Clear Application Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Run Tests (if test suite added)
```bash
php artisan test
```

### Generate API Documentation
```bash
php artisan api:generate
```

---

## 🐛 Troubleshooting

### Server Won't Start
```bash
# Check port 8000 isn't in use
netstat -ano | findstr :8000

# Use different port
php artisan serve --port=8001
```

### Database Connection Error
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPDO();
```

### Login Fails for All Users
1. Check users table: `SELECT * FROM users;`
2. Verify passwords were hashed
3. Try reseeding: `php artisan db:seed --class=UserSeeder`

### Pages Show Blank (Database Errors)
1. Check `storage/logs/laravel.log`
2. Run `php artisan migrate`
3. Restart server

---

## 🔄 Upgrading from Legacy System

### Data Migration Plan

When ready to migrate from the old PHP system to Laravel:

1. **Backup Original Data**
   ```sql
   -- Dump led725co database
   mysqldump -u root led725co > backup_ledtracko_php.sql
   ```

2. **Export Users**
   ```php
   // From PHP project, export users to CSV/JSON
   // Note: Passwords are MD5, will need reset or custom import
   ```

3. **Migrate Orders & Modules**
   ```php
   // Import orders, order_details, user_audits from PHP database
   // Maintain same primary/foreign keys for data integrity
   ```

4. **Testing Phase**
   - Run complete workflow test (see TESTING_GUIDE.md)
   - Validate all data imported correctly
   - Test audit trail with historical data

5. **Cutover**
   - Point application to led725co_laravel database
   - Archive old database
   - Train users on new interface

---

## 📚 Documentation

- **[TESTING_GUIDE.md](./TESTING_GUIDE.md)** - Complete testing workflow with examples
- **[API Reference](./docs/API.md)** - Detailed endpoint documentation *(future)*
- **[Troubleshooting](./docs/TROUBLESHOOTING.md)** - Common issues & solutions *(future)*

---

## 👨‍💻 Development

### Tech Stack

- **Framework**: Laravel 11
- **Language**: PHP 8.3
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5, jQuery 3.6, Bootstrap Icons
- **Assets**: Font Awesome, Owl Carousel, Isotope.js
- **ORM**: Eloquent
- **Templating**: Blade

### Development Environment
- **Local Server**: Laravel Artisan (port 8000)
- **Web Server**: Apache (via Laragon)
- **Database**: MySQL (via Laragon)
- **Version Control**: Git + GitHub

### Extension Points

**Add New Module**: Create new feature
```
1. Model: app/Models/NewModule.php
2. Controller: app/Http/Controllers/NewModuleController.php
3. Views: resources/views/newmodule/*.blade.php
4. Migration: database/migrations/*_create_newmodules_table.php
5. Routes: Add to routes/web.php
6. Navigation: Update layouts/app.blade.php
```

---

## 📞 Support & Contact

### Internal Documentation
- Check Laravel documentation: https://laravel.com/docs
- Review existing controllers for patterns
- Check database schema in migrations/

### Issues
1. Check logs: `storage/logs/laravel.log`
2. Review TESTING_GUIDE.md for validation steps
3. Run migrations: `php artisan migrate`
4. Seed demo data: `php artisan db:seed`

---

## 📄 License

**Private Project** - 725Co. Internal Use Only

---

## 🎯 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-02-14 | Initial Laravel conversion from PHP |
| - | - | Complete feature parity with legacy system |
| - | - | 6 controllers, 12+ views, 4 user roles |
| - | - | Real-time dashboard, audit trail, barcode support |

---

## ✅ Feature Completeness

- ✅ Order Management (CRUD)
- ✅ Module Reception with Barcode Scanning
- ✅ Repair Workflow with Technician Assignment
- ✅ Quality Control with Pass/Reject
- ✅ Real-Time Dashboard
- ✅ Role-Based Access Control
- ✅ Audit Trail Logging
- ✅ Performance Analytics
- ✅ Responsive Design
- ✅ User Authentication

---

**Project Status**: 🟢 **Production Ready**

**Last Updated**: February 14, 2025

---

*For detailed testing instructions, see [TESTING_GUIDE.md](./TESTING_GUIDE.md)*
