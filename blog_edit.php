<?php
    include 'database/database.php';
    $database = new SQLiteDB();

    if(!isset($_SESSION)){
        session_start();
    }

    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    if(!isset($_SESSION['name'])){
        header("Location: login.php");

    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' ||  $_SERVER['REQUEST_METHOD'] === 'POST') {
            
            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                //bei illegalem Zugriff
                header("Location: index.php");

            }

            if(isset($_POST['submit'])) {

                $formBoolean = (isset($_POST["title"]) && is_string($_POST["title"])) &&
                    (isset($_POST["lakename"]) && is_string($_POST["lakename"])) &&
                    (isset($_POST["coordinates"]) && is_string($_POST["coordinates"])) &&
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

                    //TODO Bilder-Validierung
                    $readOnlyForPictures = $database->getArticleByID($_GET['id']);
                    $oldPictures = $readOnlyForPictures->pictures;


                    //Delete old pictures
                    if($_POST['imagesToDelete'] != "") {
                        foreach ($_POST["imagesToDelete"] as $value) {
                            if (($key = array_search($value, $oldPictures)) !== false) {
                                unset($oldPictures[$key]);
                                if (file_exists($value)) unlink($value);
                            }
                        }
                        $database->deleteArticlePictures($_GET["id"], $_POST['imagesToDelete']);
                    }

                    
                    

                    if ($_FILES["pictures"]["tmp_name"][0] != "") {
                        $index = 0;
                        foreach ($_FILES["pictures"]["tmp_name"] as $tmp_file) {
                            //Allow only images
                            if (startsWith($_FILES["pictures"]["type"][$index], "image/")) {
                                $pictureExtension = explode(".", $_FILES["pictures"]["name"][$index])[1];
                                $picturePath = $database->findUnusedPicturePath($pictureExtension);
                                if (move_uploaded_file($tmp_file,
                                    $picturePath)) {
                                    array_push($pictures, $picturePath);
                                    header("Location: blog_add.php?msg=pictureUploadSuccess");
                                } else {
                                    header("Location: blog_add.php?msg=pictureUploadFailed");
                                }
                            } else {
                                header("Location: blog_add.php?msg=wrongFileExtension");
                            }
                            $index++;
                        }
                    }

                    $database->updateArticle($_GET["id"], $_POST["title"], $_POST["lakename"], $_POST["coordinates"], array_merge_recursive($pictures, $oldPictures), $_POST["description"],
                        $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $_SESSION["name"]);
                    header("Location: blog_detail.php?id=" . $_GET['id']);
                } else {
                    header("Location: blog_edit.php?".$_GET["id"]."&msg=emptyFields");

                }
            } else if (isset($_POST['delete'])) {
                $deleteSuccess = $database->deleteArticleRow(htmlspecialchars($_GET['id']));
                if($deleteSuccess){
                    echo "ich bin hier";
                    header("Location: blog.php?msg=deleteSuccess");
                } else {
                    header("Location: blog.php?msg=deleteFailed");
                }

                
            } else {
                $entry = $database->getArticleByID($_GET['id']);

                if($entry->id != $_GET['id']) {
                    //wenn Artikel nicht existiert
                    header("Location: blog.php");

                } else if (!($_SESSION['name'] === $entry->author)) {
                    // wenn Nutzer nicht der Autor des Artikels
                    header("Location: blog_detail.php?id=".$_GET['id']);
                }

                $pictures = $entry->pictures;
        }

        } else {
            header("Location: index.php");
        }
    }
?>



<!doctype html>
<html>

<head>
    <title>Beitrag bearbeiten | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
    <meta name="keywords"
        content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <noscript><style> .jsRequired { display: none } </style></noscript>

    <?php include 'header.php'; ?>


    <div class="container my-5">
        <section>
            <h1 class="text-center">Beitrag bearbeiten</h1>
            <?php
            if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'emptyFields') { ?>
            <p class="red-text center margin-top-bottom-05rem border-box-red">Bitte füllen Sie alle Felder aus!</p>
            <?php }} ?>

            <form action="blog_edit.php?id=<?php echo htmlspecialchars($_GET["id"]); ?>" class="jsRequired" method="post" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="imagesToDelete" value="" id="imagesToDelete">
                <div class="form-group">
                    <label for="title">Titel</label>
                    <input type="text" class="form-control " name="title" id="title" value="<?php print htmlspecialchars($entry->title); ?>" required>
                </div>

                <div class="form-group">
                    <label for="lakename">Seename</label>
                    <input type="text" class="form-control" name="lakename" id="lakename" value="<?php print htmlspecialchars($entry->lakename); ?>" required>
                </div>

                <div class="form-group">
                    <label for="coordinates">Koordinaten</label>
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <input type="text" class="form-control" name="coordinates" id="coordinates" value="<?php print htmlspecialchars($entry->coordinates); ?>"
                        readonly>
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

                <!---- Snippet aus https://bootsnipp.com/snippets/P2gor ---->
                <h4>Bilder</h4>
                <div class="container custom-container">
                    <div class="row">
                        <div class="row">

                            <?php foreach ($pictures as $picture_path) : ?>
                                <div class="col-lg-3 col-md-4 col-xs-6 thumb">
                                    <a class="thumbnail slider" href="#" data-image-id="" data-toggle="modal" data-title=""
                                        data-image="<?php echo htmlspecialchars($picture_path) ?>" data-target="#image-gallery">
                                        <img class="img-thumbnail" src="<?php echo htmlspecialchars($picture_path) ?>" alt="Another alt text">
                                    </a>
                                    <button type="button" class="close top-right close-button" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endforeach;  ?>
                        </div>
                    </div>
                </div>
                <!-------------------------------------------------------------------------------------------->
                <div class="form-group">
                    <input type="file" class="custom-file" name="pictures[]" id="pictures" multiple="multiple">
                </div>

                <div class="form-group">
                    <label for="description">Beschreibung</label>
                    <textarea name="description" class="form-control" id="description" cols="30" rows="10"
                        required><?php echo htmlspecialchars($entry->description); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Filter</label>
                    <label class="btn btn-secondary my-2 filter-checkbox <?php echo htmlspecialchars($entry->badesee==1 ?  'active':  ''); ?>"><input class="hide" type="checkbox" name="filter[]"
                            value="badesee" <?php echo htmlspecialchars($entry->badesee==1 ?  'checked':  '');?> > Badesee</label>
                    <label class="btn btn-secondary my-2 filter-checkbox <?php echo htmlspecialchars($entry->angelsee==1 ? 'active':  ''); ?>"><input class="hide" type="checkbox" name="filter[]"
                            value="angelsee" <?php echo htmlspecialchars($entry->angelsee==1 ?  'checked':  '');?> > Angelsee</label>
                    <label class="btn btn-secondary my-2 filter-checkbox <?php echo htmlspecialchars($entry->hundestrand==1 ? 'active':  ''); ?>"><input class="hide" type="checkbox" name="filter[]"
                            value="hundestrand" <?php echo htmlspecialchars($entry->hundestrand==1 ?  'checked':  '');?> > Hundestrand</label>
                    <label class="btn btn-secondary my-2 filter-checkbox <?php echo htmlspecialchars($entry->wc==1 ? 'active':  ''); ?>"><input class="hide" type="checkbox" name="filter[]"
                            value="wcduschen" <?php echo htmlspecialchars($entry->wc==1 ?  'checked':  '');?> > WC/Duschen</label>
                    <label class="btn btn-secondary my-2 filter-checkbox <?php echo htmlspecialchars($entry->grillen==1 ? 'active':  ''); ?>"><input class="hide" type="checkbox" name="filter[]"
                            value="grillen" <?php echo htmlspecialchars($entry->grillen==1 ?  'checked':  '');?> > Grillen erlaubt</label>
                    <label class="btn btn-secondary my-2 filter-checkbox <?php echo htmlspecialchars($entry->wlan==1 ? 'active':  ''); ?>"><input class="hide" type="checkbox" name="filter[]"
                            value="wlan" <?php echo htmlspecialchars($entry->wlan==1 ?  'checked':  '');?> > WLAN</label>
                </div>
                <div class="btn-group" role="group" >
                    <input type="submit" class="btn btn-danger" name="submit" value="Übernehmen">
                    <input type="submit" class="btn btn-danger" name="delete" value="Löschen">
                </div>

            </form>
            <noscript>
                <p class="red-text center margin-top-bottom-05rem border-box-red">
                    Bitte aktivieren Sie JavaScript, sonst können Sie keine Artikel bearbeiten!
                </p>
            </noscript>
        </section>
    </div>

    <script>
        $(":checkbox").on('click', function(){
            $(this).parent().toggleClass("active");
        });
    </script>

    <script src="js/main.js"></script>

    <?php include 'footer.php'; ?>
    </body>

</html>