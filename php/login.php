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
        if (!$con) {
            die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
        } else {
            $login = false;
            $docent = false;
            $student = false;
            if (!$login) {
                die('{"error":"Geen match gevonden","status":"fail"}');
            } else {
                if (($docent && $student) || (!$docent && !$student)) {
                    die('{"error":"Er is een probleem met uw gebruiker, contacteer de administrator om dit op te lossen","status":"fail"}');
                } else {
                    if (!$docent && $student) {
                        $user = ["username" => $username, "type" => "student"];
                        die('{"data":'.json_encode($user).',"status":"ok"}');
                    } elseif ($docent && !$student) {
                        $user = ["username" => $username, "type" => "docent"];
                        die('{"data":'.json_encode($user).',"status":"ok"}');
                    }
                }
            }
        }
