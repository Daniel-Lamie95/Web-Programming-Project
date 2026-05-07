<?php
session_start();
include("Config.php");

/*
This page should only work after company signup/login.
So we check session first.
*/
if (!isset($_SESSION['CompanyID'])) {
    header("Location: login.html"); // or signup
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $companyID = $_SESSION['CompanyID'];

    $field = trim($_POST['field']);
    $location = trim($_POST['location']);
    $phone = trim($_POST['phone']);
    $description = trim($_POST['description']);
    $website = trim($_POST['website']);

    if (empty($field) || empty($location) || empty($phone) || empty($description)) {
        header("Location: company-register.html?error=empty");
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

    $logoPath = "";

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {

        $uploadFolder = "uploads/";

        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder);
        }

        $logoName = $_FILES['logo']['name'];
        $logoTmp = $_FILES['logo']['tmp_name'];

        $logoPath = $uploadFolder . time() . "_" . $logoName;

        move_uploaded_file($logoTmp, $logoPath);
    }

    $sql = "UPDATE Company 
            SET Field = ?, 
            Location = ?, 
            Phone = ?,
            Description = ?,
            Website = ?, 
            Logo = ?
            WHERE ID = ?";

    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssi",
        $field,
        $location,
        $phone,
        $description,
        $website,
        $logoPath,
        $companyID
    );

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['CompanyCompleted'] = true;
        header("Location: company-dashboard.php");
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