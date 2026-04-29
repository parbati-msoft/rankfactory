<?php
session_start();

// 1. LOAD ENV
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// 2. CONFIG & CONSTANTS
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || $_SERVER['HTTP_HOST'] === 'localhost';
if ($isLocal) {
    define('API_BASE_URL', $_ENV['API_BASE_URL_LOCAL']);
    $courseId = $_ENV['COURSE_ID_LOCAL'];
    $ceeBatchId = $_ENV['ONLINE_BATCH_ID_LOCAL'];
    $ceeClassId = $_ENV['PHYSICAL_CLASS_ID_LOCAL'];
} else {
    define('API_BASE_URL', $_ENV['API_BASE_URL_PROD']);
    $courseId = $_ENV['COURSE_ID_PROD'];
    $ceeBatchId = $_ENV['ONLINE_BATCH_ID_PROD'];
    $ceeClassId = $_ENV['PHYSICAL_CLASS_ID_PROD'];
}

define('URL_BATCHES', API_BASE_URL . "/course/{$courseId}/batches");
define('URL_REGISTER_STUDENT', API_BASE_URL . '/register-student');
define('URL_LOGIN_STUDENT', API_BASE_URL . '/login');
define('URL_INSTITUTIONS',      API_BASE_URL . '/institutions');
define('URL_MEDIA', API_BASE_URL . '/media-file');
define('URL_MINI_PROFILE', API_BASE_URL . '/mini-profile');
define('URL_ONLINE_BATCH_DETAIL', API_BASE_URL . '/batch-details/' . $ceeBatchId);
define('URL_PHYSICAL_BATCH_DETAIL', API_BASE_URL . '/physical-class/' . $ceeClassId);
define('URL_MY_BOOKINS', API_BASE_URL . '/my/course/bookings/all');
define('URL_ONLINE_COURSE_BOOKING', API_BASE_URL . '/my/course/cee-bookinss');
define('URL_PHYSICAL_BOOKING_CHECK', API_BASE_URL . '/my/physical-class/my-booking');
define('URL_PHYSICAL_BOOKING_STORE', API_BASE_URL . '/my/physical-class/booking');
define('API_TIMEOUT', $_ENV['API_TIMEOUT'] ?? 15);

// 3. THE CORE FUNCTION
function callApi($url, $method = 'GET', $data = [])
{
    $ch = curl_init($url);

    $headers = ['Accept: application/json'];

    $hasFile = false;

    if (is_array($data)) {
        foreach ($data as $value) {
            if ($value instanceof CURLFile) {
                $hasFile = true;
                break;
            }
        }
    }

    if (!$hasFile) {
        $headers[] = 'Content-Type: application/json';
    }

    if (!empty($_SESSION['rf_token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['rf_token'];
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => API_TIMEOUT,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    if (($method === 'POST' || $method === 'PUT') && !empty($data)) {
        if ($hasFile) {
            // Send as multipart/form-data (required for files)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            // Send as raw JSON
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['body' => json_decode($response, true), 'code' => $httpCode];
}

// 4. HELPER FUNCTIONS
function getMediaByLocation($location)
{
    $url = URL_MEDIA . '/' . $location;
    $result = callApi($url, 'GET');
    return ($result['code'] === 200) ? $result['body']['data'] : null;
}
