<?php

session_start();
include('Config.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: login.html');
    exit();
}

$studentId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

if ($studentId <= 0) {
    mysqli_close($con);
    header('Location: login.html?error=invalid_session');
    exit();
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phonenum = isset($_POST['phonenum']) ? trim($_POST['phonenum']) : '';
    $university = isset($_POST['university']) ? trim($_POST['university']) : '';
    $major = isset($_POST['major']) ? trim($_POST['major']) : '';
    $dateOfBirth = isset($_POST['dateOfBirth']) ? trim($_POST['dateOfBirth']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($name === '' || $email === '' || $phonenum === '' || $university === '' || $major === '') {
        $errorMessage = 'Please fill in all required fields.';
    } else {
        if ($password !== '') {
            $updateSql = 'UPDATE Student SET name = ?, email = ?, password = ?, phonenum = ?, university = ?, major = ?, dateOfBirth = ? WHERE id = ? LIMIT 1';
            $updateStmt = mysqli_prepare($con, $updateSql);

            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, 'sssssssi', $name, $email, $password, $phonenum, $university, $major, $dateOfBirth, $studentId);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);

                $_SESSION['user_name'] = $name;
                mysqli_close($con);
                header('Location: student-profile.php?updated=1');
                exit();
            }
        } else {
            $updateSql = 'UPDATE Student SET name = ?, email = ?, phonenum = ?, university = ?, major = ?, dateOfBirth = ? WHERE id = ? LIMIT 1';
            $updateStmt = mysqli_prepare($con, $updateSql);

            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, 'ssssssi', $name, $email, $phonenum, $university, $major, $dateOfBirth, $studentId);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);

                $_SESSION['user_name'] = $name;
                mysqli_close($con);
                header('Location: student-profile.php?updated=1');
                exit();
            }
        }

        if ($errorMessage === '') {
            $errorMessage = 'Unable to update profile right now.';
        }
    }
}

$sql = 'SELECT id, name, email, phonenum, university, major, dateOfBirth FROM Student WHERE id = ? LIMIT 1';
$stmt = mysqli_prepare($con, $sql);
$student = null;

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

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile - Launchpath</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">

    <nav class="navbar">
        <div class="logo">🚀 Launchpath</div>

        <ul class="nav-links">
            <li><a href="student-home.html">Home</a></li>
            <li><a href="student-dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Edit Your <span class="highlight">Profile</span></h1>
            <p>
                Update your student information to keep your profile complete and up to date.
            </p>
        </div>

        <div class="form-box company-form-box">
            <h2>Edit Student Profile</h2>

            <?php if ($errorMessage !== '') { ?>
                <p class="switch-text" style="color:#dc3545;"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php } ?>

            <form action="edit-student-profile.php" method="post">
                <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($studentName); ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($studentEmail); ?>" required>
                <input type="text" name="phonenum" placeholder="Phone Number" value="<?php echo htmlspecialchars($studentPhone); ?>" required>
                <input type="password" name="password" placeholder="New Password (leave empty to keep current)">
                <input type="text" name="university" placeholder="University" value="<?php echo htmlspecialchars($studentUniversity); ?>" required>
                <input type="text" name="major" placeholder="Major" value="<?php echo htmlspecialchars($studentMajor); ?>" required>
                <label class="file-label">Date of Birth</label>
                <input type="date" name="dateOfBirth" value="<?php echo htmlspecialchars($studentDob); ?>">
                <br><br>
                <button type="submit" class="form-btn">Save Changes</button>
                <br>
                <br>
                <button type="button" class="form-btn" onclick="location.href='student-profile.php'">Cancel</button>
            </form>
        </div>
    </section>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>