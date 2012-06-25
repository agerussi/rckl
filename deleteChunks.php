<?php 

$name=$_REQUEST["name"];

$i=1;
while (file_exists($filename=$name.$i++)) {
  unlink($filename);
}

fclose($fout);
echo "OK";
?>
