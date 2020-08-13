<?php
include 'database/database.php';
$database = new SQLiteDB();

function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['name'])) {
    header("Location: login.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $coordinatePatternMatches = preg_match("/^([0-9]+(\\.[0-9]+)),([0-9]+(\\.[0-9]+))?$/", $_POST["coordinates"], $match);

    $formBoolean = (isset($_POST["title"]) && is_string($_POST["title"])) &&
        (isset($_POST["lakename"]) && is_string($_POST["lakename"])) &&
        (isset($_POST["coordinates"]) && is_string($_POST["coordinates"])) && $coordinatePatternMatches &&
        //        (isset($_FILES["pictures"])) &&
        (isset($_POST["description"]) && is_string($_POST["description"])) &&
        (isset($_POST["filter"]) && (is_array($_POST["filter"]) || is_string($_POST["filter"])));

    if ($formBoolean) {
        $pictures = array();
        $badesee = 0;
        $angelsee = 0;
        $hundestrand = 0;
        $wc = 0;
        $grillen = 0;
        $wlan = 0;

        foreach ($_POST["filter"] as $value) {
            if ($value == "badesee") $badesee = 1;
            if ($value == "angelsee") $angelsee = 1;
            if ($value == "hundestrand") $hundestrand = 1;
            if ($value == "wcduschen") $wc = 1;
            if ($value == "grillen") $grillen = 1;
            if ($value == "wlan") $wlan = 1;
        }

        $index = 0;
        foreach ($_FILES["pictures"]["tmp_name"] as $tmp_file) {
            //Allow only images
            if (startsWith($_FILES["pictures"]["type"][$index], "image/")) {
                $pictureExtension = explode(".", $_FILES["pictures"]["name"][$index])[1];
                $picturePath = $database->findUnusedPicturePath($pictureExtension);

                if (isset($picturePath) && move_uploaded_file($tmp_file,
                        $picturePath)) {
                    array_push($pictures, $picturePath);
                } else {
                    header("Location: blog_add.php?msg=pictureUploadFailed");
                }
            } else {
                header("Location: blog_add.php?msg=wrongFileExtension");
            }
            $index++;
        }
        $database->addArticleRow($_POST["title"], $_POST["lakename"], $_POST["coordinates"], $pictures, $_POST["description"],
            $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $_SESSION["name"]);
        header("Location: blog_add.php?msg=pictureUploadSuccess");


    } else {
        header("Location: blog_add.php?msg=emptyFields");
    }
}
?>

<!doctype html>
<html>

<head>
    <title>Beitrag erstellen | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. "/>
    <meta name="keywords"
          content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK"/>
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa"/>
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <noscript><style> .jsRequired { display: none } </style></noscript>

    <?php include 'header.php'; ?>

    <section>
        <div class="container my-5">
            <h1 class="text-center">Beitrag erstellen</h1>
            <?php
            if (isset($_GET['msg'])) {
                if ($_GET['msg'] == 'emptyFields') { ?>
                    <p class="red-text center margin-top-bottom-05rem border-box-red">Bitte füllen Sie alle Felder
                        aus!</p>
                <?php } else if ($_GET['msg'] == 'wrongFileExtension') { ?>
                    <p class="red-text center margin-top-bottom-05rem border-box-red">Sie dürfen nur Bilder
                        hochladen!</p>
                <?php } else if ($_GET['msg'] == 'pictureUploadFailed') { ?>
                    <p class="red-text center margin-top-bottom-05rem border-box-red">Es ist ein Fehler beim
                        Bilderupload aufgetreten!</p>
                <?php } else if ($_GET['msg'] == 'pictureUploadSuccess') { ?>
                    <p class="green-text center margin-top-bottom-05rem border-box-green">Der Blogeintrag wurde
                        erfolgreich eingefügt!</p>
                <?php }
            }
            ?>
            <form action="blog_add.php" class="jsRequired" name="blogAdd" onsubmit="return validateForm()" method="POST"
                  enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titel </label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>

                <div class="form-group">
                    <label for="lakename">Seename </label>
                    <input type="text" class="form-control" name="lakename" id="lakename" required>
                </div>

                <div class="form-group">
                    <label for="coordinates">Koordinaten</label>
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <input placeholder="Koordinaten wie z.B. 12.1242,62.1322" class="form-control" name="coordinates" id="coordinates" readonly>
                </div>

                <script>
                    var marker;
                    var map;

                    function initMap() {
                        var mapOptions      =   {
                            zoom            :   10,
                            mapTypeControl  :   false,
                            center          :   {
                                lat         :   53.14118,
                                lng         :   8.21467
                            },
                            disableDoubleClickZoom : true,
                        };

                        map = new google.maps.Map(document.getElementById('map'), mapOptions);

                        map.addListener("click", function (event) {
                            if(marker)
                                marker.setMap(null);
                            marker = new google.maps.Marker({
                                position: event.latLng,
                                map: map
                            });

                            var latitude = event.latLng.lat();
                            var longitude = event.latLng.lng();

                            // Koordinaten eintragen
                            document.getElementById("coordinates").value = latitude + ',' + longitude;
                        });
                    }

                </script>

                <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBunvE1R3oNYctjl80HnZVHu6PeZTC4eYE&callback=initMap"
                        type="text/javascript"></script>

                <div class="form-group">
                    <label for="pictures">Anzeigebilder</label>
                    <input type="file" class="custom-file" name="pictures[]" id="pictures" multiple="multiple">
                </div>

                <div class="form-group">
                    <label for="description">Beschreibung</label>
                    <textarea name="description" class="form-control" onkeyup="return validateForm()" id="description"
                              cols="30" rows="10" required></textarea>
                    <p id="alert"></p>
                </div>


                <div class="form-group">
                    <label>Filter</label>

                    <label class="btn btn-secondary my-2 filter-checkbox"><input class="hide" type="checkbox"
                                                                                 name="filter[]"
                                                                                 value="badesee"> Badesee</label>
                    <label class="btn btn-secondary my-2 filter-checkbox"><input class="hide" type="checkbox"
                                                                                 name="filter[]"
                                                                                 value="angelsee"> Angelsee</label>
                    <label class="btn btn-secondary my-2 filter-checkbox"><input class="hide" type="checkbox"
                                                                                 name="filter[]"
                                                                                 value="hundestrand">
                        Hundestrand</label>
                    <label class="btn btn-secondary my-2 filter-checkbox"><input class="hide" type="checkbox"
                                                                                 name="filter[]"
                                                                                 value="wcduschen"> WC/Duschen</label>
                    <label class="btn btn-secondary my-2 filter-checkbox"><input class="hide" type="checkbox"
                                                                                 name="filter[]"
                                                                                 value="grillen"> Grillen
                        erlaubt</label>
                    <label class="btn btn-secondary my-2 filter-checkbox"><input class="hide" type="checkbox"
                                                                                 name="filter[]"
                                                                                 value="wlan"> WLAN</label>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-danger" value="Veröffentlichen" name="submit">
                </div>
            </form>
            <noscript>
                <p class="red-text center margin-top-bottom-05rem border-box-red">
                    Bitte aktivieren Sie JavaScript, sonst können Sie keine Artikel erstellen!
                </p>
            </noscript>
        </div>
    </section>

    <script>
        $(":checkbox").on('click', function () {
            $(this).parent().toggleClass("active");
        });
    </script>
    <script src="js/main.js"></script>
    <?php include 'footer.php'; ?>
    </body>

</html>