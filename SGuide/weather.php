<?php
if(empty($_GET["id"])) {
	include_once 'header.php';
	die("<div class='jumbotron vertical-center'><div class='container'>ERROR: Facility ID is missing</div></div>");
}
$id = $_GET['id'];

//NEA 12hrs Dataset
$url_12hrs = 'http://www.nea.gov.sg/api/WebAPI/?dataset=12hrs_forecast&keyref=781CF461BB6606ADBC7C75BF9D4F60DBD41631D3F2A5EA29';

//PSI
$url_psi = 'http://www.nea.gov.sg/api/WebAPI/?dataset=psi_update&keyref=781CF461BB6606ADBC7C75BF9D4F60DBD41631D3F2A5EA29';

//Get XML
$xml = simplexml_load_string(file_get_contents($url_12hrs));
$xml_psi = simplexml_load_string(file_get_contents($url_psi));

$center_lat =  31067.287760278025;
$center_lng = 35998.27742283312;

$north_lat =  22602.848502001947;
$north_lng = 46505.147214592864;

$south_lat =  26733.649121289596;
$south_lng = 27538.181126624477;

$east_lat =  40225.21784742589;
$east_lng = 37396.20372971447;

$west_lat =  13962.118429407927;
$west_lng = 35755.3567660498;

if (mysqli_connect_errno ()) {
	die("<div class='jumbotron vertical-center'><div class='container'>Connect failed: " . mysqli_connect_error() . "</div></div>");
}

$retrieve = $con->prepare ("SELECT fa_x, fa_y, 3959*acos(cos(radians(?)) * cos(radians(fa_x)) * cos(radians(fa_y) - radians(?)) + sin(radians(?)) * sin(radians(fa_x))) AS distance1,
    (3959*acos(cos(radians(?)) * cos(radians(fa_x)) * cos(radians(fa_y) - radians(?) ) + sin(radians(?)) * sin(radians(fa_x)))) AS distance2,
    (3959*acos(cos(radians(?)) * cos(radians(fa_x)) * cos(radians(fa_y) - radians(?) ) + sin(radians(?)) * sin(radians(fa_x)))) AS distance3,
    (3959*acos(cos(radians(?)) * cos(radians(fa_x)) * cos(radians(fa_y) - radians(?) ) + sin(radians(?)) * sin(radians(fa_x)))) AS distance4,
    (3959*acos(cos(radians(?)) * cos(radians(fa_x)) * cos(radians(fa_y) - radians(?) ) + sin(radians(?)) * sin(radians(fa_x)))) AS distance5 FROM facilities WHERE fa_id = ?");
$retrieve->bind_param("dddddddddddddddi",$center_lat,$center_lng,$center_lat,$north_lat,$north_lng,$north_lat,$south_lat,$south_lng,$south_lat,$east_lat,$east_lng,$east_lat,$west_lat,$west_lng,$west_lat,$id);
$retrieve->execute();
$retrieve->bind_result($fa_x, $fa_y, $dist1, $dist2, $dist3, $dist4, $dist5);
$locations = array();
while($retrieve->fetch()){
	$locations = array($dist1,$dist2,$dist3,$dist4,$dist5);
	$key = array_search(max($locations),$locations);
	switch ($key + 1) {
		case 1:
			$region = 'Central';
			$region_key = 2;
			break;
		case 2:
			$region = 'North';
			$region_key = 0;
			break;
		case 3:
			$region = 'South';
			$region_key = 5;
			break;
		case 4:
			$region = 'East';
			$region_key = 3;
			break;
		case 5:
			$region = 'West';
			$region_key = 4;
			break;
		default:
			break;
	}
}
$retrieve->close();

switch($fa_region) {
	case 'N':
		$region = "North";
		break;
	case 'E':
		$region = "East";
		break;
	case 'W':
		$region = "West";
		break;
	case 'C':
		$region = "Central";
		break;
}
//Extract necessary data
$validityFrom = $xml->item[0]->forecastValidityFrom['date']." ".$xml->item[0]->forecastValidityFrom['time'];
$validityTill = $xml->item[0]->forecastValidityTill['date']." ".$xml->item[0]->forecastValidityTill['time'];;
$forecast = $xml->item[0]->forecast;
$temperatureHigh = $xml->item[0]->temperature['high'];
$temperatureLow = $xml->item[0]->temperature['low'];
$relativeHumidityHigh = $xml->item[0]->relativeHumidity['high'];
$relativeHumidityLow = $xml->item[0]->relativeHumidity['low'];
$psi = $xml_psi->item[0]->region[$region_key]->record[0]->reading[0]['value'];
$forecast = trim($forecast);
$forecast = str_replace('.','',$forecast);
$forecast = strtolower($forecast);

$imgs = array("cloudy", "fair", "hazy", "overcast", "partial cloudy", "partial sunny", "rain", "thundery showers");
$img = '';

for ($i = 0; $i < count($imgs); $i++) { 
	if (strpos($forecast,$imgs[$i]) !== false) {
    	$img = $imgs[$i];
    	break;
	}
}

$forecast = wordwrap($forecast, 50, "<br />\n");

?>
<div class="col-md-12 col-lg-12">
  <div class="wow slideInRight" data-wow-delay=".1s">
    <img src="assets/images/weather/<?php echo $img; ?>.png" alt="<?php echo $img; ?>" height="200" width="200" />
  </div>
  <div class="wow slideInRight" data-wow-delay=".2s" style="margin-top:-10px">
    <h2><?php echo $forecast; ?></h2>
    <h2><?php echo $temperatureLow.'-'.$temperatureHigh.' °C' ?></h2>
    <h3>Humidity: <?php echo $relativeHumidityLow.'-'.$relativeHumidityHigh.'%' ?></h3>
    <h3>PSI: <?php echo $psi ?></h3>
    <h3>Region: <?php echo $region ?></h3>
  </div>
</div>