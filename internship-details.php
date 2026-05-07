<?php
session_start();
include('Config.php');

/* 🔒 Only allow students */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header("Location: login.html");
    exit();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: Available-Internships.php");
    exit();
}

/* 📥 Get internship */
$query = "SELECT * FROM internships WHERE id = $id";
$result = mysqli_query($con, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Internship not found";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Details</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="company-dashboard-page">

<nav class="company-dashboard-navbar">
    <div class="company-profile-logo">🚀 Launchpad</div>

    <ul class="company-profile-links">
        <li><a href="student-home.html">Home</a></li>
        <li><a href="Available-Internships.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main class="company-profile-container">

<section class="company-profile-hero">
    <h1><?php echo htmlspecialchars($data['title']); ?></h1>
    <p>View internship details and apply</p>
</section>

<section class="company-profile-card">

    <div class="company-profile-left">
        <div class="company-profile-image">
            <img src="images/<?php echo !empty($data['logo']) ? htmlspecialchars($data['logo']) : 'default.png'; ?>" alt="logo">
        </div>
        <h2><?php echo htmlspecialchars($data['title']); ?></h2>
    </div>

    <div class="company-profile-right">
        <h2>Internship Information</h2>

        <div class="profile-row">
            <span class="profile-label">Duration</span>
            <span class="profile-value"><?php echo htmlspecialchars($data['duration']); ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-label">Description</span>
            <span class="profile-value"><?php echo htmlspecialchars($data['description']); ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-label">Start Date</span>
            <span class="profile-value">
                <?php echo date("d F Y", strtotime($data['start_date'])); ?>
            </span>
        </div>

        <div class="profile-row">
            <span class="profile-label">Location</span>
            <span class="profile-value"><?php echo htmlspecialchars($data['location']); ?></span>
        </div>

        <div class="profile-row">
            <span class="profile-label">Field / Industry</span>
            <span class="profile-value"><?php echo htmlspecialchars($data['field']); ?></span>
        </div>

        <div class="company-profile-buttons">
            <label for="cvUpload" class="profile-btn">Apply (Upload CV)</label>
            <input type="file" id="cvUpload" hidden>
            <p id="uploadStatus"></p>

            <a href="Available-Internships.php" class="profile-btn">Back</a>
        </div>

    </div>

</section>

</main>

<!-- ✅ JS for CV validation (UNCHANGED) -->
<script>
const fileInput = document.getElementById("cvUpload");
const statusText = document.getElementById("uploadStatus");

fileInput.addEventListener("change", function () {
    const file = fileInput.files[0];

    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
        statusText.innerText = "❌ File too large (max 2MB)";
        return;
    }

    statusText.innerText = "✅ Uploaded: " + file.name;
});
</script>

</body>
</html>