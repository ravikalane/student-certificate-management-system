<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $message = 'Student deleted successfully!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Error deleting student: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get all students
$search = $_GET['search'] ?? '';
$query = "SELECT * FROM students";
$params = [];

if ($search) {
    $query .= " WHERE student_name LIKE ? OR enrollment_no LIKE ? OR father_name LIKE ?";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Certificate Management</title>
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
        .students-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .table-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .search-box {
            max-width: 300px;
        }
        .btn-floating.btn-small {
            width: 32px;
            height: 32px;
            line-height: 32px;
        }
        .btn-floating.btn-small i {
            line-height: 32px;
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
                    <li><a href="add-student.php"><i class="material-icons left">person_add</i>Add Student</a></li>
                    <li><a href="auth/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container main-content">
        <div class="row">
            <div class="col s12">
                <h4>Students Management</h4>
                <p class="grey-text">Manage student records and generate certificates</p>
            </div>
        </div>

        <?php if (isset($message)): ?>
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
                <div class="students-table">
                    <div class="table-header">
                        <div class="row valign-wrapper" style="margin-bottom: 0;">
                            <div class="col s12 m6">
                                <h5 style="margin: 0;">Student Records (<?php echo count($students); ?>)</h5>
                            </div>
                            <div class="col s12 m6">
                                <form method="GET" class="right">
                                    <div class="input-field search-box" style="margin: 0;">
                                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search students...">
                                        <button type="submit" class="btn-flat" style="position: absolute; right: 0; top: 0;">
                                            <i class="material-icons">search</i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($students)): ?>
                    <div style="padding: 40px; text-align: center;">
                        <i class="material-icons large grey-text">people_outline</i>
                        <h5 class="grey-text">No students found</h5>
                        <p class="grey-text">Start by adding your first student record.</p>
                        <a href="add-student.php" class="btn blue waves-effect waves-light">
                            <i class="material-icons left">person_add</i>Add Student
                        </a>
                    </div>
                    <?php else: ?>
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <th>Enrollment No</th>
                                <th>Student Name</th>
                                <th>Father's Name</th>
                                <th>Course</th>
                                <th>Date of Birth</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['enrollment_no']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($student['student_name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($student['father_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['course'] ?: 'N/A'); ?></td>
                                <td><?php echo $student['date_of_birth'] ? date('d/m/Y', strtotime($student['date_of_birth'])) : 'N/A'; ?></td>
                                <td>
                                    <a href="view-student.php?id=<?php echo $student['id']; ?>" 
                                       class="btn-floating btn-small blue waves-effect waves-light tooltipped" 
                                       data-position="top" data-tooltip="View Details">
                                        <i class="material-icons">visibility</i>
                                    </a>
                                    <a href="edit-student.php?id=<?php echo $student['id']; ?>" 
                                       class="btn-floating btn-small orange waves-effect waves-light tooltipped" 
                                       data-position="top" data-tooltip="Edit Student">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <a href="generate-certificate.php?student_id=<?php echo $student['id']; ?>" 
                                       class="btn-floating btn-small green waves-effect waves-light tooltipped" 
                                       data-position="top" data-tooltip="Generate Certificate">
                                        <i class="material-icons">description</i>
                                    </a>
                                    <a href="?delete=<?php echo $student['id']; ?>" 
                                       class="btn-floating btn-small red waves-effect waves-light tooltipped delete-btn" 
                                       data-position="top" data-tooltip="Delete Student">
                                        <i class="material-icons">delete</i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.tooltipped').tooltip();
            
            $('.delete-btn').click(function(e) {
                if (!confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
