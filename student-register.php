<?php

    include('Config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $phonenum = isset($_POST['phonenum']) ? trim($_POST['phonenum']) : '';
        $university = isset($_POST['university']) ? trim($_POST['university']) : '';
        $major = isset($_POST['major']) ? trim($_POST['major']) : '';
        $dateOfBirth = isset($_POST['dateOfBirth']) ? trim($_POST['dateOfBirth']) : '';
        $profilePhotoPath = null;

        if (
            $name === '' ||
            $email === '' ||
            $password === '' ||
            $phonenum === '' ||
            $university === '' ||
            $major === ''
        ) {
            die('Please fill in all required fields.');
        }

        if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
            die('Please choose a profile photo.');
        }

        $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
        $originalFileName = basename($_FILES['profile_photo']['name']);
        $fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

        if (!in_array($fileExtension, $allowedExtensions, true)) {
            die('Only JPG, PNG, GIF, and WEBP images are allowed.');
        }

        $newFileName = 'student_' . uniqid() . '.' . $fileExtension;
        $targetFilePath = $uploadDirectory . $newFileName;

        if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFilePath)) {
            die('Unable to save the uploaded photo.');
        }

        $profilePhotoPath = 'img/' . $newFileName;

        $sql = 'INSERT INTO Student (name, email, password, phonenum, university, major, dateOfBirth, profile_photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($con, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssss', $name, $email, $password, $phonenum, $university, $major, $dateOfBirth, $profilePhotoPath);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($con);
                header('Location: login.html');
                exit();
            }

            echo mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        } else {
            echo mysqli_error($con);
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Register - Launchpath</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">

    <nav class="navbar">
        <div class="logo">🚀 Launchpath</div>

        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="About Us Page.html">About Us</a></li>
            <li><a href="Contact Page.html">Contact</a></li>
        </ul>
    </nav>

    <section class="hero">
        <div class="hero-text">
            <h1>Complete Your <span class="highlight">Profile</span></h1>
            <p>
                Add your details to start applying to internships and connect with companies.
            </p>
        </div>

        <div class="form-box company-form-box">
            <h2>Student Register</h2>

            <form action="student-register.php" method="post" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Name" required>
                <input type="text" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="phonenum" placeholder="Phone Number" required>
                <input type="text" name="university" placeholder="University" required>
                <input type="text" name="major" placeholder="Major" required>
                <label class="file-label">Date of Birth</label>
                <input type="date" name="dateOfBirth">
                <label class="file-label">Profile Photo</label>
                <input type="file" name="profile_photo" accept="image/*" required>
                <br><br>
                <button type="submit" class="form-btn">Complete Registration</button>
            </form>
        </div>
    </section>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>