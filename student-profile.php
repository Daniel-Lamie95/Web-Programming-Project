<?php

session_start();
include('Config.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.html');
    exit();
}

$student = null;
$studentId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

if ($studentId <= 0) {
    mysqli_close($con);
    header('Location: login.html?error=invalid_session');
    exit();
}

$sql = 'SELECT id, name, email, phonenum, university, major, dateOfBirth, profile_photo_path FROM Student WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $studentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $student = mysqli_fetch_assoc($result);
        }

        mysqli_stmt_close($stmt);
    }

if (!$student) {
    mysqli_close($con);
    header('Location: login.html?error=student_not_found');
    exit();
}

$studentName = isset($student['name']) ? $student['name'] : '';
$studentEmail = isset($student['email']) ? $student['email'] : '';
$studentPhone = isset($student['phonenum']) ? $student['phonenum'] : '';
$studentUniversity = isset($student['university']) ? $student['university'] : '';
$studentMajor = isset($student['major']) ? $student['major'] : '';
$studentDob = isset($student['dateOfBirth']) ? $student['dateOfBirth'] : '';
$studentPhotoPath = isset($student['profile_photo_path']) && $student['profile_photo_path'] !== '' ? $student['profile_photo_path'] : './images/Screenshot 2026-03-23 192924.png';

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="company-profile-page">

    <nav class="company-profile-navbar">
        <div class="company-profile-logo">🚀 Launchpath</div>

        <ul class="company-profile-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="student-dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-profile-container">

        <section class="company-profile-hero">
            <h1>Student Profile</h1>
            <p>View your details and manage your profile information.</p>
        </section>

        <section class="company-profile-card">

            <div class="company-profile-left">
                <div class="company-profile-image">
                    <img src="<?php echo htmlspecialchars($studentPhotoPath); ?>" alt="Student profile photo">
                </div>
                <h2><?php echo htmlspecialchars($studentName); ?></h2>
            </div>

            <div class="company-profile-right">
                <h2>Profile Information</h2>

                <div class="profile-row">
                    <span class="profile-label">Name</span>
                    <span class="profile-value"><?php echo htmlspecialchars($studentName); ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Email</span>
                    <span class="profile-value"><?php echo htmlspecialchars($studentEmail); ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Phone</span>
                    <span class="profile-value"><?php echo htmlspecialchars($studentPhone); ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">University</span>
                    <span class="profile-value"><?php echo htmlspecialchars($studentUniversity); ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Major</span>
                    <span class="profile-value"><?php echo htmlspecialchars($studentMajor); ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Date of Birth</span>
                    <span class="profile-value"><?php echo htmlspecialchars($studentDob); ?></span>
                </div>

                <div class="company-profile-buttons">
                    <a href="edit-student-profile.php" class="profile-btn">Edit Profile</a>
                    <a href="cv/cv-view.php" class="profile-btn">My CV</a>
                    <a href="student-dashboard.php" class="profile-btn">Back to Dashboard</a>
                </div>
            </div>

        </section>

    </main>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>