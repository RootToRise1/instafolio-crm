## Goal

Build an HR module for a Perfex CRM system with:
1. **Dashboard Widgets** - Attendance (clock in/out) and Pending Leave Requests widgets
2. **Employee Page** - Match exactly the Customers page design and functionality with list view, profile view/edit, and HR-specific tabs

## Instructions

- HR menu should be a parent in the sidebar with child menu items
- Employee should be a child under HR Setup in the Setup menu
- All HR pages must use proper CRM theme/layout
- Module should follow existing CRM module structure pattern (Perfex CRM pattern)
- Employee page design must match exactly the Customers page structure
- Dashboard widgets should be first position on dashboard (before other widgets)
- CSS must be in HR module assets (not hardcoded)
- Widget design should be smart, compact, with big bold buttons
- Both widgets should have same height and width
- No gap between Dashboard Options button and HR widgets
- Current time must display properly in Attendance widget
- Revert any "HR as default module" changes

## Discoveries

1. **Project is CodeIgniter-based** CRM running on XAMPP with MySQL (`instafoliodbase`)
2. **Database tables exist** with `tblhr_` prefix (e.g., `tblhr_attendance`, `tblhr_shifts`, etc.)
3. **Dashboard widgets use** `simple_html_dom.php` library to parse widgets (strips script tags)
4. **Sidebar menu uses** `metisMenu` jQuery plugin for collapsible menus
5. **CSS in widget views doesn't load properly** - must be in separate CSS file
6. **Dashboard widgets are cached** - need to reset `tbluser_meta` for all users
7. **Staff meta stored in** `tbluser_meta` table with columns `staff_id`, `meta_key`, `meta_value`
8. **Attendance uses** `checkin`/`checkout` columns (not `clock_in`/`clock_out`)
9. **Widget containers need to be registered** in `hr_get_dashboard_widgets()` function
10. **CSS loads via hook** using `hooks()->add_action('app_admin_head', 'hr_load_css')`
11. **Table configuration** uses `data_tables_init()` function with `aColumns`, `sIndexColumn`, `sTable`, `join`, `where` arrays
12. **Ambiguous column issue** - `active` column exists in multiple tables, must use full prefixes like `tblstaff.active`
13. **DataTables initialization** requires exact match between view headers and table columns
14. **Staff table** has columns: `department_id`, `designation_id`, `hr_role_id`, `manager_id`, `hr_shift_id`, `employment_type`, `salary`, `date_of_joining`, `website`
15. **HR tables exist**: `tblhr_departments`, `tblhr_designations`, `tblhr_roles`, `tblhr_shifts`, `tblhr_leave_requests`, `tblhr_attendance`, etc.
16. **Column alias detection** - Using `preg_match('/\s+as\s+/i', $_field)` instead of `strpos()` to correctly detect SQL aliases
17. **Database prefix** - Perfex uses `tbl` as database prefix (not empty string)

## Accomplished

### HR Module Core ✅
- Created HR module structure (`modules/hr/hr.php`, controllers, models, views)
- Created database tables via `install.php`
- Menu configured with HR at position 6 (after Customers)
- Employee management with role assignment
- Attendance clock in/out with break support
- Leave management (request, approve, reject)
- Dashboard widgets positioned first on dashboard

### Dashboard Widgets ✅
- CSS moved to `modules/hr/assets/css/hr_widgets.css`
- CSS loads via module hook (deactivates if module uninstalled)
- Compact design with reduced sizes
- Live clock display (moved to dashboard_js.php to work around simple_html_dom stripping scripts)
- Both widgets have matching heights
- Pending Leaves widget height matches Attendance widget
- Gap between widgets increased to 20px total
- Widget-dragger uses `position: absolute` with transparent background

### Employee Page Structure ✅
- Created `/application/views/admin/hr/employees.php` - List view with summary stats
- Created `/application/views/admin/hr/employee.php` - Profile page with tabs layout
- Created `/application/views/admin/hr/tabs.php` - Tab navigation (Profile, Attendance, Leave)
- Created `/application/views/admin/hr/groups/profile.php` - Profile form with horizontal tabs (Basic Info, Org Structure, Employment Details, Social & Permissions)
- Created `/application/views/admin/hr/employee_js.php` - JavaScript for form validation
- Created `/application/views/admin/hr/groups/attendance.php` - Attendance records tab view
- Created `/application/views/admin/hr/groups/leave.php` - Leave records tab view
- Updated controller with `employees()`, `table()`, `employee()`, `bulk_action()`, `change_employee_status()`, `mark_as_active()`, `remove_staff_profile_image()`, `delete()` methods
- Added language strings for all employee-related labels

### Employee Table Configuration ✅
- Created `/application/views/admin/tables/hr_employees.php` - Table configuration
- Table uses DataTables server-side processing
- SQL query joins staff with `tblhr_departments`, `tblhr_designations`, `tblhr_roles`
- Fixed column alias detection using `preg_match('/\s+as\s+/i', $_field)`
- Test data added: departments (HR, Engineering, Sales), designations (HR Manager, HR Executive, Software Engineer, Sales Manager)

### Employee Profile Tabs ✅
- **Profile Tab**: Basic Info (name, email, phone, social), Org Structure (department, designation, manager, shift), Employment Details (type, role, salary, dates), Social & Permissions (social links, admin status, active status, password)
- **Attendance Tab**: Shows all attendance records with date, check in/out, total hours, status, late minutes, overtime
- **Leave Tab**: Shows all leave requests with type, dates, days, status, reason, applied date

## Relevant files / directories

### HR Module Core:
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/modules/hr/hr.php` - Main module file with hooks, menu, widget registration
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/modules/hr/controllers/Hr.php` - Controller with all HR methods
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/modules/hr/models/Hr_model.php` - Model with database operations
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/modules/hr/install.php` - Database tables installation
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/modules/hr/language/english/hr_lang.php` - Language strings
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/modules/hr/assets/css/hr_widgets.css` - Widget CSS

### Widget Views:
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/widget_clock.php` - Attendance widget
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/widget_leave_requests.php` - Pending Leaves widget

### Dashboard:
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/dashboard/dashboard.php` - Main dashboard view
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/dashboard/dashboard_js.php` - Dashboard JS (clock script here now)

### Employee Pages:
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/employees.php` - Employee list view
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/employee.php` - Employee profile page
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/tabs.php` - Tab navigation
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/groups/profile.php` - Profile form
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/groups/attendance.php` - Attendance tab view
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/groups/leave.php` - Leave tab view
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/hr/employee_js.php` - Form JS

### Table Configuration:
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/tables/hr_employees.php` - Table data

### Reference Files (for structure):
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/clients/manage.php` - Customers list view
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/clients/client.php` - Customer profile view
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/clients/tabs.php` - Customer tabs
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/clients/groups/profile.php` - Customer profile form
- `/Applications/XAMPP/xamppfiles/htdocs/instafolio/application/views/admin/tables/staff.php` - Original staff table config

## Next Steps

1. **Test Employee List Page** - Verify table data loads correctly with the new configuration
2. **Test Employee Profile Page** - Verify all tabs (Profile, Attendance, Leave) work correctly
3. **Test CRUD Operations** - Add new employee, edit existing, delete employee
4. **Test Toggle Active/Inactive** - Verify the status toggle works
5. **Test Dashboard Widgets** - Verify clock in/out and leave requests widgets display correctly

## Login Credentials

**URL:** `http://localhost/instafolio/admin`
**Email:** `admin@instafolio.demo`
**Password:** `demo123`
