<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="student_template.csv"');

$headers = [
    'enrollment_no',
    'registration_no',
    'lc_no',
    'student_name',
    'father_name',
    'mother_name',
    'nationality',
    'place_of_birth',
    'tq_district',
    'religion',
    'caste',
    'date_of_birth',
    'previous_attended_institute',
    'date_of_admission',
    'year_of_admission',
    'course',
    'leaving_year',
    'leaving_course',
    'date_of_leaving',
    'seat_no',
    'grade',
    'progress',
    'conduct',
    'reason_for_leaving',
    'goi_ebc_sanction_no',
    'remarks'
];

$output = fopen('php://output', 'w');
fputcsv($output, $headers);

// Add sample data
$sample = [
    'EN001',
    'REG001',
    'LC001',
    'John Doe',
    'Robert Doe',
    'Mary Doe',
    'Indian',
    'Mumbai',
    'Mumbai',
    'Hindu',
    'General',
    '2000-01-15',
    'ABC School',
    '2018-06-01',
    '2018',
    'Computer Engineering',
    '2022',
    'Computer Engineering',
    '2022-05-31',
    'SEAT123',
    'A',
    'Good',
    'Excellent',
    'Completed Course',
    'GOI123',
    'Good Student'
];

fputcsv($output, $sample);
fclose($output);
?>
