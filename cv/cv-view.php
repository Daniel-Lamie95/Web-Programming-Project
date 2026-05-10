<?php
session_start();
include('../Config.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.html');
    exit();
}

$studentId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($studentId <= 0) {
    header('Location: ../login.html');
    exit();
}

$cv = null;
$sql = 'SELECT cv_data FROM student_cv WHERE student_id = ? LIMIT 1';
$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $studentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    if ($row && isset($row['cv_data'])) {
        $cv = json_decode($row['cv_data'], true);
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($con);

$successMsg = isset($_SESSION['cv_success']) ? $_SESSION['cv_success'] : '';
unset($_SESSION['cv_success']);

function e($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My CV - Launchpath</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="cv-style.css">
</head>
<body class="company-dashboard-page">

    <nav class="company-dashboard-navbar">
        <div class="company-dashboard-logo">🚀 Launchpath</div>
        <ul class="company-dashboard-links">
            <li><a href="../index.html">Home</a></li>
            <li><a href="../student-dashboard.php">Dashboard</a></li>
            <li><a href="../student-profile.php">Profile</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-dashboard-container">

        <section class="company-dashboard-hero">
            <div>
                <h1>My CV</h1>
                <p>View, edit, or download your professional CV.</p>
            </div>
        </section>

        <?php if ($successMsg !== '') { ?>
            <div class="cv-msg-success"><?php echo e($successMsg); ?></div>
        <?php } ?>

        <?php if (!$cv) { ?>

            <div class="cv-empty-state">
                <h2>You have not created a CV yet.</h2>
                <p>Build your professional CV to use when applying to internships.</p>
                <a href="cv-edit.php" class="profile-btn">Create CV</a>
            </div>

        <?php } else { ?>

            <div class="cv-view-actions">
                <a href="cv-edit.php" class="profile-btn">Edit CV</a>
                <button onclick="window.print()" class="profile-btn" style="border:none;cursor:pointer;">Print / Save as PDF</button>
            </div>

            <div class="cv-document">

                <?php if (!empty($cv['full_name'])) { ?>
                    <div class="cv-doc-name"><?php echo e($cv['full_name']); ?></div>
                <?php } ?>

                <?php if (!empty($cv['job_title'])) { ?>
                    <div class="cv-doc-title"><?php echo e($cv['job_title']); ?></div>
                <?php } ?>

                <div class="cv-doc-contact">
                    <?php
                    $contacts = [];
                    if (!empty($cv['email'])) $contacts[] = '<a href="mailto:' . e($cv['email']) . '">' . e($cv['email']) . '</a>';
                    if (!empty($cv['phone'])) $contacts[] = e($cv['phone']);
                    if (!empty($cv['location'])) $contacts[] = e($cv['location']);
                    if (!empty($cv['linkedin'])) $contacts[] = '<a href="' . e($cv['linkedin']) . '">' . e($cv['linkedin']) . '</a>';
                    if (!empty($cv['github'])) $contacts[] = '<a href="' . e($cv['github']) . '">' . e($cv['github']) . '</a>';
                    if (!empty($cv['portfolio'])) $contacts[] = '<a href="' . e($cv['portfolio']) . '">' . e($cv['portfolio']) . '</a>';
                    echo implode(' <span class="cv-sep">|</span> ', $contacts);
                    ?>
                </div>

                <?php if (!empty($cv['summary'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Summary</div>
                        <div class="cv-doc-summary"><?php echo e($cv['summary']); ?></div>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['education'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Education</div>
                        <?php foreach ($cv['education'] as $edu) { ?>
                            <div class="cv-doc-entry">
                                <div class="cv-doc-entry-header">
                                    <span class="cv-doc-entry-left"><?php echo e($edu['school']); ?></span>
                                    <span class="cv-doc-entry-right">
                                        <?php
                                        $dates = [];
                                        if (!empty($edu['start_date'])) $dates[] = e($edu['start_date']);
                                        if (!empty($edu['end_date'])) $dates[] = e($edu['end_date']);
                                        echo implode(' — ', $dates);
                                        ?>
                                    </span>
                                </div>
                                <?php if (!empty($edu['degree']) || !empty($edu['location'])) { ?>
                                    <div class="cv-doc-entry-sub">
                                        <span><?php echo e(isset($edu['degree']) ? $edu['degree'] : ''); ?><?php if (!empty($edu['gpa'])) echo ' | GPA: ' . e($edu['gpa']); ?></span>
                                        <span><?php echo e(isset($edu['location']) ? $edu['location'] : ''); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($edu['description'])) { ?>
                                    <div class="cv-doc-entry-desc"><?php echo e($edu['description']); ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['experience'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Experience</div>
                        <?php foreach ($cv['experience'] as $exp) { ?>
                            <div class="cv-doc-entry">
                                <div class="cv-doc-entry-header">
                                    <span class="cv-doc-entry-left"><?php echo e($exp['company']); ?></span>
                                    <span class="cv-doc-entry-right">
                                        <?php
                                        $dates = [];
                                        if (!empty($exp['start_date'])) $dates[] = e($exp['start_date']);
                                        $endDate = isset($exp['end_date']) && $exp['end_date'] !== '' ? $exp['end_date'] : '';
                                        if (isset($exp['current']) && $exp['current']) $endDate = 'Present';
                                        if ($endDate !== '') $dates[] = e($endDate);
                                        echo implode(' — ', $dates);
                                        ?>
                                    </span>
                                </div>
                                <?php if (!empty($exp['position']) || !empty($exp['location'])) { ?>
                                    <div class="cv-doc-entry-sub">
                                        <span><?php echo e(isset($exp['position']) ? $exp['position'] : ''); ?></span>
                                        <span><?php echo e(isset($exp['location']) ? $exp['location'] : ''); ?></span>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($exp['bullets'])) { ?>
                                    <ul class="cv-doc-bullets">
                                        <?php foreach ($exp['bullets'] as $b) { if (trim($b) === '') continue; ?>
                                            <li><?php echo e($b); ?></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['projects'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Projects</div>
                        <?php foreach ($cv['projects'] as $proj) { ?>
                            <div class="cv-doc-entry">
                                <div class="cv-doc-entry-header">
                                    <span class="cv-doc-entry-left">
                                        <?php echo e($proj['name']); ?>
                                        <?php if (!empty($proj['link'])) { ?>
                                            — <a href="<?php echo e($proj['link']); ?>" style="color:#333;font-weight:normal;font-size:12px;"><?php echo e($proj['link']); ?></a>
                                        <?php } ?>
                                    </span>
                                </div>
                                <?php if (!empty($proj['technologies'])) { ?>
                                    <div class="cv-doc-entry-sub"><span><?php echo e($proj['technologies']); ?></span></div>
                                <?php } ?>
                                <?php if (!empty($proj['bullets'])) { ?>
                                    <ul class="cv-doc-bullets">
                                        <?php foreach ($proj['bullets'] as $b) { if (trim($b) === '') continue; ?>
                                            <li><?php echo e($b); ?></li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['skills'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Skills</div>
                        <?php foreach ($cv['skills'] as $sk) {
                            if (empty($sk['category']) && empty($sk['items'])) continue;
                        ?>
                            <div class="cv-doc-skills-row">
                                <?php if (!empty($sk['category'])) { ?>
                                    <span class="cv-doc-skills-cat"><?php echo e($sk['category']); ?>:</span>
                                <?php } ?>
                                <?php echo e(isset($sk['items']) ? $sk['items'] : ''); ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['certificates'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Certificates</div>
                        <?php foreach ($cv['certificates'] as $cert) { ?>
                            <div class="cv-doc-cert-row">
                                <strong><?php echo e($cert['name']); ?></strong>
                                <?php if (!empty($cert['issuer'])) echo ' — ' . e($cert['issuer']); ?>
                                <?php if (!empty($cert['date'])) echo ' (' . e($cert['date']) . ')'; ?>
                                <?php if (!empty($cert['link'])) { ?>
                                    — <a href="<?php echo e($cert['link']); ?>"><?php echo e($cert['link']); ?></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['awards'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Awards</div>
                        <?php foreach ($cv['awards'] as $aw) { ?>
                            <div class="cv-doc-award-row">
                                <strong><?php echo e($aw['name']); ?></strong>
                                <?php if (!empty($aw['date'])) echo ' (' . e($aw['date']) . ')'; ?>
                                <?php if (!empty($aw['description'])) echo ' — ' . e($aw['description']); ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!empty($cv['languages'])) { ?>
                    <div class="cv-doc-section">
                        <div class="cv-doc-section-title">Languages</div>
                        <?php foreach ($cv['languages'] as $lang) { ?>
                            <div class="cv-doc-lang-row">
                                <?php echo e($lang['name']); ?>
                                <?php if (!empty($lang['level'])) echo ' — ' . e($lang['level']); ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

        <?php } ?>

    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
