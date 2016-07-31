<?php
if(!isset($_GET['slat']) || !isset($_GET['slng']) || !isset($_GET['lat']) || !isset($_GET['lng'])) {
	header("Location: index.php?getLocation=true");
	die();
}
/* retrieve config defines */
define('INCLUDE_FILE', true);
require_once 'config.php';
$center_lat = $_GET['slat'];
$center_lng = $_GET['slng'];
$radius     = 2000;
$center     = [$center_lat, $center_lng];
if (mysqli_connect_errno()) {
  die("Connect failed: " . mysqli_connect_error());
}
$retrieve = $con->prepare("SELECT fa_id, fa_streetName, fa_name, fa_x, fa_y, fa_image, (SQRT(POW(((?) - fa_x), 2) + POW(((?) - fa_y), 2))) AS distance FROM facilities HAVING distance < ? ORDER BY distance LIMIT 0,30");
$retrieve->bind_param("ddi", $center_lat, $center_lng, $radius);
$retrieve->execute();
$retrieve->bind_result($fa_id, $fa_streetName, $fa_name, $fa_x, $fa_y, $fa_img, $distance);
$locations = array();
while ($row = $retrieve->fetch()) {
  /* Markers */
  $locations[] = array(
    $fa_name,
    $fa_streetName,
    $fa_x,
    $fa_y,
    $fa_id,
    $fa_img,
    $distance
  );
}
$retrieve->close();
$con->close();
$location_center = json_encode((array) $center);
$locations_json  = json_encode((array) $locations);
define("PAGE_TITLE", "Nearby Facilities - SGuide");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once 'header.php'; ?>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script type="text/javascript" src="assets/js/svy21.min.js"></script>
  </head>
  <body style="background-image:url(assets/images/nearby-bg.jpg);background-repeat:no-repeat;background-size:100%;background-attachment:fixed;background-position:bottom center">
  <div id="google_translate_element"></div>
  <section id="topLayer" class="pfblock pfblock" style="padding:60px 0 70px;background:none">
      <div class="container-fluid">
        <div class="row row-centered">
          <div class="col-md-10 col-lg-8 col-sm-12 col-centered">
          <div style="display:inline-block;padding-top:10px" class="pull-left">
             <a href="index.php" alt="Click here to go back Home" title="Click here to go back Home"><div class="homeBtn"><i class="fa fa-home" style="color:#fff;font-size:21px;padding:0"></i></div></a>
			</div>
              <center><h1>Nearby Facilities</h1></center>
          </div>
        </div>
      </div>
    </section>
    <section id="mapLayer" class="pfblock pfblock" style="padding:0">
    <div class="container-fluid">
      <div class="row row-centered">
        <div class="col-lg-8 col-md-10 col-sm-12 col-centered resultBox" style="padding:0">
          <div id="map" style="width:100%;height:50vh"></div>
      <div class="row" style="padding:30px">
        <ul class="list-inline">
          <?php
          foreach ($locations as $location) {
          	$name    = $location[0];
          	$addr    = $location[1];
          	$map_lat = $location[2];
          	$map_lng = $location[3];
          	$id      = $location[4];
          	$map_img = $location[5];
            $dist    = $location[6];
          ?>
          <li class="col-lg-2 suggestions">
          <div style="min-height:200px;margin-bottom:10px">
            <a href="<?php echo 'facility.php?id='.$id;?>">
            <?php if(!empty($map_img)){ ?>
            <img src="<?php echo $map_img; ?>" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" height="auto" width="200"><br>
            <?php } else { ?>
				    <img src="assets/images/placeholder.png" alt="<?php echo $name; ?>" title="<?php echo $name; ?>" height="auto" width="200"><br>
			<?php } ?>
			</a>
			</div>
            <p class="suggestions-name"><a href="<?php echo 'facility.php?id='.$id;?>"><?php echo $name; ?></a></p>
            <p class="suggestions-address"><?php $truncated = (strlen($addr) > 70) ? substr($addr, 0, 70) . '...' : $addr; echo $truncated; ?></p>
            <kbd><?php if($dist >= 1000) {$dist /= 1000; echo number_format($dist,2)."km";} else {echo intval($dist) . "m"; }?> away</kbd>
		</li>
          <?php } ?>
        </ul>
      </div>
      </div>
        </div>
      </section>
    </div>
    <script type='text/javascript'>
        //Get array of locations from PHP
        var location_center = <?php echo $location_center ?>;
        var locations_FromPHP = <?php echo $locations_json ?>;
        var locations = [];
        for (i = 0; i <= locations_FromPHP.length; i++) {
            locations[i] = [];
        }
        var cv = new SVY21();
        for (i = 0; i <= locations_FromPHP.length; i++) {
            if (i == locations_FromPHP.length) {
                var resultLatLon = cv.computeLatLon(location_center[0], location_center[1]);
                locations[i][2] = resultLatLon['lat'];
                locations[i][3] = resultLatLon['lon'];
            } else {
                var resultLatLon = cv.computeLatLon(locations_FromPHP[i][3], locations_FromPHP[i][2]);
                locations[i][0] = locations_FromPHP[i][0];
                locations[i][1] = locations_FromPHP[i][1];
                locations[i].push(resultLatLon['lat']);
                locations[i].push(resultLatLon['lon']);
                locations[i][4] = locations_FromPHP[i][4];
            }
        }
        var zoomLvl = 14;
        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        if(w < 1000) { zoomLvl = 12; }
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: zoomLvl,
            center: new google.maps.LatLng(<?php echo $_GET["lat"]; ?>, <?php echo $_GET["lng"]; ?>),
            mapTypeId: google.maps.MapTypeId.ROADMAP
            });
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(<?php echo $_GET["lat"]; ?>, <?php echo $_GET["lng"]; ?>),
            map: map,
            icon: './assets/images/gps.png'
        });
        var infowindow = new google.maps.InfoWindow();
        var marker, i;
        for (i = 0; i < locations.length - 1; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][2], locations[i][3]),
                map: map
            });
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent("<span style='font-weight:bold;font-size:18px'>" + locations[i][0] + "</span><br>" + locations[i][1] + "<br><div style='margin:10px 0 0 0'><a class='btn-sm btn-primary' style='line-height:2.5!important;font-weight:500;font-size:14px' href='facility.php?id=" + locations[i][4] + "'>View Facility Details</a></div>");
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }
        </script>
    <?php include_once 'footer.php'; ?>
    <script type="text/javascript">
  	function googleTranslateElementInit() {
    	new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
  	}
  	</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  </body>
</html>