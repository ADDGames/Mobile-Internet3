<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    $body = file_get_contents('php://input');
    $postvars = json_decode($body, true);
    $function = $postvars["function"];
    $table = $postvars["table"];

    if ($function == null || $function == '') {
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
        if ($function !== 'add' && $function !== 'getone' && $function !== 'getall') {
            die('{"error":"wrong function","status":"fail"}');
        }
    } else {
        die('{"error":"missing data","table":"'. $table. '", "function":"' . $function . '","status":"fail"}');
    }

    require_once '../include/connection.php';

    if (!$con) {
        die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
    } else {
        if ($table == "docent") {
            if ($bewerking == "add") {
                $new_GEB_username = null;
                $new_GEB_naam = null;
                $new_GEB_voornaam = null;
                $new_GEB_wachtwoord = null;
                $new_GEB_email = null;
                $new_DOC_GEB_id = null;
                if (isset($_POST['username']) && isset($_POST['naam']) && isset($_POST['voornaam']) && isset($_POST['wachtwoord']) && isset($_POST['email'])) {
                    $new_GEB_username = $_POST['username'];
                    $new_GEB_naam = $_POST['naam'];
                    $new_GEB_voornaam = $_POST['voornaam'];
                    $new_GEB_wachtwoord = $_POST['wachtwoord'];
                    $new_GEB_email = $_POST['email'];
                    if ($new_GEB_username === "" || $new_GEB_naam === "" || $new_GEB_voornaam === "" || $new_GEB_wachtwoord === "" || $new_GEB_email === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "INSERT INTO `gebruiker`(`GEB_username`, `GEB_naam`, `GEB_voornaam`, `GEB_wachtwoord`, `GEB_email`) OUTPUT INSERTED.GEB_id VALUES (`$new_GEB_username`,`$new_GEB_naam`,`$new_GEB_voornaam`,`$new_GEB_wachtwoord`,`$new_GEB_email`)";
                $result = mysqli_query($query);
                $row = mysqli_fetch_assoc($result);
                $new_DOC_GEB_id = $row['GEB_id'];
                $query = "INSERT INTO `docent`(`DOC_naam`, `DOC_gebruiker_id`) VALUES (`$new_DOC_naam`,$new_DOC_GEB_id)";
                $con->query($query);
            }
            if ($bewerking == "getone") {
                $new_DOC_id = null;
                if (isset($_POST['id'])) {
                    $new_DOC_id = $_POST['id'];
                    if ($new_GEB_username === "") {
                        die('{"error":"missing data","status":"fail"}');
                    }
                } else {
                    die('{"error":"missing data","status":"fail"}');
                }
                $query = "SELECT docent.DOC_id,docent.DOC_naam,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id WHERE docent.DOC_id = $new_DOC_id";
                $result = mysqli_query($query);
                $row = mysqli_fetch_assoc($result);
                $docent = new docent($row['DOC_id'], $row['DOC_naam'], $row['DOC_GEB_id'], $row['GEB_username'], $row['GEB_naam'], $row['GEB_voornaam'], $row['GEB_wachtwoord'], $row['GEB_email']);
                die('{"data":'.json_encode($docent).',"status":"ok"}');
            }

            if ($bewerking == "getall") {
                $docenten = [];
                $query = "SELECT docent.DOC_id,docent.DOC_naam,docent.DOC_GEB_id,gebruiker.GEB_username,gebruiker.GEB_naam,gebruiker.GEB_voornaam,gebruiker.GEB_wachtwoord,gebruiker.GEB_email FROM docent INNER JOIN gebruiker ON Docent.DOC_gebruiker_id = gebruiker.GEB_id";
                $result = mysqli_query($query);
                while ($docent = mysqli_fetch_array($result)) {
                    array_push($docenten, new docent($docent['DOC_id'], $docent['DOC_naam'], $docent['DOC_GEB_id'], $docent['GEB_username'], $docent['GEB_naam'], $docent['GEB_voornaam'], $docent['GEB_wachtwoord'], $docent['GEB_email']));
                }
                die('{"data":'.json_encode($docenten).',"status":"ok"}');
            }
        }
    }
    class docent
    {
        private $DOC_id;
        private $DOC_GEB_id;
        private $GEB_username;
        private $GEB_naam;
        private $GEB_voornaam;
        private $GEB_wachtwoord;
        private $GEB_email;
        public function __construct($newDOC_id, $new_DOC_GEB_id, $new_GEB_username, $new_GEB_naam, $new_GEB_voornaam, $new_GEB_wachtwoord, $new_GEB_email)
        {
            $this->DOC_id = $new_DOC_id;
            $this->DOC_GEB_id = $new_DOC_GEB_id;
            $this->GEB_username = $new_GEB_username;
            $this->GEB_naam = $new_GEB_naam;
            $this->GEB_voornaam = $new_GEB_voornaam;
            $this->GEB_wachtwoord = $new_GEB_wachtwoord;
            $this->GEB_email = $new_GEB_email;
        }
        public function get_DOC_id()
        {
            return $this->DOC_id;
        }
        public function get_DOC_GEB_id()
        {
            return $this->DOC_GEB_id;
        }
        public function get_GEB_username()
        {
            return $this->GEB_username;
        }
        public function get_GEB_naam()
        {
            return $this->GEB_naam;
        }
        public function get_GEB_voornaam()
        {
            return $this->GEB_voornaam;
        }
        public function get_GEB_wachtwoord()
        {
            return $this->GEB_wachtwoord;
        }
        public function get_GEB_email()
        {
            return $this->GEB_email;
        }
        public function set_GEB_username($new_GEB_username)
        {
            $this->GEB_username = $new_GEB_username;
        }
        public function set_GEB_naam($new_GEB_naam)
        {
            $this->GEB_naam = $new_GEB_naam;
        }
        public function set_GEB_voornaam($new_GEB_voornaam)
        {
            $this->GEB_voornaam = $new_GEB_voornaam;
        }
        public function set_GEB_wachtwoord($new_GEB_wachtwoord)
        {
            $this->GEB_wachtwoord = $new_GEB_wachtwoord;
        }
        public function set_GEB_email($new_GEB_email)
        {
            $this->GEB_email = $new_GEB_email;
        }
    }
