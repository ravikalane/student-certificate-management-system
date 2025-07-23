<?php
try {
    $pdo = new PDO("sqlite:database/student_certificates.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
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
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            student_id INTEGER NOT NULL,
            certificate_type VARCHAR(20) NOT NULL CHECK (certificate_type IN ('leaving', 'bonafide')),
            certificate_number VARCHAR(50) UNIQUE NOT NULL,
            issue_date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
        )
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS certificate_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            school_name VARCHAR(200) NOT NULL,
            school_address TEXT NOT NULL,
            principal_name VARCHAR(100) NOT NULL,
            registrar_name VARCHAR(100) NOT NULL,
            school_logo VARCHAR(500),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert default admin if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
    }
    
    // Insert default certificate settings if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificate_settings");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO certificate_settings (school_name, school_address, principal_name, registrar_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'GOVERNMENT POLYTECHNIC, BEED',
            "Near Khandeshwari Temple, Nathapur Road, Beed - 431122\nTel - 02442 - 222603(O) Fax - 02442 - 222609\nE - mail : principal.gpbeed@dtemaharashtra.gov.in",
            'Principal',
            'Registrar'
        ]);
    }
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
