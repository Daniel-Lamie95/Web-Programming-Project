<?php
include("company-session.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $companyID = $_SESSION['CompanyID'];

    $name = trim($_POST['companyName']);
    $email = trim($_POST['companyEmail']);
    $phone = trim($_POST['phone']);
    $location = trim($_POST['location']);
    $field = trim($_POST['field']);
    $website = trim($_POST['website']);
    $description = trim($_POST['description']);

    if (
        empty($name) ||
        empty($email) ||
        empty($phone) ||
        empty($location) ||
        empty($field) ||
        empty($description)
    ) {
        header("Location: edit-company-profile.php?error=empty");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: edit-company-profile.php?error=email");
        exit();
    }

    if (!preg_match("/^[0-9+\-\s]{8,20}$/", $phone)) {
        header("Location: edit-company-profile.php?error=phone");
        exit();
    }

    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        header("Location: edit-company-profile.php?error=website");
        exit();
    }

    $logoPath = $company['Logo'];

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
            SET Name = ?, Email = ?, Phone = ?, Location = ?, Field = ?, Website = ?, Description = ?, Logo = ?
            WHERE ID = ?";

    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssssi",
        $name,
        $email,
        $phone,
        $location,
        $field,
        $website,
        $description,
        $logoPath,
        $companyID
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: company-profile.php");
        exit();
    } else {
        header("Location: edit-company-profile.php?error=database");
        exit();
    }

} else {
    header("Location: edit-company-profile.php");
    exit();
}
?>