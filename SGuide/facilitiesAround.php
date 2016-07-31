<?php
$center_lat = $lat;
$center_lng = $lng;
$radius     = 2000;
$center     = [$center_lat, $center_lng];
$locations  = array();

if (mysqli_connect_errno()) {
  echo "<div class='jumbotron vertical-center'><div class='container'>Connect failed: " . mysqli_connect_error() . "</div></div>";
} else {
  $retrieve = $con->prepare("SELECT fa_id, fa_streetName, fa_name, fa_x, fa_y, fa_image, (SQRT(POW(((?) - fa_x), 2) + POW(((?) - fa_y), 2))) AS distance FROM facilities HAVING distance < ? ORDER BY distance LIMIT 0,15");
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
}
$location_center = json_encode((array) $center);
$locations_json  = json_encode((array) $locations);
?>
<section id="facilitiesAround" class="pfblock">
  <div class="container">
    <div class="row">
      <div class="col-sm-10 col-sm-offset-1">
        <div class="pfblock-header wow fadeInUp" style="margin-bottom:20px">
          <h2 class="pfblock-title">Nearby Facilities Around This Place</h2>
          <div class="pfblock-line"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<section id="mapLayer" class="pfblock pfblock" style="padding:0">
  <div class="container-fluid">
    <div class="row row-centered">
      <div class="col-lg-8 col-md-10 col-sm-12 col-centered resultBox" style="padding:0">
        <div id="map2" style="width:100%;height:50vh"></div>
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
                if($id == $_GET["id"]) {
                	continue;
                } else {
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
            <?php
                }
              }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<script type='text/javascript'>
  var zoomLvl = <?php if($region == "Central"){echo "16";}else{echo "15";} ?>

  var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
  if(w < 1000) { zoomLvl = 13; }
  //Get array of locations from PHP
  var location_center = <?php echo $location_center ?>;
  var locations_FromPHP = <?php echo $locations_json ?>;
  var locations = [];
  for (i = 0; i <= locations_FromPHP.length; i++) {
      locations[i] = [];
  }
  // Initialize new converter
  var cv = new SVY21();
  var resultLatLon = cv.computeLatLon(<?php echo $lng ?>, <?php echo $lat ?>);
  var convert = [];
  for(i in resultLatLon) {
     convert.push(resultLatLon[i]);
  }
  // Computing Lat/Lon from SVY21 and put to new array
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
  //Get Map
  var map2 = new google.maps.Map(document.getElementById('map2'), {
      zoom: zoomLvl,
      center: new google.maps.LatLng(convert[0], convert[1]),
      mapTypeId: google.maps.MapTypeId.ROADMAP
      });
  marker = new google.maps.Marker({
      position: new google.maps.LatLng(convert[0], convert[1]),
      map: map2
  });
  var infowindow = new google.maps.InfoWindow();
  var marker, i;
  for (i = 1; i < locations.length - 1; i++) {
      marker = new google.maps.Marker({
          position: new google.maps.LatLng(locations[i][2], locations[i][3]),
          map: map2,
          icon: './assets/images/nearby.png'
      });
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
          return function() {
              infowindow.setContent("<span style='font-weight:bold;font-size:18px'>" + locations[i][0] + "</span><br>" + locations[i][1] + "<br><div style='margin:10px 0 0 0'><a class='btn-sm btn-primary' style='line-height:2.5!important;font-weight:500;font-size:14px' href='facility.php?id=" + locations[i][4] + "'>View Facility Details</a></div>");
              infowindow.open(map2, marker);
          }
      })(marker, i));
  }
</script>