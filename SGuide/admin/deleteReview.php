<?php
require_once('Connections/testdb.php');
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }
    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
    switch ($theType) {
      case "text":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "long":
      case "int":
        $theValue = ($theValue != "") ? intval($theValue) : "NULL";
        break;
      case "double":
        $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
        break;
      case "date":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "defined":
        $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}
if ((isset($_GET['re_id'])) && ($_GET['re_id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM reviews WHERE re_id=%s", GetSQLValueString($_GET['re_id'], "int"));
  mysql_select_db($database_testdb, $testdb);
  $Result1 = mysql_query($deleteSQL, $testdb) or die(mysql_error());
  $deleteGoTo = "manageReviews.php";
  header("Location: manageReviews.php?delete=success");
  die();
}
?>