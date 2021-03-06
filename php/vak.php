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
      if ($table !== 'vakdocent' && $table !== 'vakstudent') {
          die('{"error":"wrong table","status":"fail"}');
      }
      //checken of $tabel 'docent/student' is (anders gebruiker)
      if ($table === 'vakstudent' && $function !== 'getallforvak') {
          die('{"error":"wrong function","status":"fail"}');
      } elseif ($table === 'vakdocent' && $function !== 'getallforvak') {
          die('{"error":"wrong function","status":"fail"}');
      }
  }
  //-----connection maken met server (hosting)-----
  //import van connection naar database
  require_once 'connection.php';
  //checken of connection werkt
  if (!$con) {
      die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
  } else {
      if ($table === 'vakstudent') {
          if ($function === 'getallforvak') {
              $studentid = null;
              $vakid = null;
              $sessies = [];
              if (isset($_POST['id']) && isset($_POST['vakid'])) {
                  $studentid = $_POST['id'];
                  $vakid = $_POST['vakid'];
                  if ($studentid === "" || $vakid === "") {
                      die('{"error":"missing data","status":"fail"}');
                  }
              } else {
                  die('{"error":"missing data","status":"fail"}');
              }
              $query = "SELECT sessie.SES_id, sessie.SES_naam, sessie.SES_eindtijd, sessie.SES_actief, sessie.SES_vak_id, vak.VAK_naam, sessie_student.SST_student_id, student.STU_id, gebruiker.GEB_naam, gebruiker.GEB_voornaam FROM sessie INNER JOIN sessie_student on sessie_student.SST_sessie_id = sessie.SES_id INNER JOIN student on sessie_student.SST_student_id = student.STU_id INNER JOIN vak on sessie.SES_vak_id = vak.VAK_id INNER JOIN gebruiker on gebruiker.GEB_id = student.STU_GEB_id WHERE student.STU_id = $studentid AND vak.VAK_id = $vakid";
              $result = mysqli_query($con, $query);
              $index = 0;
              $actief = null;
              while ($row = mysqli_fetch_array($result)) {
                  if ($row['SES_actief'] == 1) {
                      $actief = "Online";
                  } else {
                      $actief = "Offline";
                  }
                  array_push($sessies, ["index" => $index, "values" => ["SES_id" => $row['SES_id'],"SES_naam" => $row['SES_naam'], "SES_eindtijd" => $row['SES_eindtijd'], "SES_actief" => $actief, "SES_vak_id" => $row['SES_vak_id'],
              "VAK_naam" => $row['VAK_naam'], "SST_student_id" => $row['SST_student_id'], "STU_id" => $row['STU_id'], "Stu_naam" => $row['GEB_naam'], "Stu_voornaam" => $row['GEB_voornaam']]]);
                  $index++;
              }
              mysqli_free_result($result);
              mysqli_close($con);
              die('{"data":'.json_encode($sessies).',"status":"ok"}');
          }
      } elseif ($table === 'vakdocent') {
          if ($function === 'getallforvak') {
              $VAK_id = null;
              $sessies = [];
              if (isset($_POST['id'])) {
                  $VAK_id = $_POST['id'];
                  if ($VAK_id === "") {
                      die('{"error":"missing data","status":"fail"}');
                  }
              } else {
                  die('{"error":"missing data","status":"fail"}');
              }
              $query = "SELECT sessie.SES_id, sessie.SES_code, sessie.SES_naam, sessie.SES_eindtijd, sessie.SES_actief, sessie.SES_vak_id, vak.VAK_naam FROM sessie INNER JOIN vak on sessie.SES_vak_id = vak.VAK_id INNER JOIN vakdocent on vak.VAK_id = vakdocent.VDO_vak_id INNER JOIN docent on vakdocent.VDO_docent_id = docent.DOC_id INNER JOIN gebruiker on gebruiker.GEB_id = docent.DOC_GEB_id WHERE vak.VAK_id = $VAK_id";
              $result = mysqli_query($con, $query);
              $index = 0;
              $actief = null;
              while ($row = mysqli_fetch_array($result)) {
                  if ($row['SES_actief'] == 1) {
                      $actief = "Online";
                  } else {
                      $actief = "Offline";
                  }
                  array_push($sessies, ["index" => $index, "values" => ["SES_id" => $row['SES_id'],"SES_code" => $row['SES_code'],"SES_naam" => $row['SES_naam'], "SES_eindtijd" => $row['SES_eindtijd'], "SES_actief" => $actief, "SES_vak_id" => $row['SES_vak_id'],
                    "VAK_naam" => $row['VAK_naam']]]);
                  $index++;
              }
              mysqli_free_result($result);
              mysqli_close($con);
              die('{"data":'.json_encode($sessies).',"status":"ok"}');
          }
      }
  }
