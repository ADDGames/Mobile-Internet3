<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    //-----variabelen ophalen uit input-file (android)-----
    $body = file_get_contents('php://input');
    $postvars = json_decode($body, true);
    $function = $postvars["function"];
    $table = $postvars["table"];

    //-----variabelen vullen uit POST Request-----
    //check if input-file niets op heeft gehaald of een leeg veld heeft ingevoerd per variabele
    if ($function === null || $function === '') {
        //check of POST Request van 'function' niet leeg is (isset == is ingevuld)
        if (isset($_POST['function'])) {
            //variabele vullen met POST als isset === true
            $function = $_POST['function'];
        }
    }
    if ($table === null || $table === '') {
        if (isset($_POST['table'])) {
            $table = $_POST['table'];
        }
    }

    //-----checken of $function en $table gevuld zijn
    if (!isset($function) || !isset($table)) {
        //checken of $postvars leeg is
        if (empty($postvars)) {
            //checken of POST (Niet GET,PUT,DELETE,...)
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                die('{"POST":' . json_encode($_POST) . ',"postvars":'. json_encode($postvars) .'}');
            } else {
                die('{"error":"Geen POST request","status":"fail"}');
            }
        }
    } else {
        //checken of $tabel 1 van de gekozen tabellen is
        if ($table !== 'docent' && $table !== 'student' && $table !== 'gebruiker') {
            die('{"error":"wrong table","status":"fail"}');
        }
        //checken of $tabel 'docent/student' is (anders gebruiker)
        if ($table === 'docent' || $table === 'student') {
            //checken of $function 1 van de gekozen functies is
            if ($function !== 'add' && $function !== 'getone' && $function !== 'getall' && $function !== 'change') {
                die('{"error":"wrong function","status":"fail"}');
            }
        } else {
            //checken of $function 1 van de gekozen functies is
            if ($function !== 'login') {
                die('{"error":"wrong function","status":"fail"}');
            }
        }
    }
    //-----connection maken met server (hosting)-----
    //import van connection naar database
    require_once 'connection.php';
    //checken of connection werkt
    if (!$con) {
        die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
    } else {
        //checken of $table voor docent aan te passen is (in database zal gebruiker en docent aangepast worden)
        if ($table === "docent") {
            if ($function === "add") {
                //voordefinitie van de nodige variabelen
                $GEB_username = null;
                $GEB_naam = null;
                $GEB_voornaam = null;
                $GEB_wachtwoord = null;
                $GEB_email = null;
                $DOC_code = null;
                $DOC_GEB_id = null;
                //check of POST Request van alle variabelen niet leeg zijn
                if (isset($_POST['username']) && isset($_POST['naam']) && isset($_POST['voornaam']) && isset($_POST['wachtwoord']) && isset($_POST['email']) && isset($_POST['code'])) {
                    //variabelen vullen met POST Request
                    $GEB_username = $_POST['username'];
                    $GEB_naam = $_POST['naam'];
                    $GEB_voornaam = $_POST['voornaam'];
                    $GEB_wachtwoord = $_POST['wachtwoord'];
                    $GEB_email = $_POST['email'];
                    $DOC_code = $_POST['code'];
                    //checken of variabelen niet leeg zijn
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
                    if ($row['COD_code'] === $DOC_code) {
                        $code_id = $row['COD_id'];
                        break;
                    }
                }
                if ($code_id === null) {
                    die('{"error":"foute code","status":"fail"}');
                }
                $query = "INSERT INTO `gebruiker` (`GEB_username`, `GEB_naam`, `GEB_voornaam`, `GEB_wachtwoord`, `GEB_email`) VALUES ('$GEB_username','$GEB_naam','$GEB_voornaam','$GEB_wachtwoord','$GEB_email')";
                $result = mysqli_query($con, $query);
                $DOC_GEB_id = $con -> insert_id;
                $query = "INSERT INTO `docent`(`DOC_gebruiker_id`, `DOC_COD_id`) VALUES ($DOC_GEB_id,$code_id)";
                $con->query($query);
                mysqli_close($con);
                die('{"data":"ok","message":"Record added successfully","status":"ok"}');
            } elseif ($function === "getone") {
                //voordefinitie van de nodige variabelen
                $GEB_id = null;
                //check of POST Request van alle variabelen niet leeg zijn
                if (isset($_POST['id'])) {
                    //variabelen vullen met POST Request
                    $GEB_id = $_POST['id'];
                    //checken of variabelen niet leeg zijn
                    if ($GEB_id === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "SELECT docent.DOC_id,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON docent.DOC_GEB_id = gebruiker.GEB_id WHERE gebruiker.GEB_id = $GEB_id";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $docent = ["DOC_id" => $row['DOC_id'],"DOC_GEB_id" => $row['DOC_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']];
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($docent).',"status":"ok"}');
            } elseif ($function === "getall") {
                //voordefinitie van de nodige variabelen
                $docenten = [];
                $query = "SELECT docent.DOC_id,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON docent.DOC_GEB_id = gebruiker.GEB_id";
                $result = mysqli_query($con, $query);
                $index = 0;
                while ($row = mysqli_fetch_array($result)) {
                    array_push($docenten, ["index" => $index, "values" => ["DOC_id" => $row['DOC_id'],"DOC_naam" => $row['DOC_naam'],"DOC_GEB_id" => $row['DOC_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']]]);
                    $index++;
                }
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($docenten).',"status":"ok"}');
            } elseif ($function === "change") {
                //voordefinitie van de nodige variabelen
                $column = null;
                $value = null;
                $GEB_id = null;
                //check of POST Request van alle variabelen niet leeg zijn
                if (isset($_POST['column']) && isset($_POST['value']) && isset($_POST['GEB_id'])) {
                    //variabelen vullen met POST Request
                    $column = $_POST['column'];
                    $value = $_POST['value'];
                    $GEB_id = $_POST['GEB_id'];
                    //checken of variabelen niet leeg zijn
                    if ($column === "" || $value === "" || $GEB_id === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
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
        }
        //checken of $table voor student aan te passen is (in database zal gebruiker en student aangepast worden)
        elseif ($table === "student") {
            if ($function === "add") {
                //voordefinitie van de nodige variabelen
                $GEB_username = null;
                $GEB_naam = null;
                $GEB_voornaam = null;
                $GEB_wachtwoord = null;
                $GEB_email = null;
                $STU_GEB_id = null;
                //check of POST Request van alle variabelen niet leeg zijn
                if (isset($_POST['username']) && isset($_POST['naam']) && isset($_POST['voornaam']) && isset($_POST['wachtwoord']) && isset($_POST['email'])) {
                    //variabelen vullen met POST Request
                    $GEB_username = $_POST['username'];
                    $GEB_naam = $_POST['naam'];
                    $GEB_voornaam = $_POST['voornaam'];
                    $GEB_wachtwoord = $_POST['wachtwoord'];
                    $GEB_email = $_POST['email'];
                    //checken of variabelen niet leeg zijn
                    if ($GEB_username === "" || $GEB_naam === "" || $GEB_voornaam === "" || $GEB_wachtwoord === "" || $GEB_email === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "INSERT INTO `gebruiker` (`GEB_username`, `GEB_naam`, `GEB_voornaam`, `GEB_wachtwoord`, `GEB_email`) VALUES ('$GEB_username','$GEB_naam','$GEB_voornaam','$GEB_wachtwoord','$GEB_email')";
                $result = mysqli_query($con, $query);
                $STU_GEB_id = $con -> insert_id;
                $query = "INSERT INTO `student`(`STU_GEB_id`) VALUES ($STU_GEB_id)";
                $con->query($query);
                mysqli_close($con);
                die('{"data":"ok","message":"Record added successfully","status":"ok"}');
            } elseif ($function === "getone") {
                //voordefinitie van de nodige variabelen
                $GEB_id = null;
                //check of POST Request van alle variabelen niet leeg zijn
                if (isset($_POST['id'])) {
                    //variabelen vullen met POST Request
                    $GEB_id = $_POST['id'];
                    //checken of variabelen niet leeg zijn
                    if ($GEB_id === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "SELECT student.STU_id,student.STU_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM student INNER JOIN gebruiker ON student.STU_GEB_id = gebruiker.GEB_id WHERE gebruiker.GEB_id = $GEB_id";
                $result = mysqli_query($con, $query);
                $row = mysqli_fetch_assoc($result);
                $student = ["STU_id" => $row['STU_id'],"STU_GEB_id" => $row['STU_GEB_id'],"GEB_username" => $row['GEB_username'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam'],"GEB_wachtwoord" => $row['GEB_wachtwoord'],"GEB_email" => $row['GEB_email']];
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($student).',"status":"ok"}');
            } elseif ($function === "getall") {
                //voordefinitie van de nodige variabelen
                $studenten = [];
                $query = "SELECT student.STU_id,gebruiker.GEB_naam,gebruiker.GEB_voornaam FROM student INNER JOIN gebruiker ON student.STU_GEB_id = gebruiker.GEB_id";
                $result = mysqli_query($con, $query);
                $index = 0;
                while ($row = mysqli_fetch_array($result)) {
                    array_push($studenten, ["index" => $index, "values" => ["STU_id" => $row['STU_id'],"GEB_naam" => $row['GEB_naam'],"GEB_voornaam" => $row['GEB_voornaam']]]);
                    $index++;
                }
                mysqli_free_result($result);
                mysqli_close($con);
                die('{"data":'.json_encode($studenten).',"status":"ok"}');
            }
        }
        //checken of $table voor gebruiker is (login via de gebruikerstabel en checken of het een student of een docent is)
        elseif ($table === "gebruiker") {
            //voordefinitie van de nodige variabelen
            $GEB_id = null;
            $GEB_username = null;
            $GEB_wachtwoord = null;
            //check of POST Request van alle variabelen niet leeg zijn
            if (isset($_POST['username']) && isset($_POST['wachtwoord'])) {
                //variabelen vullen met POST Request
                $GEB_username = $_POST['username'];
                $GEB_wachtwoord = $_POST['wachtwoord'];
                //checken of variabelen niet leeg zijn
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
