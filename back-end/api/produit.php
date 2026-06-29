<?php


// permet d'afficher exactement l'erreur au niveau de la console
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

require_once("../config/connexion_db.php");

// reccuprer tous ce qui peu venir du frontend
$json = file_get_contents("php://input");
$data = json_decode($json, true);

$action = $_SERVER["REQUEST_METHOD"];

switch ($action) {
    case "POST":
        // reccuperer les differents champs

        $nom = $data["nom"];
        $quantite = $data["quantite"];
        $prix_unitaire = $data["prix_unitaire"];
        $seuil_alert = $data["seuil_alert"];
        $categorie = $data["categorie"];
        $status = 0;

        if (!empty($nom) && !empty($quantite) && !empty($prix_unitaire) && !empty($seuil_alert) && !empty($categorie)) {

            $request_control = $connect->prepare("SELECT nom FROM produit WHERE nom = ?");

            if ($request_control) {
                $request_control->bind_param("s", $nom);
                $request_control->execute();

                $result_control = $request_control->get_result();

                // verifier que le produit n'existe pas en stock
                if ($result_control && $result_control->num_rows == 0) {

                    $request1 = $connect->prepare("INSERT INTO produit(nom,quantite,prix_unitaire,seuil_alerte,id_categorie,status)
                        VALUES (?,?,?,?,?,?)");

                    if ($request1) {
                        $request1->bind_param("siiiii", $nom, $quantite, $prix_unitaire, $seuil_alert, $categorie, $status);
                        $request1->execute();

                        echo json_encode([
                            "success" => true,
                            "message" => "le produit a ete ajouter avec success"
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
                        "success" => false,
                        "message" => "un produit porte deja ce nom en stock"
                    ]);
                }
                // fermeture de la requete
                $request_control->close();
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "la requete de verification du nom lors du post n'a pas aboutie"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "veuillez remplir tous les champs"
            ]);
        }

        break;
    case "GET":
        $request1 = "SELECT * FROM produit  WHERE status = 0";
        if ($request1) {
            $result_request = $connect->query($request1);
            $tab_infos_user = $result_request->fetch_all(MYSQLI_ASSOC);
            echo json_encode($tab_infos_user);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "la requete a echouer"
            ]);
        }
        break;
    case "PUT":
        if (!empty($data["id"]) && !empty($data["nom"]) && !empty($data["quantite"]) && !empty($data["prix_unitaire"]) && !empty($data["seuil_alert"]) && !empty($data["id_categorie"])) {
            $nom = strtolower($data["nom"]);
            $quantite = strtolower($data["quantite"]);
            $prix_unitaire = $data["prix_unitaire"];
            $seuil_alert = $data["seuil_alert"];
            $categorie = $data["id_categorie"];
            $id_product = $data["id"];
            $request1 = $connect->prepare("UPDATE produit SET nom = ? , quantite = ? , prix_unitaire = ? , seuil_alerte = ? , id_categorie = ? WHERE id = ?");
            if ($request1) {
                $request1->bind_param("sssssi", $nom, $quantite, $prix_unitaire,  $seuil_alert, $categorie, $id_product);
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


        break;
    case "DELETE":
        $status = 1;
        $id_product = $data["id"];
        if (!empty($id_product)) {
            $request1 = $connect->prepare("UPDATE produit SET status = ? WHERE id = ?");
            if ($request1) {
                $request1->bind_param("ii", $status, $id_product);
                $request1->execute();
                echo json_encode([
                    "success" => true,
                    "message" => "le produit a bien ete supprimer"
                ]);
                $request1->close();
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "verifier que vous avez entrer l'id du produit a supprimer"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "verifier que vous avez entrer l'id du produit a supprimer"
            ]);
        }
        break;
    default:
        echo json_encode([
            "success" => false,
            "message" => "la methode utiliser dans la requete n'est pas connu"
        ]);
}
