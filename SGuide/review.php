<?php
if (empty($_GET["id"])) {
  include_once 'header.php';
  die("<div class='jumbotron vertical-center'><div class='container'>ERROR: Facility ID is missing</div></div>");
}
$success = false;
$report  = false;
?>
<script type="text/javascript">
var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
function getId(elem) { var x = elem.id; return x; }
function confirmReport(el) {
  var r = confirm('Are you sure you want to report this review as spam?');
  if (r == true) {
	document.getElementById('report').value = getId(el);
	document.getElementById('reviewForm').submit();
  }
  return false;
}
</script>
<section id="testimonials" class="pfblock pfblock-gray">
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-sm-offset-2">
        <div class="pfblock-header wow fadeInUp">
          <h2 class="pfblock-title">Guest Reviews</h2>
          <div class="pfblock-line"></div>
          <div class="pfblock-subtitle">
            Leave a review for this place to share the love with other
            visitors!<br> Find out what others have to say and discover more.
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-8 col-sm-offset-2">
        <?php
          if(!isset($re_username)) {
          include_once 'login.php';
          } else {
        ?>
        <form action="facility.php?id=<?php echo $_GET["id"] ?>#testimonials" id="reviewForm" method="POST" role="form">
          <div class="ajax-hidden">
            <div class="form-group wow fadeInUp" data-wow-delay=".1s">
              <div class="form-control btn btn-facebook"><i class='fa fa-facebook'></i> | <?php echo $re_username; ?></div>
            </div>
            <div class="form-group wow fadeInUp" data-wow-delay=".2s">
              <textarea class="form-control" id="reviewContent" name="reviewContent" rows="7" placeholder="Write review here..." style="font-weight:500;font-size:16px;" required="true" maxlength="1000"><?php if(!isset($re_username) && isset($_POST['add_review'])){echo $_POST["reviewContent"];}?></textarea>
            </div>
            <button type="submit" class="btn btn-lg btn-block wow fadeInUp" data-wow-delay=".3s" name="add_review">Add Review</button>
            <input type="hidden" name="report" id="report" value="" />
          </div>
        </form>
        <?php 
          } ?>
      </div>
    </div>
    <div class="row row-centered">
      <div class="col-lg-12" style="margin-top:20px;padding:0 3%">
        <div class="cbp-qtrotator cbp-qtcontent">
          <?php
            if(mysqli_connect_errno()) {
            	echo "<div class='alert alert-danger'>Connect failed: " . mysqli_connect_error() . "</div><br>";
            } else {
            	/* process review reported as spam */
            	if (isset($_POST['report']) && !empty($_POST['report'])){
            		/* prepare, bind and execute SQL statement */
            		$update = $con->prepare ("UPDATE reviews SET re_flag = 1 WHERE re_id = ?");
            		$update->bind_param("i", $_POST['report']);
            		$result = $update->execute();
            		if (!$result) {
            			echo "<div class='alert alert-danger'>Execute failed: (" . $insert->errno . ") " . $insert->error . "</div><br>";
            		} else {
            			echo "<div class='row row-centered'><div class='col-sm-8 col-centered' style='padding:0'><div class='alert alert-info' style='text-align:center'>Review marked as SPAM. We will moderate the review. Thank you.</div></div></div><br>";
            		}
            		$update->close();
            	}
            
            	/* process adding of review */
            	elseif(isset($_POST['add_review'])) {
            		if(empty($_POST["reviewContent"])) {
            			echo "<div class='alert alert-danger'>ERROR: Please write a review first before submitting!</div><br>";
            		}
            		elseif(!isset($re_username)) {
            			echo "<div class='alert alert-danger'>Facebook Error</div><br>";
            		}
            		else {
            			$reviewContent = $_POST["reviewContent"];
            			$reviewAuthor = $re_username;
            			
            			$insert = $con->prepare("INSERT INTO reviews (re_username, re_description, facility_id) VALUES (?, ?, ?)");
            			$insert->bind_param("ssi", $reviewAuthor, $reviewContent, $_GET["id"]);
            			$result = $insert->execute();
            			if (!$result) {
            				echo "<div class='row row-centered'><div class='col-sm-8 col-centered' style='padding:0'><div class='alert alert-danger'>Execute failed: (" . $insert->errno . ") " . $insert->error . "</div></div></div><br>";
            			} else {
            				echo "<div class='row row-centered'><div class='col-sm-8 col-centered' style='padding:0'><div class='alert alert-success' style='text-align:center'>Review added successfully!</div></div></div><br>";
            			}
            			$insert->close();
            		}
            	}
            	$reviews = "";
            	$retrieve = $con->prepare("SELECT re_id, re_username, re_description, re_datetime FROM reviews WHERE re_flag = 0 AND facility_id = ? ORDER BY re_id DESC");
            	$search = $_GET["id"];
            	$retrieve->bind_param("i", $search);
            	$retrieve->execute();
            	$retrieve->bind_result($id, $username, $description, $datetime);
            	while ($row = $retrieve->fetch()){
            		?>
          <blockquote>
            <p><?php echo $description; ?></p>
            <footer>
              <?php echo trim($username); ?>
              <script>
              var t1 = "<?php echo $datetime; ?>";
              t1 = t1.split(/[- :]/);
              var rDate = new Date(t1[0], t1[1] - 1, t1[2], t1[3], t1[4], t1[5]);
              rDate = moment(rDate);
              </script>
              <div class="pull-right"><i><script>document.write(moment(rDate).calendar());</script></i>&nbsp;&nbsp;&nbsp;
                <a class="btn btn-default btn-xs" style="border:1px solid #e8e8e8;padding:5px 8px;" id="<?php echo $id; ?>" onclick="confirmReport(this)">Report as Spam</a>
              </div>
              <br>
            </footer>
            <br><br>
          </blockquote>
          <?php
                }
              $retrieve->close();
              if (empty($username)) {
            	  echo "<p style='text-align:center;font-weight:500;font-size:18px;padding:40px 0;'>Be the first to leave a review!</p>";
              }
            }
            ?>
        </div>
      </div>
    </div>
  </div>
</section>