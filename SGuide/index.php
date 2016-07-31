<?php
define("PAGE_TITLE", "Welcome to SGuide!");
$bg         = array('bg-01.jpg','bg-02.jpg','bg-03.jpg');
$i          = rand(0, count($bg) - 1);
$selectedBg = "$bg[$i]";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once 'header.php'; ?>
    <style type="text/css">
      .pfblock-gray{background:none;}
      body{
      background: url(assets/images/<?php echo $selectedBg; ?>) no-repeat;background-attachment:fixed;background-size:auto 100vh;
      }
      @media screen and (min-width: 1028px){body{background-size:auto auto;}}
      @media screen and (max-height: 750px) and (min-width: 1000px){body{background-size:100% 100vh;}}
      #catBtn:focus,#getNearby:focus{color:#000!important}
      @media screen and (max-height: 650px){
      body{background-size:100% 100vh}#mainContainer{margin-top:-50px!important;padding-bottom:50px!important}#navBtn{margin-top:40px!important;padding-top:20px!important}#catBtn,#getNearby{padding:10px 30px 5px 30px;font-size:14px}
      }
      @media screen and (max-width: 600px){
      #mainContainer{margin-top:-50px!important;padding-bottom:50px!important}#keyword{width:100%!important;margin-bottom:10px!important}#logoImg{width:60%;height:60%}#navBtn{margin-top:40px!important;padding-top:20px!important}#catBtn,#getNearby{padding:10px 30px 5px 30px;font-size:14px}
      }
    </style>
    <script type="text/javascript" src="assets/js/svy21.min.js"></script>
    <script type="text/javascript">
    var lat, lng, slat, slng;
    function getNearbyFacilities() {
      if (navigator.geolocation) {
    	  navigator.geolocation.getCurrentPosition(showPosition, errorHandler,  {frequency:5000,maximumAge: 0, timeout: 15000, enableHighAccuracy:true});
      } else {
        alert("Geolocation is not supported by this browser.");
      }
    }
    function errorHandler(error) {
    	switch(error.code) {
        case error.PERMISSION_DENIED:
        	alert("Error: Please turn on your device GPS first and try again. If you are using desktop, you need to first allow this website to access your location.");
            break;
        case error.POSITION_UNAVAILABLE:
        	alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
        	alert("Time out while requesting to get your GPS location.");
            break;
        case error.UNKNOWN_ERROR:
        	alert("An unknown error occurred.");
            break;
        }
     }
    function showPosition(position) {
      lat = position.coords.latitude;
      lng = position.coords.longitude;
      var cv2 = new SVY21();
      var resultsvy21 = cv2.computeSVY21(lat,lng);
      slng = resultsvy21['N'];
      slat = resultsvy21['E'];
      window.location.href = "nearbyFacilities.php?lat=" + lat + "&lng=" + lng + "&slat=" + slat + "&slng=" + slng;
    }
    <?php if(isset($_GET["getLocation"]) && $_GET["getLocation"] == "true") { ?>
	window.onload = function() {document.getElementById("getNearby").click();}
	<?php } else { ?>
	window.onload = function() {document.getElementById("keyword").focus();}
	<?php } ?>
	</script>
  </head>
  <body>
  <div id="google_translate_element" style="top:-20px"></div>
    <section id="topLayer" class="pfblock pfblock-gray" style="min-height:100vh;margin:20px 0 0 0;">
      <div class="container" id="mainContainer" style="padding-bottom:150px">
        <center>
          <img src="assets/images/logo.png" alt="SGuide" title="SGuide" id="logoImg" />
          <form action="doSearch.php" method="POST" class="form-inline">
            <div class="row wow slideInLeft" data-wow-delay=".1s">
              <br>
              <div class="form-group-lg col-md-12 col-sm-12">
                <input type="text" class="form-control" name="keyword" id="keyword" style="font-weight:500;width:60%;display:inline-block" autocomplete="off" placeholder="Type keywords here..." required="true" maxlength="50">
                <input class="btn btn-lg" type="submit" value="Search" style="padding:11px 20px;font-size:18px;">
              </div>
            </div>
          </form>
          <div class="wow slideInRight" style="margin-top:100px;" id="navBtn">
            <a href="#categories" class="btn btn-default nolight" onclick="this.blur();" id="catBtn"><i class="fa fa-tags" style="font-size:35px;vertical-align:middle"></i> &nbsp;Choose by Category</a>
            <a onclick="getNearbyFacilities()" id="getNearby" class="btn btn-default nolight setgs" style="background-color:rgba(30,167,121,0.9)" onclick="this.blur();"><i class="fa fa-location-arrow" style="font-size:35px;vertical-align:middle;"></i> &nbsp;What's Nearby?</a>
          </div>
        </center>
      </div>
    </section>
    <section id="categories" style="padding:80px 0 200px 0;margin:0;background-color:rgba(255,255,255,0.85);">
      <div class="pfblock-header wow fadeInUp animated" style="visibility: visible; animation-name: fadeInUp;">
        <h2 class="pfblock-title">CATEGORIES</h2>
        <div class="pfblock-line"></div>
      </div>
      <div class="container-fluid">
        <div class="row no-gutter">
          <div class="col-lg-4 col-sm-6">
            <a href="categories.php?category=museum" class="portfolio-box">
              <img src="assets/images/categories/museum.jpg" class="img-responsive" alt="">
              <div class="portfolio-box-caption">
                <div class="portfolio-box-caption-content">
                  <div class="project-category text-faded">
                    MUSEUM
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-sm-6">
            <a href="categories.php?category=hotels" class="portfolio-box">
              <img src="assets/images/categories/hotels.jpg" class="img-responsive" alt="">
              <div class="portfolio-box-caption">
                <div class="portfolio-box-caption-content">
                  <div class="project-category text-faded">
                    HOTELS
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-sm-6">
            <a href="categories.php?category=parks" class="portfolio-box">
              <img src="assets/images/categories/parks.jpg" class="img-responsive" alt="">
              <div class="portfolio-box-caption">
                <div class="portfolio-box-caption-content">
                  <div class="project-category text-faded">
                    PARKS
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-sm-6">
            <a href="categories.php?category=tourist%20attractions" class="portfolio-box">
              <img src="assets/images/categories/tourist.jpg" class="img-responsive" alt="">
              <div class="portfolio-box-caption">
                <div class="portfolio-box-caption-content">
                  <div class="project-category text-faded">
                    TOURIST ATTRACTIONS
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-sm-6">
            <a href="categories.php?category=heritage%20sites" class="portfolio-box">
              <img src="assets/images/categories/heritage.jpg" class="img-responsive" alt="">
              <div class="portfolio-box-caption">
                <div class="portfolio-box-caption-content">
                  <div class="project-category text-faded">
                    HERITAGE SITES
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-sm-6">
            <a href="categories.php?category=monuments" class="portfolio-box">
              <img src="assets/images/categories/monuments.jpg" class="img-responsive" alt="">
              <div class="portfolio-box-caption">
                <div class="portfolio-box-caption-content">
                  <div class="project-category text-faded">
                    MONUMENTS
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </section>
    <div style="opacity:0.8">
      <?php include_once 'footer.php'; ?>
    </div>
    <script type="text/javascript">
    $('.setgs').on('tap',function(){
    	getNearbyFacilities();return false;
    });
  	function googleTranslateElementInit() {
    	new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
  	}
  	</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  </body>
</html>