<?php
// efface un fichier dans le chemin est précisé par l'argument path
session_start();

  if (!isset($_SESSION["userid"]) || !isset($_GET["path"])) header("Location: news.php");

  unlink($_GET["path"]);
?>
