<?php

/**
 * api.php
 * Handles form submission and proxies data to the RankFactory API.
 */
session_start();

$success   = false;
$error     = '';
$submitted = false;

// ── Config ──────────────────────────────────────────────────────────────────
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
// Automatically switch between Local and Production
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || $_SERVER['HTTP_HOST'] === 'localhost';

if ($isLocal) {
    define('API_BASE_URL', $_ENV['API_BASE_URL_LOCAL']);
    $courseId = $_ENV['COURSE_ID_LOCAL'];
    $batchId  = $_ENV['BATCH_ID_LOCAL'];
} else {
    define('API_BASE_URL', $_ENV['API_BASE_URL_PROD']);
    $courseId = $_ENV['COURSE_ID_PROD'];
    $batchId  = $_ENV['BATCH_ID_PROD'];
}

define('URL_REGISTER_STUDENT', API_BASE_URL . '/register-student');
define('URL_INSTITUTIONS',      API_BASE_URL . '/institutions');
define('API_TIMEOUT', $_ENV['API_TIMEOUT'] ?? 15);

/**
 * Helper function to handle cURL requests
 */
function callApi($url, $method = 'GET', $data = [])
{
    $ch = curl_init($url);
    $headers = ['Content-Type: application/json', 'Accept: application/json'];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => API_TIMEOUT,
        CURLOPT_SSL_VERIFYPEER => false, // Set true in production
    ]);

    if (($method === 'POST' || $method === 'PUT') && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    return [
        'body' => json_decode($response, true),
        'code' => $httpCode,
        'error' => $curlErr
    ];
}

// 1. Fetch Institutions for the <select> dropdown
$instList = callApi(URL_INSTITUTIONS, 'GET');
$institutions = ($instList['code'] === 200) ? $instList['body'] : [];

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $submitted = true;

    // Data for Student Registration
    $studentData = [
        'name'           => trim($_POST['name'] ?? ''),
        'email'          => trim($_POST['email'] ?? ''),
        'contact'        => trim($_POST['contact'] ?? ''),
        'district'       => trim($_POST['district'] ?? ''),
        'course_id'      => $courseId,
        'batch_id'       => $batchId,
        'role'           => 'Student',
        'institution_id' => trim($_POST['institution_id'] ?? null),
    ];

    // Call API: Register Student
    $regResult = callApi(URL_REGISTER_STUDENT, 'POST', $studentData);

    if ($regResult['code'] === 201) {
        $_SESSION['registration_success'] = true;
        $_SESSION['is_existing'] = (bool)($regResult['body']['is_existing'] ?? false);
        echo "<script>localStorage.setItem('rf_registered', 'true');</script>";
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1#register");
        exit;
    } else {
        // Handle Laravel Validation Errors
        $body = $regResult['body'];
        if (isset($body['errors'])) {
            // Flatten Laravel's nested error array into a readable string
            $error = implode(' ', array_map(fn($e) => $e[0], $body['errors']));
        } else {
            $error = $body['message'] ?? $body['error'] ?? 'Registration could not be completed at this time. Please try again.';
        }
    }
}

// If the user clicked "Register Another", clear the session
if (isset($_GET['reset'])) {
    session_start();
    session_unset();
    session_destroy();
    // Redirect to a completely clean URL
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
