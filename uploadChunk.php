<?php 

$filename=$_REQUEST["chunkname"];
$inputHandler = fopen("php://input", "rb");
$fout = fopen($filename, "w+");

while (!feof($inputHandler)) {
  $buffer=fread($inputHandler, 4*1024);
  fwrite($fout, $buffer);
}

fclose($inputHandler);
fclose($fout);

echo $filename." OK";
?>
