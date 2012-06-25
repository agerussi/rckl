<?php 

$name=$_REQUEST["name"];
$fout = fopen($name, "w+");

$i=1;
while (file_exists($filename=$name.$i++)) {
  $fin=fopen($filename,"rb");
  fwrite($fout, fread($fin,filesize($filename)));
  fclose($fin);
  unlink($filename);
}

fclose($fout);
echo $name;
?>
