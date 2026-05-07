<?php
include("comany-session.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile - Launchpath</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="company-profile-page">

    <nav class="company-profile-navbar">
        <div class="company-profile-logo">🚀 Launchpath</div>

        <ul class="company-profile-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="company-dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-profile-container">

        <section class="company-profile-hero">
            <h1>Company Profile</h1>
            <p>View your company details and manage your profile information.</p>
        </section>

        <section class="company-profile-card">

            <div class="company-profile-left">
                <div class="company-profile-image">
                    <img src="<?php echo $company['Logo']; ?>" alt="Company Logo">
                </div>
               <h2><?php echo $company['Name']; ?></h2>
               <span><?php echo $company['Field']; ?></span>
            </div>

            <div class="company-profile-right">
                <h2>Profile Information</h2>

                <div class="profile-row">
                    <span class="profile-label">Company Name</span>
                    <span class="profile-value"><?php echo $company['Name']; ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Email</span>
                   <span class="profile-value"><?php echo $company['Email']; ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Phone Number</span>
                    <span class="profile-value"><?php echo $company['Phone']; ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Location</span>
                   <span class="profile-value"><?php echo $company['Location']; ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Field / Industry</span>
                   <span class="profile-value"><?php echo $company['Field']; ?></span>
                </div>

                <div class="profile-row">
                    <span class="profile-label">Website</span>
                   <span class="profile-value"><?php echo $company['Website']; ?></span>
                </div>

                <div class="profile-row description-row">
                    <span class="profile-label">Description</span>
                    <span class="profile-value"> <?php echo $company['Description']; ?> </span>
                </div>

                <div class="company-profile-buttons">
                    <a href="edit-company-profile.php" class="profile-btn">Edit Profile</a>
                    <a href="company-dashboard.php" class="profile-btn">Back to Dashboard</a>
                </div>
            </div>

        </section>

    </main>

</body>
</html>