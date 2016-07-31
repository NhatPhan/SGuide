<?php
session_start();
date_default_timezone_set('Asia/Singapore');
require_once 'facebook-sdk-v5/autoload.php';
$location = "facility.php?id=" . $_SESSION["facility_id"] . "#testimonials";
$fb = new Facebook\Facebook([
		'app_id' => '833074496790664',
		'app_secret' => '072dab28222aee891681629a1784c481',
		'default_graph_version' => 'v2.5'
]);
$helper = $fb->getRedirectLoginHelper();
try {
  $accessToken = $helper->getAccessToken();
}
catch (Facebook\Exceptions\FacebookResponseException $e) {
  session_destroy();
  $_SESSION = array();
  header("Location: " . $location);
  die();
}
catch (Facebook\Exceptions\FacebookSDKException $e) {
  session_destroy();
  $_SESSION = array();
  header("Location: " . $location);
  die();
}
if (isset($accessToken)) {
  // Logged in!
  $_SESSION['facebook_access_token'] = (string) $accessToken;
  
  // Now you can redirect to another page and use the
  // access token from $_SESSION['facebook_access_token']
  header("Location: " . $location);
  die();
} elseif ($helper->getError()) {
  header("Location: " . $location);
  die();
}
?>