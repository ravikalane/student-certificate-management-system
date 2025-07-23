<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_students FROM students");
$total_students = $stmt->fetch()['total_students'];

$stmt = $pdo->query("SELECT COUNT(*) as total_certificates FROM certificates");
$total_certificates = $stmt->fetch()['total_certificates'];

$stmt = $pdo->query("SELECT COUNT(*) as leaving_certificates FROM certificates WHERE certificate_type = 'leaving'");
$leaving_certificates = $stmt->fetch()['leaving_certificates'];

$stmt = $pdo->query("SELECT COUNT(*) as bonafide_certificates FROM certificates WHERE certificate_type = 'bonafide'");
$bonafide_certificates = $stmt->fetch()['bonafide_certificates'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Certificate Management</title>
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
        .stats-card {
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        .stats-label {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }
        .action-card {
            padding: 30px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }
        .action-card:hover {
            transform: translateY(-2px);
        }
        .action-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .main-content {
            margin-top: 20px;
        }
        .sidenav {
            width: 250px;
        }
        .sidenav li > a {
            color: #333;
        }
        .sidenav li > a:hover {
            background-color: #e3f2fd;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" class="brand-logo" style="margin-left: 20px;">Certificate Management</a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="#" class="dropdown-trigger" data-target="user-dropdown">
                        <i class="material-icons left">person</i><?php echo $_SESSION['admin_username']; ?>
                        <i class="material-icons right">arrow_drop_down</i>
                    </a></li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Dropdown Structure -->
    <ul id="user-dropdown" class="dropdown-content">
        <li><a href="auth/logout.php"><i class="material-icons">exit_to_app</i>Logout</a></li>
    </ul>

    <!-- Sidenav -->
    <ul class="sidenav" id="mobile-demo">
        <li><div class="user-view">
            <div class="background" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
            <a href="#user"><i class="material-icons circle">person</i></a>
            <a href="#name"><span class="white-text name"><?php echo $_SESSION['admin_username']; ?></span></a>
            <a href="#email"><span class="white-text email">Administrator</span></a>
        </div></li>
        <li><a href="dashboard.php"><i class="material-icons">dashboard</i>Dashboard</a></li>
        <li><a href="students.php"><i class="material-icons">people</i>Students</a></li>
        <li><a href="certificates.php"><i class="material-icons">description</i>Certificates</a></li>
        <li><div class="divider"></div></li>
        <li><a href="auth/logout.php"><i class="material-icons">exit_to_app</i>Logout</a></li>
    </ul>

    <div class="container main-content">
        <div class="row">
            <div class="col s12">
                <h4>Dashboard</h4>
                <p class="grey-text">Welcome back, <?php echo $_SESSION['admin_username']; ?>!</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col s12 m6 l3">
                <div class="card stats-card blue lighten-5">
                    <div class="card-content">
                        <p class="stats-number blue-text"><?php echo $total_students; ?></p>
                        <p class="stats-label">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card stats-card green lighten-5">
                    <div class="card-content">
                        <p class="stats-number green-text"><?php echo $total_certificates; ?></p>
                        <p class="stats-label">Total Certificates</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card stats-card orange lighten-5">
                    <div class="card-content">
                        <p class="stats-number orange-text"><?php echo $leaving_certificates; ?></p>
                        <p class="stats-label">Leaving Certificates</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="card stats-card purple lighten-5">
                    <div class="card-content">
                        <p class="stats-number purple-text"><?php echo $bonafide_certificates; ?></p>
                        <p class="stats-label">Bonafide Certificates</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="row">
            <div class="col s12 m6 l4">
                <div class="card action-card">
                    <i class="material-icons action-icon blue-text">person_add</i>
                    <h5>Add Student</h5>
                    <p class="grey-text">Register a new student in the system</p>
                    <a href="add-student.php" class="btn blue waves-effect waves-light">Add Student</a>
                </div>
            </div>
            <div class="col s12 m6 l4">
                <div class="card action-card">
                    <i class="material-icons action-icon green-text">description</i>
                    <h5>Generate Certificate</h5>
                    <p class="grey-text">Create leaving or bonafide certificate</p>
                    <a href="generate-certificate.php" class="btn green waves-effect waves-light">Generate</a>
                </div>
            </div>
            <div class="col s12 m6 l4">
                <div class="card action-card">
                    <i class="material-icons action-icon orange-text">people</i>
                    <h5>Manage Students</h5>
                    <p class="grey-text">View and manage student records</p>
                    <a href="students.php" class="btn orange waves-effect waves-light">View Students</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.sidenav').sidenav();
            $('.dropdown-trigger').dropdown();
        });
    </script>
</body>
</html>
