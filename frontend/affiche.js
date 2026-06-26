async function getEtudiants(){
         const reponse = await fetch("http://localhost/learn_API/api/getEtudiants.php")
    //   .then(res => res.text())  
    //   .then(data => {
    //       console.log(data)
    //   });
     const etudiants = await reponse.json();
     var compteur =0
     etudiants.forEach(element => {
        let ul = document.createElement("ul")
        let li1 = document.createElement("li")
        let li2 = document.createElement("li")
        let li3 = document.createElement("li")
        let li4 = document.createElement("li")
        let li5 = document.createElement("li")

        // affecter les valeurs 
        li1.textContent=element.nom
        li2.textContent=element.email
        li3.textContent=element.password
        li4.textContent=element.role
        li5.textContent=element.telephone

        // ajout
        ul.appendChild(li1)
        ul.appendChild(li2)
        ul.appendChild(li3)
        ul.appendChild(li4)
        ul.appendChild(li5)
        
        let contentParent = document.querySelector(".contentParent")
        if(compteur==0){
            
             ul.classList.add("color")
            compteur=1
            
        }
        else{
               ul.classList.add("flex")
            compteur=0

        }
        contentParent.appendChild(ul)
        
     });

}
document.addEventListener("DOMContentLoaded", getEtudiants)
