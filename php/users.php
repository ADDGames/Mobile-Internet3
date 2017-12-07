<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    $body = file_get_contents('php://input');
    $postvars = json_decode($body, true);
    $function = $postvars["function"];
    $table = $postvars["table"];

    if ($function === null || $function === '') {
        if (isset($_POST['function'])) {
            $function = $_POST['function'];
        }
    }
    if (!isset($function)) {
        if (!empty($postvars)) {
        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                die('{"POST":' . json_encode($_POST) . ',"postvars":'. json_encode($postvars) .'}');
            } else {
                die('{"error":"Geen POST request","status":"fail"}');
            }
        }
    }
    if (isset($function) && isset($table)) {
        if ($table !== 'docent' && $table !== 'student') {
            die('{"error":"wrong table","status":"fail"}');
        }
        if ($function !== 'add' && $function !== 'getone' && $function !== 'getall' && $function !== 'change') {
            die('{"error":"wrong function","status":"fail"}');
        }
    } else {
        die('{"error":"missing data","table":"'. $table. '", "function":"' . $function . '","status":"fail"}');
    }

    require_once '../include/connection.php';

    if (!$con) {
        die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
    } else {
        if ($table === "docent") {
            if ($function === "add") {
                $GEB_username = null;
                $GEB_naam = null;
                $GEB_voornaam = null;
                $GEB_wachtwoord = null;
                $GEB_email = null;
                $DOC_GEB_id = null;
                if (isset($_POST['username']) && isset($_POST['naam']) && isset($_POST['voornaam']) && isset($_POST['wachtwoord']) && isset($_POST['email'])) {
                    $GEB_username = $_POST['username'];
                    $GEB_naam = $_POST['naam'];
                    $GEB_voornaam = $_POST['voornaam'];
                    $GEB_wachtwoord = $_POST['wachtwoord'];
                    $GEB_email = $_POST['email'];
                    if ($GEB_username === "" || $GEB_naam === "" || $GEB_voornaam === "" || $GEB_wachtwoord === "" || $GEB_email === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "INSERT INTO `gebruiker`(`GEB_username`, `GEB_naam`, `GEB_voornaam`, `GEB_wachtwoord`, `GEB_email`) OUTPUT INSERTED.GEB_id VALUES (`$GEB_username`,`$GEB_naam`,`$GEB_voornaam`,`$GEB_wachtwoord`,`$GEB_email`)";
                $result = mysqli_query($query);
                $row = mysqli_fetch_assoc($result);
                $DOC_GEB_id = $row['GEB_id'];
                $query = "INSERT INTO `docent`(`DOC_naam`, `DOC_gebruiker_id`) VALUES (`$GEB_naam`,$DOC_GEB_id)";
                $con->query($query);
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":"ok","message":"Record added successfully","status":"ok"}');
            } elseif ($function === "getone") {
                $DOC_id = null;
                if (isset($_POST['id'])) {
                    $DOC_id = $_POST['id'];
                    if ($DOC_id === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "SELECT docent.DOC_id,docent.DOC_naam,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id WHERE docent.DOC_id = $DOC_id";
                $result = mysqli_query($query);
                $row = mysqli_fetch_assoc($result);
                $docent = ["DOC_id" => $row['DOC_id'],"DOC_naam" => $row['DOC_naam'],"DOC_GEB_id" => $row['DOC_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']];
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($docent).',"status":"ok"}');
            } elseif ($function === "getall") {
                $docenten = [];
                $query = "SELECT docent.DOC_id,docent.DOC_naam,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id";
                $result = mysqli_query($query);
                $index = 0;
                while ($row = mysqli_fetch_array($result)) {
                    array_push($docenten, ["index" => $index, "values" => ["DOC_id" => $row['DOC_id'],"DOC_naam" => $row['DOC_naam'],"DOC_GEB_id" => $row['DOC_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']]]);
                    $index++;
                }
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($docenten).',"status":"ok"}');
            } elseif ($function === "change") {
                $column = $_POST['column'];
                $value = $_POST['value'];
                $GEB_id = $_POST['GEB_id'];
                if ($column === "GEB_username" || $column === "GEB_voornaam" || $column === "GEB_wachtwoord" || $column === "GEB_email") {
                    $query = "UPDATE gebruiker SET ";
                } elseif ($column === "GEB_naam") {
                    $query = "UPDATE gebruiker SET GEB_naam = '$value' WHERE GEB_id = $GEB_id ";
                    $con->query($query);
                    $query = "UPDATE docent SET DOC_naam = '$value' WHERE DOC_GEB_id = $GEB_id";
                } else {
                    die('{"error":"wrong column","status":"fail"}');
                }
                $con->query($query);
                mysqli_close($con);
                die('{"data":"ok","message":"Record changed successfully","status":"ok"}');
            }
        }
    }
