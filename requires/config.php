<?php
    ini_set("session.hash_function","sha512");
    session_start();

    ini_set("max_execution_time",500);

    $db_host = "135.148.99.237";
    $db_user = "root";
    $db_pass = "";
    $db_data = "reallifecity";

    $con = new PDO('mysql:host=' . $db_host . ';port=3306' . ';dbname=' . $db_data,
        $db_user, $db_pass
    );
?>