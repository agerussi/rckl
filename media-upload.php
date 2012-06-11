<?php 
/* gestion du chargement en streaming d'un fichier 
 * un nom est choisi et renvoyé au client 
 * le fichier est stocké dans un répertoire temporaire en attendant la réception des autres infos 
 */ 

// TODO: bug: les fichiers temporaires ne sont pas créés dans "tmp" (problème de droit d'accès?)

$inputHandler = fopen("php://input", "rb");
$tmpfname = tempnam("tmp","media");
$fout = fopen($tmpfname, "w+");

// save data from the input stream
do {
    $buffer = fread($inputHandler, 4096);
    fwrite($fout, $buffer);
} while (strlen($buffer)>0);

fclose($inputHandler);
fclose($fout);
echo $tmpfname; 
?>
