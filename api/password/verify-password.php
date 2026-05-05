<?php
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    sendResponse(false, "Only POST method allowed", null, 405);
}

$data = getBody();

$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if ($email == "" || $password == "") {
    sendResponse(false, "Email and password required", null, 400);
}

$stmt = $conn->prepare("SELECT password_hash FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    sendResponse(true, "Password checked", ["match" => false]);
}

$match = password_verify($password, $user["password_hash"]);

sendResponse(true, "Password checked", ["match" => $match]);
?>
