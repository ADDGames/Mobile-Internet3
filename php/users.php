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

    if ($table === null || $table === '') {
        if (isset($_POST['table'])) {
            $table = $_POST['table'];
        }
    }
    if (!isset($function) || !isset($table)) {
        if (empty($postvars)) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                die('{"POST":' . json_encode($_POST) . ',"postvars":'. json_encode($postvars) .'}');
            } else {
                die('{"error":"Geen POST request","status":"fail"}');
            }
        }
    } else {
        if ($table !== 'docent' && $table !== 'student' && $table !== 'gebruiker') {
            die('{"error":"wrong table","status":"fail"}');
        }
        if ($table === 'docent' || $table === 'student') {
            if ($function !== 'add' && $function !== 'getone' && $function !== 'getall' && $function !== 'change') {
                die('{"error":"wrong function","status":"fail"}');
            }
        } else {
            if ($function !== 'login' && $function !== 'registeer') {
                die('{"error":"wrong function","status":"fail"}');
            }
        }
    }

    require_once 'connection.php';

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
                $DOC_code = null;
                $DOC_GEB_id = null;
                if (isset($_POST['username']) && isset($_POST['naam']) && isset($_POST['voornaam']) && isset($_POST['wachtwoord']) && isset($_POST['email']) isset($_POST['code'])) {
                    $GEB_username = $_POST['username'];
                    $GEB_naam = $_POST['naam'];
                    $GEB_voornaam = $_POST['voornaam'];
                    $GEB_wachtwoord = $_POST['wachtwoord'];
                    $GEB_email = $_POST['email'];
                    $DOC_code = $_POST['code'];
                    if ($GEB_username === "" || $GEB_naam === "" || $GEB_voornaam === "" || $GEB_wachtwoord === "" || $GEB_email === "" || $DOC_code === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $code_id = null;
                $query = "SELECT * FROM code";
                $result = mysqli_query($con, $query);
                while ($row = mysqli_fetch_array($result)) {
                    if($row['DOC_id'] === $DOC_code){
                        $code_id = $row['COD_id'];
                        break;
                    }
                }
                if($code_id === null){
                    die('{"error":"foute code","status":"fail"}');
                }
                $query = "INSERT INTO `gebruiker`(`GEB_username`, `GEB_naam`, `GEB_voornaam`, `GEB_wachtwoord`, `GEB_email`) OUTPUT INSERTED.GEB_id VALUES (`$GEB_username`,`$GEB_naam`,`$GEB_voornaam`,`$GEB_wachtwoord`,`$GEB_email`)";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $DOC_GEB_id = $row['GEB_id'];
                $query = "INSERT INTO `docent`(`DOC_naam`, `DOC_gebruiker_id`, `DOC_COD_id`) VALUES (`$GEB_naam`,$DOC_GEB_id,$code_id)";
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
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $docent = ["DOC_id" => $row['DOC_id'],"DOC_naam" => $row['DOC_naam'],"DOC_GEB_id" => $row['DOC_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']];
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($docent).',"status":"ok"}');
            } elseif ($function === "getall") {
                $docenten = [];
                $query = "SELECT docent.DOC_id,docent.DOC_naam,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id";
                $result = mysqli_query($con, $query);
                $index = 0;
                while ($row = mysqli_fetch_array($result)) {
                    array_push($docenten, ["index" => $index, "values" => ["DOC_id"] => $row['DOC_id'],"DOC_naam" => $row['DOC_naam'],"DOC_GEB_id" => $row['DOC_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']]]);
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
                    $query = "UPDATE gebruiker SET '$column' = '$value' WHERE GEB_id = $GEB_id";
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
        } elseif ($table === "student") {
            # code...
        } elseif ($table === "gebruiker") {
            $GEB_id = null;
            $GEB_username = null;
            $GEB_wachtwoord = null;
            if (isset($_POST['username']) && isset($_POST['wachtwoord'])) {
                $GEB_username = $_POST['username'];
                $GEB_wachtwoord = $_POST['wachtwoord'];
                if ($GEB_username === "" || $GEB_wachtwoord === "") {
                    die('{"error":"missing data","status":"fail"}');
                }
            } else {
                die('{"error":"missing data","status":"fail"}');
            }
            $query = "SELECT GEB_id, GEB_username, GEB_wachtwoord FROM gebruiker WHERE GEB_username = '$GEB_username' AND GEB_wachtwoord = '$GEB_wachtwoord'";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $GEB_id = $row['GEB_id'];
            } else {
                die('{"error":"username/password incorrect","status":"fail"}');
            }
            mysqli_free_result($result);
            $login = false;
            $docent = false;
            $student = false;
            $query = "SELECT DOC_id FROM docent WHERE DOC_GEB_id = '$GEB_id'";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $docent = true;
            }
            mysqli_free_result($result);
            $query = "SELECT STU_id FROM student WHERE STU_GEB_id = '$GEB_id'";
            $result = mysqli_query($con, $query);
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $student = true;
            }
            if (($docent && $student) || (!$docent && !$student)) {
                die('{"error":"Er is een probleem met uw gebruiker, contacteer de administrator om dit op te lossen","status":"fail"}');
            } else {
                if (!$docent && $student) {
                    $user = ["GEB_id" => $GEB_id,"username" => $GEB_username, "type" => "student"];
                    die('{"data":'.json_encode($user).',"status":"ok"}');
                } elseif ($docent && !$student) {
                    $user = ["GEB_id" => $GEB_id,"username" => $GEB_username, "type" => "docent"];
                    die('{"data":'.json_encode($user).',"status":"ok"}');
                }
            }
            mysqli_free_result($result);
            mysqli_close($con);
            die('{"data":'.json_encode($docent).',"status":"ok"}');
        }
    }
