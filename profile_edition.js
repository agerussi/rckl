window.addEventListener("load",main);
//////////////////////////////////////////////////////

// constantes de status
var UNCHANGED=0,OK=1,BROKEN=2;

function main() {
  document.getElementById("profileForm").addEventListener("submit",checkProfile);
  document.getElementById("submitbutton").addEventListener("click",submit);
  
  loginField=document.getElementById("login");
  loginField.addEventListener("change",checkLogin);
  loginMsg=document.getElementById("login-message");
  loginOK=UNCHANGED;

  passwdField=document.getElementById("motdepasse");
  passwdField.addEventListener("change",checkPasswd);
  passwdMsg=document.getElementById("passwd-message");
  passwdOK=UNCHANGED;
  passwdTry=0;

  photo=document.getElementById("photo");
  photo.addEventListener("click",choosePhoto);
  photoMsg=document.getElementById("photo-message");
  photoOK=UNCHANGED;

}

function submit() {
  document.forms["profileForm"].submit();
}

// vérifie si tout est OK avant l'enregistrement
function checkProfile() {
  // met à zéro les champs qui ne doivent pas être modifiés
  if (loginOK!=OK) loginField.value="";
  if (passwdOK!=OK) passwdField.value="";
  photo.value=photoOK;
   
  return true;
}

function choosePhoto() {
  var input=document.getElementById("fileChooser");
  input.addEventListener("change", gestionPhoto, false);
  input.click(); // déclenche le input
}

function gestionPhoto(evt) {
  document.getElementById("fileChooser").removeEventListener("change", gestionPhoto, false);

  // tests préalables sur le fichier sélectionné
  var fichier=evt.target.files[0];
  // teste si le fichier est acceptable 
  if (!fichier.type.match('image.jpeg')) {
    photoMsg.innerHTML="Le fichier "+fichier.name+" n'est pas un fichier jpeg.";
    return;
  }
  if (fichier.size>20*1024) {  // 20 KB MAX
    photoMsg.innerHTML="Le fichier est trop gros pour une miniature (20 KB maximum).";
    return;
  }
  photoMsg.innerHTML="OK";
  photoOK=OK;

  // affichage de la photo
  var reader=new FileReader();
  reader.onload=function(e) { photo.setAttribute("src", e.target.result); } 
  reader.readAsDataURL(fichier);
}

// teste si le login est constitué uniquement de lettres ou de chiffres
function checkLogin() {
  var login=loginField.value;
  if (login.search(/\W+/)!=-1) {
    loginOK=BROKEN;
    loginMsg.innerHTML="Seules les lettres non accentuées, les chiffres et le _ sont acceptés.";
  } 
  else {
    loginOK=OK;
    loginMsg.innerHTML="OK";
  }
}

// demande une double saisie du mdp avant de valider
// ainsi qu'une longueur minimale de 5 caractères
function checkPasswd() {
  passwdOK=BROKEN;
  passwdTry+=1;
  if (passwdTry%2==1) {
    passwd=passwdField.value;
    if (passwd.length<5) {
      passwdMsg.innerHTML="Le mot de passe est trop court (minimum 5 caractères)";
      passwdTry-=1; // prochain essai = premier essai
    }
    else passwdMsg.innerHTML="Saisissez le mot de passe une deuxième fois";
  }  
  else {
    if (passwd==passwdField.value) {
      passwdMsg.innerHTML="OK";
      passwdOK=OK;
      return;
    } 
    else {
      passwdMsg.innerHTML="Les deux mots de passe sont différents! Recommencez.";
    }
  }
  passwdField.value="";
}
