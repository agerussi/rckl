
// paramètres
//////////////
// intervalle entre les images (ms)
var INTERVAL=5*1000;
// vitesse de fade In/Out
var fadeSpeed=25;

// variables globales
var pathList=new Array();
var divBack, divFront;
var imgFront, imgBack;

// récupère les noms de fichiers
var xhr=new XMLHttpRequest();
  xhr.onreadystatechange=function() {
    if (this.readyState==this.DONE && this.status==200) pathList=JSON.parse(this.response);
  }
  xhr.open("POST", "background_fetchFiles.php", false); // asynchrone pour ne pas avoir d'interruptions
  xhr.send();
//alert(pathList);

// prochaine image
current=2%pathList.length;

// place les images de départ
imgFront=document.getElementById("changing-picture-img-front");
imgFront.setAttribute("src",pathList[0]);
imgBack=document.getElementById("changing-picture-img-back");
imgBack.setAttribute("src",pathList[1%pathList.length]);

// setup du div-front 
divBack=document.getElementById("changing-picture-div-back");
divFront=document.getElementById("changing-picture-div-front");

window.addEventListener("load", setDivFront);
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

function setDivFront() {
  divFront.style.setProperty("width", divBack.offsetWidth+"px");
  divFront.style.setProperty("height", divBack.offsetHeight+"px");

  // abonnements 
  window.setInterval(changePicture,INTERVAL);
  divFront.addEventListener("click", divClick);
}

// masque le slide pour 15 secondes
var divTimer;
var divOpacity;
function divClick() {
  divBack.style.visibility="hidden";
  divFront.style.setProperty("pointer-events","none");
  window.setTimeout(
      function(){
	divFront.style.setProperty("pointer-events","auto");
        divBack.style.opacity=divOpacity=0;
	divBack.style.visibility="visible";
        divTimer=window.setInterval(divFadeIn,2*fadeSpeed);
      },15*1000);
}

function divFadeIn() {
  divOpacity+=0.02;
  divBack.style.opacity=divOpacity;
  if (divOpacity==1) clearInterval(divTimer);
}

var fadeTimer;
var current;
var useBack=true;
function changePicture() {
  if (useBack) fadeTimer=window.setInterval(fadeOut,fadeSpeed);
  else fadeTimer=window.setInterval(fadeIn,fadeSpeed);

  current=(current+1)%pathList.length;
  useBack=!useBack;
}

var opacityValue=1;
function fadeOut() {
  opacityValue-=0.02;
  imgFront.style.opacity=opacityValue;
  if (opacityValue>0) return;

  imgFront.setAttribute("src",pathList[current]);
  clearInterval(fadeTimer);
}

function fadeIn() {
  opacityValue+=0.02;
  imgFront.style.opacity=opacityValue;
  if (opacityValue<1) return;

  imgBack.setAttribute("src",pathList[current]);
  clearInterval(fadeTimer);
}

