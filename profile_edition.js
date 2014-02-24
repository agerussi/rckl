window.addEventListener("load",main);
//////////////////////////////////////////////////////

// constantes de status
var ST_UNCHANGED=0,ST_OK=1,BROKEN=2;

function main() {
  document.getElementById("submitbutton").addEventListener("click",submit);
 
  // champs spécifiques au mode "new" 
  if (newProfile) {
    loginField=document.getElementById("login");
    loginField.addEventListener("change",checkLogin);
    loginMessage=document.getElementById("login-message");
    loginST=ST_UNCHANGED;

    nameField=document.getElementById("nom");
    nameField.addEventListener("change",checkName);
    nameMessage=document.getElementById("nom-message");

    surnameField=document.getElementById("prenom");
    surnameField.addEventListener("change",checkName);
    surnameMessage=document.getElementById("prenom-message");

    profileField=document.getElementById("nomprofil");
    profileField.addEventListener("change",checkIdentifier);
    profileMessage=document.getElementById("nomprofil-message");
    profileST=ST_UNCHANGED;
  }

  // champs communs aux deux modes
  passwdField=document.getElementById("motdepasse");
  passwdField.addEventListener("change",checkPasswd);
  passwdMessage=document.getElementById("passwd-message");
  passwdST=ST_UNCHANGED;
  passwdTry=0;

  // champs spécifiques au mode "edition" 
  if (!newProfile) {
    photo=document.getElementById("photo");
    photo.addEventListener("click",choosePhoto);
    photoMessage=document.getElementById("photo-message");
    photoST=ST_UNCHANGED;
  }
}

// construit une proposition d'identifiant en se basant sur nom et prénom
function nameToProfile() {
  profileST=ST_OK;
  function lastChar(str) { return str.charAt(str.length-1); }
  // premier essai: prénom+initiales nom
  var profile=surnameField.value+" ";
  profile+=nameField.value.match(/^.|-.|\s./g).map(lastChar).join("");
  if (!profileExists(profile)) {
    profileField.value=profile;
    return;
  }
  // deuxième essai: nom+initiales prénom
  var profile=nameField.value+" ";
  profile+=surnameField.value.match(/^.|-.|\s./g).map(lastChar).join("");
  if (!profileExists(profile)) {
    profileField.value=profile;
    return;
  }
  // troisième essai: prénom+nom
  var profile=surnameField.value+" "+nameField.value;
  if (!profileExists(profile)) {
    profileField.value=profile;
    return;
  }
  // sinon on abdique !
  profileField.value="";
  profileST=BROKEN;
}

// teste si le profil existe déjà dans la BD
function profileExists(profile) {
  return ajax("dbtools.php?cmd=profileexists&profile="+profile);
}

// vérifie la validité du nom de profil
// et effectue un léger nettoyage
function checkIdentifier() {
  profileST=BROKEN;
  var profile=this.value.trim();
  // supprime les espaces inutiles
  profile=profile.replace(/\s(?=(\s|-))/g,"");
  profile=profile.replace(/-\s/g,"-");
  // vérifie qu'il est non vide
  if (profile.length==0) {
    profileMessage.innerHTML="L'identifiant est obligatoire";
    return;
  }
  // vérifie qu'il n'existe pas déjà
  if (profileExists(profile)) {
    profileMessage.innerHTML="Cet identifiant existe déjà";
    return;
  }
  // sauvegarde le résultat
  this.value=profile;
  profileMessage.innerHTML="OK";
  profileST=ST_OK;
}

// impose les minuscules sauf après des espaces ou des tirets
function checkName() {
  var name=this.value.trim().toLocaleLowerCase();
  // supprime les espaces inutiles
  name=name.replace(/\s(?=(\s|-))/g,"");
  name=name.replace(/-\s/g,"-");
  // met les majuscules
  function makeUpper(car) { return car.toLocaleUpperCase(); }
  name=name.replace(/^.|-.| ./g, makeUpper);
  // sauvegarde le résultat
  this.value=name;
  // calcul une proposition d'identifiant
  if (name.length!=0) nameToProfile();
}

function submit() {
  if (checkForm()) document.forms["profileForm"].submit();
}

// vérifie si tout est OK avant l'enregistrement
function checkForm() {
  if (newProfile) { // mode nouveau compte
    var OK=true;
    if (loginST!=ST_OK) {
      loginMessage.innerHTML="Le login n'est pas rempli correctement";
      OK=false;
    }
    if (passwdST!=ST_OK) {
      passwdMessage.innerHTML="Le mot de passe n'est pas valide";
      OK=false;
    }
    if (nameField.value.length==0) {
      nameMessage.innerHTML="Le nom n'est pas valide";
      OK=false;
    }
    if (surnameField.value.length==0) {
      surnameMessage.innerHTML="Le prénom n'est pas valide";
      OK=false;
    }
    if (profileST!=ST_OK) {
      profileMessage.innerHTML="L'identifiant n'est pas valide";
      OK=false;
    }
    return OK;
  } 
  else { // en mode edition, seul le mot de passe est contrôlé
    if (passwdST!=ST_OK) passwdField.value="";
    return true;
  }
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
    photoMessage.innerHTML="Le fichier "+fichier.name+" n'est pas un fichier jpeg.";
    return;
  }
  if (fichier.size>20*1024) {  // 20 KB MAX
    photoMessage.innerHTML="Le fichier est trop gros pour une miniature (20 KB maximum).";
    return;
  }
  photoMessage.innerHTML="OK";
  photoST=ST_OK;

  // affichage de la photo
  var reader=new FileReader();
  reader.onload=function(e) { photo.setAttribute("src", e.target.result); } 
  reader.readAsDataURL(fichier);
}

// teste si le login est constitué uniquement de lettres ou de chiffres
function checkLogin() {
  var login=loginField.value;
  loginST=BROKEN;

  // teste si le login est non vide
  if (login.length==0) {
    loginMessage.innerHTML="le login est obligatoire.";
    return;
  }

  // teste si les bons caractères sont utilisés
  if (login.search(/\W+/)!=-1) {
    loginMessage.innerHTML="Seules les lettres non accentuées, les chiffres et le _ sont acceptés.";
    return;
  } 

  // teste si le login est unique
  if (loginExists(login)) {
    loginMessage.innerHTML="Ce login existe déjà.";
    return;
  }

  loginST=ST_OK;
  loginMessage.innerHTML="OK";
}

// teste si un login existe déjà dans la BD
function loginExists(login) {
  return ajax("dbtools.php?cmd=loginexists&login="+login);
}

// effectue une requête ajax et interprète la réponse en json
function ajax(query) {
  xhr= new XMLHttpRequest();
  xhr.open("GET",query,false); 
  xhr.send();
  //alert("response: "+xhr.response);
  return JSON.parse(xhr.response);
}

// demande une double saisie du mdp avant de valider
// ainsi qu'une longueur minimale de 5 caractères
function checkPasswd() {
  passwdST=BROKEN;
  passwdTry+=1;
  if (passwdTry%2==1) {
    passwd=passwdField.value;
    if (passwd.length<5) {
      passwdMessage.innerHTML="Le mot de passe est trop court (minimum 5 caractères)";
      passwdTry-=1; // prochain essai = premier essai
    }
    else passwdMessage.innerHTML="Saisissez le mot de passe une deuxième fois";
  }  
  else {
    if (passwd==passwdField.value) {
      passwdMessage.innerHTML="OK";
      passwdST=ST_OK;
      return;
    } 
    else {
      passwdMessage.innerHTML="Les deux mots de passe sont différents! Recommencez.";
    }
  }
  passwdField.value="";
}
