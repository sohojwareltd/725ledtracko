# 725TRACKO - Laravel Conversion Guide

## ✅ সম্পন্ন কাজ

### Design Transfer
- ✅ **Original PHP Design**: সম্পূর্ণ original project থেকে exact design নেওয়া হয়েছে
- ✅ **Sidebar Layout**: Teal/cyan gradient sidebar with navigation
- ✅ **Login Page**: Original design সহ modern animations
- ✅ **CSS Framework**: complete `style.css` copied from original project
- ✅ **Typography**: Inter font family (original design maintain)
- ✅ **Color Scheme**: Exact colors preserved
  - Primary Brand: #22b3c1 (Teal)
  - Sidebar Gradient: #00d8d8 → #007f80
  - Text Main: #0f172a
  - Text Subtle: #64748b

### Project Structure
- ✅ **Laravel 11** setup with PHP 8.3
- ✅ **Database**: `led725co_laravel` (separate from PHP project)
- ✅ **Models**: User, Order, OrderDetail, UserAudit (with relationships)
- ✅ **Controllers**: Auth, Order, Reception, Repair, QC, Dashboard
- ✅ **Routes**: RESTful routes with middleware
- ✅ **Seeder**: Default users with all 4 roles
- ✅ **Migrations**: All tables created with proper schema

### Authentication & Users
- ✅ Custom User model with role-based access
- ✅ 4 Role Types:
  1. **Admin** (admin / admin123)
  2. **Technician** (technician1 / tech123)  
  3. **QC** (qcagent1 / qc123)
  4. **Reception** (reception1 / reception123)

---

## 📂 File Structure

### New Design Files
```
resources/views/
├── auth/
│   └── login-new.blade.php           ← New exact design login
└── layouts/
    └── app-new.blade.php             ← New master layout with sidebar

public/css/
├── style.css                         ← Current custom CSS
└── style-original.css                ← Complete original project CSS
```

### How to Switch to New Design

**Option 1: Update existing views**
```bash
# Replace old login with new design
cp resources/views/auth/login-new.blade.php resources/views/auth/login.blade.php

# Replace old layout with new design  
cp resources/views/layouts/app-new.blade.php resources/views/layouts/app.blade.php
```

**Option 2: Use style-original.css**
```html
<!-- In your layout, change this line from: -->
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<!-- To: -->
<link rel="stylesheet" href="{{ asset('css/style-original.css') }}">
```

---

## 🎨 Design Components

### Colors & Typography
- **Font**: Inter (Google Fonts)
- **Sidebar**: Cyan/Teal gradient (#00d8d8 → #007f80)
- **Brand Color**: #22b3c1
- **Text Colors**:
  - Main Dark: #0f172a
  - Subtle Gray: #64748b
  - Danger Red: #ef4444
  - Accent Green: #14b8a6

### CSS Variables (from original)
```css
:root {
    --page-bg: #f5f7fb;
    --surface: #ffffff;
    --brand: #22b3c1;
    --sidebar-gradient-start: #00d8d8;
    --sidebar-gradient-end: #007f80;
    --anim-duration: 0.55s;
    --anim-ease: cubic-bezier(0.25, 0.1, 0.25, 1);
}
```

---

## 🔄 Current Page Layouts

### 1. Login Page (`login-new.blade.php`)
- **Features**:
  - Floating background orbs animation
  - Brand logo (725led_repair_png3.png)
  - "TRACKO" brand pill
  - Error message display
  - Form with username & password
- **Location**: `http://127.0.0.1:8000/login`

### 2. Master Layout (`app-new.blade.php`)
- **Sidebar**: 
  - Gradient background
  - Navigation menu
  - User profile
  - Logout button
- **Main Content**:
  - Alert messages
  - Content yield area
  - Responsive design

### 3. Dashboard
- Stats cards with original design
- Technician output table
- QC statistics table

---

## 📝 Demo Accounts to Test

```
┌──────────────┬──────────────┬─────────────────────┐
│ Role         │ Username     │ Password            │
├──────────────┼──────────────┼─────────────────────┤
│ Admin        │ admin        │ admin123            │
│ Technician   │ technician1  │ tech123             │
│ QC Agent     │ qcagent1     │ qc123               │
│ Reception    │ reception1   │ reception123        │
└──────────────┴──────────────┴─────────────────────┘
```

---

## 🚀 Running the Project

### Start Server
```bash
cd d:\laragon\www\725ledtracko
php artisan serve --host=127.0.0.1 --port=8000
```

### Access Application
- **URL**: http://127.0.0.1:8000/login
- **Dashboard**: http://127.0.0.1:8000/

---

## 🎯 What Each View Should Look Like

### Original Design Files
- **Old Login**: `d:\laragon\www\725tracko\login.php` ← Reference design
- **Old Dashboard**: `d:\laragon\www\725tracko\Dashboard.php` ← Reference design
- **Original CSS**: `d:\laragon\www\725tracko\css\style.css` ← Complete styling

### Recreated in Laravel
- **New Login**: `resources/views/auth/login-new.blade.php`
- **New Layout**: `resources/views/layouts/app-new.blade.php`
- **Styling**: `public/css/style-original.css`

---

## 📋 Features Preserved from Original

✅ **Visual Design**:
- Exact colors and color scheme
- Sidebar gradient
- Typography and fonts
- Card layouts
- Table styling

✅ **Functionality**:
- Role-based access control
- Order management
- Reception module
- Repair tracking
- QC inspection
- Dashboard analytics
- Audit logging

---

## 🔧 Customization Guide

### Change Colors
Edit `public/css/style-original.css`:
```css
:root {
    --brand: #22b3c1;                    /* Change brand color */
    --sidebar-gradient-start: #00d8d8;   /* Change sidebar top */
    --sidebar-gradient-end: #007f80;     /* Change sidebar bottom */
}
```

### Change Logo
Replace image file:
```bash
# Replace with your logo
cp your-logo.png public/img/725led_repair_png3.png
```

### Modify Sidebar Items
Edit `layouts/app-new.blade.php`:
```blade
<a href="{{ route('new-route') }}" class="nav-item">
    <i class="bi bi-icon-name"></i>
    <span>New Item</span>
</a>
```

---

## ✨ Design Details You'll Notice

### Animations
- **Logo Float**: Smooth up/down floating effect
- **Background Orbs**: Pulsing gradient orbs on login page
- **Fade Up**: Cards fade up on load
- **Slide In**: Sidebar slides in on mobile

### Responsive Design
- **Desktop**: Full sidebar + content
- **Tablet**: Collapsible sidebar
- **Mobile**: Stacked layout with hamburger menu

### Interactive Elements
- Hover effects on buttons
- Color focus states on form inputs
- Smooth transitions on navigation
- Gradient effects on table rows

---

## 🔍 Testing Checklist

- [ ] Login page displays correctly
- [ ] Can log in with demo accounts
- [ ] Sidebar navigation works
- [ ] Dashboard loads properly
- [ ] All colors match original design
- [ ] Fonts are correct (Inter)
- [ ] Animations are smooth
- [ ] Responsive on mobile
- [ ] Tables display data correctly
- [ ] Forms submit properly

---

## 📞 Support

For issues or questions about the design transfer:
1. Compare with original files in `d:\laragon\www\725tracko\`
2. Check CSS file: `public/css/style-original.css`
3. Review layout: `resources/views/layouts/app-new.blade.php`

---

**Status**: ✅ Design completely transferred from original PHP project
**Date**: February 15, 2026
**Version**: Laravel 11 Conversion
