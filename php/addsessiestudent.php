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
      if ($table !== 'sessiestudent' || $function !== 'add') {
          die('{"error":"wrong table or function","status":"fail"}');
      }
  }
  //-----connection maken met server (hosting)-----
  //import van connection naar database
  require_once 'connection.php';
  //checken of connection werkt
  if (!$con) {
      die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
  } else {
      if ($table === 'sessiestudent' && $function === 'add') {
          $code = null;
          $STU_id = null;
          $VAK_id = null;
          $SES_id = null;
          //check of POST Request van alle variabelen niet leeg zijn
          if (isset($_POST['vakid']) && isset($_POST['studentid']) && isset($_POST['code'])) {
              //variabelen vullen met POST Request
              $code = $_POST['code'];
              $STU_id = $_POST['studentid'];
              $VAK_id = $_POST['vakid'];
              //checken of variabelen niet leeg zijn
              if ($code === "" || $STU_id === "" || $VAK_id === "") {
                  die('{"error":"missing data","status":"fail"}');
              }
          } else {
              die('{"error":"missing data","status":"fail"}');
          }
          $query = "SELECT `SES_id` FROM `sessie` WHERE `SES_code` = '$code' AND `SES_vak_id` = $VAK_id";
          $result = mysqli_query($con, $query);
          if (mysqli_num_rows($result) > 1) {
              die('{"error":"Multiple sessions found","status":"fail"}');
          } elseif (mysqli_num_rows($result) == 0) {
              die('{"error":"No session found","status":"fail"}');
          }
          $row = mysqli_fetch_assoc($result);
          $SES_id = $row['SES_id'];
          $query = "INSERT INTO `sessie_student`(`SST_sessie_id`,`SST_student_id`) VALUES ($SES_id,$STU_id)";
          $result = mysqli_query($con, $query);
          die('{"data":"ok","message":"Record added successfully","status":"ok"}');
      }
  }
