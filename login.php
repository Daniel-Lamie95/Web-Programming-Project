<?php

session_start();
include('Config.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: login.html');
	exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$userType = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';

if ($email === '' || $password === '' || ($userType !== 'student' && $userType !== 'company')) {
	header('Location: login.html?error=missing_fields');
	exit();
}

if ($userType === 'student') {
	$sql = 'SELECT * FROM Student WHERE email = ? AND password = ? LIMIT 1';
	$stmt = mysqli_prepare($con, $sql);

	if (!$stmt) {
		header('Location: login.html?error=server_error');
		exit();
	}

	mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	if ($result && mysqli_num_rows($result) === 1) {
		$student = mysqli_fetch_assoc($result);
		$_SESSION['user_id'] = isset($student['id']) ? $student['id'] : null;
		$_SESSION['user_name'] = isset($student['name']) ? $student['name'] : '';
		$_SESSION['user_type'] = 'student';

		mysqli_stmt_close($stmt);
		mysqli_close($con);

		header('Location: student-dashboard.php');
		exit();
	}

	mysqli_stmt_close($stmt);
	mysqli_close($con);

	header('Location: login.html?error=invalid_credentials');
	exit();
}

if ($userType === 'company') {
	$sql = 'SELECT * FROM Company WHERE email = ? AND password = ? LIMIT 1';
	$stmt = mysqli_prepare($con, $sql);

	if (!$stmt) {
		header('Location: login.html?error=server_error');
		exit();
	}

	mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);

	if ($result && mysqli_num_rows($result) === 1) {
		$company = mysqli_fetch_assoc($result);
		$_SESSION['user_id'] = isset($company['id']) ? $company['id'] : null;
		$_SESSION['user_name'] = isset($company['name']) ? $company['name'] : '';
		$_SESSION['user_type'] = 'company';

		mysqli_stmt_close($stmt);
		mysqli_close($con);

		header('Location: company-dashboard.html');
		exit();
	}

	mysqli_stmt_close($stmt);
	mysqli_close($con);

	header('Location: login.html?error=invalid_credentials');
	exit();
}

mysqli_close($con);
header('Location: login.html?error=invalid_role');
exit();

?>
