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
    $profilePhotoPath = null;
    $photoUploaded = isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK;

    if ($name === '' || $email === '' || $phonenum === '' || $university === '' || $major === '') {
        $errorMessage = 'Please fill in all required fields.';
    } elseif ($photoUploaded) {
        $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
        $originalFileName = basename($_FILES['profile_photo']['name']);
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        if (!in_array($fileExtension, $allowedExtensions, true)) {
            $errorMessage = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
        } else {
            $newFileName = 'student_' . $studentId . '_' . uniqid() . '.' . $fileExtension;
            $targetFilePath = $uploadDirectory . $newFileName;

            if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFilePath)) {
                $errorMessage = 'Unable to save the uploaded photo.';
            } else {
                $profilePhotoPath = 'img/' . $newFileName;

                if ($password !== '') {
                    $updateSql = 'UPDATE Student SET name = ?, email = ?, password = ?, phonenum = ?, university = ?, major = ?, dateOfBirth = ?, profile_photo_path = ? WHERE id = ? LIMIT 1';
                    $updateStmt = mysqli_prepare($con, $updateSql);

                    if ($updateStmt) {
                        mysqli_stmt_bind_param($updateStmt, 'ssssssssi', $name, $email, $password, $phonenum, $university, $major, $dateOfBirth, $profilePhotoPath, $studentId);
                        mysqli_stmt_execute($updateStmt);
                        mysqli_stmt_close($updateStmt);

                        $_SESSION['user_name'] = $name;
                        mysqli_close($con);
                        header('Location: student-profile.php?updated=1');
                        exit();
                    }
                } else {
                    $updateSql = 'UPDATE Student SET name = ?, email = ?, phonenum = ?, university = ?, major = ?, dateOfBirth = ?, profile_photo_path = ? WHERE id = ? LIMIT 1';
                    $updateStmt = mysqli_prepare($con, $updateSql);

                    if ($updateStmt) {
                        mysqli_stmt_bind_param($updateStmt, 'sssssssi', $name, $email, $phonenum, $university, $major, $dateOfBirth, $profilePhotoPath, $studentId);
                        mysqli_stmt_execute($updateStmt);
                        mysqli_stmt_close($updateStmt);

                        $_SESSION['user_name'] = $name;
                        mysqli_close($con);
                        header('Location: student-profile.php?updated=1');
                        exit();
                    }
                }
            }
        }
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

$sql = 'SELECT id, name, email, phonenum, university, major, dateOfBirth, profile_photo_path FROM Student WHERE id = ? LIMIT 1';
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
$studentPhotoPath = isset($student['profile_photo_path']) && $student['profile_photo_path'] !== '' ? $student['profile_photo_path'] : './images/Screenshot 2026-03-23 192924.png';

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile - Launchpath</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="company-profile-page">

    <nav class="company-profile-navbar">
        <div class="company-profile-logo">🚀 Launchpath</div>

        <ul class="company-profile-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="student-dashboard.php">Dashboard</a></li>
            <li><a href="student-profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-profile-container">

        <section class="company-profile-hero">
            <h1>Edit Student Profile</h1>
            <p>Update your student information to keep your profile complete and up to date.</p>
        </section>

        <section class="company-profile-card">

            <div class="company-profile-left">
                <div class="company-profile-image">
                    <img src="<?php echo htmlspecialchars($studentPhotoPath); ?>" alt="Student profile photo">
                </div>
                <h2><?php echo htmlspecialchars($studentName); ?></h2>
                <span>Student Profile</span>
            </div>

            <div class="company-profile-right">
                <h2>Edit Information</h2>

                <?php if ($errorMessage !== '') { ?>
                    <p class="switch-text" style="color:#dc3545; margin-bottom: 16px;"><?php echo htmlspecialchars($errorMessage); ?></p>
                <?php } ?>

                <form action="edit-student-profile.php" method="post" enctype="multipart/form-data">
                    <div class="profile-row">
                        <label class="profile-label" for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($studentName); ?>" required>
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($studentEmail); ?>" required>
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="phonenum">Phone Number</label>
                        <input type="text" id="phonenum" name="phonenum" value="<?php echo htmlspecialchars($studentPhone); ?>" required>
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="university">University</label>
                        <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($studentUniversity); ?>" required>
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="major">Major</label>
                        <input type="text" id="major" name="major" value="<?php echo htmlspecialchars($studentMajor); ?>" required>
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="dateOfBirth">Date of Birth</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($studentDob); ?>">
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="profile_photo">Change Profile Photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                    </div>

                    <div class="company-profile-buttons">
                        <a href="student-profile.php" class="profile-btn">Cancel</a>
                        <button type="submit" class="profile-btn">Save Changes</button>
                    </div>
                </form>
            </div>

        </section>

    </main>
</body>
</html>