<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once("../config/connexion_db.php");

$json = file_get_contents("php://input");
$data = json_decode($json, true);

// verification de l'action a effectuer 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reccuperetion des information
    $name_user = strtolower($data["nom"]);
    $password = strtolower($data["prenom"]);
    $prenom = $data["email"];

    if (!empty($name_user) && isset($password) && !empty($prenom)) {

        // compter le nombre de ligne de la table utilisateur dans la base
        $request1 = $connect->query("SELECT COUNT(*) as total FROM utilisateurs");

        if ($request1) {
            $result_table = $request1->fetch_assoc();
            $number_line = $result_table["total"];

            // verifier si le nombre de ligne de la table est 0 si oui creer le compte du user
            if ($number_line == 0) {

                // creation du login et enregistrement du role
                $login = "ADMIN" . $name_user;
                $role = "ADMIN";

                // hachage du mot de pass
                $pass_hachage = password_hash($password, PASSWORD_DEFAULT);

                // preparer la requete d'insertion

                $inserer_userAD = $connect->prepare("INSERT INTO utilisateurs (nom_prenom,login,password,role) VALUES(?,?,?,?)");
                if ($inserer_userAD) {

                    $inserer_userAD->bind_param("ssss", $name_user, $login, $pass_hachage, $role);
                    $inserer_userAD->execute();

                    echo json_encode([
                        "success" => true,
                        "message" => "inscription reussie"
                    ]);
                    $inserer_userAD->close();
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "mauvaise connexion"
                    ]);
                }
            }
            //creer le compte de chaque utilisateur qui s'insrcrit au systeme
            else {
                //reccuperer les infos du users
                $login = "VENDEUR" . $name_user;
                $role = "VENDEUR";

                // hachage du mot de pass
                $pass_hachage = password_hash($password, PASSWORD_DEFAULT);

                // preparer la requete d'insertion

                $exist_user = $connect->prepare("SELECT nom_prenom FROM utilisateurs WHERE nom_prenom = ?");
                $exist_user->bind_param("s", $name_user);
                $exist_user->execute();
                $data_result = $exist_user->get_result();

                if ($data_result && $data_result->num_rows == 0) {
                    $inserer_user = $connect->prepare("INSERT INTO utilisateurs (nom_prenom,login,password,role) VALUES(?,?,?,?)");
                    if ($inserer_user) {

                        $inserer_user->bind_param("ssss", $name_user, $login, $pass_hachage, $role);
                        $inserer_user->execute();

                        echo json_encode([
                            "success" => true,
                            "message" => "inscription reussie"
                        ]);

                        $inserer_user->close();
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "mauvaise connexion"
                        ]);
                    }
                    $exist_user->close();
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "utilisateur existe deja"
                    ]);
                }
            }

            // else {
            //         echo json_encode([
            //             "success" => false,
            //             "message" => "utilisateur existe deja"
            //         ]);
            // }

        } else {
            echo json_encode([
                "connect" => false,
                "message" => "le serveur n'a pas pu se lancer"
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "les champ ne sont pas tous remplis"
        ]);
    }
}

////////////////--------------------/////////////////////-------------------//////////////
// modifier les information d'un utilisateur

else if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    if (!empty($data["id"]) && !empty($data["nom"]) && !empty($data["prenom"]) && !empty($data["role"]) &&  isset($data["password"])) {
        $nom = strtolower($data["nom"]);
        $prenom = strtolower($data["prenom"]);
        $password = $data["password"];
        $role = $data["role"];
        $login = $role . $nom;
        $pass_hachage = password_hash($password, PASSWORD_DEFAULT);
        $id = $data["id"];
        $request1 = $connect->prepare("UPDATE utilisateurs SET nom = ? , prenom = ? , login = ? , password = ? , role = ? WHERE id = ?");
        if ($request1) {
            $request1->bind_param("sssssi", $nom, $prenom, $login,  $pass_hachage, $role, $id);
            $request1->execute();

            echo json_encode([
                "success" => true,
                "message" => "modifivation reussie"
            ]);
            $request1->close();
        } else {
            echo json_encode([
                "success" => false,
                "message" => "la requete n'a pas aboutie"
            ]);
        }
    } else {
        echo json_encode([
            "sucess" => false,
            "message" => "verifier que tous les champ sont remplis"
        ]);
    }


    //  verifier que le tableau contient au moins les valeur



}


////////////////--------------------/////////////////////-------------------//////////////
// supprimer un utilisateur 

else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    if (!empty($data["id"])) {
        $id_user = $data["id"];
        $status = 1;
        $request1 = $connect->prepare("UPDATE utilisateurs SET status = ?  WHERE id = ?");
        if ($request1) {
            $request1->bind_param("ii",$status, $id_user);
            $request1->execute();

            echo json_encode([
                "success" => true,
                "message" => "l'utilisateur a bien ete supprimer"
            ]);

        } else {
            echo json_encode([
                "success" => false,
                "message" => "l'id inexistant dans la base "
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "message" => "l'id envoyer est vide"
        ]);
    }
}


////////////////--------------------/////////////////////-------------------//////////////
// afficher les information d'un utilisateur

else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $request1 ="SELECT * FROM utilisateurs";

    if($request1){
        $result_request = $connect->query($request1);
        $tab_infos_user = $result_request->fetch_all(MYSQLI_ASSOC);
        echo json_encode($tab_infos_user);

    }
    else{
        echo json_encode([
            "success" => false,
            "message" => "la requete de connexion a echouer"
        ]);
    }
}



////////////////--------------------/////////////////////-------------------//////////////
// retourner une erreur si action n'existe pas 

else {
    echo json_encode([
        "success" => false,
        "message" => "methode de la requete inexistant"
    ]);
}
