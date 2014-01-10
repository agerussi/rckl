///////////////////////////////////////////////////////
///////// PROGRAMME PRINCIPAL /////////////////////////
///////////////////////////////////////////////////////

/* classe SlideBox
* paramètres:
   - imgPath: chemin du répertoire contenant les images
* attributs publics:
   - slideSpeed: vitesse de défilement du slide
   - fadeSpeed: vitesse de fade in/out
* méthodes publiques:
   - start: démarre le slide
   - changePicture: passage à l'image suivante
   - hideSlide(interval): masque le slide pour un certain temps 
*/ 
function SlideBox(imgPath) {
  ////////// ATTRIBUTS PUBLICS
  this.slideSpeed=5*1000;
  this.fadeSpeed=50;

  ////////// MÉTHODES PUBLIQUES
  // fonction gérant le changement d'image
  this.changePicture=function() {
    if (useBack) fadeTimer=window.setInterval(fadeOut,this.fadeSpeed);
    else fadeTimer=window.setInterval(fadeIn,this.fadeSpeed);

    current=(current+1)%pathList.length;
    useBack=!useBack;
  }

  // fonction appelée lors d'un clic sur l'image, ou manuellement
  // masque l'image le temps donné (ms)
  // puis la fait réapparaitre progressivement
  this.hideSlide=function(interval) {
    divBack.style.visibility="hidden";
    divFront.style.setProperty("pointer-events","none");
    var self=this;
    window.setTimeout(
	function(){
	  divFront.style.setProperty("pointer-events","auto");
	  divBack.style.opacity=divOpacity=0;
	  divBack.style.visibility="visible";
	  divTimer=window.setInterval(divFadeIn,2*self.fadeSpeed);
	},interval);
  }

  // pour démarrer le slide
  // laisse le temps d'ajuster des paramètres éventuels
  this.start=function() {
    var self=this;
    window.addEventListener("load", function(){self.whenDOMReady()});
  }

  //////////////////////////////////////////////////////////////////////////////// 
  ////////////// DÉFINITIONS INTERNES ////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////
  // variables privées
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
  this.whenDOMReady=function() {
    divFront.style.setProperty("width", divBack.offsetWidth+"px");
    divFront.style.setProperty("height", divBack.offsetHeight+"px");

    // abonnements 
    var self=this;
    window.setInterval(function() {self.changePicture()},this.slideSpeed);

    // pour IE il faut abonner imgFront plutôt que divFront... mystère car ce n'est pas censé marcher
    // apparemment dans IE les éléments de premier plan ne masquent pas les clics aux éléments en arrière
    if (SlideBox.isIE) imgFront.addEventListener("mousedown", function() {self.hideSlide(15*1000)});
    else divFront.addEventListener("mousedown", function() {self.hideSlide(15*1000)});
  }

  // fonction faisant réapparaitre les images progressivement
  function divFadeIn() {
    divOpacity+=0.02;
    divBack.style.opacity=divOpacity;
    if (divOpacity==1) clearInterval(divTimer);
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
