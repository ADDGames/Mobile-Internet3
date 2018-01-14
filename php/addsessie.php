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
      if ($table !== 'sessie' || $function !== 'add') {
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
      if ($table === 'sessie' && $function === 'add') {
          $code = "";
          $SES_naam = null;
          $VAK_id = null;
          //check of POST Request van alle variabelen niet leeg zijn
          if (isset($_POST['SES_naam']) && isset($_POST['VAK_id'])) {
              $characters = '0147852369azertyuiopqsdfghjklmwxcvbn';
              $max = strlen($characters) - 1;
              for ($i = 0; $i < 10; $i++) {
                  $code .= $characters[mt_rand(0, $max)];
              }
              //variabelen vullen met POST Request
              $SES_naam = $_POST['SES_naam'];
              $VAK_id = $_POST['VAK_id'];
              //checken of variabelen niet leeg zijn
              if ($SES_naam === "" || $VAK_id === "") {
                  die('{"error":"missing data","status":"fail"}');
              }
          } else {
              die('{"error":"missing data","status":"fail"}');
          }
          $query = "INSERT INTO `sessie` (`SES_code`,`SES_naam`,`SES_vak_id`,`SES_actief`) VALUES ('$code','$SES_naam',$VAK_id,1)";
          $result = mysqli_query($con, $query);
          die('{"data":"ok","message":"Record added successfully","status":"ok"}');
      }
  }
