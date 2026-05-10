<?php

session_start();
include("Config.php");

function redirectToDetails($internshipID, $query)
{
    header("Location: internship-details.php?id=" . (int)$internshipID . "&" . $query);
    exit();
}

/* Allow students only */

if (
    !isset($_SESSION['user_type']) ||
    $_SESSION['user_type'] !== 'student'
) {

    header("Location: login.html");
    exit();
}

/* Get student ID */

$studentID = $_SESSION['user_id'];

/* Get internship ID */

$internshipID = isset($_POST['internship_id'])
    ? (int) $_POST['internship_id']
    : 0;

if ($internshipID <= 0) {

    header("Location: Available-Internships.php");
    exit();
}

/* Ensure student created a CV using CV builder */

$cvCheckSql = "SELECT id FROM student_cv WHERE student_id = ? LIMIT 1";
$cvCheckStmt = mysqli_prepare($con, $cvCheckSql);
mysqli_stmt_bind_param($cvCheckStmt, "i", $studentID);
mysqli_stmt_execute($cvCheckStmt);
$cvCheckResult = mysqli_stmt_get_result($cvCheckStmt);
$hasCv = mysqli_num_rows($cvCheckResult) > 0;
mysqli_stmt_close($cvCheckStmt);

if (!$hasCv) {
    redirectToDetails($internshipID, 'error=cv_required');
}

/* Check if student already applied */

$check = "

SELECT *
FROM student_internships

WHERE student_id = ?
AND internship_id = ?

";

$stmt = mysqli_prepare($con, $check);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $studentID,
    $internshipID
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    redirectToDetails($internshipID, 'error=already');
}

/* Save application into database */

$sql = "

INSERT INTO student_internships
(student_id, internship_id)

VALUES (?, ?)

";

$stmt = mysqli_prepare($con, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $studentID,
    $internshipID
);

if (!mysqli_stmt_execute($stmt)) {
    redirectToDetails($internshipID, 'error=db');
}

redirectToDetails($internshipID, 'success=1');

?>