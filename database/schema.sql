-- Create database
CREATE DATABASE IF NOT EXISTS student_certificates;
USE student_certificates;

-- Create admin table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    enrollment_no VARCHAR(50) NOT NULL,
    registration_no VARCHAR(50),
    lc_no VARCHAR(50),
    student_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    nationality VARCHAR(50) DEFAULT 'Indian',
    place_of_birth VARCHAR(100),
    tq_district VARCHAR(100),
    religion VARCHAR(50),
    caste VARCHAR(50),
    date_of_birth DATE NOT NULL,
    date_of_birth_words VARCHAR(200),
    previous_attended_institute VARCHAR(200),
    date_of_admission DATE,
    year_of_admission VARCHAR(20),
    course VARCHAR(100),
    leaving_year VARCHAR(20),
    leaving_course VARCHAR(100),
    date_of_leaving DATE,
    seat_no VARCHAR(50),
    grade VARCHAR(50),
    progress VARCHAR(50),
    conduct VARCHAR(50),
    reason_for_leaving TEXT,
    goi_ebc_sanction_no VARCHAR(100),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create certificates table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    certificate_type ENUM('leaving', 'bonafide') NOT NULL,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Create certificate_settings table for school details
CREATE TABLE IF NOT EXISTS certificate_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_name VARCHAR(200) NOT NULL,
    school_address TEXT NOT NULL,
    principal_name VARCHAR(100) NOT NULL,
    registrar_name VARCHAR(100) NOT NULL,
    school_logo VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default certificate settings
INSERT INTO certificate_settings (school_name, school_address, principal_name, registrar_name) 
VALUES ('GOVERNMENT POLYTECHNIC, BEED', 'Near Khandeshwari Temple, Nathapur Road, Beed - 431122\nTel - 02442 - 222603(O) Fax - 02442 - 222609\nE - mail : principal.gpbeed@dtemaharashtra.gov.in', 'Principal', 'Registrar');
