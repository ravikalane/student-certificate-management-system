<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO students (
            enrollment_no, registration_no, lc_no, student_name, father_name, mother_name,
            nationality, place_of_birth, tq_district, religion, caste, date_of_birth,
            date_of_birth_words, previous_attended_institute, date_of_admission,
            year_of_admission, course, leaving_year, leaving_course, date_of_leaving,
            seat_no, grade, progress, conduct, reason_for_leaving, goi_ebc_sanction_no, remarks
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['enrollment_no'], $_POST['registration_no'], $_POST['lc_no'],
            $_POST['student_name'], $_POST['father_name'], $_POST['mother_name'],
            $_POST['nationality'], $_POST['place_of_birth'], $_POST['tq_district'],
            $_POST['religion'], $_POST['caste'], $_POST['date_of_birth'],
            $_POST['date_of_birth_words'], $_POST['previous_attended_institute'],
            $_POST['date_of_admission'], $_POST['year_of_admission'], $_POST['course'],
            $_POST['leaving_year'], $_POST['leaving_course'], $_POST['date_of_leaving'],
            $_POST['seat_no'], $_POST['grade'], $_POST['progress'], $_POST['conduct'],
            $_POST['reason_for_leaving'], $_POST['goi_ebc_sanction_no'], $_POST['remarks']
        ]);
        
        $message = 'Student added successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error adding student: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Certificate Management</title>
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
        .form-card {
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #666;
            font-weight: 600;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e0e0e0;
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
                    <li><a href="auth/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container main-content">
        <div class="row">
            <div class="col s12">
                <h4>Add New Student</h4>
                <p class="grey-text">Enter student details for certificate generation</p>
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
            <div class="col s12">
                <div class="card form-card">
                    <form method="POST">
                        <h5 class="section-title">Basic Information</h5>
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input type="text" id="enrollment_no" name="enrollment_no" required>
                                <label for="enrollment_no">Enrollment No *</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="registration_no" name="registration_no">
                                <label for="registration_no">Registration No</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="lc_no" name="lc_no">
                                <label for="lc_no">L.C. No</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="student_name" name="student_name" required>
                                <label for="student_name">Student Name *</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="father_name" name="father_name" required>
                                <label for="father_name">Father's Name *</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="mother_name" name="mother_name" required>
                                <label for="mother_name">Mother's Name *</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="nationality" name="nationality" value="Indian">
                                <label for="nationality">Nationality</label>
                            </div>
                        </div>

                        <h5 class="section-title">Personal Details</h5>
                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="place_of_birth" name="place_of_birth">
                                <label for="place_of_birth">Place of Birth</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="tq_district" name="tq_district">
                                <label for="tq_district">TQ & District</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input type="text" id="religion" name="religion">
                                <label for="religion">Religion</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="caste" name="caste">
                                <label for="caste">Caste</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="date" id="date_of_birth" name="date_of_birth" required>
                                <label for="date_of_birth">Date of Birth *</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <input type="text" id="date_of_birth_words" name="date_of_birth_words">
                                <label for="date_of_birth_words">Date of Birth (in words)</label>
                            </div>
                        </div>

                        <h5 class="section-title">Academic Information</h5>
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="text" id="previous_attended_institute" name="previous_attended_institute">
                                <label for="previous_attended_institute">Previous Attended Institute</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="date" id="date_of_admission" name="date_of_admission">
                                <label for="date_of_admission">Date of Admission</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="year_of_admission" name="year_of_admission">
                                <label for="year_of_admission">Year of Admission</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="course" name="course">
                                <label for="course">Course</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="leaving_year" name="leaving_year">
                                <label for="leaving_year">Leaving Year</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="leaving_course" name="leaving_course">
                                <label for="leaving_course">Leaving Course</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="date" id="date_of_leaving" name="date_of_leaving">
                                <label for="date_of_leaving">Date of Leaving</label>
                            </div>
                        </div>

                        <h5 class="section-title">Performance Details</h5>
                        <div class="row">
                            <div class="input-field col s12 m4">
                                <input type="text" id="seat_no" name="seat_no">
                                <label for="seat_no">Seat No</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="grade" name="grade">
                                <label for="grade">Grade</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input type="text" id="progress" name="progress">
                                <label for="progress">Progress</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12 m6">
                                <input type="text" id="conduct" name="conduct">
                                <label for="conduct">Conduct</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input type="text" id="goi_ebc_sanction_no" name="goi_ebc_sanction_no">
                                <label for="goi_ebc_sanction_no">GOI/EBC/Sanction No</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="reason_for_leaving" name="reason_for_leaving" class="materialize-textarea"></textarea>
                                <label for="reason_for_leaving">Reason for Leaving</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="remarks" name="remarks" class="materialize-textarea"></textarea>
                                <label for="remarks">Remarks</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <button type="submit" class="btn blue waves-effect waves-light">
                                    <i class="material-icons left">save</i>Add Student
                                </button>
                                <a href="dashboard.php" class="btn grey waves-effect waves-light" style="margin-left: 10px;">
                                    <i class="material-icons left">cancel</i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        $(document).ready(function(){
            M.updateTextFields();
        });
    </script>
</body>
</html>
