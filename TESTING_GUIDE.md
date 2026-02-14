# 725TRACKO Laravel - Testing Guide 🧪

## ✅ System Overview
This guide walks you through testing the complete LED Module Repair Management System.

**Project Location**: `d:\laragon\www\725ledtracko\`
**Database**: `led725co_laravel`
**Server**: Running on `http://127.0.0.1:8000`
**Original Project**: Preserved at `d:\laragon\www\725tracko\` (PHP version)

---

## 🔐 Login Credentials

Four demo accounts have been created with different roles:

```
┌─────────────────────────────────────────────────────┐
│ Admin Account                                       │
│ Username: admin          Password: admin123          │
│ Role: System Administrator (Full Access)           │
├─────────────────────────────────────────────────────┤
│ Technician Account                                  │
│ Username: technician1    Password: tech123          │
│ Role: Repair Technician                            │
├─────────────────────────────────────────────────────┤
│ QC Account                                          │
│ Username: qcagent1       Password: qc123           │
│ Role: Quality Control Inspector                    │
├─────────────────────────────────────────────────────┤
│ Reception Account                                   │
│ Username: reception1     Password: reception123     │
│ Role: Reception/Intake Staff                       │
└─────────────────────────────────────────────────────┘
```

---

## 📋 Complete Workflow Test

### ✏️ Step 1: Create a New Order (Admin)

**Login with**: `admin` / `admin123`

1. Navigate to **Orders** → **Create New Order**
2. Fill in the following:
   - **Order Name**: `Test Order - P10 Modules`
   - **Customer Phone**: `+880 1234 567890`
   - **Customer Email**: `customer@example.com`
   - **Total Modules**: `5`
   - **Notes**: `5 LED P10 RGB modules for repair`
3. Click **Create Order**
4. Note the **Order ID** shown at the bottom (e.g., `#12`)

**Expected Result**: ✅ Order created with status **"Created"**

---

### 📦 Step 2: Mark Order as Dropped Off (Admin)

1. Go to **Orders** → Find your created order
2. Click the **View** (eye icon)
3. Click **Edit** button
4. Change Status to **"Dropped off"**
5. Click **Save Changes**

**Expected Result**: ✅ Order status now shows **"Dropped off"**

---

### 📥 Step 3: Receive Modules (Reception Staff)

**Login with**: `reception1` / `reception123`

1. Navigate to **Reception**
2. You should see your order ready for reception
3. Click on the order to start receiving modules
4. You'll see:
   - Barcode input field (top-left)
   - Order summary card (right side)
   - Progress bar showing received/total modules
   - List of received modules below

**Test Module Reception** (Scan 5 modules):

For each of the 5 modules, enter a barcode in the format:
```
P10-RGB-001
P10-RGB-002
P10-RGB-003
P10-RGB-004
P10-RGB-005
```

5. For each barcode:
   - **Module Model**: `P10-RGB-32x16`
   - **Damage**: `Pixel malfunction in area 5`, `Water damage`, `Connector loose`, etc.
   - Click **Receive Module**

**Expected Result**:
- ✅ Each module appears in the "Received Modules" list
- ✅ Progress bar updates (showing 1/5, 2/5, etc.)
- ✅ "Last Barcode Scanned" box shows in purple gradient
- ✅ Form clears after each submission, ready for next scan

**After all 5 modules are received**:
- A **"Complete Reception"** button appears at the bottom
- Click it to finish reception
- The order status automatically changes to **"In Process"**

---

### 🔧 Step 4: Repair Modules (Technician)

**Login with**: `technician1` / `tech123`

1. Navigate to **Repair**
2. You'll see:
   - Barcode scanner input (left side)
   - Table of modules awaiting repair (right side)
   - Your 5 received modules should be in the table

**Mark modules as repaired**:

For each module:
1. In the table, click the **"Done"** button next to any module, OR
2. Scan the barcode in the scanner field
3. A modal dialog opens with fields:
   - **Repair Notes**: Describe what was fixed (e.g., "Replaced damaged connector")
   - **Repair Time (Minutes)**: How long it took (e.g., `30`, `45`, `60`)
4. Click **"Mark as Repaired"**

**Example repairs**:
```
Module P10-RGB-001:
  Notes: Replaced damaged LED pixels
  Time: 45 minutes

Module P10-RGB-002:
  Notes: Fixed water damage, dried and tested
  Time: 60 minutes

Module P10-RGB-003:
  Notes: Reconnected loose connector
  Time: 15 minutes

Etc...
```

**Expected Result**:
- ✅ Modules disappear from "Awaiting Repair" table
- ✅ Audit trail records each repair
- ✅ Repair time is stored for performance tracking
- ✅ Dashboard shows updated technician output

---

### ✔️ Step 5: Quality Control Inspection (QC Agent)

**Login with**: `qcagent1` / `qc123`

1. Navigate to **Quality Control**
2. You'll see a table of modules awaiting QC inspection
3. You should see your 5 repaired modules

**Inspect and Pass/Reject each module**:

For each module, you have two buttons:
- ✅ **Pass** (green checkmark): Module passes QC
- ❌ **Reject** (red X): Module fails and needs re-repair

**Scenario A: Pass some modules**
- Click ✅ on modules P10-RGB-001, P10-RGB-002, P10-RGB-004, P10-RGB-005
- These modules are now marked as "Ready for Delivery"

**Scenario B: Reject one module**
- Click ❌ on module P10-RGB-003
- Enter rejection reason: "Solder joint cracked on connector"
- Click **"Reject & Send Back"**
- This module automatically goes back to the technician's repair queue

**Expected Result**:
- ✅ Passed modules show as "Delivered" status
- ✅ QC statistics on dashboard update
- ✅ Rejected module moves back to "Awaiting Repair"
- ✅ QC pass rate displayed as percentage on dashboard

---

### 📊 Step 6: Dashboard & Analytics

**Login as**: Any user (all can see dashboard)

Go to **Dashboard** and observe:

**Statistics Cards** (4 colored cards at top):
- 🔵 **Technician Output**: Shows number of modules repaired
- 🟢 **Active Orders**: Count of orders in "In Process"
- 🟠 **Queue**: Modules awaiting repair
- 🟣 **QC Passed**: Modules ready for delivery

**Tables**:
- **Technician Output (Today)**: Shows technician names and repair count
- **QC Statistics**: Shows QC agents and their pass/fail rates

**System Overview**:
- Total Orders ever created
- Total Modules in system
- Overall QC Pass Rate percentage
- Completed Orders count

**Expected Result**:
- ✅ Dashboard shows real-time statistics
- ✅ Auto-refreshes every 10 seconds
- ✅ Numbers match your test workflow

---

## 🔌 Advanced Testing

### Audit Trail Verification
1. Login as **Admin**
2. Go to **Dashboard**
3. Verify actions appear in the system
4. Check database:
   ```sql
   SELECT * FROM user_audits ORDER BY Date DESC LIMIT 20;
   ```

### Barcode Uniqueness Test
1. Login as **Reception**
2. Try scanning the same barcode twice
3. System should show error: **"Barcode already exists"**

### Role-Based Access Control Test
1. Login as **Technician1**
2. Try accessing `/qc` (should be redirected or denied)
3. Try accessing `/reception` (should work)
4. Try accessing `/repair` (should work)

### Order Completion Test
1. Once all modules pass QC, order status automatically becomes **"Done"**
2. Completed orders appear on Dashboard under **"Completed Orders"**

---

## 📱 Testing with Barcode Scanner Device

If you have a **physical barcode scanner**:

1. Set up scanner as keyboard input mode
2. Focus on the barcode input field
3. Scan the barcode - it will auto-complete the form
4. Scanner should emit success beep or LED flash

**Scanner Settings Needed**:
- Keyboard wedge mode (not USB HID mode)
- Append Enter key after scan
- No prefix/suffix characters

---

## 🐛 Troubleshooting

### Issue: Login not working
- Check database is running: `http://127.0.0.1/phpmyadmin`
- Verify users table has data: SELECT * FROM users;
- Ensure password matches hashed value

### Issue: Pages taking too long to load
- Check server is still running: `php artisan serve`
- Verify no database query errors (check logs/laravel.log)
- Clear cache: `php artisan cache:clear`

### Issue: Audit trail not recording
- Verify user_audits table exists: DESCRIBE user_audits;
- Check user IP is being captured
- Review database for errors

### Issue: Dashboard not auto-refreshing
- Open browser console (F12)
- Check for JavaScript errors
- Verify AJAX call to /dashboard/refresh succeeds

---

## ✅ Verification Checklist

Complete this checklist to ensure system is working:

```
Login & Authentication:
  ☐ Can login with admin account
  ☐ Can login with technician account
  ☐ Can login with QC account
  ☐ Can login with reception account
  ☐ Invalid credentials show error
  ☐ Logout clears session

Order Management:
  ☐ Can create new order
  ☐ Order appears in orders list
  ☐ Can edit order details
  ☐ Can change order status
  ☐ Total modules count works
  ☐ Can view full order details

Reception Module:
  ☐ Only reception can access
  ☐ Can scan barcodes
  ☐ Duplicate barcode detection works
  ☐ Progress bar updates
  ☐ Last scanned display shows
  ☐ Complete reception button appears when done
  ☐ Order auto-transitions to "In Process"

Repair Module:
  ☐ Only technician can access
  ☐ Awaiting repair list shows
  ☐ Can mark modules as repaired
  ☐ Repair notes are saved
  ☐ Repair time is tracked
  ☐ Modules disappear from queue when complete

Quality Control:
  ☐ Only QC can access
  ☐ Awaiting QC list shows
  ☐ Can pass modules
  ☐ Can reject modules with reason
  ☐ Rejected modules go back to repair
  ☐ Pass/fail counts are accurate
  ☐ Rejected modules list accessible

Dashboard:
  ☐ All statistics display
  ☐ Cards show correct colors
  ☐ Technician output table shows
  ☐ QC statistics table shows
  ☐ Auto-refresh works every 10 seconds
  ☐ Pass rate percentage calculates

Database:
  ☐ All tables exist and have data
  ☐ Audit trail records all actions
  ☐ Foreign key relationships intact
  ☐ Barcode uniqueness enforced
```

---

## 📞 Support

For issues or questions:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Run migrations: `php artisan migrate:refresh --seed`
3. Check database connection in `.env`
4. Verify Apache/MySQL running in Laragon

---

**Last Updated**: 2025-02-14
**Laravel Version**: 11.x
**PHP Version**: 8.3+
**Database**: MySQL 8.0+

Good luck with your testing! 🚀
