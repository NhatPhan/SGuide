<?php
session_start();
if(!isset($_GET["fa_id"]) || empty($_GET["fa_id"])) {
	header("Location: manageFacilities.php");
	die();
}
require_once('Connections/testdb.php');
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo);
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "updateFacility")) {
  $updateSQL = sprintf("UPDATE facilities SET fa_name=%s, fa_buildingName=%s, fa_floorNumber=%s, fa_postalCode=%s, fa_streetName=%s, fa_unitNumber=%s, fa_description=%s, fa_hyperlink=%s, fa_image=%s, fa_houseNumber=%s, fa_category=%s, fa_region=%s WHERE fa_id=%s",
                       GetSQLValueString($_POST['fa_name'], "text"),
                       GetSQLValueString($_POST['buildingName'], "text"),
                       GetSQLValueString($_POST['floorNumber'], "text"),
                       GetSQLValueString($_POST['postalCode'], "text"),
                       GetSQLValueString($_POST['streetName'], "text"),
                       GetSQLValueString($_POST['unitNumber'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['hyperlink'], "text"),
                       GetSQLValueString($_POST['image'], "text"),
                       GetSQLValueString($_POST['houseNumber'], "text"),
                       GetSQLValueString($_POST['category'], "text"),
                       GetSQLValueString($_POST['region'], "text"),
                       GetSQLValueString($_POST['fa_id'], "int"));

  mysql_select_db($database_testdb, $testdb);
  $Result1 = mysql_query($updateSQL, $testdb) or die(mysql_error());

  header("Location: manageFacilities.php?edit=success");
  die();
}

$colname_getUsername = "-1";
if (isset($_POST['ad_username'])) {
  $colname_getUsername = $_POST['ad_username'];
}
mysql_select_db($database_testdb, $testdb);
$query_getUsername = sprintf("SELECT ad_username FROM `admin` WHERE ad_username = %s", GetSQLValueString($colname_getUsername, "text"));
$getUsername = mysql_query($query_getUsername, $testdb) or die(mysql_error());
$row_getUsername = mysql_fetch_assoc($getUsername);
$totalRows_getUsername = mysql_num_rows($getUsername);

$colname_getFacilityInfo = "-1";
if (isset($_GET['fa_id'])) {
  $colname_getFacilityInfo = $_GET['fa_id'];
}
mysql_select_db($database_testdb, $testdb);
$query_getFacilityInfo = sprintf("SELECT fa_id, fa_name, fa_buildingName, fa_floorNumber, fa_postalCode, fa_streetName, fa_unitNumber, fa_description, fa_hyperlink, fa_image, fa_houseNumber, fa_category, fa_region FROM facilities WHERE fa_id = %s", GetSQLValueString($colname_getFacilityInfo, "int"));
$getFacilityInfo = mysql_query($query_getFacilityInfo, $testdb) or die(mysql_error());
$row_getFacilityInfo = mysql_fetch_assoc($getFacilityInfo);
$totalRows_getFacilityInfo = mysql_num_rows($getFacilityInfo);
?>
<html lang="en">
  <head>
    <title>Edit Facility :: SGuide Control Panel</title>
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
            <li class="active">
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
      <div id="page-wrapper">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
            <ol class="breadcrumb">
                <li>
                  <a href="dashboard.php"><i class="fa fa-dashboard"></i>&nbsp; Dashboard</a>
                </li>
                <li>
                  <a href="manageFacilities.php"><i class="fa fa-wrench"></i>&nbsp; Manage Facilities</a>
                </li>
                <li class="active">
                  <i class="fa fa-wrench"></i>&nbsp; Edit Facility
                </li>
              </ol>
              <h1 class="page-header" style="margin-bottom:0;border:0">Edit Facility</h1>
            </div>
          </div>
          <div>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> You are editing: <?php echo $row_getFacilityInfo['fa_name']; ?> (ID: <?php echo $_GET["fa_id"]; ?>)</h3>
              </div>
              <!--- stuff goes here!!! --->
              <form method="POST" action="<?php echo $editFormAction; ?>" name="updateFacility">
                <input type="hidden" name="fa_id" value="<?php echo $row_getFacilityInfo['fa_id']; ?>">
                <table class="table table-bordered" style="border:0">
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0;width:10%;min-width:150px">Name</td>
                    <td style="border-right:0">
                      <input type="text" name="fa_name" value="<?php echo $row_getFacilityInfo['fa_name']; ?>" class="form-control" maxlength="50" required autofocus>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Category</td>
                    <td style="border-right:0">
                      <label class="radio-inline"><input type="radio" value="Museum" name="category" id="optionsRadiosInline2" <?php echo ($row_getFacilityInfo['fa_category'] == 'Museum')? 'checked' : '' ?>> Museum
                      </label>
                      <label class="radio-inline"><input type="radio" value="Heritage Sites" name="category" id="optionsRadiosInline2" <?php echo ($row_getFacilityInfo['fa_category'] == 'Heritage Sites')? 'checked' : '' ?>> Heritage Sites</label>
                      <label class="radio-inline"><input type="radio" value="Hotels" name="category" id="optionsRadiosInline2" <?php echo ($row_getFacilityInfo['fa_category'] == 'Hotels')? 'checked' : '' ?>> Hotels</label>
                      <label class="radio-inline"><input type="radio" value="Parks" name="category" id="optionsRadiosInline2" <?php echo ($row_getFacilityInfo['fa_category'] == 'Parks')? 'checked' : '' ?>> Parks</label>
                      <label class="radio-inline"><input type="radio" value="Tourist Attractions" name="category" id="optionsRadiosInline2" <?php echo ($row_getFacilityInfo['fa_category'] == 'Tourist Attractions')? 'checked' : '' ?>> Tourist Attractions</label>
                      <label class="radio-inline"><input type="radio" value="Monuments" name="category" id="optionsRadiosInline2" <?php echo ($row_getFacilityInfo['fa_category'] == 'Monuments')? 'checked' : '' ?>> Monuments</label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Region</td>
                    <td style="border-right:0">
                      <label class="radio-inline"><input type="radio" value="N" name="region" id="optionsRadiosInline2"  <?php echo ($row_getFacilityInfo['fa_region'] == 'N')? 'checked' : '' ?>>North</label>
                      <label class="radio-inline"><input type="radio" value="E" name="region" id="optionsRadiosInline2"  <?php echo ($row_getFacilityInfo['fa_region'] == 'E')? 'checked' : '' ?>>East</label>
                      <label class="radio-inline"><input type="radio" value="W" name="region" id="optionsRadiosInline2"  <?php echo ($row_getFacilityInfo['fa_region'] == 'W')? 'checked' : '' ?>>West</label>
                      <label class="radio-inline"><input type="radio" value="C" name="region" id="optionsRadiosInline2"  <?php echo ($row_getFacilityInfo['fa_region'] == 'C')? 'checked' : '' ?>>Central</label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Building Name</td>
                    <td style="border-right:0">
                      <input type="text" name="buildingName" value="<?php echo $row_getFacilityInfo['fa_buildingName']; ?>" class="form-control" maxlength="50">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Floor Number</td>
                    <td style="border-right:0">
                      <input type="text" name="floorNumber" value="<?php echo $row_getFacilityInfo['fa_floorNumber']; ?>" class="form-control" maxlength="10">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">House Number</td>
                    <td style="border-right:0">
                      <input type="text" name="houseNumber" value="<?php echo $row_getFacilityInfo['fa_houseNumber']; ?>" class="form-control" maxlength="10">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Postal Code</td>
                    <td style="border-right:0">
                      <input type="text" name="postalCode" value="<?php echo $row_getFacilityInfo['fa_postalCode']; ?>" class="form-control" maxlength="6">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Street Name</td>
                    <td style="border-right:0">
                      <input type="text" name="streetName" value="<?php echo $row_getFacilityInfo['fa_streetName']; ?>" class="form-control" maxlength="100">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Unit Number</td>
                    <td style="border-right:0">
                      <input type="text" name="unitNumber" value="<?php echo $row_getFacilityInfo['fa_unitNumber']; ?>" class="form-control" maxlength="10">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Description</td>
                    <td style="border-right:0">
                      <textarea class="form-control" rows="3" name="description" maxlength="1000"><?php echo $row_getFacilityInfo['fa_description']; ?></textarea>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Website URL</td>
                    <td style="border-right:0">
                      <input type="text" name="hyperlink" value="<?php echo $row_getFacilityInfo['fa_hyperlink']; ?>" class="form-control" maxlength="200">
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right;font-weight:bold;border-left:0">Image URL</td>
                    <td style="border-right:0">
                      <input type="text" name="image" value="<?php echo $row_getFacilityInfo['fa_image']; ?>" class="form-control" maxlength="200">
                    </td>
                  </tr>
                  <tr>
                    <td style="border:0"></td>
                    <td style="padding:20px 8px 0;border:0">
                      <button type="submit" class="btn btn-primary" style="padding:12px 25px;font-size:16px">Save Changes</button>
                    </td>
                  </tr>
                  <input type="hidden" name="MM_update" value="updateFacility">
                </table>
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
mysql_free_result($getFacilityInfo);
?>