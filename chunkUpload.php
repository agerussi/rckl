<?php
// fonctions dédiées à la technique de l'upload par paquets.
// implémente les commandes upload, merge, delete

if (!isset($_REQUEST['cmd'])) {
  echo "NO CMD";
  return;
}

$cmd=$_REQUEST['cmd'];

switch ($cmd) {
case "upload":
  upload();
  break;
case "merge":
  merge();
  break;
case "delete":
  remove();
  break;
default:
  echo "CMD ".$cmd." NOT RECOGNIZED";
  break;
}

function upload() {
  if (!isset($_REQUEST["name"])) {
    echo "NO NAME FOR UPLOAD CMD";
    return;
  }
  $filename=$_REQUEST["name"];
  $inputHandler = fopen("php://input", "rb");
  $fout = fopen($filename, "w");

  while (!feof($inputHandler)) {
    $buffer=fread($inputHandler, 4*1024);
    fwrite($fout, $buffer);
  }

  fclose($inputHandler);
  fclose($fout);

  echo "OK";
}

function merge() {
  if (!isset($_REQUEST["name"])) {
    echo "NO NAME FOR MERGE CMD";
    return;
  }
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
}

function remove() {
  if (!isset($_REQUEST["name"])) {
    echo "NO NAME FOR DELETE CMD";
    return;
  }
  $name=$_REQUEST["name"];

  $i=1;
  while (file_exists($filename=$name.$i++)) unlink($filename);

  echo "OK";
}



