<?php
session_start();
include ('Config.php');

if(!isset($_SESSION['user_type'])  || $_SESSION['user_type'] !== 'student'){
    header('Location: login.html');
    exit();
}
$isStudent = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
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

$activeCount = 0;
$sql_count = 'SELECT COUNT(*) AS cnt FROM student_internships WHERE student_id = ?';
$stmt2 = mysqli_prepare($con, $sql_count);
if ($stmt2) {
    mysqli_stmt_bind_param($stmt2, 'i', $studentId);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    if ($res2) {
        $row2 = mysqli_fetch_assoc($res2);
        $activeCount = isset($row2['cnt']) ? (int)$row2['cnt'] : 0;
    }
    mysqli_stmt_close($stmt2);
}

$activeInternships = [];
$sql_active = 'SELECT i.id, i.title, i.field, i.start_date, i.duration FROM student_internships s JOIN internships i ON s.internship_id = i.id WHERE s.student_id = ? ORDER BY s.id DESC';
$stmt4 = mysqli_prepare($con, $sql_active);
if ($stmt4) {
    mysqli_stmt_bind_param($stmt4, 'i', $studentId);
    mysqli_stmt_execute($stmt4);
    $res4 = mysqli_stmt_get_result($stmt4);
    if ($res4) {
        while ($r = mysqli_fetch_assoc($res4)) {
            $activeInternships[] = $r;
        }
    }
    mysqli_stmt_close($stmt4);
}

$appliedCount = 0;
/*
$sql_count = 'SELECT COUNT(*) AS apcnt FROM application WHERE student_id = ?';
$stmt2 = mysqli_prepare($con, $sql_count);
if ($stmt2) {
    mysqli_stmt_bind_param($stmt2, 'i', $studentId);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    if ($res2) {
        $row2 = mysqli_fetch_assoc($res2);
        $appliedCount = isset($row2['cnt']) ? (int)$row2['cnt'] : 0;
    }
    mysqli_stmt_close($stmt2);
}

$appliedInternships = [];
$sql_apps = 'SELECT i.id, i.title, i.field, i.start_date, i.duration FROM application a JOIN internships i ON a.internship_id = i.id WHERE a.student_id = ? ORDER BY a.applied_date DESC';
$stmt3 = mysqli_prepare($con, $sql_apps);
if ($stmt3) {
    mysqli_stmt_bind_param($stmt3, 'i', $studentId);
    mysqli_stmt_execute($stmt3);
    $res3 = mysqli_stmt_get_result($stmt3);
    if ($res3) {
        while ($r = mysqli_fetch_assoc($res3)) {
            $appliedInternships[] = $r;
        }
    }
    mysqli_stmt_close($stmt3);
}
*/


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
                    <span class="dashboard-label">Major</span>
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
                <?php if($isStudent): ?>

                <div class="company-profile-buttons">
                    <a href="student-profile.php" class="profile-btn">Back to Profile</a>
                    <a href="#" class="profile-btn">CV</a>
                </div>
            </div>
        </section>

        <section class="company-dashboard-stats">
            <div class="dashboard-stat-card">
                <h3><?php echo (int) $appliedCount?></h3>
                <p>Applied Internships</p>
            </div>

            <div class="dashboard-stat-card">
                <h3><?php echo (int)$activeCount; ?></h3>
                <p>Accepted Internships</p>
            </div>

        
        </section>

        <section class="company-dashboard-internships">
            <h2>Accepted Internships</h2>

            <div class="company-dashboard-internships-grid">
                <?php if (empty($activeInternships)) { ?>
                    <p>You have no accepted internships.</p>
                <?php } else { ?>
                    <?php foreach ($activeInternships as $act) { ?>
                        <a href="internship-details.php?id=<?php echo (int)$act['id']; ?>" class="dashboard-internship-card">
                            <h3><?php echo htmlspecialchars($act['title']); ?></h3>
                            <p><?php echo htmlspecialchars($act['field']); ?></p>
                            <span><?php echo date("F Y", strtotime($act['start_date'])) . ' - ' . htmlspecialchars($act['duration']); ?></span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
<!--
        <section class="company-dashboard-internships">
            <h2>Applied Internships</h2>

            <div class="company-dashboard-internships-grid">
                <?php if (empty($appliedInternships)) { ?>
                    <p>You haven't applied to any internships yet.</p>
                <?php } else { ?>
                    <?php foreach ($appliedInternships as $app) { ?>
                        <a href="internship-details.php?id=<?php echo (int)$app['id']; ?>" class="dashboard-internship-card">
                            <h3><?php echo htmlspecialchars($app['title']); ?></h3>
                            <p><?php echo htmlspecialchars($app['field']); ?></p>
                            <span><?php echo date("F Y", strtotime($app['start_date'])) . ' - ' . htmlspecialchars($app['duration']); ?></span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
-->
        <?php endif; ?>

    </main>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close DB connection after rendering
if (isset($con) && is_resource($con)) {
    mysqli_close($con);
}
?>