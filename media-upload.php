<?php 
/* gestion du chargement en streaming d'un fichier 
 * un nom est choisi et renvoyé au client 
 * le fichier est stocké dans un répertoire temporaire en attendant la réception des autres infos 
 */ 
// read contents from the input stream
$inputHandler = fopen('php://input', "r");
 
// save data from the input stream
$count=0;
while(true) {
    $buffer = fgets($inputHandler, 4096);
    if (strlen($buffer) == 0) {
        fclose($inputHandler);
        echo 'read '.$count.' 4096-bytes chunks';
        return true;
    }
    
    $count++; 
}
   
 
?>
