<?php
session_start();
if (isset($_SESSION['MM_Username'])) {
	header("Location: dashboard.php");
	die();
}
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
$currentPage           = $_SERVER["PHP_SELF"];
$maxRows_getCategories = 10;
$pageNum_getCategories = 0;
if (isset($_GET['pageNum_getCategories'])) {
  $pageNum_getCategories = $_GET['pageNum_getCategories'];
}
$startRow_getCategories = $pageNum_getCategories * $maxRows_getCategories;
$colname_getCategories  = "-1";
if (isset($_GET['category'])) {
  $colname_getCategories = $_GET['category'];
}
mysql_select_db($database_testdb, $testdb);
$query_getCategories       = sprintf("SELECT fa_id, fa_name, fa_buildingName, fa_floorNumber, fa_postalCode, fa_streetName, fa_unitNumber, fa_description, fa_houseNumber, fa_category FROM facilities WHERE fa_category = %s", GetSQLValueString($colname_getCategories, "text"));
$query_limit_getCategories = sprintf("%s LIMIT %d, %d", $query_getCategories, $startRow_getCategories, $maxRows_getCategories);
$getCategories = mysql_query($query_limit_getCategories, $testdb) or die(mysql_error());
$row_getCategories = mysql_fetch_assoc($getCategories);
if (isset($_GET['totalRows_getCategories'])) {
  $totalRows_getCategories = $_GET['totalRows_getCategories'];
} else {
  $all_getCategories       = mysql_query($query_getCategories);
  $totalRows_getCategories = mysql_num_rows($all_getCategories);
}
$totalPages_getCategories  = ceil($totalRows_getCategories / $maxRows_getCategories) - 1;
$queryString_getCategories = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params    = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_getCategories") == false && stristr($param, "totalRows_getCategories") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_getCategories = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_getCategories = sprintf("&totalRows_getCategories=%d%s", $totalRows_getCategories, $queryString_getCategories);
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}
$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}
if (isset($_POST['username'])) {
  $loginUsername           = $_POST['username'];
  $password                = md5($_POST['password']);
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "dashboard.php";
  $MM_redirectLoginFailed  = "index.php?login=error";
  $MM_redirecttoReferrer   = false;
  mysql_select_db($database_testdb, $testdb);
  $LoginRS__query = sprintf("SELECT ad_username, ad_password FROM `admin` WHERE ad_username=%s AND ad_password=%s", GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text"));
  $LoginRS = mysql_query($LoginRS__query, $testdb) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    $loginStrGroup = "";
    if (PHP_VERSION >= 5.1) {
      session_regenerate_id(true);
    } else {
      session_regenerate_id();
    }
    //declare two session variables and assign them
    $_SESSION['MM_Username']  = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
    }
    header("Location: " . $MM_redirectLoginSuccess);
    die();
  } else {
    header("Location: " . $MM_redirectLoginFailed);
    die();
  }
}
?>
<html lang="en">
  <head>
  <title>SGuide Control Panel</title>
    <?php include_once 'header.php'; ?>
    <style type="text/css">.category,.fa-desc,.fa-name{font-weight:500}.fa{padding-top:7px}a:hover{color:#BF0E07}.category{font-size:12px;color:#C7C7C7;letter-spacing:.1em}.fa-name{font-size:22px;margin:0}.fa-location{font-size:12px;margin-top:15px}h1{text-transform:none;font-size:4.5em}.form-signin{max-width:400px;padding:50px 15px;margin:0 auto}.form-signin .form-control{position:relative;height:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:10px;font-size:16px}.form-signin .form-control:focus{z-index:2}.form-signin input[type=text]{margin-bottom:-1px;border-bottom-right-radius:0;border-bottom-left-radius:0;font-weight:600}.form-signin input[type=password]{font-weight:600;margin-top:20px;margin-bottom:10px;border-top-left-radius:0;border-top-right-radius:0}</style>
  </head>
  <body style="background-image:url(../assets/images/nearby-bg.jpg);background-repeat:no-repeat;background-size:100%;background-attachment:fixed;background-position:center center;margin:0">
    <section id="topLayer" class="pfblock" style="background:none;min-height:100vh;padding-bottom:105px">
      <div class="container-fluid">
      <div class="row row-centered">
        <div class="col-lg-12 col-centered">
          <div align="center">
            <h1>SGuide</h1>
            <h2>CONTROL PANEL</h2>
            <form action="<?php echo $loginFormAction; if(isset($_GET['accesscheck'])) { echo '?accesscheck=' . $_GET['accesscheck']; } ?>" method="POST" name="login" class="form-signin">
            <?php
              if (isset($_GET['login'])) {
                if($_GET['login'] == "error") {
            ?>
            <div class="alert alert-danger">ERROR: Incorrect username or password!</div>
            <?php } else if($_GET['login'] == "true") { ?>
            <div class="alert alert-danger">ERROR: You need to login first!</div>
            <?php
              }
              }
            ?>
              <table style="width:100%">
                <tr>
                  <td><input type="text" name="username" class="form-control" placeholder="Username" maxlength="30" required autofocus></td>
                </tr>
                <tr>
                  <td><input type="password" name="password" class="form-control" placeholder="Password" maxlength="32" required></td>
                </tr>
                <tr>
                  <td>
                    <br>
                    <button class="btn btn-primary btn-block" type="submit">Sign in</button>
                  </td>
                </tr>
              </table>
            </form>    
          </div>
        </div>
      </div>
    </section>
    <div style="margin-top:-115px">
      <?php include_once 'footer.php'; ?>
    </div>
  </body>
</html>
<?php
mysql_free_result($getCategories);
?>