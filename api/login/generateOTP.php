<?php
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, "Only POST method allowed", null, 405);
}

$data = getBody();

$email = trim($data["email"] ?? "");
$mobile = trim($data["mobile"] ?? "");

if (($email != "" && $mobile != "") || ($email == "" && $mobile == "")) {
    sendResponse(false, "Send either email or mobile only", null, 400);
}

$otp = makeOTP();
$now = currentDateTime();

$expiry = new DateTime($now, new DateTimeZone("Asia/Kolkata"));
$expiry->modify("+5 minutes");

if ($email != "") {
    $stmt = $conn->prepare("UPDATE users SET email_otp=?, email_otp_generated_at=?, email_otp_used=0, updated_at=? WHERE email=?");
    $stmt->bind_param("ssss", $otp, $now, $now, $email);
} else {
    $stmt = $conn->prepare("UPDATE users SET mobile_otp=?, mobile_otp_generated_at=?, mobile_otp_used=0, updated_at=? WHERE mobile=?");
    $stmt->bind_param("ssss", $otp, $now, $now, $mobile);
}

$stmt->execute();

if ($stmt->affected_rows < 1) {
    sendResponse(false, "User not found", null, 404);
}

sendResponse(true, "OTP generated successfully", [
    "otp" => $otp,
    "generated_at_ist" => $now,
    "expires_at_ist" => $expiry->format("Y-m-d H:i:s")
]);
?>
