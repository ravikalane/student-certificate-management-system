<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$student_id = $_GET['id'] ?? null;
if (!$student_id) {
    header("Location: students.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: students.php");
    exit();
}

// Get certificates for this student
$stmt = $pdo->prepare("SELECT * FROM certificates WHERE student_id = ? ORDER BY created_at DESC");
$stmt->execute([$student_id]);
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - <?php echo htmlspecialchars($student['student_name']); ?></title>
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
        .student-card {
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
        .info-row {
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-label {
            font-weight: 600;
            color: #666;
            display: inline-block;
            width: 200px;
        }
        .info-value {
            color: #333;
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
                <h4>Student Details</h4>
                <p class="grey-text">Complete information for <?php echo htmlspecialchars($student['student_name']); ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m8">
                <div class="card student-card">
                    <div class="card-action" style="padding: 0 0 20px 0; border-bottom: 1px solid #e0e0e0;">
                        <a href="edit-student.php?id=<?php echo $student['id']; ?>" class="btn orange waves-effect waves-light">
                            <i class="material-icons left">edit</i>Edit Student
                        </a>
                        <a href="generate-certificate.php?student_id=<?php echo $student['id']; ?>" class="btn green waves-effect waves-light">
                            <i class="material-icons left">description</i>Generate Certificate
                        </a>
                        <a href="students.php" class="btn grey waves-effect waves-light">
                            <i class="material-icons left">arrow_back</i>Back to Students
                        </a>
                    </div>

                    <h5 class="section-title">Basic Information</h5>
                    <div class="info-row">
                        <span class="info-label">Enrollment No:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['enrollment_no']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Registration No:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['registration_no'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">L.C. No:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['lc_no'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value"><strong><?php echo htmlspecialchars($student['student_name']); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Father's Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['father_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mother's Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['mother_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nationality:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['nationality'] ?: 'Indian'); ?></span>
                    </div>

                    <h5 class="section-title">Personal Details</h5>
                    <div class="info-row">
                        <span class="info-label">Place of Birth:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['place_of_birth'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">TQ & District:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['tq_district'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Religion:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['religion'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Caste:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['caste'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Birth:</span>
                        <span class="info-value"><?php echo $student['date_of_birth'] ? date('d/m/Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Birth (words):</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['date_of_birth_words'] ?: 'N/A'); ?></span>
                    </div>

                    <h5 class="section-title">Academic Information</h5>
                    <div class="info-row">
                        <span class="info-label">Previous Institute:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['previous_attended_institute'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Admission:</span>
                        <span class="info-value"><?php echo $student['date_of_admission'] ? date('d/m/Y', strtotime($student['date_of_admission'])) : 'N/A'; ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Year of Admission:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['year_of_admission'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Course:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['course'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Leaving Year:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['leaving_year'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Leaving Course:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['leaving_course'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date of Leaving:</span>
                        <span class="info-value"><?php echo $student['date_of_leaving'] ? date('d/m/Y', strtotime($student['date_of_leaving'])) : 'N/A'; ?></span>
                    </div>

                    <h5 class="section-title">Performance Details</h5>
                    <div class="info-row">
                        <span class="info-label">Seat No:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['seat_no'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Grade:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['grade'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Progress:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['progress'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Conduct:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['conduct'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Reason for Leaving:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['reason_for_leaving'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">GOI/EBC/Sanction No:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['goi_ebc_sanction_no'] ?: 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Remarks:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['remarks'] ?: 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="card">
                    <div class="card-content">
                        <span class="card-title"><i class="material-icons left">description</i>Generated Certificates</span>
                        <?php if (empty($certificates)): ?>
                        <p class="grey-text">No certificates generated yet.</p>
                        <a href="generate-certificate.php?student_id=<?php echo $student['id']; ?>" class="btn blue waves-effect waves-light">
                            <i class="material-icons left">add</i>Generate First Certificate
                        </a>
                        <?php else: ?>
                        <ul class="collection">
                            <?php foreach ($certificates as $cert): ?>
                            <li class="collection-item">
                                <div>
                                    <strong><?php echo ucfirst($cert['certificate_type']); ?> Certificate</strong>
                                    <br>
                                    <small class="grey-text">
                                        <?php echo $cert['certificate_number']; ?><br>
                                        Issued: <?php echo date('d/m/Y', strtotime($cert['issue_date'])); ?>
                                    </small>
                                    <a href="print-certificate.php?id=<?php echo $cert['id']; ?>" class="secondary-content">
                                        <i class="material-icons">print</i>
                                    </a>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="generate-certificate.php?student_id=<?php echo $student['id']; ?>" class="btn blue waves-effect waves-light">
                            <i class="material-icons left">add</i>Generate New Certificate
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <span class="card-title"><i class="material-icons left">info</i>Record Information</span>
                        <p><strong>Created:</strong> <?php echo date('d/m/Y H:i', strtotime($student['created_at'])); ?></p>
                        <p><strong>Student ID:</strong> <?php echo $student['id']; ?></p>
                        <p><strong>Total Certificates:</strong> <?php echo count($certificates); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
