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
      if ($table !== 'vak' || $function !== 'addvak') {
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
      if ($table === 'vak' && $function === 'addvak') {
          $VAK_naam = null;
          $DOC_id = null;
          //check of POST Request van alle variabelen niet leeg zijn
          if (isset($_POST['VAK_naam']) && isset($_POST['DOC_id'])) {
              //variabelen vullen met POST Request
              $VAK_naam = $_POST['VAK_naam'];
              $DOC_id = $_POST['DOC_id'];
              //checken of variabelen niet leeg zijn
              if ($VAK_naam === "" || $DOC_id === "") {
                  die('{"error":"missing data","status":"fail"}');
              }
          } else {
              die('{"error":"missing data","status":"fail"}');
          }
          $query = "INSERT INTO `vak` (`VAK_naam`) VALUES ('$VAK_naam')";
          $result = mysqli_query($con, $query);
          $VAK_id = $con -> insert_id;
          $query = "INSERT INTO `vakdocent`(`VDO_docent_id`, `VDO_vak_id`) VALUES ($DOC_id,$VAK_id)";
          $con->query($query);
          mysqli_close($con);
          die('{"data":"ok","message":"Record added successfully","status":"ok"}');
      }
  }
