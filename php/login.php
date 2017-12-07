<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        $body = file_get_contents('php://input');
        $postvars = json_decode($body, true);
        $username = $postvars["username"];
        $wachtwoord = $postvars["wachtwoord"];

        if ($username === null || $username === '') {
            if (isset($_POST['username'])) {
                $username = $_POST['username'];
            }
        }
        if ($wachtwoord === null || $wachtwoord === '') {
            if (isset($_POST['wachtwoord'])) {
                $wachtwoord = $_POST['wachtwoord'];
            }
        }
        if (!isset($username) || !isset($wachtwoord)) {
            if (empty($postvars)) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    die('{"POST":' . json_encode($_POST) . ',"postvars":'. json_encode($postvars) .'}');
                } else {
                    die('{"error":"Geen POST request","status":"fail"}');
                }
            }
        }
        require_once '../include/connection.php';
