<?php
session_start();
include('Config.php');

if (!isset($_SESSION['user_type'])) {
    header('Location: login.html');
    exit();
}

if ($_SESSION['user_type'] !== 'student') {
    header('Location: company-dashboard.php');
    exit();
}

$query = "SELECT * FROM internships";
$result = mysqli_query($con, $query);

$postedCount = 0;
$applicantsCount = 0;
$activeCount = 0;

$countQuery = 'SELECT COUNT(*) AS cnt FROM internships';
$countResult = mysqli_query($con, $countQuery);
if ($countResult) {
    $countRow = mysqli_fetch_assoc($countResult);
    $postedCount = isset($countRow['cnt']) ? (int) $countRow['cnt'] : 0;
}
/*
$applicantsQuery = 'SELECT COUNT(*) AS cnt FROM application';
$applicantsResult = mysqli_query($con, $applicantsQuery);
if ($applicantsResult) {
    $applicantsRow = mysqli_fetch_assoc($applicantsResult);
    $applicantsCount = isset($applicantsRow['cnt']) ? (int) $applicantsRow['cnt'] : 0;
}
*/

$activeQuery = 'SELECT COUNT(*) AS cnt FROM student_internships';
$activeResult = mysqli_query($con, $activeQuery);
if ($activeResult) {
    $activeRow = mysqli_fetch_assoc($activeResult);
    $activeCount = isset($activeRow['cnt']) ? (int) $activeRow['cnt'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Internship Dashboard - Launchpad</title>
<link rel="stylesheet" href="css/style.css">
</head>

<body class="company-dashboard-page">

<nav class="company-dashboard-navbar">
<div class="company-dashboard-logo">🚀 Launchpad</div>

<ul class="company-dashboard-links">
<li><a href="student-dashboard.php">Home</a></li>
<li><a href="student-profile.php">Profile</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</nav>

<main class="company-dashboard-container">

<section class="company-dashboard-hero">
<div>
<h1>Latest Internships & Summer Training</h1>
<p>Internships help you gain practical skills, grow your professional network, and stand out to future employers. </p>
</div>
</section>

<section class="company-dashboard-stats">
<div class="dashboard-stat-card">
<h3><?php echo $postedCount; ?></h3>
<p>Posted Internships</p>
</div>

<div class="dashboard-stat-card">
<h3><?php echo $applicantsCount; ?></h3>
<p>Applicants</p>
</div>

<div class="dashboard-stat-card">
<h3><?php echo $activeCount; ?></h3>
<p>Active Internships</p>
</div>
</section>

<section class="search-section">
<div class="search-box">
<form action="" method="get">
<input type="text" name="keyword" placeholder="Search internships...">
<button type="submit">Search</button>
</form>
</div>
</section>

<section class="company-dashboard-internships">
<h2>Recent Internships</h2>

<div class="company-dashboard-internships-grid">

<?php while($row = mysqli_fetch_assoc($result)) { 

    $link = "internship-details.php?id=" . $row['id'];
?>

<a href="<?php echo $link; ?>" class="dashboard-internship-card">
    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
    <p><?php echo htmlspecialchars($row['field']); ?></p>
    <span><?php echo htmlspecialchars($row['start_date']); ?></span>
</a>

<?php } ?>

</div>
</section>

</main>

</body>
</html>