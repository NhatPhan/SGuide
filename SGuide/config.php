<?php
if(!defined('INCLUDE_FILE')){
  include_once 'header.php';
  die("<div class='jumbotron vertical-center'><div class='container'>You may not access this file directly!</div></div>");
}
define("SERVERNAME","localhost");
define("USERNAME","root");
define("PASSWORD","");
define("DBNAME","traveller");
/* open a connection */
$con = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DBNAME);
?>