<?php

include_once 'database/database.php';
$database = new SQLiteDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $articlesToDisplay = $database->getSearchResults("");

        $article_array = [];
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

            $articleToAdd = ["id" => $article->id, "lakename" => $article->lakename, "coordinates" => $article->coordinates, "rating" => $averageRating, "filter" => $filtersAsString];

            array_push($article_array, $articleToAdd);
        } 

        echo json_encode($article_array);
    
    } else {
        echo json_encode($article_array);
    }
    ?>
<?php
} else {
    echo json_encode($article_array);
}



?>