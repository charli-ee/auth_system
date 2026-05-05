<?php
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, "Only POST method allowed", null, 405);
}

$data = getBody();

$email = trim($data["email"] ?? "");
$mobile = trim($data["mobile"] ?? "");
$otp = trim($data["otp"] ?? "");

if ($otp == "") {
    sendResponse(false, "OTP required", null, 400);
}

if (($email != "" && $mobile != "") || ($email == "" && $mobile == "")) {
    sendResponse(false, "Send email+OTP or mobile+OTP only", null, 400);
}

if ($email != "") {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE mobile=?");
    $stmt->bind_param("s", $mobile);
}

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    sendResponse(false, "User not found", null, 404);
}

if ($email != "") {
    if ($user["email_otp_used"] == 1) {
        sendResponse(false, "OTP already used", null, 400);
    }

    if ($user["email_otp"] != $otp) {
        sendResponse(false, "Invalid OTP", null, 400);
    }

    if (isExpired($user["email_otp_generated_at"])) {
        sendResponse(false, "OTP expired", null, 400);
    }

    $update = $conn->prepare("UPDATE users SET email_otp_used=1, successful_login_count=successful_login_count+1, updated_at=? WHERE id=?");
} else {
    if ($user["mobile_otp_used"] == 1) {
        sendResponse(false, "OTP already used", null, 400);
    }

    if ($user["mobile_otp"] != $otp) {
        sendResponse(false, "Invalid OTP", null, 400);
    }

    if (isExpired($user["mobile_otp_generated_at"])) {
        sendResponse(false, "OTP expired", null, 400);
    }

    $update = $conn->prepare("UPDATE users SET mobile_otp_used=1, successful_login_count=successful_login_count+1, updated_at=? WHERE id=?");
}

$now = currentDateTime();
$update->bind_param("si", $now, $user["id"]);
$update->execute();

$token = createToken($user["email"], $user["mobile"]);

sendResponse(true, "OTP verified and login successful", [
    "token" => $token,
    "email" => $user["email"],
    "mobile" => $user["mobile"],
    "successful_login_count" => $user["successful_login_count"] + 1
]);
?>
