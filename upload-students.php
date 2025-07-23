<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$message = '';
$message_type = '';

// Handle CSV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK && pathinfo($file['name'], PATHINFO_EXTENSION) === 'csv') {
        $handle = fopen($file['tmp_name'], 'r');
        $header = fgetcsv($handle);
        
        $success_count = 0;
        $error_count = 0;
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            try {
                $stmt = $pdo->prepare("INSERT INTO students (
                    enrollment_no, registration_no, lc_no, student_name, father_name, mother_name,
                    nationality, place_of_birth, tq_district, religion, caste, date_of_birth,
                    date_of_birth_words, previous_attended_institute, date_of_admission,
                    year_of_admission, course, leaving_year, leaving_course, date_of_leaving,
                    seat_no, grade, progress, conduct, reason_for_leaving, goi_ebc_sanction_no, remarks
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                // Convert date to words
                $date_of_birth = $data[11] ?? '';
                $date_of_birth_words = '';
                if ($date_of_birth) {
                    $date = new DateTime($date_of_birth);
                    $day = $date->format('d');
                    $month = $date->format('F');
                    $year = $date->format('Y');
                    
                    // Convert day to words
                    $day_words = convertNumberToWords((int)$day);
                    $year_words = convertNumberToWords((int)$year);
                    
                    $date_of_birth_words = "$day_words $month $year_words";
                }
                
                $stmt->execute([
                    $data[0] ?? '', // enrollment_no
                    $data[1] ?? '', // registration_no
                    $data[2] ?? '', // lc_no
                    $data[3] ?? '', // student_name
                    $data[4] ?? '', // father_name
                    $data[5] ?? '', // mother_name
                    $data[6] ?? 'Indian', // nationality
                    $data[7] ?? '', // place_of_birth
                    $data[8] ?? '', // tq_district
                    $data[9] ?? '', // religion
                    $data[10] ?? '', // caste
                    $date_of_birth, // date_of_birth
                    $date_of_birth_words, // date_of_birth_words
                    $data[12] ?? '', // previous_attended_institute
                    $data[13] ?? '', // date_of_admission
                    $data[14] ?? '', // year_of_admission
                    $data[15] ?? '', // course
                    $data[16] ?? '', // leaving_year
                    $data[17] ?? '', // leaving_course
                    $data[18] ?? '', // date_of_leaving
                    $data[19] ?? '', // seat_no
                    $data[20] ?? '', // grade
                    $data[21] ?? '', // progress
                    $data[22] ?? '', // conduct
                    $data[23] ?? '', // reason_for_leaving
                    $data[24] ?? '', // goi_ebc_sanction_no
                    $data[25] ?? '' // remarks
                ]);
                
                $success_count++;
            } catch (PDOException $e) {
                $error_count++;
            }
        }
        
        fclose($handle);
        $message = "Successfully uploaded $success_count students. Failed: $error_count";
        $message_type = 'success';
    } else {
        $message = 'Please upload a valid CSV file';
        $message_type = 'error';
    }
}

function convertNumberToWords($num) {
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
    if ($num === 0) return 'Zero';
    if ($num < 20) return $ones[$num];
    if ($num < 100) return $tens[floor($num / 10)] . ($num % 10 ? ' ' . $ones[$num % 10] : '');
    if ($num < 1000) return $ones[floor($num / 100)] . ' Hundred' . ($num % 100 ? ' ' . convertNumberToWords($num % 100) : '');
    
    return $num;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Students - Certificate Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f5f5;
        }
        .navbar-fixed nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .main-content {
            margin-top: 20px;
        }
        .upload-card {
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .csv-template {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .csv-headers {
            font-family: monospace;
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="dashboard.php" class="brand-logo" style="margin-left: 20px;">Certificate Management</a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="dashboard.php"><i class="material-icons left">dashboard</i>Dashboard</a></li>
                    <li><a href="students.php"><i class="material-icons left">people</i>Students</a></li>
                    <li><a href="auth/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container main-content">
        <div class="row">
            <div class="col s12">
                <h4>Upload Students via CSV</h4>
                <p class="grey-text">Bulk import student data using Excel/CSV format</p>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="row">
            <div class="col s12">
                <div class="card-panel <?php echo $message_type === 'success' ? 'green lighten-4' : 'red lighten-4'; ?>">
                    <span class="<?php echo $message_type === 'success' ? 'green-text text-darken-2' : 'red-text text-darken-2'; ?>">
                        <?php echo $message; ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col s12 m8">
                <div class="card upload-card">
                    <form method="POST" enctype="multipart/form-data">
                        <h5>Upload CSV File</h5>
                        
                        <div class="file-field input-field">
                            <div class="btn blue">
                                <span>File</span>
                                <input type="file" name="csv_file" accept=".csv" required>
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Choose CSV file">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <button type="submit" class="btn blue waves-effect waves-light">
                                    <i class="material-icons left">upload</i>Upload Students
                                </button>
                                <a href="dashboard.php" class="btn grey waves-effect waves-light" style="margin-left: 10px;">
                                    <i class="material-icons left">cancel</i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title"><i class="material-icons left">info</i>CSV Template</span>
                        
                        <div class="csv-template">
                            <h6>Required CSV Format:</h6>
                            <div class="csv-headers">
enrollment_no,registration_no,lc_no,student_name,father_name,mother_name,nationality,place_of_birth,tq_district,religion,caste,date_of_birth,previous_attended_institute,date_of_admission,year_of_admission,course,leaving_year,leaving_course,date_of_leaving,seat_no,grade,progress,conduct,reason_for_leaving,goi_ebc_sanction_no,remarks
                            </div>
                            
                            <h6>Sample Data:</h6>
                            <div class="csv-headers">
EN001,REG001,LC001,John Doe,Robert Doe,Mary Doe,Indian,Mumbai,Mumbai,Hindu,General,2000-01-15,ABC School,2018-06-01,2018,Computer Engineering,2022,Computer Engineering,2022-05-31,SEAT123,A,Good,Excellent,Completed Course,GOI123,Good Student
                            </div>
                        </div>
                        
                        <a href="download-template.php" class="btn green waves-effect waves-light">
                            <i class="material-icons left">download</i>Download Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        $(document).ready(function(){
            $('select').formSelect();
        });
    </script>
</body>
</html>
