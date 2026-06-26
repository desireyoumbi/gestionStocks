<?php

session_start();
// prevenir la page js qui va recevoir les donne que ceux ci sont du json pur
header("Content-Type: application/json");

require_once("../config/connexion_db.php");

//reccuperer le login et le passe envoyer par nextjs
$json = file_get_contents("php://input");

//transformer en tableau associatif
$data = json_decode($json, true);

//verification
if (isset($data["login"]) && isset($data["password"])) {
    // transformer en minuscule avant de comparer dans la base de donnee
    $login = ($data['login']);
    $passWord = strtolower($data['password']);


    $data_base_object = $connect->prepare("SELECT id , password, role FROM utilisateurs WHERE login = ? AND status = ?");
    if ($data_base_object) {
        $status = 0;
        $data_base_object->bind_param("si", $login,$status);
        $data_base_object->execute();
        $resultat = $data_base_object->get_result();

        // verifier le resultat fournir par la requette
        if ($resultat && $resultat->num_rows > 0) {

            // reccuperer le premier tableau du resultat renvoyer par la requette
            $utilisateur = $resultat->fetch_assoc();
            $reccuperer_le_password = $utilisateur["password"];

            // stocker le role et l'id du l'utilisateur qui souhaite se connecter
            $_SESSION["id_user"] = $utilisateur["id"];
            $_SESSION["role_user"] = $utilisateur["role"];


            // verifier que le pass entrer correspond a celui ascher dans la base via password_verify
            if (password_verify($passWord, $reccuperer_le_password)) {
                echo json_encode([
                    "success" => true,
                    "message" => "utilisateur a bien ete trouver",
                    "verif" => $_SESSION["id_user"],
                    "role" => $_SESSION["role_user"]
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "mauvaise connexion pass ne correspond pas"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "mauvaise connexion :: login entrer pas trouverv dans la base"
            ]);
        }

        $data_base_object->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "mauvaise connexion !! login n'a pas ete trouver ou utilisateur inexistant"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "connexion error : mauvaise structure"
    ]);
}
exit();
