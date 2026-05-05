<?php
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, "Only POST method allowed", null, 405);
}

$auth = loginRequired();
$data = getBody();

$oldPassword = $data["old_password"] ?? "";
$newPassword = $data["new_password"] ?? "";
$confirmPassword = $data["confirm_password"] ?? "";

if ($oldPassword == "" || $newPassword == "" || $confirmPassword == "") {
    sendResponse(false, "All password fields required", null, 400);
}

if ($newPassword != $confirmPassword) {
    sendResponse(false, "New password and confirm password do not match", null, 400);
}

if ($oldPassword == $newPassword) {
    sendResponse(false, "Old password not allowed", null, 400);
}

$stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email=? AND mobile=?");
$stmt->bind_param("ss", $auth["email"], $auth["mobile"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    sendResponse(false, "User not found", null, 404);
}

if (!password_verify($oldPassword, $user["password_hash"])) {
    sendResponse(false, "Invalid old password", null, 400);
}

if (password_verify($newPassword, $user["password_hash"])) {
    sendResponse(false, "Old password not allowed", null, 400);
}

$newHash = password_hash($newPassword, PASSWORD_BCRYPT);
$now = currentDateTime();

$update = $conn->prepare("UPDATE users SET password_hash=?, updated_at=? WHERE id=?");
$update->bind_param("ssi", $newHash, $now, $user["id"]);

if ($update->execute()) {
    sendResponse(true, "Password changed successfully");
} else {
    sendResponse(false, "Password change failed", null, 500);
}
?>
