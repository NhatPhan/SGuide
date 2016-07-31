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
$currentPage         = $_SERVER["PHP_SELF"];
$colname_getUsername = "-1";
if (isset($_POST['ad_username'])) {
  $colname_getUsername = $_POST['ad_username'];
}
mysql_select_db($database_testdb, $testdb);
$query_getUsername = sprintf("SELECT ad_username FROM `admin` WHERE ad_username = %s", GetSQLValueString($colname_getUsername, "text"));
$getUsername = mysql_query($query_getUsername, $testdb) or die(mysql_error());
$row_getUsername        = mysql_fetch_assoc($getUsername);
$totalRows_getUsername  = mysql_num_rows($getUsername);
$maxRows_getLastReviews = 20;
$pageNum_getLastReviews = 0;
if (isset($_GET['pageNum_getLastReviews'])) {
  $pageNum_getLastReviews = $_GET['pageNum_getLastReviews'];
}
$startRow_getLastReviews = $pageNum_getLastReviews * $maxRows_getLastReviews;
mysql_select_db($database_testdb, $testdb);
$query_getLastReviews       = "SELECT re_id, re_username, SUBSTRING(re_datetime, 1, 10), SUBSTRING(re_description, 1, 60), re_flag FROM reviews ORDER BY re_flag DESC";
$query_limit_getLastReviews = sprintf("%s LIMIT %d, %d", $query_getLastReviews, $startRow_getLastReviews, $maxRows_getLastReviews);
$getLastReviews = mysql_query($query_limit_getLastReviews, $testdb) or die(mysql_error());
$row_getLastReviews = mysql_fetch_assoc($getLastReviews);
if (isset($_GET['totalRows_getLastReviews'])) {
  $totalRows_getLastReviews = $_GET['totalRows_getLastReviews'];
} else {
  $all_getLastReviews       = mysql_query($query_getLastReviews);
  $totalRows_getLastReviews = mysql_num_rows($all_getLastReviews);
}
$totalPages_getLastReviews  = ceil($totalRows_getLastReviews / $maxRows_getLastReviews) - 1;
$queryString_getLastReviews = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params    = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_getLastReviews") == false && stristr($param, "totalRows_getLastReviews") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_getLastReviews = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_getLastReviews = sprintf("&totalRows_getLastReviews=%d%s", $totalRows_getLastReviews, $queryString_getLastReviews);
?>
<html lang="en">
  <head>
    <title>Manage Reviews :: SGuide Control Panel</title>
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
            <?php if(isset($_GET["delete"]) && $_GET["delete"] == "success") { ?>
            <div class="alert alert-success" style="padding:10px;font-weight:600;margin:10px 0 30px 0">
              <i class="fa fa-check"></i>&nbsp; Review deleted successfully!
            </div>
            <?php } else if(isset($_GET["edit"]) && $_GET["edit"] == "success") { ?>
            <div class="alert alert-success" style="padding:10px;font-weight:600;margin:10px 0 30px 0">
              <i class="fa fa-check"></i>&nbsp; Review updated successfully!
            </div>
            <?php } ?>
            <ol class="breadcrumb">
                <li>
                  <a href="dashboard.php"><i class="fa fa-dashboard"></i>&nbsp; Dashboard</a>
                </li>
                <li class="active">
                  <i class="fa fa-wrench"></i>&nbsp; Manage Reviews
                </li>
              </ol>
              <h1 class="page-header" style="margin-bottom:0;border:0">Manage Reviews</h1>
            </div>
          </div>
          <div class="alert alert-info" style="padding:10px;font-weight:600">
            <strong>Viewing <?php if (empty($row_getLastReviews)) { echo "0"; } else { echo ($startRow_getLastReviews + 1); } ?> - <?php echo min($startRow_getLastReviews + $maxRows_getLastReviews, $totalRows_getLastReviews); ?> out of <?php echo $totalRows_getLastReviews; ?> records.</strong> 
          </div>
          <div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> All Reviews</h3>
              </div>
              <div class="table-responsive">
                <table class="table table-hover table-striped">
                  <tr class="trheader">
                    <th width="10%">Review ID</th>
                    <th>Reviewer</th>
                    <th width="12%">Date of review</th>
                    <th>Description</th>
                    <th>Status</th>
                  </tr>
                  <?php
                    if (empty($row_getLastReviews)) {
                    } else {
                      do {
                    ?>
                  <tr style="cursor:pointer" onClick="window.location.href = 'editReview.php?re_id=<?php echo $row_getLastReviews['re_id']; ?>';">
                    <td><?php echo $row_getLastReviews['re_id']; ?></td>
                    <td><?php echo $row_getLastReviews['re_username']; ?></td>
                    <td><?php echo $row_getLastReviews['SUBSTRING(re_datetime, 1, 10)']; ?></td>
                    <td><?php echo $row_getLastReviews['SUBSTRING(re_description, 1, 60)']; ?>...</td>
                    <td>
                      <?php
                        if ($row_getLastReviews['re_flag'] == '1') {
                        ?><font color="red">Marked as Spam</font><?php
                        } else {
                          print "OK";
                        }
                        ?>
                    </td>
                  </tr>
                  <?php
                    } while ($row_getLastReviews = mysql_fetch_assoc($getLastReviews));
                    }
                    ?>                
                </table>
              </div>
            </div>
          </div>
          <br />
          <table class="table table-bordered" style="width:50%;margin:0 auto">
            <tr style="cursor:pointer;font-weight:600">
              <td align="center" onClick="window.location.href = '<?php printf("%s?pageNum_getLastReviews=%d%s", $currentPage, 0, $queryString_getLastReviews); ?>';">First results</td>
              <td align="center" onClick="window.location.href = '<?php printf("%s?pageNum_getLastReviews=%d%s", $currentPage, min($totalPages_getLastReviews, $pageNum_getLastReviews + 1), $queryString_getLastReviews); ?>';">Next results</td>
              <td align="center" onClick="window.location.href = '<?php printf("%s?pageNum_getLastReviews=%d%s", $currentPage, max(0, $pageNum_getLastReviews - 1), $queryString_getLastReviews); ?>';">Previous results</a></td>
              <td align="center" onClick="window.location.href = '<?php printf("%s?pageNum_getLastReviews=%d%s", $currentPage, $totalPages_getLastReviews, $queryString_getLastReviews); ?>';">Last results</td>
          </table>
        </div>
      </div>
    </div>
    <?php include_once 'footer.php'; ?>
  </body>
</html>
<?php
mysql_free_result($getUsername);
mysql_free_result($getLastReviews);
?>