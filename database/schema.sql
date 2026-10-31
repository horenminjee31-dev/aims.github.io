-- School Management System Database Schema
-- Created for AIMS (Academic Institution Management System)

CREATE DATABASE IF NOT EXISTS school_mgmt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_mgmt;

-- Users Table (Admin, Teachers, Staff)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'staff', 'student') DEFAULT 'admin',
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Basic Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(200) NOT NULL,
    school_address TEXT,
    school_phone VARCHAR(50),
    school_email VARCHAR(100),
    current_year INT,
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Options Tables
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL,
    class_name_bn VARCHAR(50),
    numeric_name INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE class_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(50) NOT NULL,
    group_name_bn VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL,
    section_name_bn VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shift_name VARCHAR(50) NOT NULL,
    shift_name_bn VARCHAR(50),
    start_time TIME,
    end_time TIME,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE class_times (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    is_current ENUM('yes', 'no') DEFAULT 'no',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_name_bn VARCHAR(100),
    subject_code VARCHAR(20),
    class_id INT,
    full_mark DECIMAL(5,2) DEFAULT 100.00,
    pass_mark DECIMAL(5,2) DEFAULT 33.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

CREATE TABLE exam_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(100) NOT NULL,
    exam_name_bn VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE exam_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grade_name VARCHAR(10) NOT NULL,
    min_mark DECIMAL(5,2) NOT NULL,
    max_mark DECIMAL(5,2) NOT NULL,
    grade_point DECIMAL(3,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE designations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    designation_name VARCHAR(100) NOT NULL,
    designation_name_bn VARCHAR(100),
    designation_type ENUM('teacher', 'staff') DEFAULT 'staff',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    class_id INT NOT NULL,
    group_id INT,
    section_id INT,
    shift_id INT,
    year_id INT NOT NULL,
    roll_number INT,
    full_name VARCHAR(100) NOT NULL,
    full_name_bn VARCHAR(100),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE,
    blood_group VARCHAR(5),
    religion VARCHAR(50),
    nationality VARCHAR(50) DEFAULT 'Bangladeshi',
    photo VARCHAR(255),
    
    -- Guardian Information
    father_name VARCHAR(100),
    father_name_bn VARCHAR(100),
    father_phone VARCHAR(20),
    father_occupation VARCHAR(100),
    mother_name VARCHAR(100),
    mother_name_bn VARCHAR(100),
    mother_phone VARCHAR(20),
    mother_occupation VARCHAR(100),
    guardian_name VARCHAR(100),
    guardian_phone VARCHAR(20),
    guardian_relation VARCHAR(50),
    
    -- Address
    present_address TEXT,
    permanent_address TEXT,
    
    -- Previous Education
    previous_school VARCHAR(100),
    previous_class VARCHAR(50),
    
    status ENUM('active', 'inactive', 'transferred', 'graduated') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (group_id) REFERENCES class_groups(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    FOREIGN KEY (year_id) REFERENCES years(id)
);

-- Teachers Table
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    full_name_bn VARCHAR(100),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE,
    designation_id INT,
    qualification VARCHAR(200),
    specialization VARCHAR(100),
    joining_date DATE,
    phone VARCHAR(20),
    email VARCHAR(100),
    photo VARCHAR(255),
    address TEXT,
    basic_salary DECIMAL(10,2),
    status ENUM('active', 'inactive', 'retired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (designation_id) REFERENCES designations(id)
);

-- Staff Table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    full_name_bn VARCHAR(100),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE,
    designation_id INT,
    joining_date DATE,
    phone VARCHAR(20),
    email VARCHAR(100),
    photo VARCHAR(255),
    address TEXT,
    basic_salary DECIMAL(10,2),
    status ENUM('active', 'inactive', 'retired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (designation_id) REFERENCES designations(id)
);

-- Subject Assignment
CREATE TABLE subject_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    class_id INT NOT NULL,
    group_id INT,
    subject_id INT NOT NULL,
    section_id INT,
    year_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (group_id) REFERENCES class_groups(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (year_id) REFERENCES years(id)
);

-- Class Routine
CREATE TABLE class_routines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    group_id INT,
    section_id INT,
    shift_id INT,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    class_time_id INT NOT NULL,
    day ENUM('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday') NOT NULL,
    year_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (group_id) REFERENCES class_groups(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id),
    FOREIGN KEY (class_time_id) REFERENCES class_times(id),
    FOREIGN KEY (year_id) REFERENCES years(id)
);

-- Attendance
CREATE TABLE student_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'leave') DEFAULT 'present',
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    UNIQUE KEY unique_attendance (student_id, date)
);

CREATE TABLE teacher_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'leave') DEFAULT 'present',
    in_time TIME,
    out_time TIME,
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id),
    UNIQUE KEY unique_teacher_attendance (teacher_id, date)
);

CREATE TABLE staff_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'leave') DEFAULT 'present',
    in_time TIME,
    out_time TIME,
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id),
    UNIQUE KEY unique_staff_attendance (staff_id, date)
);

-- Marks
CREATE TABLE marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    exam_type_id INT NOT NULL,
    year_id INT NOT NULL,
    written_mark DECIMAL(5,2) DEFAULT 0,
    mcq_mark DECIMAL(5,2) DEFAULT 0,
    practical_mark DECIMAL(5,2) DEFAULT 0,
    total_mark DECIMAL(5,2) DEFAULT 0,
    grade VARCHAR(10),
    grade_point DECIMAL(3,2),
    status ENUM('published', 'unpublished') DEFAULT 'unpublished',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (exam_type_id) REFERENCES exam_types(id),
    FOREIGN KEY (year_id) REFERENCES years(id),
    UNIQUE KEY unique_marks (student_id, subject_id, exam_type_id, year_id)
);

-- Result Summary
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    exam_type_id INT NOT NULL,
    year_id INT NOT NULL,
    total_marks DECIMAL(8,2),
    gpa DECIMAL(3,2),
    grade VARCHAR(10),
    status ENUM('pass', 'fail', 'pending') DEFAULT 'pending',
    position INT,
    published ENUM('yes', 'no') DEFAULT 'no',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (exam_type_id) REFERENCES exam_types(id),
    FOREIGN KEY (year_id) REFERENCES years(id),
    UNIQUE KEY unique_result (student_id, exam_type_id, year_id)
);

-- Testimonials
CREATE TABLE testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    leaving_year INT,
    leaving_class VARCHAR(50),
    character_description TEXT,
    certificate_text TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- SMS Records
CREATE TABLE sms_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_type ENUM('teacher', 'guardian', 'staff', 'governing_body') NOT NULL,
    recipient_id INT,
    phone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('sent', 'pending', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Library
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100),
    publisher VARCHAR(100),
    isbn VARCHAR(30),
    category VARCHAR(50),
    edition VARCHAR(50),
    quantity INT DEFAULT 1,
    available INT DEFAULT 1,
    price DECIMAL(10,2),
    rack_no VARCHAR(20),
    status ENUM('available', 'issued', 'damaged', 'lost') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE book_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    borrower_type ENUM('student', 'teacher', 'staff') NOT NULL,
    borrower_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE,
    fine_amount DECIMAL(8,2) DEFAULT 0.00,
    status ENUM('issued', 'returned', 'overdue') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Fee Management
CREATE TABLE fee_heads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fee_name VARCHAR(100) NOT NULL,
    fee_name_bn VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE class_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    fee_head_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    year_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (fee_head_id) REFERENCES fee_heads(id),
    FOREIGN KEY (year_id) REFERENCES years(id)
);

CREATE TABLE fee_collections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    fee_head_id INT NOT NULL,
    year_id INT NOT NULL,
    month VARCHAR(20),
    amount DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    fine DECIMAL(10,2) DEFAULT 0.00,
    total_paid DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'bank', 'mobile_bank') DEFAULT 'cash',
    receipt_no VARCHAR(50),
    collected_by INT,
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (fee_head_id) REFERENCES fee_heads(id),
    FOREIGN KEY (year_id) REFERENCES years(id),
    FOREIGN KEY (collected_by) REFERENCES users(id)
);

-- Bank History
CREATE TABLE bank_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL,
    transaction_type ENUM('deposit', 'withdraw') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    bank_name VARCHAR(100),
    account_number VARCHAR(50),
    reference_no VARCHAR(50),
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Salary Management
CREATE TABLE salary_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_type ENUM('teacher', 'staff') NOT NULL,
    employee_id INT NOT NULL,
    month VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    allowances DECIMAL(10,2) DEFAULT 0.00,
    deductions DECIMAL(10,2) DEFAULT 0.00,
    bonus DECIMAL(10,2) DEFAULT 0.00,
    net_salary DECIMAL(10,2) NOT NULL,
    payment_date DATE,
    payment_method ENUM('cash', 'bank', 'mobile_bank') DEFAULT 'cash',
    status ENUM('paid', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES users(id)
);

-- Accounting
CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_name VARCHAR(100) NOT NULL,
    account_type ENUM('asset', 'liability', 'income', 'expense') NOT NULL,
    opening_balance DECIMAL(12,2) DEFAULT 0.00,
    current_balance DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL,
    account_id INT NOT NULL,
    transaction_type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    reference_no VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Insert default admin user (password: bd123)
INSERT INTO users (username, password, role, full_name, email, status) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administrator', 'admin@school.com', 'active');

-- Insert default settings
INSERT INTO settings (school_name, school_address, school_phone, school_email, current_year) VALUES 
('Your School Name', 'School Address', '01XXXXXXXXX', 'school@email.com', 2026);

-- Insert default exam grades
INSERT INTO exam_grades (grade_name, min_mark, max_mark, grade_point) VALUES
('A+', 80.00, 100.00, 5.00),
('A', 70.00, 79.99, 4.00),
('A-', 60.00, 69.99, 3.50),
('B', 50.00, 59.99, 3.00),
('C', 40.00, 49.99, 2.00),
('D', 33.00, 39.99, 1.00),
('F', 0.00, 32.99, 0.00);
