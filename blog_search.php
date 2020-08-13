<?php

include_once 'database/database.php';
$database = new SQLiteDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["search"])) {
        $articlesToDisplay = $database->getSearchResults($_GET["search"]);
?>  
    <?php 
    if(sizeof($articlesToDisplay) > 0) {
        foreach ($articlesToDisplay as $article) {
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
                            <div class="card px-3 py-3 mb-3 ">
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
    <?php } 
    } else {
        echo "Keine Suchergebnisse gefunden";
    }
    ?>
<?php
} else {
    header("Location: index.php");
}



?>