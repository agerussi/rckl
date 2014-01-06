// paramètres
//////////////
// liste des fichiers (théoriquement préfabriquée par background.php)
var pathList=new Array("FONDS/AUTO/dsc_3839-reduced.jpg","FONDS/AUTO/DSC_7981-reduced.jpg","FONDS/AUTO/DSC_9356-reduced.jpg");
// intervalle entre les images (ms)
var INTERVAL=5*1000;
// vitesse de fade In/Out
var fadeSpeed=25;

// abonnement du div pour masquage du slide
var div=document.getElementById("changing-picture-div");
div.addEventListener("click", divClick);
// image à modifier
var imgFront=document.getElementById("changing-picture-img-front");
imgFront.setAttribute("src",pathList[0]);
var imgBack=document.getElementById("changing-picture-img-back");
imgBack.setAttribute("src",pathList[1%pathList.length]);
var fadeTimer;

// déclenchement du timer
window.setInterval(changePicture,INTERVAL);

/****************************************
  ********* FIN PROGRAMME PRINCIPAL *****
  ***************************************/

// masque le slide pour 30 secondes
var divTimer;
var divOpacity;
function divClick() {
  div.style.visibility="hidden";
  window.setTimeout(
      function(){
        div.style.opacity=divOpacity=0;
	div.style.visibility="visible";
        divTimer=window.setInterval(divFadeIn,2*fadeSpeed);
      },5*1000);
}

function divFadeIn() {
  divOpacity+=0.02;
  div.style.opacity=divOpacity;
  if (divOpacity==1) clearInterval(divTimer);
}

var current=2%pathList.length;
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

