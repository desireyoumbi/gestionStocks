const video = document.getElementById("introVideo");

if(sessionStorage.getItem("introPlayed")){

    window.location.href="login.php";

}

video.addEventListener("ended",()=>{

    sessionStorage.setItem("introPlayed","true");

    window.location.href="login.php";

});