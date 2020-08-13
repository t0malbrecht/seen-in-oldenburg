<?php
include 'database/database.php';
$database = new SQLiteDB();

if (!isset($_SESSION)) {
    session_start();
}
$articlesToShow = $database->getAllArticles();
?>

<!doctype html>
<html lang="de">

<head>
    <title>Home | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. "/>
    <meta name="keywords"
          content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK"/>
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa"/>
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <?php include 'header.php'; ?>

    <div class="container my-5">

        <!-- <form class="form-group" action="blog.php" method="post">
            <div class="input-group col-lg-8 col-md-8 col-sm-12 col-xs-12 mx-auto">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                </div>
                <input type="text" class="form-control mr-2" placeholder="See, PLZ, Ortsname"
                       aria-label="See, PLZ, Ortsname" aria-describedby="basic-addon1">
                <input class="btn btn-danger mx-auto" type="submit" value="Suchen">
            </div>
        </form> -->


        <section>
            <div>
                <h1 class="h1-dark">Top bewertete Seen in Oldenburg</h1>
                <p>
                    Willkommen bei Seen in Oldenburg!<br>
                    Hier finden Sie die bestbewerteten Seen in der Oldenburger Umgebung.
                </p>
                <p>
                    Unsere Webseite bietet eine Karte mit den jeweiligen Standorten der Seen, die von unseren Benutzern hinzugefügt worden sind!
                    Durchstöbern Sie unsere Webseiten, in dem Sie zum Beispiel die Filterfunktion verwenden, um für Sie den am best passenden See zu finden.
                    Sie kennen einen tollen See in der Nähe? Dann melden Sie sich jetzt an und fügen Sie eine Beschreibung und tolle Bilder hinzu!
                </p>
            </div>
        </section>
        <article>
            <?php
            if (isset($articlesToShow)) :
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
            <div class="card px-3 py-3 mb-3">
                <div class="row">
                    <div class="col-md-6 col-xs-12 my-2">
                        <h4><?php echo htmlspecialchars($article->title); ?></h4>
                    </div>

                    <div class="col-md-4 col-xs-12 text-right-dekstop margin-top-bottom-05rem">
                        Niedersachsen <img src="img/germany_icon.png" alt="Deutschland Flagge" width="18"
                                           height="13">
                    </div>

                    <div class="col-md-2 col-xs-12 text-right-dekstop margin-top-bottom-05rem">
                    <?php for ($i = 0; $i<$averageRating;$i++) { echo htmlspecialchars("⭐"); } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-xs-12 my-2">
                        <!-- TODO funktion zum Bilder iterieren in DB -->
                        <img class="img-fluid" src="<?php echo $articlePreviewPicture; ?>" alt="<?php echo htmlspecialchars($article->lakename); ?>">
                    </div>
                    <div class="col-md-6 col-xs-12 my-2">
                        <p><?php echo htmlspecialchars($article->description); ?></p>
                        <a href="blog_detail.php?id=<?php echo htmlspecialchars($article->id); ?>" class="red-text">Mehr erfahren...</a>
                    </div>
                    <div class="col-md-3 col-xs-12 text-right my-2">
                        <p>                                            
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
            <article>
                <p class="red-text center margin-top-bottom-05rem border-box-red">Nichts gefunden!</p>
            </article>
        <?php
        endif;
        ?>


    </div>

    <?php include 'footer.php'; ?>
    </body>

</html>