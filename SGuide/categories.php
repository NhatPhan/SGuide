<?php
require_once('admin/Connections/testdb.php');
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_getCategories = 10;
$pageNum_getCategories = 0;
if (isset($_GET['pageNum_getCategories'])) {
  $pageNum_getCategories = $_GET['pageNum_getCategories'];
}
$startRow_getCategories = $pageNum_getCategories * $maxRows_getCategories;

$colname_getCategories = "-1";
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
$totalPages_getCategories = ceil($totalRows_getCategories / $maxRows_getCategories) - 1;

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
define("PAGE_TITLE", $row_getCategories['fa_category'] . " - SGuide");
?>
<html lang="en">
  <head>
    <?php include_once 'header.php'; ?>
    <style type="text/css">
      .fa{padding-top:7px}
      a:hover{color:#BF0E07}
      .category{font-weight:500;font-size:12px;color:#C7C7C7;letter-spacing:0.1em}
      .fa-name{font-weight:600;font-size:22px;margin:0}
      .fa-desc{font-weight:500;width:90%;margin:0 auto}
      .fa-location{font-size:12px;margin-top:15px}
    </style>
  </head>
  <body style="background-image:url(assets/images/nearby-bg.jpg);background-repeat:no-repeat;background-size:100%;background-attachment:fixed">
    <div id="google_translate_element"></div>
    <script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
      }
    </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <!-- Page Content -->
    <section id="topLayer" class="pfblock pfblock" style="padding:20px;background:none">
      <div class="container-fluid">
        <div class="row row-centered">
          <div style="margin:0 8px 8px 0;display:block;text-align:left;margin:0 auto" class="col-lg-8 col-md-8 col-sm-8 col-centered">
            <a href="index.php" alt="Click here to go back Home" title="Click here to go back Home">
              <div class="homeBtn"><i class="fa fa-home" style="color:#fff;font-size:21px;padding:0"></i></div>
            </a>
          </div>
          <div class="col-md-8 col-lg-8 col-centered" style="text-align:center">
            <img src="assets/images/categories/icons/<?php echo $row_getCategories['fa_category']; ?>.png" />
            <h1><?php echo $row_getCategories['fa_category']; ?></h1>
          </div>
        </div>
      </div>
    </section>
    <section id="results" class="pfblock pfblock" style="padding-top:12px">
      <div class="container-fluid">
        <div class="row row-centered">
          <div class="col-lg-8 col-md-8 col-sm-8 col-centered resultBox">
            <!---start of content-->
            <div>
              <?php do { ?>
              <p class="fa-name" style="margin-top:40px"><a href="facility.php?id=<?php echo 
                $row_getCategories['fa_id']; ?>"><?php echo $row_getCategories['fa_name']; ?></a></p>
              <p class="category text-uppercase"><?php echo $row_getCategories['fa_category']; ?></p>
              <p class="fa-desc"><?php echo $row_getCategories['fa_description']; ?></p>
              <p class="fa-location" style="margin-bottom:40px">
                <?php echo $row_getCategories['fa_buildingName']; ?> <?php echo 
                  $row_getCategories['fa_floorNumber']; ?> <?php echo $row_getCategories['fa_streetName']; ?> <?php echo 
                  $row_getCategories['fa_unitNumber']; ?> <?php echo $row_getCategories['fa_houseNumber']; ?>, Singapore <?php echo $row_getCategories['fa_postalCode']; ?>
              </p>
              <hr style="border-color:#E4E4E4" />
              <?php } while ($row_getCategories = mysql_fetch_assoc($getCategories)); ?>
              <br><br>
              <center>
                <p style="font-weight:500">Displaying <?php echo ($startRow_getCategories + 1) ?> - <?php echo min
                  ($startRow_getCategories + $maxRows_getCategories, $totalRows_getCategories) ?> out of <?php echo 
                  $totalRows_getCategories ?> records.</p>
                <ul class="pagination pagination-lg" style="font-weight:500">
                  <?php if(isset($_GET["pageNum_getCategories"]) && $_GET["pageNum_getCategories"] > 0) { ?>
                  <li><a href="<?php printf("%s?pageNum_getCategories=%d%s", $currentPage, 0, $queryString_getCategories); ?>" style="min-height:50px;padding-top:13px">&laquo; First Page</a></li>
                  <li><a href="<?php printf("%s?pageNum_getCategories=%d%s", $currentPage, max(0, $pageNum_getCategories - 1), $queryString_getCategories); ?>"><i class="fa fa-chevron-circle-left"></i>&nbsp; Previous Page</a></li>
                  <?php
                    }
                    $maxCheck = "pageNum_getCategories=" . $totalPages_getCategories . $queryString_getCategories;
                    if(strcasecmp($maxCheck,$_SERVER['QUERY_STRING']) != 0) { ?>
                  <li><a href="<?php printf("%s?pageNum_getCategories=%d%s", $currentPage, min($totalPages_getCategories, $pageNum_getCategories + 1), $queryString_getCategories); ?>">Next Page &nbsp;<i class="fa fa-chevron-circle-right"></i></a></li>
                  <li><a href="<?php printf("%s?pageNum_getCategories=%d%s", $currentPage,$totalPages_getCategories,$queryString_getCategories); ?>" style="min-height:50px;padding-top:13px">Last Page &raquo;</a></li>
                  <?php } ?>
                </ul>
              </center>
            </div>
            <!--end of content-->
          </div>
        </div>
      </div>
    </section>
    <!-- /#page-content-wrapper -->
    <?php include_once 'footer.php'; ?>
  </body>
</html>
<?php mysql_free_result($getCategories); ?>