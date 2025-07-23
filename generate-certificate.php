<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$student_id = $_GET['student_id'] ?? null;
$student = null;
$message = '';
$message_type = '';

if ($student_id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Generate certificate number
        $certificate_type = $_POST['certificate_type'];
        $prefix = $certificate_type === 'leaving' ? 'LC' : 'BC';
        $certificate_number = $prefix . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        // Insert certificate record
        $stmt = $pdo->prepare("INSERT INTO certificates (student_id, certificate_type, certificate_number, issue_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['student_id'], $certificate_type, $certificate_number, date('Y-m-d')]);
        
        // Redirect to print certificate
        header("Location: print-certificate.php?id=" . $pdo->lastInsertId());
        exit();
    } catch (PDOException $e) {
        $message = 'Error generating certificate: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get all students for dropdown
$stmt = $pdo->query("SELECT id, student_name, enrollment_no FROM students ORDER BY student_name");
$all_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Certificate - Certificate Management</title>
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
        .student-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .certificate-preview {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            border-radius: 8px;
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
                <h4>Generate Certificate</h4>
                <p class="grey-text">Create leaving or bonafide certificate for students</p>
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
                <div class="card form-card">
                    <form method="POST" id="certificateForm">
                        <h5>Certificate Details</h5>
                        
                        <div class="row">
                            <div class="input-field col s12">
                                <select name="student_id" id="student_select" required>
                                    <option value="" disabled selected>Choose a student</option>
                                    <?php foreach ($all_students as $s): ?>
                                    <option value="<?php echo $s['id']; ?>" <?php echo ($student && $student['id'] == $s['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($s['student_name'] . ' (' . $s['enrollment_no'] . ')'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <label>Select Student *</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12">
                                <p>Certificate Type *</p>
                                <p>
                                    <label>
                                        <input name="certificate_type" type="radio" value="leaving" required />
                                        <span>Leaving Certificate</span>
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input name="certificate_type" type="radio" value="bonafide" required />
                                        <span>Bonafide Certificate</span>
                                    </label>
                                </p>
                            </div>
                        </div>

                        <?php if ($student): ?>
                        <div class="student-info">
                            <h6><i class="material-icons left">person</i>Student Information</h6>
                            <div class="row" style="margin-bottom: 0;">
                                <div class="col s12 m6">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['student_name']); ?></p>
                                    <p><strong>Enrollment No:</strong> <?php echo htmlspecialchars($student['enrollment_no']); ?></p>
                                    <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($student['father_name']); ?></p>
                                </div>
                                <div class="col s12 m6">
                                    <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course'] ?: 'N/A'); ?></p>
                                    <p><strong>Date of Birth:</strong> <?php echo $student['date_of_birth'] ? date('d/m/Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></p>
                                    <p><strong>Class:</strong> <?php echo htmlspecialchars($student['leaving_course'] ?: 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col s12">
                                <button type="submit" class="btn green waves-effect waves-light">
                                    <i class="material-icons left">print</i>Generate & Print Certificate
                                </button>
                                <a href="students.php" class="btn grey waves-effect waves-light" style="margin-left: 10px;">
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
                        <span class="card-title"><i class="material-icons left">info</i>Information</span>
                        <p><strong>Leaving Certificate:</strong> Official document certifying that a student has left the institution.</p>
                        <br>
                        <p><strong>Bonafide Certificate:</strong> Document certifying that a student is a genuine student of the institution.</p>
                        <br>
                        <p class="grey-text">Select a student and certificate type to generate the document.</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <span class="card-title"><i class="material-icons left">history</i>Recent Certificates</span>
                        <?php
                        $stmt = $pdo->query("SELECT c.*, s.student_name FROM certificates c 
                                           JOIN students s ON c.student_id = s.id 
                                           ORDER BY c.created_at DESC LIMIT 5");
                        $recent_certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php if (empty($recent_certificates)): ?>
                        <p class="grey-text">No certificates generated yet.</p>
                        <?php else: ?>
                        <ul class="collection">
                            <?php foreach ($recent_certificates as $cert): ?>
                            <li class="collection-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($cert['student_name']); ?></strong>
                                    <br>
                                    <small class="grey-text">
                                        <?php echo ucfirst($cert['certificate_type']); ?> - 
                                        <?php echo date('d/m/Y', strtotime($cert['issue_date'])); ?>
                                    </small>
                                    <a href="print-certificate.php?id=<?php echo $cert['id']; ?>" class="secondary-content">
                                        <i class="material-icons">print</i>
                                    </a>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
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
            
            $('#student_select').change(function() {
                if ($(this).val()) {
                    window.location.href = 'generate-certificate.php?student_id=' + $(this).val();
                }
            });
        });
    </script>
</body>
</html>
