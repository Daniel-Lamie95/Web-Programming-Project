<?php
session_start();
include('Config.php');

$userType = $_SESSION['user_type'] ?? 'student';

/* 📥 Get internships from DB */
$query = "SELECT * FROM internships";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internships</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="company-dashboard-page">

<nav class="company-dashboard-navbar">
    <div class="company-dashboard-logo">🚀 Launchpad</div>

    <ul class="company-dashboard-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main class="company-dashboard-container">

<section class="company-dashboard-hero">
    <h1>Latest Internships</h1>
</section>

<section class="company-dashboard-internships">
    <h2>Recent Internships</h2>

    <div class="company-dashboard-internships-grid">

<?php while($row = mysqli_fetch_assoc($result)) { 

    /* 🔥 Decide link based on role */
    if ($userType == 'company') {
        $link = "edit-internship.php?id=" . $row['id'];
    } else {
        $link = "internship-details.php?id=" . $row['id'];
    }
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