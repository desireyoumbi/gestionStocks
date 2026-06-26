<?php
session_start();

$_SESSION = [];
session_encode();

echo json_encode([
    "success" => true,
    "message" => "deconnexion reussie"
]);


?>