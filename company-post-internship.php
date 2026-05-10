<?php
session_start();
include('Config.php');

/* 🔒 Only company allowed */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'company') {
    header("Location: login.html");
    exit();
}

/* 📌 Get company id from session */
$company_id = $_SESSION['user_id'];
/* 📥 Get company information */
$company_query = "SELECT * FROM Company WHERE ID = ?";
$company_stmt = mysqli_prepare($con, $company_query);
mysqli_stmt_bind_param($company_stmt, 'i', $company_id);
mysqli_stmt_execute($company_stmt);
$company_result = mysqli_stmt_get_result($company_stmt);
$company = mysqli_fetch_assoc($company_result);

if (!$company) {
    header('Location: company-dashboard.php');
    exit();
}

/* 📤 Handle form submission */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $startDate = $_POST['startDate'];
    $location = $_POST['location'];
    $field = $_POST['field'];

    /* simple default logo */
    $logo = "default.png";

    $query = "INSERT INTO internships 
    (company_id, title, description, duration, start_date, location, field, logo)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "isssssss", 
        $company_id, $title, $description, $duration, $startDate, $location, $field, $logo
    );

    mysqli_stmt_execute($stmt);

    /* redirect after posting */
    header("Location: Available-Internships.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Post Internship Profile</title>
<link rel="stylesheet" href="css/style.css">
</head>

<body class="company-profile-page">

<nav class="company-profile-navbar">
    <div class="company-profile-logo">🚀 Launchpad</div>

    <ul class="company-profile-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="company-dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main class="company-profile-container">

<section class="company-profile-hero">
    <h1>Post Internship</h1>
    <p>Enter internship information.</p>
</section>

<section class="company-profile-card">

<div class="company-profile-left">
    <div class="company-profile-image">
        <img src="<?php echo htmlspecialchars($company['Logo']); ?>" alt="Company Logo">
    </div>
    <h2><?php echo htmlspecialchars($company['Name']); ?></h2>
    <span>Internship Posting</span>
</div>

<div class="company-profile-right">
    <h2>New Internship</h2>

    <form method="POST">

        <div class="profile-row">
            <label class="profile-label">Internship Title</label>
            <input type="text" name="title" required>
        </div>

        <div class="profile-row">
            <label class="profile-label">Duration</label>
            <input type="text" name="duration" required>
        </div>

        <div class="profile-row description-row">
            <label class="profile-label">Description</label>
            <textarea name="description" rows="5" required></textarea>
        </div>

        <div class="profile-row">
            <label class="profile-label">Start Date</label>
            <input type="date" name="startDate" required>
        </div>

        <div class="profile-row">
            <label class="profile-label">Location</label>
            <input type="text" name="location" required>
        </div>

        <div class="profile-row">
            <label class="profile-label">Field / Industry</label>
            <input type="text" name="field" required>
        </div>

        <div class="company-profile-buttons">
            <a href="company-dashboard.php" class="profile-btn">Cancel</a>
            <button type="submit" class="profile-btn">Post Internship</button>
        </div>

    </form>
</div>

</section>

</main>

</body>
</html>