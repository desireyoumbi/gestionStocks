//  utilisation de la fonction asyc : pour indiquer un processus qui va prendre du temp pour se realiser
// utilisation de la fonction await : pour demander au js d'attendre qu'une action soit faite avant de continuer ou de faire quoi que ce soit

// async function chargerEtudiants(){
//     const reponse = await fetch("http://localhost/learn_API/api/getEtudiants.php")
//     // .then(res => res.text())  ces lignes permettent d'afficher exactement l'erreur qui se produit
//     // .then(data => {
//     //     console.log(data)
//     // });
//     const etudiants = await reponse.json();
//     console.log(etudiants);
// }
 var API_URL = "http://localhost/gestionStocks/back-end/api"
const enregistrement = document.getElementById("enregistrement");
enregistrement.addEventListener("submit" , async (e)=>{
    e.preventDefault();
    const nom = document.getElementById("nom").value.trim();
    const prenom = document.getElementById("prenom").value.trim();
    const email = document.getElementById("email").value.trim();
    const select = document.getElementById("filiere").value.trim();
    const role ="admin"
    // inscription_utilisateur(nom, prenom, email)
    // ajouterEtudiant(nom, prenom)
    // deconnexion()
    // modifier(nom,prenom,email,select,email)
    // delete_user()
    show_user()
 })






//  connexion utilisateur
async function ajouterEtudiant(nom , prenom){

    const reponse = await fetch(`${API_URL}/login.php` , {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({login:nom, password:prenom})
    })
    
    //transformer la reponse obtenu ans un tableau objet
    const resultat = await reponse.json();
    console.log(resultat)
}

// inscription etudiant
async function inscription_utilisateur(nom,prenom,email){
    const reponse_inscription = await fetch(`${API_URL}/utilisateur.php` , {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            action:"ajouter",
            nom:nom,
            prenom:prenom,
            email:email

        })

        
    })

    
    const resultat_inscription = await reponse_inscription.json();
    console.log(resultat_inscription);

}


async function modifier(nom,prenom,email,password,email) {
    reponse_modifier = await fetch(`${API_URL}/utilisateur.php`,{
        "method": "PUT",
        headers: {"Content-Type" : "application/json"},
        body: JSON.stringify({
            id:9,
            nom:nom,
            prenom:prenom,
            password:password,
            role:email
        })
    }

    )

    const resultat_modification = await reponse_modifier.json()
    console.log(resultat_modification)
    
}

// supprimer un etudiant

async function delete_user() {
    const reponse_delete = await fetch(`${API_URL}/utilisateur.php`,{
        "method": "DELETE",
        headers: {"Content-Type" : "application/json"},
        body: JSON.stringify({
            id:9
        })
    })

    const resultat_delete = await reponse_delete.json();
    console.log(resultat_delete)
}

// afficher les utilisateur 

async function show_user() {
    const reponse_show = await fetch(`${API_URL}/utilisateur.php`,{
        "method": "GET"
    })

    const resultat_show = await reponse_show.json();
    console.log(resultat_show)
    
}
// deconnexio

async function deconnexion() {
  const reponse_deconnexion = await fetch(`${API_URL}/logout.php`)
  const resultat_deconnexion = await reponse_deconnexion.json();
  console.log(resultat_deconnexion.message);    
}

// async function getEtudiants(){
//          const reponse = await fetch("http://localhost/learn_API/api/getEtudiants.php")
//     //   .then(res => res.text())  
//     //   .then(data => {
//     //       console.log(data)
//     //   });
//      const etudiants = await reponse.json();
//      etudiants.forEach(element => {
//         let ul = document.createElement("ul")
//         ul.id="flex"
//         let li1 = document.createElement("li")
//         let li2 = document.createElement("li")
//         let li3 = document.createElement("li")
//         let li4 = document.createElement("li")
//         let li5 = document.createElement("li")

//         // affecter les valeurs 
//         li1.textContent=element.nom
//         li2.textContent=element.email
//         li3.textContent=element.password
//         li4.textContent=element.role
//         li5.textContent=element.telephone

//         // ajout
//         ul.appendChild(li1)
//         ul.appendChild(li2)
//         ul.appendChild(li3)
//         ul.appendChild(li4)
//         ul.appendChild(li5)

//         let contentParent = document.getElementsByClassName("contentParent")[0]
//         contentParent.appendChild(ul)
        
//      });

// }
// document.addEventListener("DOMContentLoaded", getEtudiants)
