# School Management System (AIMS)

A comprehensive web-based School Management System built with PHP and MySQL.

## Features

### Software Part:
1. **Create Options** - Class, Group, Section, Shift, Class Time, Year, Subject, Exam Type, Exam Grade, Designation
2. **Student Management** - Insert/Search Student, ID Card, Admit Card, Progress Report, Seat Plan, Class Promotion
3. **Teacher Management** - Insert/Search Teacher, Teacher ID Card
4. **Staff Management** - Insert/Search Staff
5. **Attendance System** - Student, Teacher & Staff Attendance, Reports
6. **Subject Assignment**
7. **Class Routine** - Create, Edit, View
8. **Marks Distribution** - Input Marks, Tabulation Sheet, SMS
9. **Result** - Total, Annual, Merit List, Fail List, At A Glance, Publish
10. **Certificates** - Testimonial
11. **SMS System** - Create SMS, Send to Teachers, Guardians, Staff, Governing Body
12. **Library** - Insert/Issue/Add Books
13. **Backup**

### Accounts Part:
1. **Fees Collection** - Fee Collection, Fee Heads, Class-wise Settings, Reports
2. **Bank History**
3. **Employees Salary**
4. **Accounting**

## Installation

### Requirements:
- PHP 7.4 or higher
- MySQL/MariaDB
- Apache/Nginx web server
- XAMPP (recommended for local development)

### Setup Steps:

1. **Clone or extract the project** to your web server directory (e.g., `htdocs/aims` for XAMPP)

2. **Create the database:**
   - Open phpMyAdmin or MySQL command line
   - Import the database schema from `database/schema.sql`
   - Or run: `mysql -u root -p < database/schema.sql`

3. **Configure database connection:**
   - Edit `config/database.php` if needed
   - Default settings:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: school_mgmt

4. **Access the application:**
   - Open browser: `http://localhost/aims/`
   - Default login credentials:
     - Username: `admin`
     - Password: `bd123`

5. **Create upload directories** (if not already created):
   ```
   mkdir uploads
   mkdir uploads/students
   chmod 777 uploads -R
   ```

## Default Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | bd123 |
| Teacher | (create in system) | - |
| Staff | (create in system) | - |

## Directory Structure

```
aims/
├── auth/               # Authentication (login/logout)
├── backup/             # Backup module
├── config/             # Configuration files
├── dashboard/          # Dashboard
├── database/           # Database schema
├── includes/           # Header/footer templates
├── options/            # Create options modules
├── students/           # Student management
├── teachers/           # Teacher management
├── staff/              # Staff management
├── attendance/         # Attendance system
├── subject/            # Subject assignment
├── routine/            # Class routine
├── marks/              # Marks distribution
├── result/             # Results
├── certificates/       # Certificates
├── sms/                # SMS system
├── library/            # Library management
├── accounts/           # Accounts/fees
├── assets/             # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── uploads/
└── index.php           # Entry point
```

## Security Notes

- Change default admin password after first login
- Use strong passwords for all users
- Keep database credentials secure
- Regularly backup your database
- Update PHP and MySQL to latest stable versions

## Support

For support or customization, please contact the system administrator.

## License

This is a custom-built software. All rights reserved.
