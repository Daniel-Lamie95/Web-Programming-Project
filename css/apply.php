<?php

session_start();
include("Config.php");

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

    header("Location: internship-details.php?id=$internshipID&error=already");
    exit();
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

if (mysqli_stmt_execute($stmt)) {

    header("Location: internship-details.php?id=$internshipID&success=1");
    exit();

} else {

    echo "Error";
}
?>