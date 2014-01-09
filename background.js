///////////////////////////////////////////////////////
///////// PROGRAMME PRINCIPAL /////////////////////////
///////////////////////////////////////////////////////

// classe SlideBox
// imgPath: chemin du répertoire contenant les images
function SlideBox(imgPath) {
  // vitesse du slide et du fade par défaut
  this.slideSpeed=5*1000;
  var fadeSpeed=50;

  // variables globales
  SlideBox.isIE = /*@cc_on!@*/false; // comprends rien mais ça à l'air de marcher!!
  var pathList=new Array();
  var divBack, divFront;
  var imgFront, imgBack;
  var divTimer;
  var divOpacity;
  var fadeTimer;
  var current;
  var useBack=true;
  var opacityValue=1;

  // récupère les noms de fichiers
  // ne tient pas encore compte de path !
  function getPaths() {
    var json;
    var xhr=new XMLHttpRequest();
      xhr.onreadystatechange=function() {
	if (this.readyState==this.DONE && this.status==200) json=JSON.parse(this.response);
      }
      xhr.open("GET", "background_fetchFiles.php?path="+imgPath, false); 
      xhr.send();
      return json;
  }

  // fonction qui donne à divFront les dimensions des images affichées
  // elle doit être appelée après l'évenement 'load' pour être sûr que 
  // le DOM soit à jour
  function whenDOMReady() {
    divFront.style.setProperty("width", divBack.offsetWidth+"px");
    divFront.style.setProperty("height", divBack.offsetHeight+"px");

    // abonnements 
    window.setInterval(changePicture,this.slideSpeed);

    // pour IE il faut abonner imgFront plutôt que divFront... mystère car ce n'est pas censé marcher
    // apparemment dans les éléments de premier plan ne masquent pas les clics aux éléments en arrière
    if (SlideBox.isIE) imgFront.addEventListener("mousedown", divClick);
    else divFront.addEventListener("mousedown", divClick);
  }

  // fonction appelée lors d'un clic sur l'image
  // masque l'image pour 15 secondes
  // puis la fait réapparaitre progressivement
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

  // fonction faisant réapparaitre les images progressivement
  function divFadeIn() {
    divOpacity+=0.02;
    divBack.style.opacity=divOpacity;
    if (divOpacity==1) clearInterval(divTimer);
  }

  // fonction gérant le changement d'image
  function changePicture() {
    if (useBack) fadeTimer=window.setInterval(fadeOut,fadeSpeed);
    else fadeTimer=window.setInterval(fadeIn,fadeSpeed);

    current=(current+1)%pathList.length;
    useBack=!useBack;
  }

  // fonction qui fait disparaître progressivement l'image de devant
  // (faisant apparaître celle de derrière)
  function fadeOut() {
    opacityValue-=0.02;
    imgFront.style.opacity=opacityValue;
    if (opacityValue>0) return;

    clearInterval(fadeTimer);
    // met en place la prochaine image (qui sera devant)
    imgFront.setAttribute("src",pathList[current]);
  }

  // fonction qui fait apparaître progressivement l'image de devant
  // (faisant disparaître celle de derrière)
  function fadeIn() {
    opacityValue+=0.02;
    imgFront.style.opacity=opacityValue;
    if (opacityValue<1) return;

    clearInterval(fadeTimer);
    // met en place la prochaine image (qui sera derrière)
    imgBack.setAttribute("src",pathList[current]);
  }

  // méthode publique pour démarrer le slide
  // laisse le temps d'ajuster des paramètres éventuels
  this.start=function() {
    window.addEventListener("load", whenDOMReady.call(this));
  }
 
  /////////////////////////
  // CORPS DU CONSTRUCTEUR 
  /////////////////////////
  // récupère la liste des fichiers images
  pathList=getPaths();

  // place les images de départ
  imgFront=document.getElementById("changing-picture-img-front");
  imgFront.setAttribute("src",pathList[0]);
  imgBack=document.getElementById("changing-picture-img-back");
  imgBack.setAttribute("src",pathList[1%pathList.length]);

  // «troisième» image
  current=2%pathList.length;

  // setup du div-front 
  divBack=document.getElementById("changing-picture-div-back");
  divFront=document.getElementById("changing-picture-div-front");
}

var slideBox1=new SlideBox("FONDS/AUTO");
slideBox1.start();
