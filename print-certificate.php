<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/database.php';

$certificate_id = $_GET['id'] ?? null;
if (!$certificate_id) {
    header("Location: dashboard.php");
    exit();
}

// Get certificate and student details
$stmt = $pdo->prepare("
    SELECT c.*, s.*, cs.school_name, cs.school_address, cs.principal_name, cs.registrar_name
    FROM certificates c 
    JOIN students s ON c.student_id = s.id 
    CROSS JOIN certificate_settings cs
    WHERE c.id = ?
");
$stmt->execute([$certificate_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($data['certificate_type']); ?> Certificate - <?php echo htmlspecialchars($data['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .certificate {
            padding: 40px;
            border: 3px solid #000;
            margin: 20px;
            background: white;
            min-height: 1000px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            float: left;
            margin-right: 20px;
        }
        .school-info {
            text-align: center;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #d32f2f;
            margin: 0;
            line-height: 1.2;
        }
        .school-address {
            font-size: 12px;
            margin: 5px 0;
            line-height: 1.4;
        }
        .certificate-title {
            background: #666;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 2px;
        }
        .certificate-content {
            margin: 20px 0;
        }
        .warning-text {
            font-size: 11px;
            text-align: justify;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        .details-table {
            width: 100%;
            margin: 20px 0;
        }
        .details-table td {
            padding: 8px 0;
            vertical-align: top;
            font-size: 14px;
        }
        .label {
            font-weight: bold;
            width: 200px;
        }
        .value {
            border-bottom: 1px solid #000;
            min-height: 20px;
            padding-left: 10px;
        }
        .two-column {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .column {
            width: 48%;
        }
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: end;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 10px;
        }
        .date-section {
            margin: 30px 0;
        }
        .seal-section {
            text-align: center;
            margin: 40px 0;
        }
        .seal-placeholder {
            width: 120px;
            height: 120px;
            border: 2px dashed #666;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
        }
        .print-controls {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-controls {
                display: none;
            }
            .certificate-container {
                box-shadow: none;
                max-width: none;
            }
            .certificate {
                margin: 0;
                border: 3px solid #000;
                page-break-inside: avoid;
            }
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(0,0,0,0.05);
            z-index: 1;
            font-weight: bold;
            pointer-events: none;
        }
        .certificate-content {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button onclick="window.print()" class="btn">
            🖨️ Print Certificate
        </button>
        <a href="generate-certificate.php" class="btn btn-secondary">
            ← Back to Generate
        </a>
        <a href="dashboard.php" class="btn btn-secondary">
            🏠 Dashboard
        </a>
    </div>

    <div class="certificate-container">
        <div class="certificate">
            <div class="watermark">
                <?php echo strtoupper($data['school_name']); ?>
            </div>
            
            <div class="certificate-content">
                <div class="header">
                    <div style="overflow: hidden;">
                        <div style="float: left; width: 80px; height: 80px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-size: 12px; text-align: center;">
                            SCHOOL<br>LOGO
                        </div>
                        <div style="margin-left: 100px;">
                            <div class="school-info">
                                <h1 class="school-name"><?php echo htmlspecialchars($data['school_name']); ?></h1>
                                <div class="school-address"><?php echo nl2br(htmlspecialchars($data['school_address'])); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="certificate-title">
                    <?php echo strtoupper($data['certificate_type']); ?> CERTIFICATE
                </div>

                <?php if ($data['certificate_type'] === 'leaving'): ?>
                <div class="warning-text">
                    No change in an entry in this certificate shall be made except by the issuing authority. Any infringement of this requirement is liable to invoke imposition of penalty such as that of rustication.
                </div>

                <div class="two-column">
                    <div class="column">
                        <strong>Registration No:</strong> <?php echo htmlspecialchars($data['registration_no'] ?: 'N/A'); ?>
                    </div>
                    <div class="column">
                        <strong>L.C. No:</strong> <?php echo htmlspecialchars($data['lc_no'] ?: 'N/A'); ?>
                    </div>
                </div>

                <table class="details-table">
                    <tr>
                        <td class="label">Enrollment No</td>
                        <td class="value"><?php echo htmlspecialchars($data['enrollment_no']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Student Name</td>
                        <td class="value"><?php echo htmlspecialchars($data['student_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Father's Name</td>
                        <td class="value"><?php echo htmlspecialchars($data['father_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Mother's Name</td>
                        <td class="value"><?php echo htmlspecialchars($data['mother_name']); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Nationality</td>
                        <td class="value"><?php echo htmlspecialchars($data['nationality'] ?: 'Indian'); ?></td>
                    </tr>
                </table>

                <div class="two-column">
                    <div class="column">
                        <strong>Place Of Birth:</strong> <?php echo htmlspecialchars($data['place_of_birth'] ?: 'N/A'); ?>
                    </div>
                    <div class="column">
                        <strong>TQ & DISTRICT:</strong> <?php echo htmlspecialchars($data['tq_district'] ?: 'N/A'); ?>
                    </div>
                </div>

                <div class="two-column">
                    <div class="column">
                        <strong>Religion:</strong> <?php echo htmlspecialchars($data['religion'] ?: 'N/A'); ?>
                    </div>
                    <div class="column">
                        <strong>Caste:</strong> <?php echo htmlspecialchars($data['caste'] ?: 'N/A'); ?>
                    </div>
                </div>

                <table class="details-table">
                    <tr>
                        <td class="label">Date Of Birth</td>
                        <td class="value"><?php echo $data['date_of_birth'] ? date('d-m-Y', strtotime($data['date_of_birth'])) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td class="label">Date Of Birth(in words)</td>
                        <td class="value"><?php echo htmlspecialchars($data['date_of_birth_words'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Previous Attended Institute</td>
                        <td class="value"><?php echo htmlspecialchars($data['previous_attended_institute'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Date Of Admission</td>
                        <td class="value"><?php echo $data['date_of_admission'] ? date('d-m-Y', strtotime($data['date_of_admission'])) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td class="label">Year Of Admission</td>
                        <td class="value"><?php echo htmlspecialchars($data['year_of_admission'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Course</td>
                        <td class="value"><?php echo htmlspecialchars($data['course'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Leaving Year</td>
                        <td class="value"><?php echo htmlspecialchars($data['leaving_year'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Leaving Course</td>
                        <td class="value"><?php echo htmlspecialchars($data['leaving_course'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Date Of Leaving</td>
                        <td class="value"><?php echo $data['date_of_leaving'] ? date('d-m-Y', strtotime($data['date_of_leaving'])) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td class="label">Seat No</td>
                        <td class="value"><?php echo htmlspecialchars($data['seat_no'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Grade</td>
                        <td class="value"><?php echo htmlspecialchars($data['grade'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Progress</td>
                        <td class="value"><?php echo htmlspecialchars($data['progress'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Conduct</td>
                        <td class="value"><?php echo htmlspecialchars($data['conduct'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Reason For Leaving</td>
                        <td class="value"><?php echo htmlspecialchars($data['reason_for_leaving'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">GOI/EBC/Sanction No</td>
                        <td class="value"><?php echo htmlspecialchars($data['goi_ebc_sanction_no'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Remarks</td>
                        <td class="value"><?php echo htmlspecialchars($data['remarks'] ?: 'N/A'); ?></td>
                    </tr>
                </table>

                <?php else: // Bonafide Certificate ?>
                
                <div style="text-align: center; margin: 40px 0; font-size: 16px; line-height: 2;">
                    <p>This is to certify that <strong><?php echo htmlspecialchars($data['student_name']); ?></strong>, 
                    Son/Daughter of <strong><?php echo htmlspecialchars($data['father_name']); ?></strong> 
                    is a bonafide student of this institution.</p>
                    
                    <p>Enrollment No: <strong><?php echo htmlspecialchars($data['enrollment_no']); ?></strong></p>
                    
                    <p>Course: <strong><?php echo htmlspecialchars($data['course'] ?: 'N/A'); ?></strong></p>
                    
                    <p>Academic Year: <strong><?php echo htmlspecialchars($data['year_of_admission'] ?: 'N/A'); ?></strong></p>
                    
                    <p style="margin-top: 40px;">This certificate is issued for official purposes.</p>
                </div>

                <?php endif; ?>

                <div style="text-align: center; margin: 30px 0; font-weight: bold;">
                    Certified that above information is in accordance with the office record.
                </div>

                <div class="date-section">
                    <strong>Date:</strong> <?php echo date('d/m/Y', strtotime($data['issue_date'])); ?>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div><strong>Prepared by</strong></div>
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div><strong><?php echo htmlspecialchars($data['registrar_name']); ?></strong></div>
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div><strong><?php echo htmlspecialchars($data['principal_name']); ?></strong><br>
                        <?php echo htmlspecialchars($data['school_name']); ?></div>
                    </div>
                </div>

                <div class="seal-section">
                    <div class="seal-placeholder">
                        Seal of<br>the Institute
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="print-controls">
        <p><strong>Certificate Number:</strong> <?php echo htmlspecialchars($data['certificate_number']); ?></p>
        <p><strong>Generated on:</strong> <?php echo date('d/m/Y H:i:s', strtotime($data['created_at'])); ?></p>
    </div>
</body>
</html>
