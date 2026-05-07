<?php
session_start();
include ('Config.php');

if(!isset($_SESSION['user_type'])  || $_SESSION['user_type'] !== 'student'){
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
$studentUniversity = isset($student['university']) ? $student['university'] : '';
$studentMajor = isset($student['major']) ? $student['major'] : '';
$studentPhotoPath = isset($student['profile_photo_path']) && $student['profile_photo_path'] !== '' ? $student['profile_photo_path'] : './images/Screenshot 2026-03-23 192924.png';

mysqli_close($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="company-dashboard-page">

    <nav class="company-dashboard-navbar">
        <div class="company-dashboard-logo">🚀 Launchpath</div>

        <ul class="company-dashboard-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="student-profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-dashboard-container">

        <section class="company-dashboard-hero">
            <div>
                <h1>Student Dashboard</h1>
                <p>Manage your profile, internships in one place.</p>
            </div>
        </section>

        <section class="company-dashboard-top">
            <div class="company-dashboard-profile-card">
                <div class="company-dashboard-avatar">
                    <img src="<?php echo htmlspecialchars($studentPhotoPath); ?>" alt="Student profile photo">
                </div>
                <h3><?php echo htmlspecialchars($studentName); ?></h3>
                <span><?php echo htmlspecialchars($studentMajor); ?></span>
            </div>

            <div class="company-dashboard-info-card">
                <h2>Your Information</h2>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Name</span>
                    <span class="dashboard-value"><?php echo htmlspecialchars($studentName); ?></span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Field</span>
                    <span class="dashboard-value"><?php echo htmlspecialchars($studentMajor); ?></span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Email</span>
                    <span class="dashboard-value"><?php echo htmlspecialchars($studentEmail); ?></span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">University</span>
                    <span class="dashboard-value"><?php echo htmlspecialchars($studentUniversity); ?></span>
                </div>
            </div>
        </section>

        <section class="company-dashboard-stats">
            <div class="dashboard-stat-card">
                <h3>3</h3>
                <p>Applied Internships</p>
            </div>

            <div class="dashboard-stat-card">
                <h3>2</h3>
                <p>Active Internships</p>
            </div>

        
        </section>

        <section class="company-dashboard-internships">
            <h2>Applied Internships</h2>

            <div class="company-dashboard-internships-grid">
                <div class="dashboard-internship-card">
                    <h3>Frontend Internship</h3>
                    <p>Web Development</p>
                    <span>June 2026 - August 2026</span>
                </div>

                <div class="dashboard-internship-card">
                    <h3>Backend Internship</h3>
                    <p>Software Engineering</p>
                    <span>July 2026 - September 2026</span>
                </div>

                <div class="dashboard-internship-card">
                    <h3>UI/UX Internship</h3>
                    <p>Design</p>
                    <span>August 2026 - October 2026</span>
                </div>
            </div>
        </section>

    </main>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>