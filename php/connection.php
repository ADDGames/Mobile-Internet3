<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

    $dbhost = '127.0.0.1';
    $dbuser = 'root';
    $dbpass = '';

    $db = 'project';
    $con = mysqli_connect($dbhost, $dbuser, $dbpass, $db) or die('{"error":"Connection failed","status":"fail"}');
