<?php
session_start();
include('../Config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cv-edit.php');
    exit();
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    header('Location: ../login.html');
    exit();
}

$studentId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
if ($studentId <= 0) {
    header('Location: ../login.html');
    exit();
}

$errors = [];

$fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($fullName === '' || mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
    $errors[] = 'Full name is required (2-100 characters).';
}
if (!preg_match('/^[\p{L}\s\'-]+$/u', $fullName) && $fullName !== '') {
    $errors[] = 'Full name contains invalid characters.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}

$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
if ($phone !== '' && !preg_match('/^[0-9\s\+\-\(\)]{5,25}$/', $phone)) {
    $errors[] = 'Phone number format is invalid.';
}

$jobTitle = isset($_POST['job_title']) ? trim($_POST['job_title']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$linkedin = isset($_POST['linkedin']) ? trim($_POST['linkedin']) : '';
$github = isset($_POST['github']) ? trim($_POST['github']) : '';
$portfolio = isset($_POST['portfolio']) ? trim($_POST['portfolio']) : '';
$summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';

$urlFields = ['linkedin' => $linkedin, 'github' => $github, 'portfolio' => $portfolio];
foreach ($urlFields as $fieldName => $fieldVal) {
    if ($fieldVal !== '' && strpos($fieldVal, 'http://') !== 0 && strpos($fieldVal, 'https://') !== 0) {
        $errors[] = ucfirst($fieldName) . ' must start with http:// or https://';
    }
}

function cleanArray($arr) {
    if (!is_array($arr)) return [];
    return array_values($arr);
}

function cleanBullets($bullets) {
    if (!is_array($bullets)) return [];
    $clean = [];
    foreach ($bullets as $b) {
        $b = trim($b);
        if ($b !== '' && mb_strlen($b) <= 500) {
            $clean[] = $b;
        }
    }
    return $clean;
}

function validateUrl($url) {
    if ($url === '') return '';
    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        return $url;
    }
    return '';
}

$education = [];
if (isset($_POST['education']) && is_array($_POST['education'])) {
    foreach ($_POST['education'] as $edu) {
        $school = isset($edu['school']) ? trim($edu['school']) : '';
        if ($school === '') continue;
        $ed = [
            'school' => mb_substr($school, 0, 200),
            'degree' => isset($edu['degree']) ? mb_substr(trim($edu['degree']), 0, 200) : '',
            'location' => isset($edu['location']) ? mb_substr(trim($edu['location']), 0, 200) : '',
            'gpa' => isset($edu['gpa']) ? mb_substr(trim($edu['gpa']), 0, 30) : '',
            'start_date' => isset($edu['start_date']) ? mb_substr(trim($edu['start_date']), 0, 30) : '',
            'end_date' => isset($edu['end_date']) ? mb_substr(trim($edu['end_date']), 0, 30) : '',
            'description' => isset($edu['description']) ? mb_substr(trim($edu['description']), 0, 500) : ''
        ];
        $education[] = $ed;
    }
}

$experience = [];
if (isset($_POST['experience']) && is_array($_POST['experience'])) {
    foreach ($_POST['experience'] as $exp) {
        $company = isset($exp['company']) ? trim($exp['company']) : '';
        if ($company === '') continue;
        $e = [
            'company' => mb_substr($company, 0, 200),
            'position' => isset($exp['position']) ? mb_substr(trim($exp['position']), 0, 200) : '',
            'location' => isset($exp['location']) ? mb_substr(trim($exp['location']), 0, 200) : '',
            'start_date' => isset($exp['start_date']) ? mb_substr(trim($exp['start_date']), 0, 30) : '',
            'end_date' => isset($exp['end_date']) ? mb_substr(trim($exp['end_date']), 0, 30) : '',
            'current' => isset($exp['current']) ? true : false,
            'bullets' => isset($exp['bullets']) ? cleanBullets($exp['bullets']) : []
        ];
        if ($e['current']) $e['end_date'] = 'Present';
        $experience[] = $e;
    }
}

$projects = [];
if (isset($_POST['projects']) && is_array($_POST['projects'])) {
    foreach ($_POST['projects'] as $proj) {
        $name = isset($proj['name']) ? trim($proj['name']) : '';
        if ($name === '') continue;
        $p = [
            'name' => mb_substr($name, 0, 200),
            'link' => isset($proj['link']) ? validateUrl(trim($proj['link'])) : '',
            'technologies' => isset($proj['technologies']) ? mb_substr(trim($proj['technologies']), 0, 300) : '',
            'bullets' => isset($proj['bullets']) ? cleanBullets($proj['bullets']) : []
        ];
        $projects[] = $p;
    }
}

$skills = [];
if (isset($_POST['skills']) && is_array($_POST['skills'])) {
    foreach ($_POST['skills'] as $sk) {
        $cat = isset($sk['category']) ? trim($sk['category']) : '';
        $items = isset($sk['items']) ? trim($sk['items']) : '';
        if ($cat === '' && $items === '') continue;
        $skills[] = [
            'category' => mb_substr($cat, 0, 100),
            'items' => mb_substr($items, 0, 500)
        ];
    }
}

$certificates = [];
if (isset($_POST['certificates']) && is_array($_POST['certificates'])) {
    foreach ($_POST['certificates'] as $cert) {
        $cname = isset($cert['name']) ? trim($cert['name']) : '';
        if ($cname === '') continue;
        $certificates[] = [
            'name' => mb_substr($cname, 0, 200),
            'issuer' => isset($cert['issuer']) ? mb_substr(trim($cert['issuer']), 0, 200) : '',
            'date' => isset($cert['date']) ? mb_substr(trim($cert['date']), 0, 30) : '',
            'link' => isset($cert['link']) ? validateUrl(trim($cert['link'])) : ''
        ];
    }
}

$awards = [];
if (isset($_POST['awards']) && is_array($_POST['awards'])) {
    foreach ($_POST['awards'] as $aw) {
        $aname = isset($aw['name']) ? trim($aw['name']) : '';
        if ($aname === '') continue;
        $awards[] = [
            'name' => mb_substr($aname, 0, 200),
            'date' => isset($aw['date']) ? mb_substr(trim($aw['date']), 0, 30) : '',
            'description' => isset($aw['description']) ? mb_substr(trim($aw['description']), 0, 300) : ''
        ];
    }
}

$languages = [];
if (isset($_POST['languages']) && is_array($_POST['languages'])) {
    foreach ($_POST['languages'] as $lang) {
        $lname = isset($lang['name']) ? trim($lang['name']) : '';
        if ($lname === '') continue;
        $languages[] = [
            'name' => mb_substr($lname, 0, 100),
            'level' => isset($lang['level']) ? mb_substr(trim($lang['level']), 0, 50) : ''
        ];
    }
}

$cvData = [
    'full_name' => mb_substr($fullName, 0, 100),
    'email' => mb_substr($email, 0, 200),
    'job_title' => mb_substr($jobTitle, 0, 200),
    'phone' => mb_substr($phone, 0, 30),
    'location' => mb_substr($location, 0, 200),
    'linkedin' => validateUrl($linkedin),
    'github' => validateUrl($github),
    'portfolio' => validateUrl($portfolio),
    'summary' => mb_substr($summary, 0, 2000),
    'education' => $education,
    'experience' => $experience,
    'projects' => $projects,
    'skills' => $skills,
    'certificates' => $certificates,
    'awards' => $awards,
    'languages' => $languages
];

$jsonStr = json_encode($cvData, JSON_UNESCAPED_UNICODE);
if ($jsonStr === false) {
    $errors[] = 'Failed to encode CV data.';
}

if (count($errors) > 0) {
    $_SESSION['cv_errors'] = $errors;
    header('Location: cv-edit.php');
    exit();
}

$sqlCheck = 'SELECT id FROM student_cv WHERE student_id = ? LIMIT 1';
$stmtCheck = mysqli_prepare($con, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, 'i', $studentId);
mysqli_stmt_execute($stmtCheck);
$resultCheck = mysqli_stmt_get_result($stmtCheck);
$existing = mysqli_fetch_assoc($resultCheck);
mysqli_stmt_close($stmtCheck);

if ($existing) {
    $sqlUpdate = 'UPDATE student_cv SET cv_data = ? WHERE student_id = ?';
    $stmtUpdate = mysqli_prepare($con, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, 'si', $jsonStr, $studentId);
    $success = mysqli_stmt_execute($stmtUpdate);
    mysqli_stmt_close($stmtUpdate);
} else {
    $sqlInsert = 'INSERT INTO student_cv (student_id, cv_data) VALUES (?, ?)';
    $stmtInsert = mysqli_prepare($con, $sqlInsert);
    mysqli_stmt_bind_param($stmtInsert, 'is', $studentId, $jsonStr);
    $success = mysqli_stmt_execute($stmtInsert);
    mysqli_stmt_close($stmtInsert);
}

mysqli_close($con);

if ($success) {
    $_SESSION['cv_success'] = 'CV saved successfully.';
    header('Location: cv-view.php');
} else {
    $_SESSION['cv_errors'] = ['Failed to save CV. Please try again.'];
    header('Location: cv-edit.php');
}
exit();
?>
