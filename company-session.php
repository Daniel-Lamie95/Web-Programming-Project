<?php
session_start();
include("Config.php");

if (!isset($_SESSION['CompanyID'])) {
    header("Location: login.html");
    exit();
}

$companyID = $_SESSION['CompanyID'];

$sql = "SELECT * FROM Company WHERE ID = ?";

$stmt = mysqli_prepare($con, $sql);

mysqli_stmt_bind_param($stmt, "i", $companyID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$company = mysqli_fetch_assoc($result);

if (!$company) {
    header("Location: login.html");
    exit();
}
?>