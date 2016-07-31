<?php
$helper   = $fb->getRedirectLoginHelper();
$loginUrl = $helper->getLoginUrl('http://localhost/SGuide/login-callback.php');
?>
<center><button onclick="location.href='<?php echo $loginUrl; ?>'" class="btn btn-facebook"><i class="fa fa-facebook"></i> | Login with Facebook<script>if(w > 440) {document.write(" to write a review");}</script></button></center>