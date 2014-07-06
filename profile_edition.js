window.addEventListener("load",main);
//////////////////////////////////////////////////////

// constantes de status
var ST_UNCHANGED=0,ST_OK=1,ST_BROKEN=2;
// coordonnées du centre de la France (approx.)
var latitude=46.890232; 
var longitude=2.874755;

// crée la carte et le marqueur déplaçable
function initializeMap() {
  // la carte
  var mapOptions = {
    center: new google.maps.LatLng(latitude,longitude),
    zoom: (newProfile) ? 5:11,
    streetViewControl: false
  }
  var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
  // le marqueur
  var markerOptions = {
    map: map,
    position: new google.maps.LatLng(latitude,longitude)
  }
  var marker=new google.maps.Marker(markerOptions);
  // abonnement pour le marqueur
  function moveMarker(event) {
    latitude=event.latLng.lat();
    longitude=event.latLng.lng();
    latitudeField.value=latitude;
    longitudeField.value=longitude;
    marker.setPosition(event.latLng);
    latLngST=ST_OK;
    gpsMessage.innerHTML="";
  }
  google.maps.event.addListener(map,"rightclick",moveMarker);
}

function main() {
  document.getElementById("profilesubmitbutton").addEventListener("click",submit);
  if (upgradeProfile) userId=ajax("dbtools.php?cmd=getuserid");

  // champs spécifiques au mode "new" 
  if (newProfile) {
    if (!upgradeProfile) {
      loginField=document.getElementById("login");
      loginField.value="";
      loginField.addEventListener("change",checkLogin);
      loginMessage=document.getElementById("login-message");
      loginST=ST_UNCHANGED;
    }

    emailField=document.getElementById("email");
    emailField.value="";
    emailField.addEventListener("change",checkEmail);
    emailMessage=document.getElementById("email-message");

    nameField=document.getElementById("nom");
    nameField.value="";
    nameField.addEventListener("change",checkName);
    nameMessage=document.getElementById("nom-message");

    surnameField=document.getElementById("prenom");
    surnameField.value="";
    surnameField.addEventListener("change",checkSurname);
    surnameMessage=document.getElementById("prenom-message");

    profileField=document.getElementById("nomprofil");
    profileField.value="";
    profileField.addEventListener("change",checkIdentifier);
    profileMessage=document.getElementById("nomprofil-message");
    profileST=ST_UNCHANGED;

    dateField=document.getElementById("datenaissance");
    dateField.value="";
    dateMessage=document.getElementById("datenaissance-message");
    var date=new Date();
    var year=date.getFullYear();
    new JsDatePick( 
	{ useMode:2, 
	  target:"datenaissance", 
	  dateFormat:"%d-%m-%Y",
          selectedDate:{ // se place il y a 40 ans par défaut
	    year:year-40,
            month:date.getMonth()+1,
            day:date.getDate()
	  },
          yearsRange: new Array(year-100,year-10),
	});
  }


  // champs communs aux deux modes
  if (!upgradeProfile) {
    passwdField=document.getElementById("motdepasse");
    passwdField.value="";
    passwdField.addEventListener("change",checkPasswd);
    passwdMessage=document.getElementById("passwd-message");
    passwdST=ST_UNCHANGED;
    passwdTry=0;
  }

  initializeMap();
  latitudeField=document.getElementById("latitude");
  longitudeField=document.getElementById("longitude");
  latLngST=ST_UNCHANGED;
  gpsMessage=document.getElementById("gps-message");
  
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
  profileST=ST_BROKEN;
}

// teste si le profil existe déjà dans la BD
function profileExists(profile) {
  var id=ajax("dbtools.php?cmd=profileexists&profile="+profile);
  return id!=0 && (!upgradeProfile || id!=userId);
}

// vérifie la présence de l'adresse email de validation
function checkEmail() {
  emailST=ST_BROKEN;
  var email=this.value.trim();
  // vérifie qu'il semble valide
  if (email.search(/.+@.+\..+/)==-1) {
    emailMessage.innerHTML="Il faut entrer une adresse valide !";
    return;
  }
  // sauvegarde le résultat
  this.value=email;
  emailMessage.innerHTML="OK";
  emailST=ST_OK;
}

// vérifie la validité du nom de profil
// et effectue un léger nettoyage
function checkIdentifier() {
  profileST=ST_BROKEN;
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
function standardName(name) {
  // supprime les espaces inutiles
  name=name.replace(/\s(?=(\s|-))/g,"");
  name=name.replace(/-\s/g,"-");
  // met les majuscules
  function makeUpper(car) { return car.toLocaleUpperCase(); }
  name=name.replace(/^.|-.| ./g, makeUpper);
  return name;
}

function checkName() {
  var name=this.value.trim().toLocaleLowerCase();
  nameMessage.innerHTML=""; 
  // sauvegarde le résultat
  this.value=standardName(name);
  // calcul une proposition d'identifiant
  if (this.value.length!=0) nameToProfile();
}

function checkSurname() {
  var surname=this.value.trim().toLocaleLowerCase();
  surnameMessage.innerHTML=""; 
  // sauvegarde le résultat
  this.value=standardName(surname);
  // calcul une proposition d'identifiant
  if (this.value.length!=0) nameToProfile();
}

function submit() {
  if (checkForm()) document.forms["profileForm"].submit();
}

// vérifie si tout est OK avant l'enregistrement
function checkForm() {
  if (newProfile) { // mode nouveau compte
    var OK=true;
    if (!upgradeProfile && loginST!=ST_OK) {
      loginMessage.innerHTML="Le login n'est pas rempli correctement";
      OK=false;
    }
    if (!upgradeProfile && emailST!=ST_OK) {
      emailMessage.innerHTML="L'email n'est pas valide";
      OK=false;
    }
    if (!upgradeProfile && passwdST!=ST_OK) {
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
    if (dateField.value.length==0) {
      dateMessage.innerHTML="La date n'est pas valide";
      OK=false;
    }
    if (latLngST!=ST_OK) {
      gpsMessage.innerHTML="Le point GPS n'a pas été modifié!";
      OK=false;
    }
    else dateMessage.innerHTML="";
    return OK;
  } 
  else { // mode edition
    if (passwdST!=ST_OK) passwdField.value=""; // ne sera pas modifié
    if (latLngST!=ST_OK) {
      latitudeField.value=""; // ne sera pas modifié
      longitudeField.value=""; // ne sera pas modifié
    }
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
  loginST=ST_BROKEN;

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
  passwdST=ST_BROKEN;
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
