<?php
session_start();
include('../Config.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'company') {
    header('Location: ../login.html');
    exit();
}

$companyId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($companyId <= 0) {
    header('Location: ../login.html');
    exit();
}

$studentId = isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0;
$internshipId = isset($_GET['internship_id']) ? (int) $_GET['internship_id'] : 0;

if ($studentId <= 0 || $internshipId <= 0) {
    echo '<!DOCTYPE html><html><head><title>Error</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="cv-style.css"></head>';
    echo '<body class="company-dashboard-page"><div class="company-dashboard-container" style="padding-top:120px;"><div class="cv-msg-error">Invalid request.</div></div></body></html>';
    exit();
}

$sqlInternship = 'SELECT id FROM internships WHERE id = ? AND company_id = ? LIMIT 1';
$stmtI = mysqli_prepare($con, $sqlInternship);
mysqli_stmt_bind_param($stmtI, 'ii', $internshipId, $companyId);
mysqli_stmt_execute($stmtI);
$resI = mysqli_stmt_get_result($stmtI);
$internship = mysqli_fetch_assoc($resI);
mysqli_stmt_close($stmtI);

if (!$internship) {
    echo '<!DOCTYPE html><html><head><title>Access Denied</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="cv-style.css"></head>';
    echo '<body class="company-dashboard-page"><div class="company-dashboard-container" style="padding-top:120px;"><div class="cv-msg-error">You do not have access to this internship.</div></div></body></html>';
    mysqli_close($con);
    exit();
}

$sqlApp = 'SELECT id, status FROM student_internships WHERE student_id = ? AND internship_id = ? LIMIT 1';
$stmtA = mysqli_prepare($con, $sqlApp);
mysqli_stmt_bind_param($stmtA, 'ii', $studentId, $internshipId);
mysqli_stmt_execute($stmtA);
$resA = mysqli_stmt_get_result($stmtA);
$application = mysqli_fetch_assoc($resA);
mysqli_stmt_close($stmtA);

$appStatus = ($application && isset($application['status']) && $application['status'] !== '') ? $application['status'] : 'pending';

if (!$application) {
    echo '<!DOCTYPE html><html><head><title>Access Denied</title><link rel="stylesheet" href="../css/style.css"><link rel="stylesheet" href="cv-style.css"></head>';
    echo '<body class="company-dashboard-page"><div class="company-dashboard-container" style="padding-top:120px;"><div class="cv-msg-error">This student has not applied to this internship.</div></div></body></html>';
    mysqli_close($con);
    exit();
}

$sqlStudent = 'SELECT name FROM Student WHERE id = ? LIMIT 1';
$stmtS = mysqli_prepare($con, $sqlStudent);
mysqli_stmt_bind_param($stmtS, 'i', $studentId);
mysqli_stmt_execute($stmtS);
$resS = mysqli_stmt_get_result($stmtS);
$student = mysqli_fetch_assoc($resS);
mysqli_stmt_close($stmtS);

$studentName = $student ? $student['name'] : 'Student';

$cv = null;
$sqlCv = 'SELECT cv_data FROM student_cv WHERE student_id = ? LIMIT 1';
$stmtCv = mysqli_prepare($con, $sqlCv);
mysqli_stmt_bind_param($stmtCv, 'i', $studentId);
mysqli_stmt_execute($stmtCv);
$resCv = mysqli_stmt_get_result($stmtCv);
$rowCv = mysqli_fetch_assoc($resCv);
if ($rowCv && isset($rowCv['cv_data'])) {
    $cv = json_decode($rowCv['cv_data'], true);
}
mysqli_stmt_close($stmtCv);
mysqli_close($con);

function e($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant CV - Launchpath</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="cv-style.css">
</head>
<body class="company-dashboard-page">

    <nav class="company-dashboard-navbar">
        <div class="company-dashboard-logo">🚀 Launchpath</div>
        <ul class="company-dashboard-links">
            <li><a href="../index.html">Home</a></li>
            <li><a href="../company-dashboard.php">Dashboard</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-dashboard-container">

        <section class="company-dashboard-hero">
            <div>
                <h1>Applicant CV</h1>
                <p>CV of <?php echo e($studentName); ?></p>
            </div>
        </section>

        <?php if (!$cv) { ?>

            <div class="cv-empty-state">
                <h2>This student has not created a CV yet.</h2>
                <p>The applicant has not submitted a CV on the platform.</p>
                <a href="javascript:history.back()" class="profile-btn">Go Back</a>
            </div>

        <?php } else { ?>

            <div class="cv-view-actions">
                <a href="../view-applicants.php?internship_id=<?php echo (int)$internshipId; ?>" class="profile-btn">Back to Applicants</a>
                <button onclick="window.print()" class="profile-btn" style="border:none;cursor:pointer;">Print / Save as PDF</button>

                <span style="display:inline-block;padding:8px 16px;border-radius:12px;font-weight:600;font-size:14px;
                    <?php if ($appStatus === 'accepted') echo 'background:#d4edda;color:#155724;';
                    elseif ($appStatus === 'rejected') echo 'background:#f8d7da;color:#721c24;';
                    else echo 'background:#fff3cd;color:#856404;'; ?>">Status: <?php echo ucfirst(e($appStatus)); ?></span>

                <?php if ($appStatus !== 'accepted') { ?>
                    <form method="POST" action="../update-applicant-status.php" style="display:inline;">
                        <input type="hidden" name="student_id" value="<?php echo (int)$studentId; ?>">
                        <input type="hidden" name="internship_id" value="<?php echo (int)$internshipId; ?>">
                        <input type="hidden" name="action" value="accepted">
                        <input type="hidden" name="redirect" value="cv">
                        <button type="submit" class="profile-btn" style="background:#28a745;border:none;cursor:pointer;">✓ Accept</button>
                    </form>
                <?php } ?>
                <?php if ($appStatus !== 'rejected') { ?>
                    <form method="POST" action="../update-applicant-status.php" style="display:inline;">
                        <input type="hidden" name="student_id" value="<?php echo (int)$studentId; ?>">
                        <input type="hidden" name="internship_id" value="<?php echo (int)$internshipId; ?>">
                        <input type="hidden" name="action" value="rejected">
                        <input type="hidden" name="redirect" value="cv">
                        <button type="submit" class="profile-btn" style="background:#dc3545;border:none;cursor:pointer;">✗ Reject</button>
                    </form>
                <?php } ?>
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
