<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Kolkata');

$conn = new mysqli("localhost", "root", "", "auth_system");

if ($conn->connect_error) {
    sendResponse(false, "Database connection failed: " . $conn->connect_error, null, 500);
}

$conn->set_charset("utf8mb4");

define("JWT_SECRET", "my_simple_secret_key_123");

function sendResponse($status, $message, $data = null, $code = 200) {
    http_response_code($code);
    header("Content-Type: application/json");

    $response = [
        "status" => $status,
        "message" => $message
    ];

    if ($data !== null) {
        $response["data"] = $data;
    }

    echo json_encode($response);
    exit;
}

function getBody() {
    $data = json_decode(file_get_contents("php://input"), true);
    return is_array($data) ? $data : [];
}

function currentDateTime() {
    $date = new DateTime("now", new DateTimeZone("Asia/Kolkata"));
    return $date->format("Y-m-d H:i:s");
}

function makeOTP() {
    return str_pad(rand(0, 999999), 6, "0", STR_PAD_LEFT);
}

function isExpired($time) {
    if (!$time) return true;

    $tz = new DateTimeZone("Asia/Kolkata");
    $otpTime = new DateTime($time, $tz);
    $otpTime->modify("+5 minutes");

    $now = new DateTime("now", $tz);

    return $now > $otpTime;
}

function b64url_encode($data) {
    return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
}

function b64url_decode($data) {
    $padding = 4 - (strlen($data) % 4);
    if ($padding < 4) {
        $data .= str_repeat("=", $padding);
    }
    return base64_decode(strtr($data, "-_", "+/"));
}

function createToken($email, $mobile) {
    $header = [
        "alg" => "HS256",
        "typ" => "JWT"
    ];

    $payload = [
        "email" => $email,
        "mobile" => $mobile,
        "iat" => time(),
        "exp" => time() + 86400
    ];

    $header64 = b64url_encode(json_encode($header));
    $payload64 = b64url_encode(json_encode($payload));

    $signature = hash_hmac("sha256", $header64 . "." . $payload64, JWT_SECRET, true);
    $signature64 = b64url_encode($signature);

    return $header64 . "." . $payload64 . "." . $signature64;
}

function verifyToken($token) {
    $parts = explode(".", $token);

    if (count($parts) != 3) {
        return false;
    }

    $header64 = $parts[0];
    $payload64 = $parts[1];
    $signature64 = $parts[2];

    $checkSignature = hash_hmac("sha256", $header64 . "." . $payload64, JWT_SECRET, true);
    $checkSignature64 = b64url_encode($checkSignature);

    if (!hash_equals($checkSignature64, $signature64)) {
        return false;
    }

    $payload = json_decode(b64url_decode($payload64), true);

    if (!$payload || !isset($payload["exp"]) || time() > $payload["exp"]) {
        return false;
    }

    return $payload;
}

function getTokenFromHeader() {
    $headers = getallheaders();

    $auth = $headers["Authorization"] ?? $headers["authorization"] ?? "";

    if (!$auth && isset($_SERVER["HTTP_AUTHORIZATION"])) {
        $auth = $_SERVER["HTTP_AUTHORIZATION"];
    }

    if (preg_match("/Bearer\s+(.*)$/i", $auth, $matches)) {
        return $matches[1];
    }

    return null;
}

function loginRequired() {
    $token = getTokenFromHeader();

    if (!$token) {
        sendResponse(false, "Token required", null, 401);
    }

    $user = verifyToken($token);

    if (!$user) {
        sendResponse(false, "Invalid or expired token", null, 401);
    }

    return $user;
}
?>
