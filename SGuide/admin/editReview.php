<?php
session_start();
if(!isset($_GET["re_id"]) || empty($_GET["re_id"])) {
	header("Location: manageReviews.php");
	die();
}
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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateReview")) {
  $updateSQL = sprintf("UPDATE reviews SET re_description=%s, re_flag=%s WHERE re_id=%s", GetSQLValueString($_POST['description'], "text"), GetSQLValueString($_POST['flag'], "int"), GetSQLValueString($_POST['re_id'], "int"));
  mysql_select_db($database_testdb, $testdb);
  $Result1 = mysql_query($updateSQL, $testdb) or die(mysql_error());
  header("Location: manageReviews.php?edit=success");
  die();
}
$colname_getUsername = "-1";
if (isset($_POST['ad_username'])) {
  $colname_getUsername = $_POST['ad_username'];
}
mysql_select_db($database_testdb, $testdb);
$query_getUsername = sprintf("SELECT ad_username FROM `admin` WHERE ad_username = %s", GetSQLValueString($colname_getUsername, "text"));
$getUsername = mysql_query($query_getUsername, $testdb) or die(mysql_error());
$row_getUsername       = mysql_fetch_assoc($getUsername);
$totalRows_getUsername = mysql_num_rows($getUsername);
$colname_getReviewInfo = "-1";
if (isset($_GET['re_id'])) {
  $colname_getReviewInfo = $_GET['re_id'];
}
mysql_select_db($database_testdb, $testdb);
$query_getReviewInfo = sprintf("SELECT * FROM reviews WHERE re_id = %s", GetSQLValueString($colname_getReviewInfo, "int"));
$getReviewInfo = mysql_query($query_getReviewInfo, $testdb) or die(mysql_error());
$row_getReviewInfo       = mysql_fetch_assoc($getReviewInfo);
$totalRows_getReviewInfo = mysql_num_rows($getReviewInfo);
?>
<html lang="en">
  <head>
    <title>Edit Review :: SGuide Control Panel</title>
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
            <li>
              <a href="dashboard.php"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
            </li>
            <li>
              <a href="manageFacilities.php"><i class="fa fa-fw fa-edit"></i> Manage Facilities</a>
            </li>
            <li class="active">
              <a href="manageReviews.php"><i class="fa fa-fw fa-edit"></i> Manage Reviews</a>
            </li>
            <li>
              <a href="logout.php"><i class="fa fa-fw fa-desktop"></i> Logout</a>
            </li>
          </ul>
        </div>
      </nav>
      <div id="page-wrapper">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <ol class="breadcrumb">
                <li>
                  <a href="dashboard.php"><i class="fa fa-dashboard"></i>&nbsp; Dashboard</a>
                </li>
                <li>
                  <a href="manageReviews.php"><i class="fa fa-wrench"></i>&nbsp; Manage Reviews</a>
                </li>
                <li class="active">
                  <i class="fa fa-wrench"></i>&nbsp; Edit Review
                </li>
              </ol>
              <h1 class="page-header" style="margin-bottom:0;border:0">Edit Review</h1>
            </div>
          </div>
          <div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> This review was submitted by: <?php echo $row_getReviewInfo['re_username']; ?> </h3>
              </div>
              <form action="<?php echo $editFormAction; ?>" method="POST" name="updateReview">
                <input type="hidden" name="re_id" value="<?php echo $row_getReviewInfo['re_id']; ?>"> 
                <table class="table table-bordered" style="border:0">
                <tr>
                  <td style="text-align:right;font-weight:bold;border-left:0;width:10%;min-width:150px">Reviewer</td>
                  <td style="border-right:0">
                    <?php echo $row_getReviewInfo['re_username']; ?>
                  </td>
                </tr>
                <tr>
                  <td style="text-align:right;font-weight:bold;border-left:0">Review</td>
                  <td style="border-right:0"><textarea class="form-control" rows="3" name="description" maxlength="1000"><?php echo $row_getReviewInfo['re_description']; ?></textarea></td>
                </tr>
                <tr>
                  <td style="text-align:right;font-weight:bold;border-left:0">Status</td>
                  <td style="border-right:0"><?php if( $row_getReviewInfo['re_flag'] == '1'){print "<span style='color:red'>Marked as Spam</span>";}else{print "OK";} ?></td>
                </tr>
                <?php if( $row_getReviewInfo['re_flag'] == '1'){ ?>
                <tr>
                  <td style="text-align:right;font-weight:bold;border-left:0">Modify Status</td>
                  <td style="border-right:0">
                    <label class="checkbox-inline">
                    <input type="checkbox" name="flag" value="1" checked> Mark as Spam
                    </label>
                  </td>
                </tr>
                <?php } ?>
                <tr>
                  <td style="border:0"></td>
                    <td style="padding:20px 8px 0;border:0">
                      <button type="submit" class="btn btn-primary" style="padding:12px 25px;font-size:16px">Save Changes</button>
                      <a href="deleteReview.php?re_id=<?php echo $row_getReviewInfo['re_id']; ?>" class="btn btn-danger pull-right" style="padding:12px 25px;font-size:16px"><i class="fa fa-times"></i> Delete Review</a>
                    </td>
                </tr>
                </table>
                <input type="hidden" name="MM_update" value="updateReview">
              </form>
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
mysql_free_result($getReviewInfo);
?>