<?php
include("config.php");
include("company-session.php");
$postedInternships = [];
$sql_active = 'SELECT i.id, i.title, i.field, i.start_date, i.duration FROM internships i WHERE i.company_id = ? ORDER BY i.id DESC';
$stmt4 = mysqli_prepare($con, $sql_active);
if ($stmt4) {
    mysqli_stmt_bind_param($stmt4, 'i', $companyID);
    mysqli_stmt_execute($stmt4);
    $res4 = mysqli_stmt_get_result($stmt4);
    if ($res4) {
        while ($r = mysqli_fetch_assoc($res4)) {
            $postedInternships[] = $r;
        }
    }
    mysqli_stmt_close($stmt4);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard - Launchpath</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="company-dashboard-page">

    <nav class="company-dashboard-navbar">
        <div class="company-dashboard-logo">🚀 Launchpath</div>

        <ul class="company-dashboard-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="company-profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-dashboard-container">

        <section class="company-dashboard-hero">
            <div>
                <h1>Company Dashboard</h1>
                <p>Manage your company profile, internships, and applicants in one place.</p>
            </div>
        </section>

        <section class="company-dashboard-top">
            <div class="company-dashboard-profile-card">
                <div class="company-dashboard-avatar">
                    <img src="<?php echo $company['Logo']; ?>" alt="Company Logo">
                </div>
                <h3><?php echo $company['Name']; ?></h3>
                <span><?php echo $company['Field']; ?></span>
            </div>

            <div class="company-dashboard-info-card">
                <h2>Company Information</h2>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Company Name</span>
                   <span class="dashboard-value"> <?php echo $company['Name']; ?></span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Field</span>
                    <span class="dashboard-value"> <?php echo $company['Field']; ?> </span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Email</span>
                    <span class="dashboard-value"> <?php echo $company['Email']; ?> </span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Location</span>
                    <span class="dashboard-value"> <?php echo $company['Location']; ?> </span>
                </div>

                <div class="dashboard-info-row">
                    <span class="dashboard-label">Phone</span>
                   <span class="dashboard-value"> <?php echo $company['Phone']; ?> </span>
                </div>
            </div>
        </section>

        <section class="company-dashboard-stats">
            <div class="dashboard-stat-card">
                <h3><?php echo count($postedInternships); ?></h3>
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

        <section class="company-dashboard-actions">
            <h2>Quick Actions</h2>

            <div class="company-dashboard-actions-grid">
                

                <a href="company-post-internship.php" class="dashboard-action-card">
                    <h3>Post Internship</h3>
                    <p>Add a new internship opportunity</p>
                </a>

                <a href="#recent-internships" class="dashboard-action-card">
                    <h3>Manage Internships</h3>
                    <p>View and edit posted internships</p>
                </a>

                <a href="view-applicants.php" class="dashboard-action-card">
                    <h3>View Applicants</h3>
                    <p>Check students applications</p>
                </a>
            </div>
        </section>

        <section class="company-dashboard-internships" id="recent-internships">
            <h2>Recent Internships</h2>

            <div class="company-dashboard-internships-grid">
                <?php if (empty($postedInternships)) { ?>
                    <p>You have no accpostedepted internships.</p>
                <?php } else { ?>
                    <?php foreach ($postedInternships as $act) { ?>
                        <a href="internship-details.php?id=<?php echo (int)$act['id']; ?>" class="dashboard-internship-card">
                            <h3><?php echo htmlspecialchars($act['title']); ?></h3>
                            <p><?php echo htmlspecialchars($act['field']); ?></p>
                            <span><?php echo date("F Y", strtotime($act['start_date'])) . ' - ' . htmlspecialchars($act['duration']); ?></span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
        </section>

    </main>

</body>
</html>