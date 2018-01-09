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
      if ($table === 'vakdocent') {
          if ($function === 'getallfromdocent') {
              $docentid = null;
              $vakken = [];
              if (isset($_POST['id'])) {
                  $docentid = $_POST['id'];
                  if ($docentid === "") {
                      die('{"error":"missing data","status":"fail"}');
                  }
              } else {
                  die('{"error":"missing data","status":"fail"}');
              }
              $query = "SELECT vak.VAK_id,vak.VAK_naam FROM vak INNER JOIN vakdocent ON vak.VAK_id =vakdocent.VDO_vak_id WHERE vakdocent.VDO_docent_id = $docentid";
              $result = mysqli_query($con, $query);
              $index = 0;
              while ($row = mysqli_fetch_array($result)) {
                  array_push($vakken, ["index" => $index, "values" => ["VAK_id" => $row['VAK_id'],"VAK_naam" => $row['VAK_naam']]]);
                  $index++;
              }
              mysqli_free_result($result);
              mysqli_close($con);
              die('{"data":'.json_encode($vakken).',"status":"ok"}');
          }
      } elseif ($table === 'vakstudent') {
          if ($function === 'getallfromstudent') {
              $studentid = null;
              $vakken = [];
              if (isset($_POST['id'])) {
                  $studentid = $_POST['id'];
                  if ($studentid === "") {
                      die('{"error":"missing data","status":"fail"}');
                  }
              } else {
                  die('{"error":"missing data","status":"fail"}');
              }
              $query = "SELECT vak.VAK_id,vak.VAK_naam, gebruiker.GEB_naam FROM vak INNER JOIN vakstudent ON vak.VAK_id = vakstudent.VAS_vak_id INNER JOIN vakdocent ON vakdocent.VDO_vak_id = vak.VAK_id INNER JOIN docent ON docent.DOC_id = vakdocent.VDO_docent_id INNER JOIN gebruiker ON gebruiker.GEB_id = docent.DOC_GEB_id WHERE vakstudent.VAS_student_id = $studentid";
              $result = mysqli_query($con, $query);
              $index = 0;
              while ($row = mysqli_fetch_array($result)) {
                  array_push($vakken, ["index" => $index, "values" => ["VAK_id" => $row['VAK_id'],"VAK_naam" => $row['VAK_naam'], "Docent_naam" => $row['GEB_naam']]]);
                  $index++;
              }
              mysqli_free_result($result);
              mysqli_close($con);
              die('{"data":'.json_encode($vakken).',"status":"ok"}');
          } elseif ($function === 'getallstudentenfromvak') {
              $vakid = null;
              $studenten = [];
              $vakken = [];
              if (isset($_POST['id'])) {
                  $vakid = $_POST['id'];
                  if ($vakid === "") {
                      die('{"error":"missing data","status":"fail"}');
                  }
              } else {
                  die('{"error":"missing data","status":"fail"}');
              }
              $query = "SELECT gebruiker.GEB_voornaam, gebruiker.GEB_naam FROM student INNER JOIN vakstudent ON student.STU_id = vakstudent.VAS_student_id INNER JOIN gebruiker ON gebruiker.GEB_id = student.STU_GEB_id WHERE vakstudent.VAS_vak_id = $vakid";
              $result = mysqli_query($con, $query);
              $index = 0;
              while ($row = mysqli_fetch_array($result)) {
                  array_push($studenten, ["index" => $index, "values" => ["Voornaam" => $row['GEB_voornaam'], "Naam" => $row['GEB_naam']]]);
                  $index++;
              }
              mysqli_free_result($result);
              mysqli_close($con);
/*

              $query2 = "SELECT vak.VAK_id, vak.VAK_naam, gebruiker.GEB_naam FROM vak inner join vakdocent ON vakdocent.VDO_vak_id = vak.VAK_id INNER JOIN docent ON docent.DOC_id = vakdocent.VDO_docent_id INNER JOIN gebruiker ON gebruiker.GEB_id = docent.DOC_GEB_id WHERE vakdocent.VDO_vak_id = $vakid";
              $result2 = mysqli_query($con, $query2);
              $index = 0;
              while ($row = mysqli_fetch_array($result2)) {
                  array_push($vakken, ["index" => $index, "values" => ["Voornaam" => $row['GEB_voornaam'], "Naam" => $row['GEB_naam']]]);
                  $index++;
              }
              mysqli_free_result($result2);
              mysqli_close($con);
*/
              die('{"data":'.json_encode($studenten /*, $vakken*/).',"status":"ok"}');
          } elseif ($function === 'getallallfromVak'){
            $vakid = null;
            $vakken = [];
            if (isset($_POST['id'])) {
                $vakid = $_POST['id'];
                if ($vakid === "") {
                    die('{"error":"missing data","status":"fail"}');
                }
            } else {
                die('{"error":"missing data","status":"fail"}');
            }
            $query = "SELECT vak.VAK_id, vak.VAK_naam, gebruiker.GEB_naam FROM vak inner join vakdocent ON vakdocent.VDO_vak_id = vak.VAK_id INNER JOIN docent ON docent.DOC_id = vakdocent.VDO_docent_id INNER JOIN gebruiker ON gebruiker.GEB_id = docent.DOC_GEB_id WHERE vakdocent.VDO_vak_id = $vakid";
            $result = mysqli_query($con, $query);
            $index = 0;
            while ($row = mysqli_fetch_array($result)) {
                array_push($vakken, ["index" => $index, "values" => ["VAK_id" => $row['VAK_id'],"VAK_naam" => $row['VAK_naam'], "Docent_naam" => $row['GEB_naam']]]);
                $index++;
            }
            mysqli_free_result($result);
            mysqli_close($con);
            die('{"data":'.json_encode($vakken).',"status":"ok"}');
          }
      }
  }
