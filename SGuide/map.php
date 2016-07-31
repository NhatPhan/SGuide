<?php
  if(empty($_GET["id"]) || empty($lat) || empty($lng)) {
  	include_once 'header.php';
  	die("<div class='jumbotron vertical-center'><div class='container'>ERROR: Facility ID / Location is missing</div></div>");
  }
?>
<style>body,html{height:100%;margin:0;padding:0}#map{height:300px}</style>
<script type="text/javascript" src="assets/js/svy21.min.js"></script>
<script type='text/javascript'>
  var cv = new SVY21();
  var resultLatLon = cv.computeLatLon(<?php echo $lng ?>, <?php echo $lat ?>);
  var convert = [];
  for(i in resultLatLon) {
     convert.push(resultLatLon[i]);
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false"></script>
<script>
  var map;
  var marker = null;
  var myLatLng = {lat: convert[0], lng: convert[1]};
  function initialize() {
    var mapOptions = {
      zoom: 16,
      minZoom: 14,
      maxZoom: 25,
      disableDefaultUI: true,
      zoomControl: true,
      zoomControlOptions: {
        style: google.maps.ZoomControlStyle.LARGE 
      },
      center: myLatLng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    var marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      title: "<?php echo $name ?>"
    });
  }
  google.maps.event.addDomListener(window, "load", initialize);
</script>