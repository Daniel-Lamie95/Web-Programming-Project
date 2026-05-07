<?php
session_start();
include('Config.php');

$userType = $_SESSION['user_type'] ?? 'student';

$query = "SELECT * FROM internships";
$result = mysqli_query($con, $query);
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
        <li><a href="index.html">Home</a></li>
        <li><a href="student-profile.html">Profile</a></li>
        <li><a href="login.html">Logout</a></li>
    </ul>
</nav>

<main class="company-dashboard-container">

<section class="company-dashboard-hero">
    <div>
        <h1>Latest Internships & Summer Training</h1>
        <p>Internships help you gain practical skills, grow your professional network, and stand out to future employers.</p>
    </div>
</section>

<section class="company-dashboard-stats">
    <div class="dashboard-stat-card">
        <h3>12</h3>
        <p>Posted Internships</p>
    </div>

    <div class="dashboard-stat-card">
        <h3>48</h3>
        <p>Applicants</p>
    </div>

    <div class="dashboard-stat-card">
        <h3>6</h3>
        <p>Active Internships</p>
    </div>
</section>

<section class="search-section">
    <div class="search-box">
        <form method="get">
            <input type="text" name="keyword" placeholder="Search internships...">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<section class="company-dashboard-internships">
    <h2>Recent Internships</h2>

    <div class="company-dashboard-internships-grid">

<?php while($row = mysqli_fetch_assoc($result)) { 

    $link = ($userType == 'company') 
        ? "edit-internship.php?id=" . $row['id'] 
        : "internship-details.php?id=" . $row['id'];
?>

<a href="<?php echo $link; ?>" class="dashboard-internship-card">
    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
    <p><?php echo htmlspecialchars($row['field']); ?></p>

    <span>
        <?php 
        echo date("F Y", strtotime($row['start_date'])) . " - " . $row['duration']; 
        ?>
    </span>
</a>

<?php } ?>

    </div>
</section>

</main>

</body>
</html>