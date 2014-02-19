
/* /////////////////// classe SlideBox
Quelques éléments de style peuvent être ajustés par CSS via les
classes SlideBox-<name>-div et SlideBox-<name>-img 
et l'élément SlideBox-<name>-div

* paramètres:
   - name: nom du SlideBox (utilisé pour le CSS)
   - imgPath: chemin du répertoire contenant les images
* attributs publics:
   - slideSpeed: vitesse de défilement du slide
   - fadeSpeed: vitesse de fade in/out
* méthodes publiques:
   - start: démarre le slide
   - changePicture: passage à l'image suivante
   - hideSlide(interval): masque le slide pour un certain temps 
*/ 
function SlideBox(name, imgPath) {
  ////////// ATTRIBUTS PUBLICS
  this.slideSpeed=7*1000;
  this.fadeSpeed=35;

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
    if (SlideBox.isIE) imgFront.addEventListener("mousedown", function() {self.hideSlide(20*1000)});
    else divFront.addEventListener("mousedown", function() {self.hideSlide(20*1000)});
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

  // crée les éléments HTML nécéssaires à une divBox
  // initialise imgFront, imgBack, divFront, divBack
  function buildHTML() {
    divBack=document.createElement("div");
    divBack.setAttribute("class", "SlideBox-"+name+"-div");
    divBack.setAttribute("id", "SlideBox-"+name+"-div");
    divBack.style.setProperty("z-index", -1);

    divFront=document.createElement("div");
    divFront.setAttribute("class", "SlideBox-"+name+"-div");
    divFront.setAttribute("id", "SlideBox-"+name+"-div-front"); // pour debug
    divFront.style.setProperty("z-index", 0);

    imgBack=document.createElement("img");
    imgBack.setAttribute("class", "SlideBox-"+name+"-img");
    imgBack.style.setProperty("z-index", -1);
    imgBack.style.setProperty("position", "absolute");
    imgBack.style.setProperty("top", "0pt");
    imgBack.style.setProperty("left", "0pt");
    divBack.appendChild(imgBack);

    imgFront=document.createElement("img");
    imgFront.setAttribute("class", "SlideBox-"+name+"-img");
    divBack.appendChild(imgFront);

    var body=document.getElementsByTagName("body")[0];
    body.appendChild(divBack);
    body.appendChild(divFront);
  }

  // mélange les éléments de tableau
  function shuffle(tableau) {
    var n=tableau.length;
    for (var i=0; i<n; i++) {
      var randomIndex=i+Math.floor(Math.random()*(n-i));
      var save=tableau[i];
      tableau[i]=tableau[randomIndex];
      tableau[randomIndex]=save;
    }
  }
  
  /////////////////////////
  // CORPS DU CONSTRUCTEUR 
  /////////////////////////
  // récupère la liste des fichiers images
  pathList=getPaths();
  shuffle(pathList);

  // construit les éléments HTML nécessaires à une SlideBox
  buildHTML();

  // place les images de départ
  imgFront.setAttribute("src",pathList[0]);
  imgBack.setAttribute("src",pathList[1%pathList.length]);

  // «troisième» image
  current=2%pathList.length;
}

