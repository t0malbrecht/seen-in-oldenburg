<?php
include 'database/database.php';
$database = new SQLiteDB();

if(!isset($_SESSION)){
    session_start();
}

if(isset($_POST["text"]))
    $articlesToShow = $database->getSearchResults($_POST["text"]);
else
    $articlesToShow = $database->getAllArticles();

?>
<!doctype html>
<html>

<head>
    <title>Blog | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
    <meta name="keywords"
          content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <noscript><style> .jsRequired { display: none } </style></noscript>

    <?php include 'header.php'; ?>

    <?php include 'banner.php'; ?>

    <div class="container-fluid my-3">
        <?php if (isset($_GET['msg'])) {
                if ($_GET['msg'] == 'deleteSuccess') { ?>
                    <p class="center margin-top-bottom-05rem border-box-green">Ihr Artikel wurde erfolgreich gelöscht!</p>
                <?php } else if ($_GET['msg'] == 'deleteFailed') { ?>
                    <p class="red-text center margin-top-bottom-05rem border-box-red">Beim löschen der Bilder ist etwas schief gelaufen! Artikel wurde dennoch entfernt.</p>
               <?php }
            }
            ?>
        <div class="row content">
            <div class="col-md-4 col-xs-12 margin-top-15">
                <form class="form-group" action="blog.php" method="POST" onsubmit="return false">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" name="text" placeholder="Seename..."
                               aria-label="Seename..." aria-describedby="basic-addon1" onkeyup="search(this.value)">
                        <noscript><input class="btn btn-danger ml-1" type="submit" value="Suchen"></noscript>
                    </div>
                </form>
            </div>
        </div>
        <div class="row py-4 content">
            <div class="col-sm-9 margin-left-15">
                <div class="livesearch hide" id="livesearch-id"> </div>
                <?php
                if(isset($articlesToShow) && count($articlesToShow)>0) :
                    foreach ($articlesToShow as $article) :
                        $averageRating = $database->getAverageRating($article->id);
                        if($averageRating == null) $averageRating = 0;
                        $filtersAsString = "";
                        if ($article->badesee == 1) $filtersAsString .= "Badesee";
                        if ($article->angelsee == 1) $filtersAsString .= " Angelsee";
                        if ($article->hundestrand == 1) $filtersAsString .= " Hundestrand";
                        if ($article->wc == 1) $filtersAsString .= " WC / Duschen";
                        if ($article->grillen == 1) $filtersAsString .= " Grillen";
                        if ($article->wlan == 1) $filtersAsString .= " WLAN";

                        $articlePreviewPicture = count($article->pictures) > 0 && file_exists(($article->pictures)[0]) ? $article->pictures[0] : "img\alt\alternative.png";

                        ?>
                        <article>
                            <div class="no-search card px-3 py-3 mb-3 ">
                                <div class="row">
                                    <div class="col-md-6 col-xs-12 my-2">
                                        <h4><?php echo htmlspecialchars($article->title) ?></h4>
                                    </div>

                                    <div class="col-md-4 col-xs-12 text-right-desktop margin-top-bottom-05rem">
                                        Niedersachsen <img src="img/germany_icon.png" alt="Deutschland Flagge" width="18"
                                                           height="13">
                                    </div>

                                    <div class="col-md-2 col-xs-12 text-right-desktop margin-top-bottom-05rem">
                                        <?php for ($i = 0; $i<floor($averageRating);$i++) { echo htmlspecialchars("⭐"); } ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-xs-12 my-2">
                                        <img class="img-fluid" src="<?php echo $articlePreviewPicture ?>" alt="<?php echo htmlspecialchars($article->lakename) ?>">
                                    </div>
                                    <div class="col-md-6 col-xs-12 my-2">
                                        <p><?php echo htmlspecialchars($article->description) ?></p>
                                        <a href="blog_detail.php?id=<?php echo htmlspecialchars($article->id) ?>" class="red-text">Mehr erfahren...</a>
                                    </div>
                                    <div class="col-md-3 col-xs-12 text-right my-2">
                                        <p class="keywords">
                                            <?php 
                                                if($filtersAsString != "") {
                                                    $filter_array = explode(" ", $filtersAsString);
                                                    foreach($filter_array as $filter) {
                                                        switch($filter) {
                                                            case "Badesee": ?>
                                                                <img class="ml-2" title="Badesee" src="img/icons/swimming.png" alt="Badesee" />
                                                            <?php break;
                                                            case "Angelsee": ?>
                                                                <img class="ml-2" title="Angelsee" src="img/icons/fishing-rod.png" alt="Angelsee" />
                                                            <?php break;
                                                            case "Hundestrand": ?>
                                                                <img class="ml-2" title="Hundestrand" src="img/icons/dog.png" alt="Hundestrand" />
                                                            <?php break;
                                                            case "WC": ?>
                                                                <img class="ml-2" title="WC / Duschen vorhanden" src="img/icons/shower.png" alt="WC / Dusche" />
                                                            <?php break;
                                                            case "Grillen": ?>
                                                                <img class="ml-2" title="Grillen möglich" src="img/icons/grill.png" alt="Grillen" />
                                                            <?php break;
                                                            case "WLAN": ?>
                                                                <img class="ml-2" title="freies WLAN" src="img/icons/wifi.png" alt="WLAN" />
                                                            <?php break;
                                                        }
                                                    }
                                                }
                                            ?>
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </article>
                    <?php
                    endforeach;
                else :
                    ?>
                    <p class="no-search">Keine Suchergebnisse gefunden</p>
                <?php
                endif;
                ?>

            </div>
            <div class="col content jsRequired">
                <h3 class="border-bottom-red">Filter</h3>
                <ul>
                    <li class="border-bottom-dotted-grey"><input type="checkbox" class="hide" id="badesee">
                        <label for="badesee" class="filter-list">Badesee</label></li>
                    <li class="border-bottom-dotted-grey"><input type="checkbox" class="hide" id="angelsee">
                        <label for="angelsee" class="filter-list">Angelsee</label></li>
                    <li class="border-bottom-dotted-grey"><input type="checkbox" class="hide" id="hundestrand">
                        <label for="hundestrand" class="filter-list">Hundestrand</label></li>
                    <li class="border-bottom-dotted-grey"><input type="checkbox" class="hide" id="wc">
                        <label for="wc" class="filter-list">WC / Duschen</label></li>
                    <li class="border-bottom-dotted-grey"><input type="checkbox" class="hide" id="grillen">
                        <label for="grillen" class="filter-list">Grillen erlaubt</label></li>
                    <li class="border-bottom-dotted-grey"><input type="checkbox" class="hide" id="wlan">
                        <label for="wlan" class="filter-list">WLAN</label></li>
                </ul>
            </div>
        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="js/main.js"></script>

    </body>

</html>