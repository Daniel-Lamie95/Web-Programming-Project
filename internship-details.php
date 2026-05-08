<?php
session_start();
include('Config.php');

/* 🔒 Allow both students and companies */
$isStudent = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
$isCompany = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'company';

if (!$isStudent && !$isCompany) {
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

$error_message = null;

/* 🔐 Check if company owns this internship */
$isOwner = false;
if ($isCompany && isset($data['company_id'])) {
    $isOwner = ($data['company_id'] == $_SESSION['user_id']);
}

/* 🗑️ Handle delete request */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_internship'])) {
    if ($isCompany && $isOwner) {
        $delete_query = "DELETE FROM internships WHERE id = ? AND company_id = ?";
        $delete_stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, 'ii', $id, $_SESSION['user_id']);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            header("Location: company-dashboard.php?deleted=1");
            exit();
        } else {
            $error_message = "Error deleting internship. Please try again.";
        }
    }
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
    <h1>Intenship Details</h1>
    <p><?php echo $isStudent ? 'View internship details and apply' : 'View and manage internship details'; ?></p>
</section>

<?php if (isset($error_message)): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin: 20px; border-radius: 4px; border: 1px solid #f5c6cb;">
        ⚠️ <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<section class="company-profile-card">

    <div class="company-profile-left">
        <div class="company-profile-image">
            <img src="uploads/internships/<?php echo !empty($data['logo']) ? htmlspecialchars($data['logo']) : 'default.png'; ?>" alt="logo" onerror="this.src='images/default-internship.png'">
        </div>
        <h2><?php echo htmlspecialchars($data['title']); ?></h2>
    </div>

    <div class="company-profile-right">
        <h2>Internship Information</h2>

        <div class="profile-row">
            <span class="profile-label">Internship Name</span>
            <span class="profile-value"><?php echo htmlspecialchars($data['title']); ?></span>
        </div>

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
            <?php if ($isStudent): ?>
                <label for="cvUpload" class="profile-btn">Apply (Upload CV)</label>
                <input type="file" id="cvUpload" hidden>
                <p id="uploadStatus"></p>
            <?php elseif ($isCompany && $isOwner): ?>
                <a href="edit-internship-profile.php?id=<?php echo $id; ?>" class="profile-btn">Edit Internship</a>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="delete_internship" class="profile-btn" style="background-color: #dc3545;" onclick="return confirm('Are you sure you want to delete this internship? This action cannot be undone.');">Delete Internship</button>
                </form>
            <?php endif; ?>

            <a href="Available-Internships.php" class="profile-btn">Back</a>
        </div>

    </div>

</section>

</main>

<!-- ✅ JS for CV validation (only for students) -->
<?php if ($isStudent): ?>
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
<?php endif; ?>

</body>
</html>