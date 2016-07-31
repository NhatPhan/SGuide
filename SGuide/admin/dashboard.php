<?php
session_start();
require_once('Connections/testdb.php');
$MM_authorizedUsers  = "";
$MM_donotCheckaccess = "true";
// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False;
  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) {
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers  = Explode(",", $strUsers);
    $arrGroups = Explode(",", $strGroups);
    if (in_array($UserName, $arrUsers)) {
      $isValid = true;
    }
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) {
      $isValid = true;
    }
    if (($strUsers == "") && true) {
      $isValid = true;
    }
  }
  return $isValid;
}
$MM_restrictGoTo = "index.php?login=true";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar   = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?"))
    $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
    $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  die();
}
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
$colname_getUsername = "-1";
if (isset($_POST['ad_username'])) {
  $colname_getUsername = $_POST['ad_username'];
}
mysql_select_db($database_testdb, $testdb);
$query_getUsername = sprintf("SELECT ad_username FROM `admin` WHERE ad_username = %s", GetSQLValueString($colname_getUsername, "text"));
$getUsername = mysql_query($query_getUsername, $testdb) or die(mysql_error());
$row_getUsername        = mysql_fetch_assoc($getUsername);
$totalRows_getUsername  = mysql_num_rows($getUsername);
$maxRows_getLastReviews = 5;
$pageNum_getLastReviews = 0;
if (isset($_GET['pageNum_getLastReviews'])) {
  $pageNum_getLastReviews = $_GET['pageNum_getLastReviews'];
}
$startRow_getLastReviews = $pageNum_getLastReviews * $maxRows_getLastReviews;
mysql_select_db($database_testdb, $testdb);
$query_getLastReviews       = "SELECT re_id, re_username, SUBSTRING(re_datetime, 1, 10), SUBSTRING(re_description, 1, 60) FROM reviews ORDER BY re_id DESC";
$query_limit_getLastReviews = sprintf("%s LIMIT %d, %d", $query_getLastReviews, $startRow_getLastReviews, $maxRows_getLastReviews);
$getLastReviews = mysql_query($query_limit_getLastReviews, $testdb) or die(mysql_error());
$row_getLastReviews = mysql_fetch_assoc($getLastReviews);
if (isset($_GET['totalRows_getLastReviews'])) {
  $totalRows_getLastReviews = $_GET['totalRows_getLastReviews'];
} else {
  $all_getLastReviews       = mysql_query($query_getLastReviews);
  $totalRows_getLastReviews = mysql_num_rows($all_getLastReviews);
}
$totalPages_getLastReviews = ceil($totalRows_getLastReviews / $maxRows_getLastReviews) - 1;
$maxRows_getLastFacilities = 5;
$pageNum_getLastFacilities = 0;
if (isset($_GET['pageNum_getLastFacilities'])) {
  $pageNum_getLastFacilities = $_GET['pageNum_getLastFacilities'];
}
$startRow_getLastFacilities = $pageNum_getLastFacilities * $maxRows_getLastFacilities;
mysql_select_db($database_testdb, $testdb);
$query_getLastFacilities       = "SELECT fa_id, fa_name, SUBSTRING(fa_description, 1, 60) FROM facilities ORDER BY fa_id DESC";
$query_limit_getLastFacilities = sprintf("%s LIMIT %d, %d", $query_getLastFacilities, $startRow_getLastFacilities, $maxRows_getLastFacilities);
$getLastFacilities = mysql_query($query_limit_getLastFacilities, $testdb) or die(mysql_error());
$row_getLastFacilities = mysql_fetch_assoc($getLastFacilities);
if (isset($_GET['totalRows_getLastFacilities'])) {
  $totalRows_getLastFacilities = $_GET['totalRows_getLastFacilities'];
} else {
  $all_getLastFacilities       = mysql_query($query_getLastFacilities);
  $totalRows_getLastFacilities = mysql_num_rows($all_getLastFacilities);
}
$totalPages_getLastFacilities = ceil($totalRows_getLastFacilities / $maxRows_getLastFacilities) - 1;
?>
<html lang="en">
  <head>
    <title>SGuide Control Panel</title>
    <?php include_once 'header.php'; ?>
  </head>
  <body>
    <div id="wrapper">
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="border:0;background:#0A5A9C">
        <div class="navbar-header">
          <a class="navbar-brand" href="dashboard.php" style="font-weight:500"><i class="fa fa-cog"></i>&nbsp; SGuide Control Panel</a>
        </div>
        <div class="navbar-header pull-right"><a class ="navbar-brand" href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav side-nav">
            <li class="active">
              <a href="dashboard.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
              <a href="manageFacilities.php"><i class="fa fa-fw fa-edit"></i> Manage Facilities</a>
            </li>
            <li>
              <a href="manageReviews.php"><i class="fa fa-fw fa-edit"></i> Manage Reviews</a>
            </li>
            <li>
              <a href="logout.php"><i class="fa fa-fw fa-desktop"></i> Logout</a>
            </li>
          </ul>
        </div>
      </nav>
      <div id="page-wrapper" style="min-height:70vh">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <h1 class="page-header">
                <i class="fa fa-tachometer"></i> Dashboard
              </h1>
            </div>
          </div>
          <div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> Recent reviews</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-hover table-striped">
                  <tr>
                    <th width="10%" style="min-width:98px">Review ID</th>
                    <th width="20%">Reviewer</th>
                    <th width="12%" style="min-width:135px">Date of Review</th>
                    <th>Description</th>
                  </tr>
                  <?php if(!empty($row_getLastReviews)) { do { ?>
                  <tr>
                    <td>
                      <?php echo $row_getLastReviews['re_id']; ?>
                    </td>
                    <td>
                      <?php echo $row_getLastReviews['re_username']; ?>
                    </td>
                    <td>
                      <?php echo $row_getLastReviews['SUBSTRING(re_datetime, 1, 10)']; ?>   
                    </td>
                    <td><?php echo $row_getLastReviews['SUBSTRING(re_description, 1, 60)']; ?>...</td>
                  </tr>
                  <?php } while ($row_getLastReviews = mysql_fetch_assoc($getLastReviews)); } ?>                
                </table>
              </div>
            </div>
          </div>
          <div class="text-right">
            <a href="manageReviews.php" class="btn btn-primary">View All Reviews &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
          </div>
          <div>
            <div class="panel panel-default" style="margin-top:35px">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> Latest facilities added</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-hover table-striped">
                  <tr>
                    <th style="min-width:98px">Facility ID</th>
                    <th>Facility Name</th>
                    <th>Facility Description</th>
                  </tr>
                  <?php do { ?>
                  <tr>
                    <td width="10%">
                      <?php echo $row_getLastFacilities['fa_id']; ?> 
                    </td>
                    <td width="30%">
                      <?php echo $row_getLastFacilities['fa_name']; ?> 
                    </td>
                    <td>
                      <?php echo $row_getLastFacilities['SUBSTRING(fa_description, 1, 60)']; ?>..."</em> 
                    </td>
                  </tr>
                  <?php } while ($row_getLastFacilities = mysql_fetch_assoc($getLastFacilities)); ?>
                </table>
              </div>
            </div>
            <div class="text-right">
              <a href="manageFacilities.php" class="btn btn-primary">View All Facilities &nbsp;<i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php'; ?>
  </body>
</html>
<?php
mysql_free_result($getUsername);
mysql_free_result($getLastReviews);
mysql_free_result($getLastFacilities);
?>