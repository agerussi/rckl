// constantes et variables globales */
var beeingUploaded=0; // nombre de médias en cours d'upload
var uploadLIMIT=3; // nombre maximal de connexions simultanées
var chunkSize=256*1024; // 256 KB
var MediaType = { // enumération pour le type de media (attention à garder synchro avec celui de helper.php !!)
  On: 1, Photo: 2, Video: 4, New: 8, Miniature: 16, Vimeo: 32
};
var mediaList = new Array(); // liste des médias
var IMGDB="IMGDB"; // chemin du répertoire d'images et vidéos

// gestion de l'ajout d'une vidéo de vimeo
function gestionAjoutVimeo() {
  // récupération du n° de la vidéo
  vimeoId=parseInt(document.getElementById("VimeoId").value);
  if (isNaN(vimeoId)) {
    alert("Numéro non valide !");
    return;
  }

  // récupère les informations sur la vidéo
  var vimeo=new Vimeo();
  if (vimeo.setId(vimeoId)) mediaList.push(vimeo); 
  else window.alert(erreur+"\nVérifiez le numéro.");
}

// efface un fichier du serveur
function fileDelete(path) {
  //alert("effacement du fichier: "+path);
  xhr= new XMLHttpRequest();
  xhr.open("GET","archives_fileDelete.php?path="+path,false); 
  xhr.send();
}

// teste s'il reste des fichiers en cours d'upload
// et affiche un message
function uploadFini() {
  if (beeingUploaded!=0) {
    var msg=(beeingUploaded==1) ? "Un fichier est " : beeingUploaded+" fichiers sont ";
    window.alert(msg+"en cours d'upload!");
    return false;
  }
  else return true;
}

// annule toutes les modifications effectuées
// supprime les fichiers uploadés "pour rien"
function gestionAnnulation() {
  if (!uploadFini()) return;
 
  // efface les médias fraîchement uploadés
  for (var i in mediaList) {
    if (mediaList[i].uploaded) mediaList[i].erase();
  }

  // si l'archive était nouvelle, on annule sa création, donc on l'efface de la BD
  if (isNewArchive) {
    xhr= new XMLHttpRequest();
    xhr.open("GET","archives_delete.php?id="+idArchive,false); 
    xhr.send();
  }

  window.back();
}

// vérification et préparation avant soumission de l'archive
// appelée par l'élément <form> quand la modification de l'archive est demandée
function validationArchive() { 
  // return false; // à décommenter pour empêcher les mises à jour intempestives 
  if (!uploadFini()) return false;
  // la date
  if (document.getElementById("valeurdate").value.length==0) {
    window.alert("La date n'est pas définie!");
    return false;
  }

  // récolte des participants dans listeparticipants
  var listexml="";
  var liste=document.getElementsByName("participant");
  for (var i=0; i<liste.length; i++) listexml += "<nom>"+htmlspecialchars(liste[i].firstChild.data.trim())+"</nom>";

  document.getElementById("listeparticipants").value = listexml;  

  // récolte les médias toujours vivants, détruit les autres
  var mediasXML="";
  for (var i in mediaList) {
    if (mediaList[i].isAlive()) mediasXML+=mediaList[i].toXML();
    else mediaList[i].erase();
  }

  document.getElementById("xmlmedias").value=mediasXML;

  return true;
}

// récupère l'extension (écrite en minuscules) à partir du nom de fichier
function extension(nom) { 
  var n=nom.lastIndexOf(".");
  return nom.substr(n+1).toLowerCase();
}

// efface les chunks restants, en cas d'échec
function deleteUploadedChunks(baseName) {
  var xhr=new XMLHttpRequest();
  xhr.open("POST", "chunkUpload.php?cmd=delete&name="+baseName, false);
  xhr.send();
  if (xhr.status!=200) throw("deleteUploadedChunks error: xhr.status="+xhr.status);
}

// fonction qui se charge d'assembler les chunks 
function mergeChunks(baseName) {
  var xhr=new XMLHttpRequest();
  xhr.open("POST", "chunkUpload.php?cmd=merge&name="+baseName, false);
  xhr.send();
  if (xhr.status!=200) throw("mergeChunks error: xhr.status="+xhr.status);
}

// gère tous les ajouts de fichiers à partir du chooser
// dispatche crée le Media suivant le type de fichier selectionné
function gestionAjoutFichiers(evt) { 
  var listeFichiers = evt.target.files; // FileList object
  var fichier;

  for (var i=0; fichier=listeFichiers[i]; i++) { // traitement individuel de chaque fichier
    if (fichier.type.match('image.*')) {
      var photo=new Photo();
      photo.upLoad(fichier);
      mediaList.push(photo);
      continue;
    }
    if (fichier.type.match('video.*')) { 
      var msg="Attention, vous avez sélectionné un fichier de type «vidéo», est-ce une erreur ?\nL'upload de fichiers vidéos est possible mais n'est souhaitée qu'à titre exceptionnel.\nLa procédure préconisée est de déposer votre vidéo sur YouTube ou Vimeo.\n\nVoulez-vous *vraiment* continuer ?";
      if (window.confirm(msg)) {
	var video=new Video();
	video.upLoad(fichier);
	mediaList.push(video);
      }
      continue;
    } 
    // arrivé ici, le fichier n'a pas été traité
    window.alert("Le fichier '"+fichier.name+"' n'est ni une photo, ni une vidéo - non traité");
  }
}

/////////////////////////////////////
//// classe Media : implémente l'essentiel de la représentation graphique
/////////////////////////////////////
function Media(commentaire,urlMiniature) {
  ////////////////////////////////////////// méthodes
  // remplace la miniature
  this.setMiniatureURL=function(path) {
    this.urlMiniature=path;
    document.getElementById("miniImg"+this.id).setAttribute("src",path+"?version="+Date());
  }

  // remplace le commentaire (décodé)
  this.setCommentaire=function(comment) {
    this.commentaire=comment;
    document.getElementById("miniImg"+this.id).setAttribute("title",this.commentaire);
  }

  // supprime entièrement le média (graphiquement)
  this.kill=function() {
    // suppression du HTML
    var div=document.getElementById("media"+this.id);
    div.parentNode.removeChild(div);
    // auto suppression de la mediaList
    // ainsi l'objet sera récolté (en théorie) par le garbage collector
    var i=0;
    while(mediaList[i].id!=this.id) i++;
    mediaList.splice(i,1);
  }

  // crée un identifiant «unique» (avec probabilité très grande)
  function createUniqueId() {
    var id="";
    for (var i=1; i<=10; i++) id+=String(Math.floor(Math.random()*10));
    return id;
  }

  // méthode appelée pour changer le statut
  this.changeStatus = function(img) {
    vivant=!vivant;
    if (vivant) {
      img.setAttribute("src","ICONS/b_drop.png");
      document.getElementById("miniImg"+this.id).style.opacity=1;
    }
    else {
      img.setAttribute("src","ICONS/b_add.png");
      document.getElementById("miniImg"+this.id).style.opacity=0.4;
    }
    //alert("vivant="+vivant);
  }

  // méthode appelée pour changer le commentaire par l'interface graphique
  this.changeCommentaire=function() {
    // copie le commentaire actuel dans la zone de saisie
    document.getElementById("inputCommentaire").value = this.commentaire;
    // abonnements des boutons de l'interface
    document.getElementById("boutonModifierCommentaire").addEventListener("click",modifierCommentaireCaller);
    document.getElementById("boutonAnnulerCommentaire").addEventListener("click",annulerCommentaireCaller);
    // affiche la zone de saisie
    document.getElementById("zoneSaisie").style.display = "inline";
    //alert("commentaire="+this.commentaire);
  }

  // récupère le commentaire de la boîte de saisie 
  this.enregistrerCommentaire=function(change) { 
    document.getElementById("boutonModifierCommentaire").removeEventListener("click",modifierCommentaireCaller);
    document.getElementById("boutonAnnulerCommentaire").removeEventListener("click",annulerCommentaireCaller);
    document.getElementById("zoneSaisie").style.display = "none";
    if (!change) return; 
    this.commentaire=document.getElementById("inputCommentaire").value;
    document.getElementById("miniImg"+this.id).setAttribute("title",this.commentaire);
  }

  // affichage du média
  this.display = function() {
    var div=document.createElement("div");
    div.setAttribute("id", "media"+this.id);
    div.setAttribute("class", "media");
    var urlMini=(this.urlMiniature==undefined) ? "ICONS/media-default-mini.jpg":this.urlMiniature;
    div.innerHTML=[
      '<img class="miniature" src="',urlMini,'" id="miniImg',this.id,'" title="',htmlspecialchars(this.commentaire),'"/>',
      '<img title="supprimer le média" src="ICONS/b_drop.png" id="supprimer',this.id,'"/>', 
      '<img title="éditer le commentaire" src="ICONS/b_edit.png" id="editerCommentaire',this.id,'"/>'
     ].join('');
    // insertion de l'image dans la liste
    var liste=document.getElementById("listeMedias");
    liste.appendChild(div); 
    // abonnements
    document.getElementById("supprimer"+this.id).addEventListener("click",function() {self.changeStatus(this)});
    document.getElementById("editerCommentaire"+this.id).addEventListener("click",function() {self.changeCommentaire()});
  }

  // retourne le statut du média
  this.isAlive=function() {
    return vivant;
  } 

  // rajoute l'icône "playable" au média
  // (utilisé par les vidéos)
  this.addPlayableIcon=function() {
    var img=document.createElement("img");
    img.setAttribute("src","ICONS/playable.png");
    img.setAttribute("class","playableIcon");
    var media=document.getElementById("media"+this.id);
    media.insertBefore(img,media.firstChild);
  }

  ////////////////////////////////////////// constructeur de l'objet
  //////////////////////////////// attributs publics
  // le commentaire du média
  this.commentaire=(commentaire==undefined) ? "":decode(commentaire);
  // n° d'identification de ce média
  this.id=createUniqueId();
  // url de la miniature
  this.urlMiniature=urlMiniature;

  //////////////////////////////// attributs privés
  // position dans la liste
  var position=mediaList.length; // dernier par défaut
  // statut du média
  var vivant=true;
  var self=this;
  var modifierCommentaireCaller=function() { self.enregistrerCommentaire(true) };
  var annulerCommentaireCaller=function() { self.enregistrerCommentaire(false) };

  // construction de la partie graphique et affichage
  this.display();
}

// méthode erase() par défaut: ne fait rien
// cette fonction est appelée si le média doit physiquement disparaître du serveur
Media.prototype.erase=function() {
}

///////////////////////////////////////////////
// classe FileMedia, spécialisation de Media
// implémente la capacité à uploader des fichiers
///////////////////////////////////////////////
FileMedia.prototype=Object.create(Media.prototype);
FileMedia.prototype.constructor=FileMedia;
function FileMedia(commentaire,urlMiniature) {
  // appel du constructeur de la classe mère
  Media.call(this,commentaire,urlMiniature);

  /////////////////////// méthodes
  // affichage de la progression d'un upload
  this.uploadChunksProgressHandler=function(evt) { 
    var position = evt.position || evt.loaded;
    var total = evt.totalSize || evt.total;
    var percentage = Math.round(100*position/total);

    var progressSpan=document.getElementById("progressBar"+this.id);     
    progressSpan.innerHTML="chunk n°"+this.numChunk+": "+percentage+"%"; 
  }
  
  // fonction qui se charge de gérer l'upload d'un fichier par petits morceaux
  // réassemblés sur le serveur 
  this.uploadByChunks=function(evt) {
    if (this.xhr.readyState==0 || (this.xhr.readyState==4 && this.xhr.status==200)) { // il faut commencer ou continuer l'upload
      if (this.mediaFile.size==0) { // il faut reconstituer le fichier à partir de ses bouts
	beeingUploaded--;
	document.getElementById("progressBar"+this.id).innerHTML="merging chunks...";
	try {
	  mergeChunks(this.tmpName);
          this.serverFileName=this.tmpName;
	  this.afterUpload();
	}
	catch (erreur) {
	  window.alert(this.mediaFile.name+": échec lors du réassemblage: "+erreur);
	  this.kill();
	  deleteUploadedChunks(this.tmpName);
	}
	return;
      }

      // sépare le fichier à uploader en chunk+rest
      var chunk=this.mediaFile.mozSlice(0,chunkSize);
      var rest=this.mediaFile.mozSlice(chunkSize);

      // déclenche l'upload de chunk
      this.xhr=new XMLHttpRequest(); 
      this.xhr.open("POST", "chunkUpload.php?cmd=upload&name="+this.tmpName+this.numChunk, true);

      // affichage de la progression de l'upload 
      var self=this;
      var eventSource = this.xhr.upload || this.xhr;
      eventSource.addEventListener("progress", function(evt) {self.uploadChunksProgressHandler(evt);}); 

      this.numChunk++;
      this.xhr.onreadystatechange = function(evt) {self.mediaFile=rest; self.uploadByChunks(evt);}; 
      this.xhr.send(chunk);
      return;
    }

    if (this.xhr.readyState==4 && this.xhr.status!=200) { // erreur dans l'envoi de chunk, on annule tout
      window.alert(this.mediaFile.name+": échec de l'upload d'un chunk: xhr.status="+this.xhr.status);
      this.kill();
      deleteUploadedChunks(this.tmpName);
      beeingUploaded--;
      return;
    }
  }

  ////////////// attributs
  this.uploaded=false;

} // fileMedia

// fonction appelée une fois le média en place sur le serveur
// serverFileName contient alors le nom du fichier sur le serveur
FileMedia.prototype.afterUpload=function() {
  // supprime la barre de progression
  var span=document.getElementById("progressBar"+this.id);
  span.parentNode.removeChild(span);
  this.uploaded=true;
}

// fonction qui se charge de l'upLoad d'un fichier
FileMedia.prototype.upLoad=function(fichier) {
  if (beeingUploaded==uploadLIMIT) { // réessaye dans 5 secondes !
    var self=this;
    window.setTimeout(function(){self.upLoad(fichier);},5*1000);
    return;
  }

  // initialise la barre de progression du chargement 
  var span=document.createElement("span");
  span.setAttribute("id", "progressBar"+this.id);
  span.innerHTML="uploading..."; 
  document.getElementById("media"+this.id).appendChild(span);

  // initialise et démarre l'upload
  beeingUploaded++;
  this.mediaFile=fichier;
  this.tmpName="tmp/file-"+this.id;
  this.xhr=new XMLHttpRequest(); 
  this.numChunk=1;
  this.uploadByChunks(null); 
}

///////////////////////////////////////////////
// classe Vimeo, spécialisation de Media.
// implémente la gestion particulière des vidéos vimeo par l'API VIMEO
///////////////////////////////////////////////
Vimeo.prototype=Object.create(Media.prototype);
Vimeo.prototype.constructor=Vimeo;
function Vimeo(commentaire,url, miniUrl) { // attributs @url et @miniurl de l'archive
  // constructeur de la classe mère
  Media.call(this,commentaire);
  this.addPlayableIcon();

  // fonction récupérant l'url, le commentaire et la miniature à partir de l'id d'une vidéo 
  // retourne true ou false si une erreur est survenue
  this.setId=function(vimeoId) { 
    // récupère les informations via l'API Simple
    var json;
    var xhr=new XMLHttpRequest();
      xhr.onreadystatechange=function() {
	if (this.readyState==this.DONE && this.status==200) json=JSON.parse(this.response);
      }
     xhr.open("GET", "http://vimeo.com/api/v2/video/"+vimeoId+".json", false); 
     xhr.send();
     if (typeof(json)=="undefined" || json[0].id!=vimeoId) return false;
     // json[0] contient les infos sur la vidéo
     
     // règle le commentaire
     this.setCommentaire(json[0].title+" (par "+json[0].user_name+")");
     _url=json[0].url;
     _miniUrl=json[0].thumbnail_small;
     this.setMiniatureURL(_miniUrl);
     return true;
  } 

  // donne le code XML du média tel qu'il est sauvegardé dans les archives du serveur
  this.toXML=function() {
    var xml="<vimeo ";
    xml+='url="'+_url+'" ';
    xml+='miniurl="'+_miniUrl+'"';
    if (this.commentaire.length>0) xml+=' commentaire="'+htmlspecialchars(encode(this.commentaire.trim()))+'"';
    xml+=" />";
    return xml;
  }

  /////////// construction de l'objet
  var _url=url;
  var _miniUrl=miniUrl;
  if (_miniUrl!=undefined) this.setMiniatureURL(_miniUrl);
}

///////////////////////////////////////////////
// classe Photo, spécialisation de FileMedia.
// implémente la gestion particulière des photos: miniatures automatiques, ...
///////////////////////////////////////////////
Photo.prototype=Object.create(FileMedia.prototype);
Photo.prototype.constructor=Photo;
function Photo(commentaire,fichierImage) { // fichierImage = attribut @fichier de l'XML 
  // appel du constructeur de la classe mère
  var urlMiniature;
  if (fichierImage!=undefined) urlMiniature=IMGDB+"/"+getMiniFileName(fichierImage);
  FileMedia.call(this,commentaire,urlMiniature);

  /////////////////////// méthodes
  // donne le code XML du média tel qu'il est sauvegardé dans les archives du serveur
  this.toXML=function() {
    var xml="<photo ";
    xml+='fichier="'+cible+'"';
    if (this.commentaire.length>0) xml+=' commentaire="'+htmlspecialchars(encode(this.commentaire.trim()))+'"';
    xml+=" />";
    return xml;
  }

  // efface du serveur le fichier photo et sa miniature 
  this.erase=function() {
    if (this.urlMiniature!=undefined) fileDelete(this.urlMiniature);
    if (cible!=undefined) fileDelete(IMGDB+"/"+cible);
  }

  // déduit le nom de la miniature à partir du nom du fichier
  function getMiniFileName(file) {
    var index=file.lastIndexOf(".");
    return file.slice(0,index)+"-mini.jpg";
  }

  // fonction appelée automatiquement une fois le média uploadé sur le serveur
  // serverFileName contient alors le nom du fichier sur le serveur
  // extensionFichier contient l'extension du fichier original
  this.afterUpload=function() {
    // fait le ménage dans la classe mère
    FileMedia.prototype.afterUpload.call(this);

    // renomme le fichier et fabrique la miniature
    var nouveauNom;
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function() {
      if (this.readyState==this.DONE && this.status==200) nouveauNom=this.response;
    };
    xhr.open("GET", "archives_tools.php?mode=photo&name="+this.serverFileName+"&ext="+extensionFichier+"&pre="+IMGDB+"/"+idArchive+"-photo-", false); 
    xhr.send();
    
    // le fichier devient à présent la cible officielle
    cible=nouveauNom;  
    // remplace la miniature provisoire par la vraie miniature
    this.setMiniatureURL(IMGDB+"/"+getMiniFileName(cible));
  } 

  // prise en charge d'un fichier photo à uploader
  this.upLoad=function(fichier) {
    // récupère l'extension pour le renommage final
    extensionFichier=extension(fichier.name);
    // upload du fichier, suite dans afterUpload()
    FileMedia.prototype.upLoad.call(this,fichier);
  }
 
  //////////////// construction de l'objet
  var cible=fichierImage;
  var extensionFichier;
}

///////////////////////////////////////////////
// classe Video, spécialisation de FileMedia.
// implémente la gestion particulière des vidéos: miniatures sélectionnables, ...
///////////////////////////////////////////////
Video.prototype=Object.create(FileMedia.prototype);
Video.prototype.constructor=Video;
function Video(commentaire,fichierImage) { // fichierImage = attribut @fichier de l'XML 
  // appel du constructeur de la classe mère
  var urlMiniature;
  if (fichierImage!=undefined) urlMiniature=IMGDB+"/"+getMiniFileName(fichierImage);
  FileMedia.call(this,commentaire,urlMiniature);
  this.addPlayableIcon();

  /////////////////////// méthodes
  // donne le code XML du média tel qu'il est sauvegardé dans les archives du serveur
  this.toXML=function() {
    var xml="<video ";
    xml+='fichier="'+cible+'"';
    if (this.commentaire.length>0) xml+=' commentaire="'+htmlspecialchars(encode(this.commentaire.trim()))+'"';
    xml+=" />";
    return xml;
  }

  // efface du serveur le fichier vidéo et sa miniature 
  this.erase=function() {
    fileDelete(this.urlMiniature);
    if (cible!=undefined) fileDelete(IMGDB+"/"+cible);
  }

  // déduit le nom de la miniature à partir du nom du fichier
  function getMiniFileName(file) {
    var index=file.lastIndexOf(".");
    return file.slice(0,index)+"-mini.jpg";
  }

  // fonction appelée automatiquement une fois le média uploadé sur le serveur
  // serverFileName contient alors le nom du fichier sur le serveur
  // extensionFichier contient l'extension du fichier original
  this.afterUpload=function() {
    // fait le ménage dans la classe mère
    FileMedia.prototype.afterUpload.call(this);

    // affiche la miniature provisoire
    document.getElementById("miniImg"+this.id).setAttribute("src","ICONS/video-default-mini.jpg");

    // affichage du gadget de sélection de la miniature
    var img=document.createElement("img");
    img.setAttribute("id", "miniatureSelect"+this.id);
    img.setAttribute("src", "ICONS/insert_image.png");
    img.setAttribute("title", "choix une miniature pour la vidéo");
    document.getElementById("media"+this.id).appendChild(img);
    var self=this;
    document.getElementById("miniatureSelect"+this.id).addEventListener("click", function(){self.chooseMiniature();});

    // renomme le fichier 
    var nouveauNom;
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function() {
      if (this.readyState==this.DONE && this.status==200) nouveauNom=this.response;
    };
    xhr.open("GET", "archives_tools.php?mode=video&name="+this.serverFileName+"&ext="+extensionFichier+"&pre="+IMGDB+"/"+idArchive+"-"+extensionFichier+"-video-", false); 
    // remarque: le motif "ext-video" est nécessaire pour éviter que deux vidéos de type différent recoivent le même numéro et donc la même miniature !
    xhr.send();
    
    // le fichier devient à présent la cible officielle
    cible=nouveauNom;  
  } 

  // prise en charge d'un fichier photo à uploader
  this.upLoad=function(fichier) {
    // récupère l'extension pour le renommage final
    extensionFichier=extension(fichier.name);
    // upload du fichier, suite dans afterUpload()
    FileMedia.prototype.upLoad.call(this,fichier);
  }

  // fonction qui se charge de la sélection d'une miniature 
  this.chooseMiniature=function() {
    // déclenche artificiellement le input
    var input=document.getElementById("ajoutMiniature");
    input.addEventListener("change", gestionAjoutMiniatureCaller, false);
    input.click(); // déclenche le input
    // on récupère le fil dans gestionAjoutMiniature si l'utilisateur a sélectionné un fichier
  } 

  this.gestionAjoutMiniature=function(fichier) {
    document.getElementById("ajoutMiniature").removeEventListener("change", gestionAjoutMiniatureCaller, false);

    // teste si le fichier est acceptable 
    if (!fichier.type.match('image.jpeg')) {
      window.alert("Le fichier "+fichier.name+" n'est pas un fichier jpeg");
      return;
    }
    if (fichier.size>15*1024)  {  // 15 KB MAX
      window.alert("Le fichier "+fichier.name+" est trop gros pour une miniature (15 KB maximum).");
      return;
    }
    // upload du fichier (en écrasant l'ancien s'il y en avait déjà un autre)
    var xhr=new XMLHttpRequest(); 
    urlMiniature=IMGDB+"/"+getMiniFileName(cible);
    xhr.open("POST", "chunkUpload.php?cmd=upload&name="+urlMiniature, false);
    var result;
    xhr.onreadystatechange=function() {
      if (this.readyState==this.DONE && this.status==200) result=this.response;
    }
    xhr.send(fichier); // synchrone
    if (result!="OK") {
      alert("L'upload de la miniature a échoué !");
      return;
    }
    // déclare la nouvelle miniature
    this.setMiniatureURL(urlMiniature);
  }

  //////////////// construction de l'objet
  var self=this;
  var gestionAjoutMiniatureCaller=function(evt) {
    self.gestionAjoutMiniature(evt.target.files[0]);
  };
  var cible=fichierImage;
  var extensionFichier;
}

// main() est appelée lorsque la page est chargée
// les variables suivantes sont définies:
//   isNewArchive = booléen qui dit si l'archive a déjà été éditée ou non
//   idArchive = id de l'archive en cours d'édition 
//   suggestions = tableau donnant la liste des membres pour formuler les suggestions
window.addEventListener("load",main);
function main() {
  initGestionDate();
  initGestionParticipants();
  // affiche les médias chargés
  createMedias(); // cette fonction est écrite par archives_edit.xsl
  // gestion du bouton d'ajout de fichiers 
  document.getElementById("ajoutFichiers").addEventListener("change", gestionAjoutFichiers, false);
  // ajout d'un vidéo Vimeo
  document.getElementById("ajouterVimeo").addEventListener("click", gestionAjoutVimeo, false);
  // annulation des modifs
  document.getElementById("cancel").addEventListener("click", gestionAnnulation, false);
}

function initGestionDate() { // règle les paramètres du chooser de date
  new JsDatePick({
	  useMode:2,
	  target:"valeurdate",
	  dateFormat:"%d-%m-%Y"
  });
}

function initGestionParticipants() { // déclenche les annulations et l'ajout
  // ajout
  document.getElementById("ajouterparticipant").addEventListener("click", ajouterParticipant);
  document.getElementById("nouveauparticipant").addEventListener("keyup", suggestionParticipant);
  // annulations
  var listeSupprimer = document.getElementsByName("supprimerparticipant");
  var i=0;
  while (listeSupprimer[i]) listeSupprimer[i++].addEventListener("click", supprimerParticipant);
}

function supprimerParticipant() { // supprime un participant de la liste
  var span=this.parentNode;
  span.parentNode.removeChild(span);
}

function ajouterParticipant() { // ajoute un participant de la liste
 // récupère le nom
 var input=document.getElementById("nouveauparticipant");
 var nom=input.value;
 if (nom.length==0) return;
 input.value=""; // efface le nom
 effaceSuggestions();

 // crée le nom
 var span=document.createElement("span");
 span.setAttribute("class","participant");
 span.setAttribute("name","participant");
 var croix=document.createElement("img");
 croix.src="ICONS/b_drop.png";
 croix.title="supprimer ce participant";
 croix.addEventListener("click", supprimerParticipant);

 span.appendChild(document.createTextNode(nom));
 span.appendChild(croix);
 document.getElementById("ligneparticipants").appendChild(span);
 //this.parentNode.insertBefore(span,input);
}

function suggestionParticipant() { // affiche des suggestions sélectionnables à partir du nom
 var nom=this.value.toLowerCase();
 effaceSuggestions();
 if (nom.length==0) return;

 // parcours le tableau des noms à la recherche de suggestions
 for (var i=0; i<suggestions.length; i++) {
   var sugg=suggestions[i].toLowerCase();
   if (sugg.indexOf(nom)==0) ajouteSuggestion(suggestions[i]);
 }
}

function ajouteSuggestion(nom) { // ajoute le nom comme suggestion
 var affichage=document.getElementById("suggestions");
 
 var suggestion=document.createElement("span");
 suggestion.setAttribute("name","suggestionParticipant");
 suggestion.setAttribute("class","suggestionParticipant");
 suggestion.appendChild(document.createTextNode(nom));
 suggestion.addEventListener("click",ecritSuggestion);
 affichage.appendChild(suggestion);
}

function effaceSuggestions() { // clair !
 var liste=document.getElementsByName("suggestionParticipant");
 for (var i=liste.length-1; i>=0; i--) liste[i].parentNode.removeChild(liste[i]);
}

function ecritSuggestion() { // réagit lorsqu'on a cliqué sur une suggestion
  document.getElementById("nouveauparticipant").value = this.innerHTML;
}

function htmlspecialchars(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

function encode(text) {
  return text
      .replace(/"/g, "[dq]");
}
   
function decode(text) {
  return text
      .replace(/\[dq\]/g, '"');
}

//////////////////////////////////////////////////////
///////////// VIEILLES FONCTIONS À REMISER ///////////
// gère le bouton choixMiniature.
// se contente de déclencher le input
function choisirMiniature() {
  var input=this.nextSibling.nextSibling;
  input.addEventListener("change", makeGestionAjoutMiniature(this), false);
  input.click(); // déclenche le input
  // on récupère le fil dans gestionAjoutMiniature si l'utilisateur a sélectionné un fichier
}

// chargement d'une miniature + affichage
// se contente de déclencher la lecture asynchrone du fichier
function makeGestionAjoutMiniature(img) { // img est l'image sur laquelle on a cliqué 
return function(evt) { 
  // récupération du fichier
  var fichier = evt.target.files[0]; // objet File 

  // teste si le fichier est acceptable 
  if (!fichier.type.match('image.*')) {
    window.alert("Le fichier "+fichier.name+" n'est pas un fichier image");
    return;
  }
  if (fichier.size>10*1024)  {  // 10 KB MAX
    window.alert("Le fichier "+fichier.name+" est trop gros pour une miniature!");
    return;
  }
  // TODO: le champ 'value' de l'input n'est pas mis à zéro lorsque le fichier est inacceptable, donc 
  // en théorie il sera uploadé et traité... 
  // mais en pratique l'utilisateur va re-sélectionner un autre fichier miniature.

  // signale que la miniature a changé
  var typeMedia=domMove(img,"PPnnccn");
  typeMedia.value = typeMedia.value|MediaType.Miniature;

  // lecture du fichier pour affichage 
  var reader=new FileReader();
  reader.onload = makeAffichageMiniature(img);
  reader.readAsDataURL(fichier); // lecture asynchrone => atterri dans AffichageMiniature
}}

// se déplace dans l'arbre DOM et renvoie le noeud correspondant
// p=previousSibling, P=parentNode, n=nextSibling, c=child
function domMove(noeud, deplacements) { 
  for (var i=0; i<deplacements.length; i++) {
    switch (deplacements.charAt(i)) {
      case "p": noeud=noeud.previousSibling; break;
      case "P": noeud=noeud.parentNode; break;
      case "n": noeud=noeud.nextSibling; break;
      case "c": noeud=noeud.firstChild; break;
    }
  }
  return noeud;
}

function makeAffichageMiniature(img) {
return function(evt) { // evt.target.result contient l'URL 
    var miniature=domMove(img,"p");
    miniature.setAttribute("src",evt.target.result);
}}


// récupère la partie "nom" dans une chaine de type "ext/nom"
function nomDuFichier(nom) {
  var n=nom.indexOf("/");
  return nom.substr(n+1);
}

// media=le fichier à uploader
// mediaNum=le numéro sous lequel le media est enregistré (permet de retrouver les labels)
// obtient un nom pour le media puis découpe le media en packet qui seront reconstitués sur le serveur à la fin
function uploadAsynchroneByChunks(mediaFile, mediaNum) { 
  if (beeingUploaded==uploadLIMIT) { // il faut retarder l'upload
    setTimeout(function(){uploadAsynchroneByChunks(mediaFile,mediaNum);},5*1000);
    return;
  }

  var fileName=getMediaName();
 
  beeingUploaded++;
  var xhr=new XMLHttpRequest(); 
  makeUploadByChunk(xhr,mediaFile,mediaNum,fileName,1)(null);
}

function makeUploadByChunk(xhr,mediaFile, mediaNum, serverFileName, numChunk) {
return function(evt) {
  if (xhr.readyState==0 || (xhr.readyState==4 && xhr.status==200)) { // il faut commencer ou continuer l'upload
    if (mediaFile.size==0) { // il faut reconstituer le fichier à partir de ses bouts
      var affichage=document.getElementById("progresMedia"+mediaNum);     
      affichage.innerHTML="merging chunks..."
      if (mergeChunks(serverFileName)) { // succès
        var inputNomMedia=document.getElementById("nomMedia"+mediaNum);
        inputNomMedia.value+=serverFileName;
        affichage.innerHTML="upload OK";
      }
      else { // échec
	window.alert(mediaFile.name+": échec lors du réassemblage: xhr.status="+newxhr.status);
        supprimeMedia(mediaNum);
        deleteUploadedChunks(serverFileName);
      }
      beeingUploaded--;
      return;
    }

    var chunk=mediaFile.mozSlice(0,chunkSize);
    var rest=mediaFile.mozSlice(chunkSize);

    // déclenche l'upload de chunk
    var newxhr=new XMLHttpRequest();
    //newxhr.open("POST", "uploadChunk.php?chunkname="+serverFileName+numChunk, true);
    newxhr.open("POST", "chunkUpload.php?cmd=upload&name="+serverFileName+numChunk, true);

    // affichage de la progression de l'upload 
    var eventSource = newxhr.upload || newxhr;
    eventSource.addEventListener("progress", makeUploadChunkProgressHandler(mediaNum,numChunk)); 

    newxhr.onreadystatechange = makeUploadByChunk(newxhr,rest,mediaNum,serverFileName,numChunk+1); 
    newxhr.send(chunk);
    return;
  }

  if (xhr.readyState==4 && xhr.status!=200) { // erreur dans l'envoi de chunk, on annule tout
    window.alert(mediaFile.name+": échec de l'upload d'un chunk: xhr.status="+xhr.status);
    supprimeMedia(mediaNum);
    deleteUploadedChunks(serverFileName);
    beeingUploaded--;
    return;
  }
}}

// affichage de la progression d'un upload
function makeUploadChunkProgressHandler(mediaNum,chunkNum) { // int, int
return function(evt) {
  var position = evt.position || evt.loaded;
  var total = evt.totalSize || evt.total;
  var percentage = Math.round(100*position/total);

  var progressSpan=document.getElementById("progresMedia"+mediaNum);     
  progressSpan.innerHTML="chunk n°"+chunkNum+": "+percentage+"%"; 
}}

var filesToProcess=0;
var numeroMedia=1;

// ajout d'une vidéo
// on utilise la miniature par défaut et on lance l'upload du fichier
function gestionAjoutVideo(fichier) { 
  // création d'une nouvelle vidéo
  var table=document.createElement("table");
  table.setAttribute("id", "table"+numeroMedia);
  table.setAttribute("class", "mediaTable");
  table.innerHTML=[
    '<tr><td>',
    '<img src="ICONS/video-default-mini.jpg" height="85px" name="miniatureVideo" />',
    '<img title="choisir une miniature" src="ICONS/insert_image.png" name="choisirminiature"/>',
    '<input type="hidden" name="MAX_FILE_SIZE" value="10240" />',
    '<input type="file" style="display:none" name="ajoutMiniature"/>',
    '</td></tr><tr><td>', 
    '<img title="supprimer la vidéo" src="ICONS/b_drop.png" name="supprimervideo"/>', 
    '<input type="hidden" name="typeMedia" value="',MediaType.On|MediaType.Video|MediaType.New,'"/>',
    '<img title="éditer le commentaire" src="ICONS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" name="commentaireMedia" value=""/>',
    '<input type="hidden" id="nomMedia',numeroMedia,'" name="nomMedia" value="',extension(fichier.name),'/"/>',
    '<span id="progresMedia',numeroMedia,'">chargement...</span>',
    '</td></tr>'
   ].join('');
  // insertion de l'image dans la liste
  var input=document.getElementById("listeVideos");
  input.insertBefore(table,null); 
  abonnementsVideos(); // abonnements aux diverses fonctions
  
  // démarre le chargement du fichier
  uploadAsynchroneByChunks(fichier,numeroMedia++);
}

function makeGestionAjoutImage(fichier) { // renvoie la fonction qui va s'occuper du rajout de l'image lorsqu'elle sera chargée
return function(evt) { // ajout d'une image: evt.target.result contient l'URL
  // création d'un nouveau média
  var table=document.createElement("table");
  table.setAttribute("id", "table"+numeroMedia);
  table.setAttribute("class", "mediaTable");
  table.innerHTML=[
    '<tr><td>',
    '<img src="', evt.target.result, '" height="85px" name="photo" />',
    '</td></tr><tr><td>',
    '<img title="supprimer la photo" src="ICONS/b_drop.png" name="supprimerphoto"/>', 
    '<input type="hidden" name="typeMedia" value="',MediaType.On|MediaType.Photo|MediaType.New,'"/>',
    '<img title="éditer le commentaire" src="ICONS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" name="commentaireMedia" value=""/>',
    '<input type="hidden" id="nomMedia',numeroMedia,'" name="nomMedia" value="jpg/"/>',
    '<span id="progresMedia',numeroMedia,'">chargement...</span>'
   ].join('');
  // insertion de l'image dans la liste
  var input=document.getElementById("listePhotos");
  input.insertBefore(table,null); 
  
  // démarre le chargement du fichier
  uploadAsynchroneByChunks(fichier,numeroMedia++);

  // gestion des abonnements pour les nouveaux fichiers
  filesToProcess--;
  if (filesToProcess==0) abonnementsPhotos(); // pour l'ensemble des nouvelles images
}}


function supprimerMedia() { // gère la suppression / réhabilitation de photos ou vidéos
  var imgMedia = domMove(this,"PPPccc");
  var input = this.nextSibling;
  var type=(input.value&MediaType.Photo) ? "la photo":"la vidéo";
  if (input.value&MediaType.On) {
    input.value = input.value&(~MediaType.On);
    this.setAttribute("src","ICONS/b_add.png");
    this.setAttribute("title","rajouter "+type);
    imgMedia.style.opacity = "0.5";
  } else {
    input.value = input.value|MediaType.On;
    this.setAttribute("src","ICONS/b_drop.png");
    this.setAttribute("title","supprimer "+type);
    imgMedia.style.opacity = "1";
  } 
  //window.alert("fin supprimerMedia");
}
  
// TODO: buggy, à remanier: le problème est apparemment que lorsque l'on clone, on perd les infos
// sur les abonnements (et peut-être même sur d'autres attributs ?) 
var switchDeplacement=false, tableauSource, tableauSourceClone;
function deplacementPhoto() { // gestion du déplacement d'une photo (1er ou 2e clic)
 // récupère le noeud "tableau" correspondant à l'image 
 var tableau=this.parentNode.parentNode.parentNode;
 if (switchDeplacement) {
  if (tableau==tableauSource) {
    this.style.border = "0px";
  } else {
    var tableauDestClone=tableau.cloneNode(true); 
    tableauSource.parentNode.replaceChild(tableauDestClone,tableauSource);
    tableau.parentNode.replaceChild(tableauSourceClone,tableau);
    abonnementsPhotos();
  } 
 } else {
   tableauSource=tableau;
   tableauSourceClone = tableauSource.cloneNode(true);
   this.style.border = "5px solid black";
 }
 switchDeplacement = !switchDeplacement;
}

