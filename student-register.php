<?php

    include('Config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "INSERT INTO Student (name, email, password, phonenum, university, major, dateOfBirth) VALUES ('$_POST[name]', '$_POST[email]', '$_POST[password]', '$_POST[phonenum]', '$_POST[university]', '$_POST[major]', '$_POST[dateOfBirth]')";

        if (mysqli_query($con, $sql)) {
            header('Location: login.html');
            exit();
        }

        echo mysqli_error($con);
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Register - Launchpath</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">

    <nav class="navbar">
        <div class="logo">🚀 Launchpath</div>

        <ul class="nav-links">
            <li><a href="student-home.html">Home</a></li>
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
                <br><br>
                <button type="submit" class="form-btn">Complete Registration</button>
            </form>
        </div>
    </section>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>