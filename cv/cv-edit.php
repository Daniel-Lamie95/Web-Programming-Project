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

$errors = isset($_SESSION['cv_errors']) ? $_SESSION['cv_errors'] : [];
unset($_SESSION['cv_errors']);

function val($cv, $key, $default = '') {
    return isset($cv[$key]) ? htmlspecialchars($cv[$key]) : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Builder - Launchpath</title>
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
            <li><a href="cv-view.php">My CV</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="company-dashboard-container">

        <section class="company-dashboard-hero">
            <div>
                <h1>CV Builder</h1>
                <p><?php echo $cv ? 'Edit your CV information below.' : 'Create your professional CV.'; ?></p>
            </div>
        </section>

        <?php if (!empty($errors)) { ?>
            <div class="cv-msg-error">
                <?php foreach ($errors as $err) { ?>
                    <p><?php echo htmlspecialchars($err); ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form action="cv-save.php" method="POST" onsubmit="return validateCvForm()">

            <div class="cv-form-section">
                <h2>Personal Information</h2>
                <div class="cv-form-row">
                    <div class="cv-form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" value="<?php echo val($cv, 'full_name'); ?>" required>
                    </div>
                    <div class="cv-form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?php echo val($cv, 'email'); ?>" required>
                    </div>
                </div>
                <div class="cv-form-row">
                    <div class="cv-form-group">
                        <label>Job Title</label>
                        <input type="text" name="job_title" value="<?php echo val($cv, 'job_title'); ?>" placeholder="e.g. Software Engineer">
                    </div>
                    <div class="cv-form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo val($cv, 'phone'); ?>" placeholder="+1 234 567 8900">
                    </div>
                </div>
                <div class="cv-form-row">
                    <div class="cv-form-group">
                        <label>Location</label>
                        <input type="text" name="location" value="<?php echo val($cv, 'location'); ?>" placeholder="City, Country">
                    </div>
                    <div class="cv-form-group">
                        <label>LinkedIn</label>
                        <input type="text" name="linkedin" value="<?php echo val($cv, 'linkedin'); ?>" placeholder="https://linkedin.com/in/...">
                    </div>
                </div>
                <div class="cv-form-row">
                    <div class="cv-form-group">
                        <label>GitHub</label>
                        <input type="text" name="github" value="<?php echo val($cv, 'github'); ?>" placeholder="https://github.com/...">
                    </div>
                    <div class="cv-form-group">
                        <label>Portfolio / Website</label>
                        <input type="text" name="portfolio" value="<?php echo val($cv, 'portfolio'); ?>" placeholder="https://...">
                    </div>
                </div>
            </div>

            <div class="cv-form-section">
                <h2>Summary</h2>
                <div class="cv-form-row full">
                    <div class="cv-form-group">
                        <textarea name="summary" rows="3" placeholder="A brief professional summary..."><?php echo val($cv, 'summary'); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="cv-form-section">
                <h2>Education</h2>
                <div id="education-entries">
                    <?php
                    $eduList = isset($cv['education']) ? $cv['education'] : [];
                    foreach ($eduList as $idx => $edu) { ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Education #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>School / University</label><input type="text" name="education[<?php echo $idx; ?>][school]" value="<?php echo htmlspecialchars($edu['school']); ?>"></div>
                                <div class="cv-form-group"><label>Degree</label><input type="text" name="education[<?php echo $idx; ?>][degree]" value="<?php echo htmlspecialchars(isset($edu['degree']) ? $edu['degree'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Location</label><input type="text" name="education[<?php echo $idx; ?>][location]" value="<?php echo htmlspecialchars(isset($edu['location']) ? $edu['location'] : ''); ?>"></div>
                                <div class="cv-form-group"><label>GPA</label><input type="text" name="education[<?php echo $idx; ?>][gpa]" value="<?php echo htmlspecialchars(isset($edu['gpa']) ? $edu['gpa'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Start Date</label><input type="text" name="education[<?php echo $idx; ?>][start_date]" value="<?php echo htmlspecialchars(isset($edu['start_date']) ? $edu['start_date'] : ''); ?>"></div>
                                <div class="cv-form-group"><label>End Date</label><input type="text" name="education[<?php echo $idx; ?>][end_date]" value="<?php echo htmlspecialchars(isset($edu['end_date']) ? $edu['end_date'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row full">
                                <div class="cv-form-group"><label>Description</label><textarea name="education[<?php echo $idx; ?>][description]"><?php echo htmlspecialchars(isset($edu['description']) ? $edu['description'] : ''); ?></textarea></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addEducation()">+ Add Education</button>
            </div>

            <div class="cv-form-section">
                <h2>Experience</h2>
                <div id="experience-entries">
                    <?php
                    $expList = isset($cv['experience']) ? $cv['experience'] : [];
                    foreach ($expList as $idx => $exp) {
                        $isCurrent = isset($exp['current']) && $exp['current'];
                    ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Experience #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Company</label><input type="text" name="experience[<?php echo $idx; ?>][company]" value="<?php echo htmlspecialchars($exp['company']); ?>"></div>
                                <div class="cv-form-group"><label>Position</label><input type="text" name="experience[<?php echo $idx; ?>][position]" value="<?php echo htmlspecialchars(isset($exp['position']) ? $exp['position'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Location</label><input type="text" name="experience[<?php echo $idx; ?>][location]" value="<?php echo htmlspecialchars(isset($exp['location']) ? $exp['location'] : ''); ?>"></div>
                                <div class="cv-form-group"><label>Start Date</label><input type="text" name="experience[<?php echo $idx; ?>][start_date]" value="<?php echo htmlspecialchars(isset($exp['start_date']) ? $exp['start_date'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group">
                                    <div class="cv-checkbox-row">
                                        <input type="checkbox" name="experience[<?php echo $idx; ?>][current]" value="1" <?php echo $isCurrent ? 'checked' : ''; ?> onchange="toggleEndDate(this)">
                                        <label>Currently working here</label>
                                    </div>
                                </div>
                                <div class="cv-form-group"><label>End Date</label><input type="text" name="experience[<?php echo $idx; ?>][end_date]" value="<?php echo htmlspecialchars(isset($exp['end_date']) && $exp['end_date'] !== 'Present' ? $exp['end_date'] : ''); ?>" <?php echo $isCurrent ? 'disabled placeholder="Present"' : ''; ?>></div>
                            </div>
                            <div class="cv-form-row full">
                                <div class="cv-form-group">
                                    <label>Bullet Points</label>
                                    <div class="cv-bullets-container">
                                        <?php
                                        $bullets = isset($exp['bullets']) ? $exp['bullets'] : [];
                                        foreach ($bullets as $b) { ?>
                                            <div class="cv-bullet-row">
                                                <input type="text" name="experience[<?php echo $idx; ?>][bullets][]" value="<?php echo htmlspecialchars($b); ?>">
                                                <button type="button" class="cv-bullet-remove" onclick="this.closest('.cv-bullet-row').remove()">&times;</button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <button type="button" class="cv-btn-add-bullet" onclick="addBullet(this, 'experience', <?php echo $idx; ?>)">+ Add Bullet</button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addExperience()">+ Add Experience</button>
            </div>

            <div class="cv-form-section">
                <h2>Projects</h2>
                <div id="project-entries">
                    <?php
                    $projList = isset($cv['projects']) ? $cv['projects'] : [];
                    foreach ($projList as $idx => $proj) { ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Project #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Project Name</label><input type="text" name="projects[<?php echo $idx; ?>][name]" value="<?php echo htmlspecialchars($proj['name']); ?>"></div>
                                <div class="cv-form-group"><label>Link</label><input type="text" name="projects[<?php echo $idx; ?>][link]" value="<?php echo htmlspecialchars(isset($proj['link']) ? $proj['link'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row full">
                                <div class="cv-form-group"><label>Technologies</label><input type="text" name="projects[<?php echo $idx; ?>][technologies]" value="<?php echo htmlspecialchars(isset($proj['technologies']) ? $proj['technologies'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row full">
                                <div class="cv-form-group">
                                    <label>Bullet Points</label>
                                    <div class="cv-bullets-container">
                                        <?php
                                        $bullets = isset($proj['bullets']) ? $proj['bullets'] : [];
                                        foreach ($bullets as $b) { ?>
                                            <div class="cv-bullet-row">
                                                <input type="text" name="projects[<?php echo $idx; ?>][bullets][]" value="<?php echo htmlspecialchars($b); ?>">
                                                <button type="button" class="cv-bullet-remove" onclick="this.closest('.cv-bullet-row').remove()">&times;</button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <button type="button" class="cv-btn-add-bullet" onclick="addBullet(this, 'projects', <?php echo $idx; ?>)">+ Add Bullet</button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addProject()">+ Add Project</button>
            </div>

            <div class="cv-form-section">
                <h2>Skills</h2>
                <div id="skills-entries">
                    <?php
                    $skillList = isset($cv['skills']) ? $cv['skills'] : [];
                    foreach ($skillList as $idx => $sk) { ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Skill Group #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Category</label><input type="text" name="skills[<?php echo $idx; ?>][category]" value="<?php echo htmlspecialchars($sk['category']); ?>"></div>
                                <div class="cv-form-group"><label>Skills (comma separated)</label><input type="text" name="skills[<?php echo $idx; ?>][items]" value="<?php echo htmlspecialchars(isset($sk['items']) ? $sk['items'] : ''); ?>"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addSkill()">+ Add Skill Group</button>
            </div>

            <div class="cv-form-section">
                <h2>Certificates</h2>
                <div id="certificate-entries">
                    <?php
                    $certList = isset($cv['certificates']) ? $cv['certificates'] : [];
                    foreach ($certList as $idx => $cert) { ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Certificate #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Certificate Name</label><input type="text" name="certificates[<?php echo $idx; ?>][name]" value="<?php echo htmlspecialchars($cert['name']); ?>"></div>
                                <div class="cv-form-group"><label>Issuer</label><input type="text" name="certificates[<?php echo $idx; ?>][issuer]" value="<?php echo htmlspecialchars(isset($cert['issuer']) ? $cert['issuer'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Date</label><input type="text" name="certificates[<?php echo $idx; ?>][date]" value="<?php echo htmlspecialchars(isset($cert['date']) ? $cert['date'] : ''); ?>"></div>
                                <div class="cv-form-group"><label>Link</label><input type="text" name="certificates[<?php echo $idx; ?>][link]" value="<?php echo htmlspecialchars(isset($cert['link']) ? $cert['link'] : ''); ?>"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addCertificate()">+ Add Certificate</button>
            </div>

            <div class="cv-form-section">
                <h2>Awards</h2>
                <div id="award-entries">
                    <?php
                    $awardList = isset($cv['awards']) ? $cv['awards'] : [];
                    foreach ($awardList as $idx => $aw) { ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Award #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Award Name</label><input type="text" name="awards[<?php echo $idx; ?>][name]" value="<?php echo htmlspecialchars($aw['name']); ?>"></div>
                                <div class="cv-form-group"><label>Date</label><input type="text" name="awards[<?php echo $idx; ?>][date]" value="<?php echo htmlspecialchars(isset($aw['date']) ? $aw['date'] : ''); ?>"></div>
                            </div>
                            <div class="cv-form-row full">
                                <div class="cv-form-group"><label>Description</label><input type="text" name="awards[<?php echo $idx; ?>][description]" value="<?php echo htmlspecialchars(isset($aw['description']) ? $aw['description'] : ''); ?>"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addAward()">+ Add Award</button>
            </div>

            <div class="cv-form-section">
                <h2>Languages</h2>
                <div id="language-entries">
                    <?php
                    $langList = isset($cv['languages']) ? $cv['languages'] : [];
                    foreach ($langList as $idx => $lang) { ?>
                        <div class="cv-entry">
                            <div class="cv-entry-header">
                                <span>Language #<?php echo $idx + 1; ?></span>
                                <button type="button" class="cv-btn-remove" onclick="this.closest('.cv-entry').remove()">Remove</button>
                            </div>
                            <div class="cv-form-row">
                                <div class="cv-form-group"><label>Language</label><input type="text" name="languages[<?php echo $idx; ?>][name]" value="<?php echo htmlspecialchars($lang['name']); ?>"></div>
                                <div class="cv-form-group"><label>Level</label>
                                    <select name="languages[<?php echo $idx; ?>][level]">
                                        <option value="">Select level</option>
                                        <option value="Native" <?php echo (isset($lang['level']) && $lang['level'] === 'Native') ? 'selected' : ''; ?>>Native</option>
                                        <option value="Fluent" <?php echo (isset($lang['level']) && $lang['level'] === 'Fluent') ? 'selected' : ''; ?>>Fluent</option>
                                        <option value="Advanced" <?php echo (isset($lang['level']) && $lang['level'] === 'Advanced') ? 'selected' : ''; ?>>Advanced</option>
                                        <option value="Intermediate" <?php echo (isset($lang['level']) && $lang['level'] === 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                        <option value="Beginner" <?php echo (isset($lang['level']) && $lang['level'] === 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <button type="button" class="cv-btn-add" onclick="addLanguage()">+ Add Language</button>
            </div>

            <div class="cv-form-actions">
                <button type="submit" class="cv-form-submit">Save CV</button>
                <a href="cv-view.php" class="profile-btn">Cancel</a>
            </div>

        </form>

    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="cv-editor.js"></script>
</body>
</html>
