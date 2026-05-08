<?php
include("Config.php");
include("company-session.php");

$postedInternships = [];
$sql = 'SELECT id, title, field, start_date, duration FROM internships WHERE company_id = ? ORDER BY id DESC';
$stmt = mysqli_prepare($con, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $companyID);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $postedInternships[] = $r;
    }
    mysqli_stmt_close($stmt);
}

$selectedInternshipId = isset($_GET['internship_id']) ? (int) $_GET['internship_id'] : 0;
$selectedInternship = null;
$applicants = [];

if ($selectedInternshipId > 0) {
    $sqlCheck = 'SELECT id, title, field, start_date, duration FROM internships WHERE id = ? AND company_id = ? LIMIT 1';
    $stmtCheck = mysqli_prepare($con, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, 'ii', $selectedInternshipId, $companyID);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    $selectedInternship = mysqli_fetch_assoc($resCheck);
    mysqli_stmt_close($stmtCheck);

    if ($selectedInternship) {
        $sqlApplicants = 'SELECT s.id AS student_id, s.name, s.email, s.university, s.major, s.profile_photo_path,
                          si.accepted_date, si.status,
                          (SELECT COUNT(*) FROM student_cv WHERE student_id = s.id) AS has_cv
                          FROM student_internships si
                          JOIN Student s ON si.student_id = s.id
                          WHERE si.internship_id = ?
                          ORDER BY si.accepted_date DESC';
        $stmtApp = mysqli_prepare($con, $sqlApplicants);
        mysqli_stmt_bind_param($stmtApp, 'i', $selectedInternshipId);
        mysqli_stmt_execute($stmtApp);
        $resApp = mysqli_stmt_get_result($stmtApp);
        while ($a = mysqli_fetch_assoc($resApp)) {
            $applicants[] = $a;
        }
        mysqli_stmt_close($stmtApp);
    }
}

function e($val) {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applicants - Launchpath</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="cv/cv-style.css">
    <style>
        .applicants-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .applicants-layout { grid-template-columns: 1fr; }
        }

        .internship-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .internship-list-item {
            display: block;
            background: white;
            border-radius: 14px;
            padding: 16px 18px;
            border: 2px solid transparent;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            text-decoration: none;
            color: #1d1b5b;
            transition: 0.2s;
        }
        .internship-list-item:hover {
            border-color: #4fc3f7;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(79,195,247,0.15);
        }
        .internship-list-item.active {
            border-color: #4fc3f7;
            background: linear-gradient(135deg, #1d1b5b 0%, #2d2b7b 100%);
            color: white;
        }
        .internship-list-item.active .ili-field,
        .internship-list-item.active .ili-date {
            color: #ccc;
        }
        .ili-title {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
        }
        .ili-field {
            font-size: 13px;
            color: #6f6161;
        }
        .ili-date {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        .applicants-panel {
            min-height: 300px;
        }
        .applicants-panel h2 {
            font-size: 22px;
            color: #1d1b5b;
            margin-bottom: 6px;
        }
        .applicants-panel .ap-subtitle {
            color: #6f6161;
            font-size: 14px;
            margin-bottom: 18px;
        }
        .applicant-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: white;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            text-decoration: none;
            color: inherit;
            transition: 0.2s;
            border: 2px solid transparent;
        }
        .applicant-card:hover {
            border-color: #4fc3f7;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(79,195,247,0.15);
        }
        .applicant-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background: #eee;
        }
        .applicant-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .applicant-info {
            flex: 1;
        }
        .applicant-name {
            font-weight: 700;
            font-size: 16px;
            color: #1d1b5b;
            margin-bottom: 2px;
        }
        .applicant-detail {
            font-size: 13px;
            color: #6f6161;
        }
        .applicant-badges {
            display: flex;
            flex-direction: column;
            gap: 6px;
            align-items: flex-end;
            flex-shrink: 0;
        }
        .applicant-badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-has-cv {
            background: #d4edda;
            color: #155724;
        }
        .badge-no-cv {
            background: #f8d7da;
            color: #721c24;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge-accepted {
            background: #d4edda;
            color: #155724;
        }
        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .status-actions {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .status-btn {
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }
        .status-btn:hover {
            transform: translateY(-1px);
        }
        .btn-accept {
            background: #28a745;
            color: white;
        }
        .btn-accept:hover {
            background: #218838;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background: #c82333;
        }
        .btn-undo {
            background: #6c757d;
            color: white;
        }
        .btn-undo:hover {
            background: #5a6268;
        }
        .no-applicants {
            text-align: center;
            padding: 40px 20px;
            color: #6f6161;
            font-size: 15px;
        }
        .select-prompt {
            text-align: center;
            padding: 60px 20px;
            color: #6f6161;
        }
        .select-prompt h2 {
            color: #1d1b5b;
            margin-bottom: 10px;
        }
        .sidebar-title {
            font-size: 18px;
            font-weight: 700;
            color: #1d1b5b;
            margin-bottom: 14px;
        }
    </style>
</head>
<body class="company-dashboard-page">

    <nav class="company-dashboard-navbar">
        <div class="company-dashboard-logo">🚀 Launchpath</div>
        <ul class="company-dashboard-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="company-dashboard.php">Dashboard</a></li>
            <li><a href="company-profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-dashboard-container">

        <section class="company-dashboard-hero">
            <div>
                <h1>View Applicants</h1>
                <p>Select an internship to see who applied.</p>
            </div>
        </section>

        <?php if (empty($postedInternships)) { ?>
            <div class="cv-empty-state">
                <h2>No internships posted yet.</h2>
                <p>Post an internship first to start receiving applications.</p>
                <a href="company-post-internship.php" class="profile-btn">Post Internship</a>
            </div>
        <?php } else { ?>

            <div class="applicants-layout">

                <div>
                    <div class="sidebar-title">Your Internships (<?php echo count($postedInternships); ?>)</div>
                    <div class="internship-list">
                        <?php foreach ($postedInternships as $intern) {
                            $isActive = ($selectedInternshipId === (int)$intern['id']);
                        ?>
                            <a href="view-applicants.php?internship_id=<?php echo (int)$intern['id']; ?>"
                               class="internship-list-item <?php echo $isActive ? 'active' : ''; ?>">
                                <div class="ili-title"><?php echo e($intern['title']); ?></div>
                                <div class="ili-field"><?php echo e($intern['field']); ?></div>
                                <div class="ili-date">
                                    <?php
                                    if (!empty($intern['start_date'])) echo date("M Y", strtotime($intern['start_date']));
                                    if (!empty($intern['duration'])) echo ' · ' . e($intern['duration']);
                                    ?>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <div class="applicants-panel">
                    <?php if ($selectedInternshipId === 0) { ?>
                        <div class="select-prompt">
                            <h2>👈 Select an internship</h2>
                            <p>Click on an internship from the left to view its applicants.</p>
                        </div>
                    <?php } elseif (!$selectedInternship) { ?>
                        <div class="cv-msg-error">Internship not found or you don't have access.</div>
                    <?php } else { ?>
                        <h2><?php echo e($selectedInternship['title']); ?></h2>
                        <p class="ap-subtitle">
                            <?php echo count($applicants); ?> applicant<?php echo count($applicants) !== 1 ? 's' : ''; ?>
                        </p>

                        <?php if (empty($applicants)) { ?>
                            <div class="no-applicants">
                                <p>No students have applied to this internship yet.</p>
                            </div>
                        <?php } else { ?>
                            <?php foreach ($applicants as $app) {
                                $photo = !empty($app['profile_photo_path']) ? $app['profile_photo_path'] : './images/Screenshot 2026-03-23 192924.png';
                                $hasCv = (int)$app['has_cv'] > 0;
                                $status = isset($app['status']) && $app['status'] !== '' ? $app['status'] : 'pending';
                            ?>
                                <div class="applicant-card" style="cursor:default;">
                                    <a href="cv/applicant-cv.php?student_id=<?php echo (int)$app['student_id']; ?>&internship_id=<?php echo (int)$selectedInternshipId; ?>"
                                       style="display:flex;align-items:center;gap:16px;text-decoration:none;color:inherit;flex:1;">
                                        <div class="applicant-avatar">
                                            <img src="<?php echo e($photo); ?>" alt="Student photo">
                                        </div>
                                        <div class="applicant-info">
                                            <div class="applicant-name"><?php echo e($app['name']); ?></div>
                                            <div class="applicant-detail">
                                                <?php echo e($app['major']); ?>
                                                <?php if (!empty($app['university'])) echo ' · ' . e($app['university']); ?>
                                            </div>
                                            <div class="applicant-detail">
                                                <?php echo e($app['email']); ?>
                                                <?php if (!empty($app['accepted_date'])) echo ' · Applied ' . date("M j, Y", strtotime($app['accepted_date'])); ?>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="applicant-badges">
                                        <span class="applicant-badge <?php echo $hasCv ? 'badge-has-cv' : 'badge-no-cv'; ?>">
                                            <?php echo $hasCv ? '✓ Has CV' : 'No CV'; ?>
                                        </span>
                                        <span class="applicant-badge badge-<?php echo e($status); ?>">
                                            <?php echo ucfirst(e($status)); ?>
                                        </span>
                                        <div class="status-actions">
                                            <?php if ($status !== 'accepted') { ?>
                                                <form method="POST" action="update-applicant-status.php" style="display:inline;">
                                                    <input type="hidden" name="student_id" value="<?php echo (int)$app['student_id']; ?>">
                                                    <input type="hidden" name="internship_id" value="<?php echo (int)$selectedInternshipId; ?>">
                                                    <input type="hidden" name="action" value="accepted">
                                                    <button type="submit" class="status-btn btn-accept">✓ Accept</button>
                                                </form>
                                            <?php } ?>
                                            <?php if ($status !== 'rejected') { ?>
                                                <form method="POST" action="update-applicant-status.php" style="display:inline;">
                                                    <input type="hidden" name="student_id" value="<?php echo (int)$app['student_id']; ?>">
                                                    <input type="hidden" name="internship_id" value="<?php echo (int)$selectedInternshipId; ?>">
                                                    <input type="hidden" name="action" value="rejected">
                                                    <button type="submit" class="status-btn btn-reject">✗ Reject</button>
                                                </form>
                                            <?php } ?>
                                            <?php if ($status !== 'pending') { ?>
                                                <form method="POST" action="update-applicant-status.php" style="display:inline;">
                                                    <input type="hidden" name="student_id" value="<?php echo (int)$app['student_id']; ?>">
                                                    <input type="hidden" name="internship_id" value="<?php echo (int)$selectedInternshipId; ?>">
                                                    <input type="hidden" name="action" value="pending">
                                                    <button type="submit" class="status-btn btn-undo">↩ Undo</button>
                                                </form>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>

            </div>

        <?php } ?>

    </main>

</body>
</html>
