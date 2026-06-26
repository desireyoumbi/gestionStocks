<?php
//connexion a la base de donnee
$host = "localhost";
$user = "root";
$passWord = "";
$db_name = "base_test";

$connect = new mysqli($host,$user,$passWord,$db_name);
// verification de la connexion
if($connect->connect_error){
    echo json_encode(["success" => false , "message" => "connexion echouer"]);
    exit;
}

?>