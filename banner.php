<?php
    include_once 'database/database.php';

    $database = new SQLiteDB();

    //TODO lieber ne Zufallsfunktion
    $tempVar = $database->getPopularPosts();

    if(isset($tempVar)) {
    if(count($tempVar) != 0) $highlightedArticle = $tempVar[0];
    }

?>
    <section>
            <div class="slider">
                <img class="img-fluid" src="img/banner.png" alt="Banner">
                <div class="centered">
                    <?php if(isset($highlightedArticle) && isset($tempVar)): ?>
                    <h1><?php echo htmlspecialchars($highlightedArticle->lakename) ?></h1>
                    <a href="blog_detail.php?id=<?php echo htmlspecialchars($highlightedArticle->id) ?>" class="button-1 center header-button">Jetzt kennenlernen!</a>
                    <?php endif; ?>
                </div>
            </div>
    </section>
