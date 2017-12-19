<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    $body = file_get_contents('php://input');
    $postvars = json_decode($body, true);
    $table = $postvars["table"];
    

      require_once 'connection.php';
      if (!$con) {
          die('{"error":"Connection failed","mysqlError":"' . json_encode($con -> error) .'","status":"fail"}');
      } else {

        $SQL = "SELECT GEB_username FROM gebruiker" . "WHERE ID = " . $_GET['id'];


        $result = mysql_query($SQL);

        while ($db_field = mysql_fetch_assoc($result)) {

           print "User ID: " . $db_field['ID'] . "<BR>";
           print "User Name: " . $db_field['User_Name'] . "<BR>";
           print "First Name: " . $db_field['First_Name'] . "<BR>";
           print "Last Name: " . $db_field['Last_Name'] . "<BR>";
        };
      }
