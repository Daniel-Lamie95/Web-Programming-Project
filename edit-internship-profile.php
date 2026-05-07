<?php
session_start();
include('Config.php');

/* 🔒 Check company login */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'company') {
    header('Location: login.html');
    exit();
}

$company_id = $_SESSION['user_id'];

/* 📌 Get internship ID */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: Available-Internships.php');
    exit();
}

/* 📥 GET internship (ONLY this company’s internship) */
$sql = "SELECT * FROM internships WHERE id = ? AND company_id = ? LIMIT 1";
$stmt = mysqli_prepare($con, $sql);

mysqli_stmt_bind_param($stmt, 'ii', $id, $company_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = mysqli_fetch_assoc($result);

if (!$data) {
    header('Location: Available-Internships.php');
    exit();
}

/* 💾 UPDATE */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $startDate = $_POST['startDate'];
    $location = $_POST['location'];
    $field = $_POST['field'];

    $update = "UPDATE internships SET 
        duration = ?, 
        description = ?, 
        start_date = ?, 
        location = ?, 
        field = ?
        WHERE id = ? AND company_id = ?";

    $stmt = mysqli_prepare($con, $update);
    mysqli_stmt_bind_param($stmt, 'sssssii', 
        $duration, $description, $startDate, $location, $field, $id, $company_id
    );
    mysqli_stmt_execute($stmt);

    header("Location: edit-internship.php?id=" . $id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Internship</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="company-profile-page">

<nav class="company-profile-navbar">
    <div class="company-profile-logo">🚀 Launchpad</div>

    <ul class="company-profile-links">
        <li><a href="homepage.html">Home</a></li>
        <li><a href="Available-Internships.html">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main class="company-profile-container">

<section class="company-profile-hero">
    <h1>Edit Internship Profile</h1>
    <p>Update Internship information.</p>
</section>

<section class="company-profile-card">

    <!-- 🔵 LEFT SIDE -->
    <div class="company-profile-left">
        <div class="company-profile-image">
            <img src="images/<?php echo htmlspecialchars($data['logo']); ?>" alt="Logo">
        </div>
        <h2><?php echo htmlspecialchars($data['title']); ?></h2>
        <span><?php echo htmlspecialchars($data['field']); ?></span>
    </div>

    <!-- 🔵 RIGHT SIDE -->
    <div class="company-profile-right">
        <h2>Edit Internship</h2>

        <form method="POST">

            <div class="profile-row">
                <label class="profile-label">Duration</label>
                <input type="text" name="duration" value="<?php echo htmlspecialchars($data['duration']); ?>">
            </div>

            <div class="profile-row description-row">
                <label class="profile-label">Description</label>
                <textarea name="description" rows="5"><?php echo htmlspecialchars($data['description']); ?></textarea>
            </div>

            <div class="profile-row">
                <label class="profile-label">Start Date</label>
                <input type="date" name="startDate" value="<?php echo $data['start_date']; ?>">
            </div>

            <div class="profile-row">
                <label class="profile-label">Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($data['location']); ?>">
            </div>

            <div class="profile-row">
                <label class="profile-label">Field / Industry</label>
                <input type="text" name="field" value="<?php echo htmlspecialchars($data['field']); ?>">
            </div>

            <div class="company-profile-buttons">
                <a href="Available-Internships.html" class="profile-btn">Cancel</a>
                <button type="submit" class="profile-btn">Save Changes</button>
            </div>

        </form>
    </div>

</section>

</main>

</body>
</html>