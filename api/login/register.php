<?php
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, "Only POST method allowed", null, 405);
}

$mobile = trim($_POST["mobile"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($mobile == "" || $email == "" || $password == "") {
    sendResponse(false, "Mobile, email and password required", null, 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, "Invalid email", null, 400);
}

if (strlen($password) < 6) {
    sendResponse(false, "Password minimum 6 characters", null, 400);
}

$check = $conn->prepare("SELECT id, email, mobile FROM users WHERE email=? OR mobile=?");
$check->bind_param("ss", $email, $mobile);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {
    if ($row["email"] == $email) {
        sendResponse(false, "Email already registered", null, 409);
    }

    if ($row["mobile"] == $mobile) {
        sendResponse(false, "Mobile already registered", null, 409);
    }
}

$imageBase64 = null;

if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
    $imageData = file_get_contents($_FILES["profile_image"]["tmp_name"]);
    $imageType = mime_content_type($_FILES["profile_image"]["tmp_name"]);
    $imageBase64 = "data:" . $imageType . ";base64," . base64_encode($imageData);
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$emailOtp = makeOTP();
$mobileOtp = makeOTP();
$now = currentDateTime();

$stmt = $conn->prepare("INSERT INTO users 
(mobile, email, profile_image, password_hash, email_otp, email_otp_generated_at, email_otp_used, mobile_otp, mobile_otp_generated_at, mobile_otp_used, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, 0, ?, ?)");

$stmt->bind_param(
    "ssssssssss",
    $mobile,
    $email,
    $imageBase64,
    $passwordHash,
    $emailOtp,
    $now,
    $mobileOtp,
    $now,
    $now,
    $now
);

if ($stmt->execute()) {
    sendResponse(true, "User registered successfully", [
        "email_otp" => $emailOtp,
        "mobile_otp" => $mobileOtp,
        "time_ist" => $now
    ], 201);
} else {
    sendResponse(false, "Registration failed: " . $stmt->error, null, 500);
}
?>
