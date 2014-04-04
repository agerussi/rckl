<?php
// ensemble de fonctions destinées aux envois de mails 

require_once("dbconnect.php");

// retourne l'adresse mail d'un membre
// $id: id du membre
function getEmailAddress($id) {
  global $db;
  $query="SELECT email FROM membres WHERE id='".$id."'";
  $result=mysql_query($query,$db) or die("Erreur lors de la récupération de l'email: ".mysql_error());
  $row=mysql_fetch_array($result);
  return $row['email'];
}

// envoi d'un mail "automatique" RCKL
function sendAutoMail($email, $subject, $body) {
  if (empty($email)) return;
  echo $email." ".$subject." ".$body;
 
  $msg="** Ceci est un message envoyé automatiquement par le RCKL. Inutile d'essayer d'y répondre directement **\r\n\r\n";

  $headers = ""; // TODO

  if (mail($email,$subject,$msg.$body,$headers)) 
    echo "Success!";
  else 
    echo "Failure!";
}

?>
