<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Max-Age: 1000');
  header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

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
      if ($table !== 'gebruiker' ) {
          die('{"error":"wrong table","status":"fail"}');
      }
      //checken of $tabel 'docent/student' is (anders gebruiker)
      if ($table === 'vakdocent' && $function !== 'getallfromdocent') {
          die('{"error":"wrong function","status":"fail"}');
      } elseif ($table === 'vakstudent') {
          if ($function !== 'getallfromstudent' && $function !== 'getallstudentenfromvak' && $function !== 'getallallfromVak' ) {
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
    if ($function === 'nieuweleerling' && $table === 'gebruiker') {

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






    $result = mysql_query("SELECT * FROM gebruiker WHERE city = 'c7'");

    $query = "SELECT STU_id FROM gebruiker WHERE GEB_naam = $naam and GEB_voornaam = $voornaam" ;
    $stuid = $query;
    $result = mysqli_query($con, $query);
    $matchFound = mysql_num_rows($result) > 0 ? 'Y' : 'N';
    if ($matchFound === 'N')
    {
      $result = "Student niet gevonden";
    } elseif ($matchFound === 'Y'){
      $query = "INSERT INTO `vak_student` ( `VAS_student_id`, `VAS_vak_id`) VALUES ('$GEB_username','$GEB_naam')";
      $result = mysqli_query($con, $query);
    }

    mysqli_free_result($result);
    mysqli_close($con);
    die('{"data":'.json_encode($result).',"status":"ok"}');
    }
  }
