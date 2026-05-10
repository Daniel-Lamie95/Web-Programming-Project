<?php
session_start();
include('Config.php');


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'company') {
    header('Location: login.html');
    exit();
}

$company_id = $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: Available-Internships.php');
    exit();
}


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


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = isset($_POST['title']) ? $_POST['title'] : $data['title'];
    $duration = isset($_POST['duration']) ? $_POST['duration'] : $data['duration'];
    $description = isset($_POST['description']) ? $_POST['description'] : $data['description'];
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : $data['start_date'];
    $location = isset($_POST['location']) ? $_POST['location'] : $data['location'];
    $field = isset($_POST['field']) ? $_POST['field'] : $data['field'];
    $logo = $data['logo']; 

   
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/internships/';
        
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['photo']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'internship_' . $id . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                
                if ($data['logo'] && $data['logo'] !== 'default.png' && file_exists('uploads/internships/' . $data['logo'])) {
                    unlink('uploads/internships/' . $data['logo']);
                }
                $logo = $new_file_name;
            }
        }
    }

    $update = "UPDATE internships SET title = ?, duration = ?, description = ?, start_date = ?, location = ?, field = ?, logo = ? WHERE id = ? AND company_id = ?";

    $stmt = mysqli_prepare($con, $update);
    
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'sssssssii', $title, $duration, $description, $startDate, $location, $field, $logo, $id, $company_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    
   
    header("Location: internship-details.php?id=" . $id);
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
        <li><a href="index.html">Home</a></li>
        <li><a href="company-dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<main class="company-profile-container">

<section class="company-profile-hero">
    <h1>Edit Internship Profile</h1>
    <p>Update Internship information.</p>
</section>

<section class="company-profile-card">

    
    <div class="company-profile-left">
        <div class="company-profile-image">
            <img src="uploads/internships/<?php echo htmlspecialchars($data['logo']); ?>" alt="Logo" onerror="this.src='images/default-internship.png'">
        </div>
        <h2><?php echo htmlspecialchars($data['title']); ?></h2>
        <span><?php echo htmlspecialchars($data['field']); ?></span>
    </div>

   
    <div class="company-profile-right">
        <h2>Edit Internship</h2>

        <form method="POST" enctype="multipart/form-data">

            <div class="profile-row">
                <label class="profile-label">Internship Name</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
            </div>

            <div class="profile-row">
                <label class="profile-label">Field / Industry</label>
                <input type="text" name="field" value="<?php echo htmlspecialchars($data['field']); ?>" required>
            </div>

            <div class="profile-row">
                <label class="profile-label">Duration</label>
                <input type="text" name="duration" value="<?php echo htmlspecialchars($data['duration']); ?>" required>
            </div>

            <div class="profile-row">
                <label class="profile-label">Start Date</label>
                <input type="date" name="startDate" value="<?php echo $data['start_date']; ?>" required>
            </div>

            <div class="profile-row">
                <label class="profile-label">Location</label>
                <input type="text" name="location" value="<?php echo htmlspecialchars($data['location']); ?>" required>
            </div>

            <div class="profile-row description-row">
                <label class="profile-label">Description</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($data['description']); ?></textarea>
            </div>

            <div class="profile-row">
                <label class="profile-label">Change Internship Photo</label>
                <div class="photo-preview" id="photoPreview" style="margin-bottom: 10px;">
                    <img src="uploads/internships/<?php echo htmlspecialchars($data['logo']); ?>" alt="Current Photo" style="max-width: 200px; height: auto; border-radius: 8px;">
                </div>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewPhoto(event)">
                <small style="color: #666;">Supported formats: JPG, PNG, GIF (Max size: 5MB)</small>
            </div>

            <div class="company-profile-buttons">
                <a href="company-dashboard.php" class="profile-btn">Cancel</a>
                <button type="submit" class="profile-btn">Save Changes</button>
            </div>

        </form>
    </div>

</section>

</main>

<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 200px; height: auto; border-radius: 8px;">';
        };
        reader.readAsDataURL(file);
    }
}
</script>

</body>
</html>