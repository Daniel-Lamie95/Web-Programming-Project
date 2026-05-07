<?php

include("company-session.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="company-profile-page">

    <nav class="company-profile-navbar">
        <div class="company-profile-logo">🚀 Launchpath</div>

        <ul class="company-profile-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="company-dashboard.php">Dashboard</a></li>
            <li><a href="company-profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-profile-container">

        <section class="company-profile-hero">
            <h1>Edit Company Profile</h1>
            <p>Update your company information.</p>
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
                <h2>Edit Information</h2>

                <form action="update-company-profile.php" method="post" enctype="multipart/form-data">
                    <div class="profile-row">
                        <label class="profile-label" for="companyName">Company Name</label>
                       <input type="text" id="companyName" name="companyName" value="<?php echo $company['Name']; ?>" >
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="companyEmail">Email</label>
                      <input type="email" id="companyEmail" name="companyEmail" value="<?php echo $company['Email']; ?>">
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo $company['Phone']; ?>">
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="location">Location</label>
                       <input type="text" id="location" name="location" value="<?php echo $company['Location']; ?>">
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="field">Field / Industry</label>
                        <input type="text" id="field" name="field" value="<?php echo $company['Field']; ?>">
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="website">Website</label>
                       <input type="url" id="website" name="website" value="<?php echo $company['Website']; ?>">
                    </div>

                    <div class="profile-row description-row">
                        <label class="profile-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="5"><?php echo $company['Description']; ?></textarea>
                    </div>

                    <div class="profile-row">
                        <label class="profile-label" for="logo">Company Logo</label>
                        <input type="file" id="logo" name="logo">
                    </div>

                    <div class="company-profile-buttons">
                        <a href="company-profile.php" class="profile-btn">Cancel</a>
                        <button type="submit" class="profile-btn">Save Changes</button>
                    </div>
                </form>
            </div>

        </section>

    </main>

</body>
</html>