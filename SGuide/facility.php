<?php
session_start();
date_default_timezone_set('Asia/Singapore');
if(empty($_GET["id"])) {
  header("Location: index.php");
}
// set facility id into session for FB use
$_SESSION["facility_id"] = $_GET["id"];

/* retrieve config defines */
define('INCLUDE_FILE',true);
require_once 'config.php';

//----- START FACEBOOK CONFIG -----
require_once 'facebook-sdk-v5/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '833074496790664',
  'app_secret' => '072dab28222aee891681629a1784c481',
  'default_graph_version' => 'v2.5',
]);
if(isset($_SESSION['facebook_access_token'])) {
  $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
  try {
    $response = $fb->get('/me');
    $userNode = $response->getGraphUser();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    session_destroy();
    $_SESSION = array();
    header("Refresh:0");
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    session_destroy();
    $_SESSION = array();
    header("Refresh:0");
  }
  $re_fbid = $userNode->getId();
  $re_username = $userNode->getName();
}
//----- END FACEBOOK CONFIG -----

/* check connection */
if (mysqli_connect_errno ()) {
  die("Connect failed: " . mysqli_connect_error());
} else {
  /* retrieve reviews */
  $location = "";
  $retrieve = $con->prepare("SELECT fa_name, fa_description, fa_x, fa_y, fa_buildingName, fa_floorNumber, fa_postalCode, fa_streetName, fa_unitNumber, fa_hyperlink, fa_image, fa_houseNumber, fa_category, fa_hyperlink, fa_region FROM facilities WHERE fa_id = ?");
  $search = $_GET["id"];
  $retrieve->bind_param("i", $search);
  $retrieve->execute();
  $retrieve->bind_result($name, $description, $lat, $lng, $bldg, $floor, $postal, $street, $unit, $url, $image, $hseNo, $cat, $hyperlink, $fa_region);
  $notFound = true;
  while($row = $retrieve->fetch()){
    if (!empty($hseNo)){
      $location .= $hseNo . " ";
    }
    $location .= $street;
    if (!empty($bldg)){
      $location .= "<br>" . $bldg;
    }
    if (!empty($floor) || ! empty($unit)){
      $location .= "<br>";
    }
    if (!empty($floor)){
      $location .= "Level " . $floor;
      if (!empty($unit))
        $location .= ", ";
    }
    if (!empty($unit)){
      $location .= "Unit " . $unit;
    }
    if (!empty($postal)){
      $location .= "<br>Singapore " . $postal;
    }
    $notFound = false;
  }
  $retrieve->close();
  define("PAGE_TITLE", $name . " - SGuide");
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <script type="text/javascript" src="assets/js/moment.min.js"></script>
    <script type="text/javascript">
    //Test for the ugliness left by FB
    if (window.location.hash == '#_=_'){
      if (history.replaceState) {
        var cleanHref = window.location.href.split('#')[0];
        history.replaceState(null, null, cleanHref);
      } else { window.location.hash = ''; }
    }
    moment.locale('en', {
        calendar: {
            lastDay: '[Yesterday at] LT',
            sameDay: '[Today at] LT',
            nextDay: '[Tomorrow at] LT',
            lastWeek: '[Last] dddd [at] LT',
            nextWeek: 'dddd [at] LT',
            sameElse: 'DD MMM YYYY [-] LT'
        }
    });
    </script>
    <?php
      include_once 'header.php';
      if ($notFound) {
      	die("<div class='jumbotron vertical-center'><div class='container'>ERROR: Facility not found or does not exist!</div></div>");
      }
      include 'map.php';
    ?>
    <style type="text/css">.btn:focus{color:#fff!important}.btn-facebook{color:#fff;text-shadow:0 -1px 0 rgba(0,0,0,.25);background-color:#2b4b90;background-image:-moz-linear-gradient(top,#3b5998,#133783);background-image:-webkit-gradient(linear,0 0,0 100%,from(#3b5998),to(#133783));background-image:-webkit-linear-gradient(top,#3b5998,#133783);background-image:-o-linear-gradient(top,#3b5998,#133783);background-image:linear-gradient(to bottom,#3b5998,#133783);background-repeat:repeat-x;border-color:#133783 #133783 #091b40;border-color:rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.25);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff3b5998', endColorstr='#ff133783', GradientType=0);filter:progid:DXImageTransform.Microsoft.gradient(enabled=false)}.btn-facebook.active,.btn-facebook.disabled,.btn-facebook:active,.btn-facebook:focus,.btn-facebook:hover,.btn-facebook[disabled]{color:#fff;background-color:#133783}.btn-facebook.active,.btn-facebook:active{background-color:#0d2456}.fa-name{font-weight:500;font-size:20px;margin:0}.fa-location{font-size:14px;margin:0;font-weight:500}#map2{width:100%;height:450px}@media screen and (max-width: 730px){.homeBtn{margin-left:15px}}</style>
  </head>
  <body>
  <div id="google_translate_element"></div>
    <section id="topLayer" class="pfblock pfblock-gray" style="padding-top:20px">
      <div class="container">
      <div class="row">
      <div style="margin:0 8px 8px 0;display:inline-block" class="pull-left">
             <a href="index.php" alt="Click here to go back Home" title="Click here to go back Home"><div class="homeBtn"><i class="fa fa-home" style="color:#fff;font-size:21px;padding:0"></i></div></a>
	  </div>
      </div>
        <?php if(!empty($image)){ ?>
        <div class="row">
          <div class="col-md-12" style="margin-bottom: 50px">
            <img src="<?php echo $image; ?>"
              class="center-block img-responsive" alt="<?php echo $name; ?>"
              title="<?php echo $name; ?>"
              style="max-height:50vh;" />
          </div>
        </div>
        <?php } else { echo "<br><br><br>"; } ?>
        <div class="row row-centered">
          <div class="col-lg-10 col-centered">
            <div class="pfblock-header wow fadeInUp">
              <h2 class="pfblock-title"><?php echo $name; ?></h2>
              <div class="pfblock-line"></div>
              <div class="pfblock-subtitle">
                <?php
                  if(!empty($description)) {
                  	echo $description;
                  	if(!empty($hyperlink)){
                  		echo "<br><br>";
                  	}
                  }
                ?>
                <?php if(!empty($hyperlink)){echo "<a class='btn btn-primary' target='_blank' href='" . $hyperlink . "'><b>Visit Website</b></a>";} ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="portfolio" class="pfblock">
      <div class="container">
        <div class="row row-centered">
          <div class="col-lg-10 col-centered">
            <div class="pfblock-header wow fadeInUp">
              <h2 class="pfblock-title">
              <?php
              	$check = explode(' ',trim($cat))[0];
              	if($check == "Tourist") {$cat = "Attraction";}
              	elseif($check == "Heritage") {$cat = "Heritage";}
                if(substr($cat, -1) == "s") {
                	$cat2 = substr_replace($cat, "", -1);
                    echo $cat2 . " Location";
                }
                else {
                	echo $cat . " Location";
                }
              ?>
              </h2>
              <div class="pfblock-line"></div>
              <div class="pfblock-subtitle">
                <?php echo $location; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12" style="margin-bottom: 90px">
            <div id="map"></div>
          </div>
        </div>
      </div>
    </section>
    <section class="calltoaction" style="background:none;background-color:#222;color:#FFF;">
      <div class="container">
        <div class="row">
        	<?php include 'weather.php'; ?>
        </div>
      </div>
    </section>
    <br>
    <?php
      include_once 'review.php';
      include_once 'facilitiesAround.php';
      /* close all connections when done */
      $con->close();
      include_once 'footer.php';
    ?>
    <script type="text/javascript">
  	function googleTranslateElementInit() {
    	new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
  	}
  	</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  </body>
</html>