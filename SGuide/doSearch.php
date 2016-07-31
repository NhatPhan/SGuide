<?php
if (!isset($_POST['keyword'])) {
    header("Location: index.php");
    die();
}
define('INCLUDE_FILE', true);
define("PAGE_TITLE", "Search Results - SGuide");
require_once 'config.php';
$keyword = $con->real_escape_string($_POST['keyword']);
$keywordList = explode(" ", $keyword);
$filters = ["Museum", "Hotels", "Parks", "Tourist Attractions", "Heritage Sites", "Monuments", "N", "E", "W", "C"];
$regions = ["N" => " | North", "E" => " | East", "W" => " | West", "C" => " | Central", "" => ""];
//no filter
if (!isset($_POST["reset"]) && !isset($_POST["filter"])) {
    for ($i = 0; $i < 10; $i++) {
        $check[$i] = "checked";
    }
} else {
    //after reset
    if (isset($_POST["reset"])) {
        for ($i = 0; $i < 10; $i++) {
            $check[$i] = "checked";
        }
    } else {
        //after filter
        $formCheck = $_POST["formCheck"];
        for ($i = 0; $i < 10; $i++) {
            if (in_array($filters[$i], $formCheck)) {
                $check[$i] = "checked";
            } else {
                $check[$i] = "";
            }
        }
    }
}
?>
<html lang="en">
    <head>
        <?php include_once 'header.php'; ?>
        <script type="text/javascript">
            function validateFilter(form)
            {
                error = "";
                var checked = document.querySelectorAll('input:checked');
                if (checked.length === 0) {
                    error = "You didn't select any filter!";
                }

                if (error !== "")
                {
                    alert(error);
                    return false;
                }
                return true;
            }
        </script>
        <style type="text/css">
            .fa{padding-top:7px;}
            a:hover{color:#BF0E07;}
            .category{font-weight:500;font-size:12px;color:#C7C7C7;letter-spacing:0.1em;}
            .fa-name{font-weight:500;font-size:22px;margin:0;}
            .fa-desc{font-weight:500}
            .fa-location{font-size:12px;margin-top:15px;}
        </style>
    </head>
    <body>
        <div id="google_translate_element"></div>
        <div id="wrapper">
            <!--//https://ironsummitmedia.github.io/startbootstrap-simple-sidebar/#-->
            <div id="sidebar-wrapper">
                <form action="doSearch.php" method="POST" name="filterForm" onSubmit="return validateFilter(this);">
                    <ul class="sidebar-nav">
                        <li class="sidebar-brand">            
                            <span style="font-size:90%"><b>Filter Category</b></span>                   
                        </li>
                        <li style="margin-top:5px;">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="formCheck[]" value="Museum" <?php echo $check[0]; ?>>Museum
                                </label>
                            </div>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="Hotels" <?php echo $check[1]; ?>>Hotels
                            </label>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="Parks" <?php echo $check[2]; ?>>Parks
                            </label>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="Tourist Attractions" <?php echo $check[3]; ?>>Tourist Attractions
                            </label>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="Heritage Sites" <?php echo $check[4]; ?>>Heritage Sites
                            </label>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="Monuments" <?php echo $check[5]; ?>>Monuments
                            </label>
                        </li>
                        <li class="sidebar-brand">            
                            <span style="font-size:90%"><b>Filter Region</b></span>                     
                        </li>
                        <li style="margin-top:5px;">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="formCheck[]" value="N" <?php echo $check[6]; ?>>North
                                </label>
                            </div>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="E" <?php echo $check[7]; ?>>East
                            </label>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="W" <?php echo $check[8]; ?>>West
                            </label>
                        </li>
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="formCheck[]" value="C" <?php echo $check[9]; ?>>Central
                            </label>
                        </li>
                        <li>
                            <input class="btn btn-primary" type="submit" name="filter" value="Filter" style="background-color:#222">
                            <input class="btn btn-primary" type="submit" name="reset" value="Reset" style="background-color:#222">
                        </li>
                    </ul>
                    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
                </form>
            </div>
            <section id="topLayer" class="pfblock pfblock-gray" style="padding:30px 20px;min-height:100vh;padding-bottom:105px">
                <div class="container-fluid">
                    <div class="row" style="display:inline;vertical-align:middle">
                        <form action="doSearch.php" method="POST" class="form-inline">
                            <div style="margin:0 8px 8px 0;display:inline-block" class="pull-left">
                                <a href="index.php" alt="Click here to go back Home" title="Click here to go back Home">
                                    <div class="homeBtn"><i class="fa fa-home" style="color:#fff;font-size:21px;padding:0"></i></div>
                                </a>
                            </div>
                            <div class="col-md-5 col-lg-5 col-sm-12" style="padding-left:0;display:inline-block">
                                <input type="text" class="form-control input-lg" name="keyword" id="keyword" value="<?php echo $keyword; ?>" style="margin-bottom:8px;width:100%;font-weight:500" autocomplete="off" placeholder="Type keywords here..." required="true" maxlength="50">
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12" style="padding-left:0;display:inline-block">
                                <input class="btn btn-lg" type="submit" value="Search" style="margin-bottom:8px;font-weight:500">&nbsp;
                                <a href="#topLayer" onclick="this.blur();" class="btn btn-primary" style="padding:10px 25px;height:37px;margin-bottom:8px;font-weight:500" id="menu-toggle"><i class="fa fa-filter" style="padding:0"></i>&nbsp; Toggle Filter</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 col-lg-12" style="margin-top:20px">
                            <?php
                            if (mysqli_connect_errno()) {
                                die("<div class='jumbotron vertical-center'><div class='container'>Connect failed: " . mysqli_connect_error() . "</div></div>");
                            } else {
                                if (count($keywordList) > 1) {
                                    $others = "";
                                    $others2 = "";
                                    for ($i = 1; $i < count($keywordList); $i++) {
                                        $others .= "AND (fa_name LIKE '%$keywordList[$i]%')";
                                        $others2 .= "AND (fa_description LIKE '%$keywordList[$i]%'OR fa_buildingName LIKE '%$keywordList[$i]%' OR
                               fa_floorNumber LIKE '%$keywordList[$i]%' OR fa_postalCode LIKE '%$keywordList[$i]%' OR fa_streetName LIKE '%$keywordList[$i]%' OR
                               fa_unitNumber LIKE '%$keywordList[$i]%' OR fa_houseNumber LIKE '%$keywordList[$i]%' OR fa_category LIKE '%$keywordList[$i]%'OR 
                               fa_region LIKE '%$keywordList[$i]%')";
                                    }
                                    $condition = "WHERE (fa_name LIKE '%$keywordList[0]%')$others";

                                    $condition2 = "WHERE (fa_name LIKE '%$keywordList[0]%'OR fa_description LIKE '%$keywordList[0]%'OR fa_buildingName LIKE '%$keywordList[0]%' OR
                               fa_floorNumber LIKE '%$keywordList[0]%' OR fa_postalCode LIKE '%$keywordList[0]%' OR fa_streetName LIKE '%$keywordList[0]%' OR
                               fa_unitNumber LIKE '%$keywordList[0]%' OR fa_houseNumber LIKE '%$keywordList[0]%' OR fa_category LIKE '%$keywordList[0]%'OR 
                               fa_region LIKE '%$keywordList[0]%')$others2";
                                } else {
                                    $condition = "WHERE fa_name LIKE '%$keyword%'";

                                    $condition2 = "WHERE fa_description LIKE '%$keyword%'OR fa_buildingName LIKE '%$keyword%' OR
                               fa_floorNumber LIKE '%$keyword%' OR fa_postalCode LIKE '%$keyword%' OR fa_streetName LIKE '%$keyword%' OR
                               fa_unitNumber LIKE '%$keyword%' OR fa_houseNumber LIKE '%$keyword%' OR fa_category LIKE '%$keyword%'OR 
                               fa_region LIKE '%$keyword%'";
                                }

                                $retrieve = $con->prepare("SELECT fa_id, fa_name, fa_description, fa_category, fa_buildingName,
                               fa_floorNumber, fa_postalCode, fa_streetName, fa_unitNumber, fa_houseNumber, fa_region FROM facilities                           
                               $condition");

                                $retrieve2 = $con->prepare("SELECT fa_id, fa_name, fa_description, fa_category, fa_buildingName,
                               fa_floorNumber, fa_postalCode, fa_streetName, fa_unitNumber, fa_houseNumber, fa_region FROM facilities                           
                               $condition2");

                                $retrieve->execute();
                                $retrieve->bind_result($faId, $name, $description, $category, $bldg, $floor, $postal, $street, $unit, $hseNo, $region);

                                $faList = array();

                                $count = 0;
                                $location = "";

                                while ($row = $retrieve->fetch()) {
                                    array_push($faList, $faId);

                                    $location = "";
                                    if (!empty($hseNo)) {
                                        $location .= $hseNo . " ";
                                    }
                                    $location .= $street;
                                    if (!empty($bldg)) {
                                        $location .= ", " . $bldg;
                                    }
                                    if (!empty($floor) || !empty($unit)) {
                                        $location .= ", ";
                                    }
                                    if (!empty($floor)) {
                                        $location .= "Level " . $floor;
                                        if (!empty($unit))
                                            $location .= ", ";
                                    }
                                    if (!empty($unit)) {
                                        $location .= "Unit " . $unit;
                                    }
                                    if (!empty($postal)) {
                                        $location .= ", Singapore " . $postal;
                                    }
                                    if (!empty($name)) {
                                        //after filter 
                                        if (isset($formCheck)) {
                                            if (in_array($category, $formCheck) && in_array($region, $formCheck)) {
                                                ?>
                                                <div>
                                                    <p class="fa-name"><a href="facility.php?id=<?php echo $faId; ?>"><?php echo $name; ?></a></p>
                                                    <p class="category text-uppercase"><?php echo $category . $regions[$region]; ?></p>
                                                    <p class="fa-desc"><?php echo $description; ?></p>
                                                    <p class="fa-location"><?php echo $location; ?></p>
                                                    <hr>
                                                </div>
                                                <?php
                                                $count++;
                                            }
                                        } else {
                                            ?>
                                            <div>
                                                <p class="fa-name"><a href="facility.php?id=<?php echo $faId; ?>"><?php echo $name; ?></a></p>
                                                <p class="category text-uppercase"><?php echo $category . $regions[$region]; ?></p>
                                                <p class="fa-desc"><?php echo $description; ?></p>
                                                <p class="fa-location"><?php echo $location; ?></p>
                                                <hr>
                                            </div>
                                            <?php
                                            $count++;
                                        }
                                    }
                                }
                                $retrieve->close();

                                $retrieve2->execute();
                                $retrieve2->bind_result($faId, $name, $description, $category, $bldg, $floor, $postal, $street, $unit, $hseNo, $region);

                                $count2 = 0;
                                $location = "";

                                while ($row = $retrieve2->fetch()) {
                                    if (!in_array($faId, $faList)) {


                                        $location = "";
                                        if (!empty($hseNo)) {
                                            $location .= $hseNo . " ";
                                        }
                                        $location .= $street;
                                        if (!empty($bldg)) {
                                            $location .= ", " . $bldg;
                                        }
                                        if (!empty($floor) || !empty($unit)) {
                                            $location .= ", ";
                                        }
                                        if (!empty($floor)) {
                                            $location .= "Level " . $floor;
                                            if (!empty($unit))
                                                $location .= ", ";
                                        }
                                        if (!empty($unit)) {
                                            $location .= "Unit " . $unit;
                                        }
                                        if (!empty($postal)) {
                                            $location .= ", Singapore " . $postal;
                                        }
                                        if (!empty($name)) {
                                            //after filter 
                                            if (isset($formCheck)) {
                                                if (in_array($category, $formCheck) && in_array($region, $formCheck)) {
                                                    ?>
                                                    <div>
                                                        <p class="fa-name"><a href="facility.php?id=<?php echo $faId; ?>"><?php echo $name; ?></a></p>
                                                        <p class="category text-uppercase"><?php echo $category . $regions[$region]; ?></p>
                                                        <p class="fa-desc"><?php echo $description; ?></p>
                                                        <p class="fa-location"><?php echo $location; ?></p>
                                                        <hr>
                                                    </div>
                                                    <?php
                                                    $count2++;
                                                }
                                            } else {
                                                ?>
                                                <div>
                                                    <p class="fa-name"><a href="facility.php?id=<?php echo $faId; ?>"><?php echo $name; ?></a></p>
                                                    <p class="category text-uppercase"><?php echo $category . $regions[$region]; ?></p>
                                                    <p class="fa-desc"><?php echo $description; ?></p>
                                                    <p class="fa-location"><?php echo $location; ?></p>
                                                    <hr>
                                                </div>
                                                <?php
                                                $count2++;
                                            }
                                        }
                                    }
                                }
                                $retrieve2->close();

                                
                                if ($count == 0 && $count2 == 0) {
                                    echo("<div class='alert alert-info' style='font-weight:500'>No facility found. Please try refining your search term or filter.</div>");
                                } else {
                                    echo("<p style='padding:30px 0;font-weight:500'><i>" . ($count + $count2) . " results found</i></p>");
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div style="margin-top:-105px">
            <?php include_once 'footer.php'; ?>
        </div>
        <script>
            $("#menu-toggle").click(function (e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
        </script>
        <script type="text/javascript">
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
            }
        </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    </body>
</html>