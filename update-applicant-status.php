<?php
session_start();
include('Config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: company-dashboard.php');
    exit();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'company') {
    header('Location: login.html');
    exit();
}

$companyId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($companyId <= 0) {
    header('Location: login.html');
    exit();
}

$studentId = isset($_POST['student_id']) ? (int) $_POST['student_id'] : 0;
$internshipId = isset($_POST['internship_id']) ? (int) $_POST['internship_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($studentId <= 0 || $internshipId <= 0 || ($action !== 'accepted' && $action !== 'rejected' && $action !== 'pending')) {
    header('Location: company-dashboard.php');
    exit();
}

$sqlCheck = 'SELECT id FROM internships WHERE id = ? AND company_id = ? LIMIT 1';
$stmtCheck = mysqli_prepare($con, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, 'ii', $internshipId, $companyId);
mysqli_stmt_execute($stmtCheck);
$resCheck = mysqli_stmt_get_result($stmtCheck);
$internship = mysqli_fetch_assoc($resCheck);
mysqli_stmt_close($stmtCheck);

if (!$internship) {
    header('Location: company-dashboard.php');
    exit();
}

$sqlUpdate = 'UPDATE student_internships SET status = ? WHERE student_id = ? AND internship_id = ?';
$stmtUpdate = mysqli_prepare($con, $sqlUpdate);
mysqli_stmt_bind_param($stmtUpdate, 'sii', $action, $studentId, $internshipId);
mysqli_stmt_execute($stmtUpdate);
mysqli_stmt_close($stmtUpdate);

mysqli_close($con);

$redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '';
if ($redirect === 'cv') {
    header('Location: cv/applicant-cv.php?student_id=' . $studentId . '&internship_id=' . $internshipId);
} else {
    header('Location: view-applicants.php?internship_id=' . $internshipId);
}
exit();
?>
