<?php
require_once __DIR__ . '/includes/api.php';

$url = $_GET['url'] ?? '';

// Global variables for existing features
$success = false;
$error = '';

switch ($url) {
    case 'cee-booking':
        // No institutions needed here if they are on the home page
        require 'pages/cee-booking.php';
        break;

    case 'reset':
        session_destroy();
        header("Location: ./");
        exit;

    default:
        // 1. DATA FOR HOME PAGE (Existing Features)

        // Fetch Institutions (Moved here as requested)
        $instList = callApi(URL_INSTITUTIONS, 'GET');
        $institutions = ($instList['code'] === 200) ? $instList['body'] : [];

        // Fetch Batches
        $batchList = callApi(URL_BATCHES, 'GET');
        $batches = ($batchList['code'] === 200 && isset($batchList['body']['batches']))
            ? $batchList['body']['batches'] : [];

        // Fetch Media (Sliders/Schedules)
        $sliderImages = getMediaByLocation('slider');
        $scheduleImage = getMediaByLocation('schedule');

        // 2. HANDLE OLD REGISTRATION FORM (Existing Feature)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $studentData = [
                'name'           => trim($_POST['name'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
                'contact'        => trim($_POST['contact'] ?? ''),
                'district'       => trim($_POST['district'] ?? ''),
                'course_id'      => $courseId, // Defined in api.php
                'batch_id'       => trim($_POST['batch_id'] ?? ''),
                'role'           => 'Student',
                'institution_id' => trim($_POST['institution_id'] ?? null),
            ];

            $regResult = callApi(URL_REGISTER_STUDENT, 'POST', $studentData);

            if ($regResult['code'] === 201) {
                header("Location: ./?success=1#register");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }

        $data = [
            'institutions' => $institutions,
            'batches' => $batches,
            'sliderImages' => $sliderImages,
            'scheduleImage' => $scheduleImage,
        ];

        extract($data);

        require 'pages/home.php';
        break;
}
