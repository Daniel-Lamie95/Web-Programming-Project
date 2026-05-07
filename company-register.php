<?php
session_start();
include("Config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $companyName = trim($_POST['companyName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $field = trim($_POST['field']);
    $location = trim($_POST['location']);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['description']);
    $website = trim($_POST['website']);

    if (
        empty($companyName) ||
        empty($email) ||
        empty($password) ||
        empty($field) ||
        empty($location) ||
        empty($phone) ||
        empty($description)
    ) {
        header("Location: company-register.html?error=empty");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: company-register.html?error=email");
        exit();
    }

    if (strlen($password) < 8) {
        header("Location: company-register.html?error=password");
        exit();
    }

    if (!preg_match("/^[0-9+\-\s]{8,20}$/", $phone)) {
        header("Location: company-register.html?error=phone");
        exit();
    }

    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        header("Location: company-register.html?error=website");
        exit();
    }

    $checkSql = "SELECT ID FROM Company WHERE Email = ?";

    $checkStmt = mysqli_prepare($con, $checkSql);

    mysqli_stmt_bind_param($checkStmt, "s", $email);

    mysqli_stmt_execute($checkStmt);

    $checkResult = mysqli_stmt_get_result($checkStmt);

    if (mysqli_num_rows($checkResult) > 0) {

        header("Location: company-register.html?error=email_exists");
        exit();
    }

    $logoPath = "";

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {

        $uploadFolder = "uploads/";

        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0777, true);
        }

        $logoName = basename($_FILES['logo']['name']);

        $logoTmp = $_FILES['logo']['tmp_name'];

        $logoPath = $uploadFolder . time() . "_" . $logoName;

        move_uploaded_file($logoTmp, $logoPath);
    }

    $sql = "INSERT INTO Company
            (Name, Email, Password, Field, Location, Phone, Description, Website, Logo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $companyName,
        $email,
        $password,
        $field,
        $location,
        $phone,
        $description,
        $website,
        $logoPath
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: login.html?success=registered");
        exit();

    } else {

        header("Location: company-register.html?error=database");
        exit();
    }

} else {

    header("Location: company-register.html");
    exit();
}
?>