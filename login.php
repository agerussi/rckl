<?php
//check that the user is calling the page from the login form and not accessing it directly
//and redirect back to the login form if necessary
if (!isset($_POST['login']) || !isset($_POST['password'])) {
  header( "Location: loginpage.php" );
}
//check that the form fields are not empty, and redirect back to the login page if they are
elseif (empty($_POST['login']) || empty($_POST['password'])) {
  header( "Location: loginpage.php" );
}

//convert the field values to simple variables

//add slashes to the username and md5() the password
$user = addslashes($_POST['login']);
$pass = md5($_POST['password']);

session_start();

//set the database connection variables

require("dbconnect.php");

$result=mysql_query("SELECT * FROM membres WHERE login='$user' AND motdepasse='$pass' AND actif<>0", $db);

$rowCheck = mysql_num_rows($result);
if($rowCheck == 1){ // exactly one result must have been returned
  $row = mysql_fetch_array($result);
  //start the session and register a variable
  session_start();
  $_SESSION['login']=$user; 
  $_SESSION['userid']=$row['id'];
  $_SESSION['realname']=$row['nom'];
  header("Location: news.php?menu");
}
else {
  session_destroy();
  header("Location: loginfailed.php?menu");
}
?> 
