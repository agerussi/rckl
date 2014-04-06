<?php
// ensemble de fonctions destinées aux envois de mails 

require_once("dbconnect.php");

// retourne l'adresse mail d'un membre
// $id: id du membre
function getEmailAddress($id) {
  global $db;
  $query="SELECT email FROM membres WHERE id='{$id}'";
  $result=mysql_query($query,$db) or die("Erreur lors de la récupération de l'email: ".mysql_error());
  $row=mysql_fetch_array($result);
  return $row['email'];
}

// envoi d'un mail "automatique" RCKL
function sendAutoMail($email, $subject, $body) {
  if (empty($email)) return;
 
  $msg="** Ceci est un message envoyé automatiquement par le RCKL. Vous ne devriez pas y répondre directement **\r\n\r\n";

  $headers   = array();
  $headers[] = "MIME-Version: 1.0";
  $headers[] = "Content-type: text/plain; charset=utf-8";
  $headers[] = "From: RCKL <rckl@free.fr>";
  $headers[] = "Reply-To: Ne pas répondre! <noreply@free.fr>";
  $headers[] = "Subject: {$subject}";
  $headers[] = "X-Mailer: PHP/".phpversion();

  mail($email,$subject,$msg.$body,implode("\r\n",$headers));
}

?>
