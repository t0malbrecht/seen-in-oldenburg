<?php
    include 'database/database.php';
    $database = new SQLiteDB();

    if(!isset($_SESSION)){
        session_start();
    }

    if($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {

        if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header("Location: index.php");
        }

        $articleID = intval($_GET['id']);
        
        $entry = $database->getArticleByID($articleID);

        if(!isset($entry) || $entry->id != $_GET['id']) {
            header("Location: blog.php");
        }


        //If Comment was sent
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!isset($_SESSION['name'])) {
                header('Location: blog_detail.php?id='.$articleID.'&msg=notLoggedIn');
            }



            $formBoolean = (
                (isset($_POST['message']) && is_string($_POST['message'])) &&
                (isset($_POST['review']) && is_array($_POST['review']))
            );

            if($formBoolean) {
                //If user already commented this article
                $statement = $database->hasUserAlreadyCommented($_POST['articleID'], $_SESSION['name']);
                if($statement) {
                    header('Location: blog_detail.php?id='.$articleID.'&msg=alreadyCommented');
                } else {
                    $database->addReviewRow($_POST['name'], $_POST['message'], $_POST['review'], $_POST['articleID']);
                }

                
            } else {
                header('Location: blog_detail.php?id='.$articleID.'&msg=emptyFields');
            }

        }

        $reviews = $database->getReviewsByBlogID($articleID);

        $popularPosts = $database->getPopularPosts();


        $articlePictures = $entry->pictures;
        if(count($articlePictures) == 0) $articlePictures[] = "img\alt\alternative.php";

    } else {
        header("Location: blog.php");
    }




?>



<!doctype html>
<html lang="de">

<head>
    <title>Beitrag | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
    <meta name="keywords"
        content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <noscript><style> .jsRequired { display: none!important; } </style></noscript>

    <?php include 'header.php'; ?>

    <section>
        <div class="slider">

            <img class="img-fluid" src="img/banner.png" alt="Großer Bornhorster See - eine Oase der Entspannung">
            <div class="bottom-left">
                <h1><?php echo htmlspecialchars($entry->title) ?></h1>
            </div>
        </div>
    </section>

    <div class="container-fluid my-5">
        <div class="row">
            <div class="col-md-8 col-xs-12">
                <section>
                    <div>
                        <?php 
                            if(isset($_GET['msg'])){
                                if($_GET['msg'] == 'emptyFields'){ ?>
                                <p class="red-text center margin-top-bottom-05rem border-box-red">Bitte füllen Sie alle Felder aus!</p><br>
                                <?php } else if($_GET['msg'] == 'notLoggedIn'){ ?>
                                <p class="red-text center margin-top-bottom-05rem border-box-red">Sie müssen eingeloggt sein, um einen Kommentar zu verfassen!</p><br>
                                <?php } else if($_GET['msg'] == 'alreadyCommented'){ ?>
                                <p class="red-text center margin-top-bottom-05rem border-box-red">Sie haben bereits einen Kommentar für diesen Artikel verfasst!</p><br>
                            <?php  }
                            }
                        ?>
                        <h2>
                            <?php echo htmlspecialchars($entry->title) ?> 
                            <?php if(isset($_SESSION["name"])) { 
                                    if($entry->author == $_SESSION["name"]) { ?>
                                        <a class="btn btn-primary jsRequired" href="blog_edit.php?id=<?php echo htmlspecialchars($articleID) ?>" role="button">Edit Article</a>
                                    <?php } ?>
                            <?php } ?>
                            
                        </h2> 
                        <p><i>verfasst von <?php echo htmlspecialchars($entry->author) ?></i></p>
                        <p><?php echo htmlspecialchars($entry->description); ?></p>
                    </div>
                </section>

                <!---- Snippet aus https://bootsnipp.com/snippets/P2gor ---->
                <h4>Bilder</h4>
                <div class="container custom-container">
                    <div class="row">
                        <div class="row">
                        <?php  foreach ($articlePictures as $picture_path) {
                            if(!file_exists($picture_path)) $picture_path = "img\alt\alternative.png";
                            ?>
                                <div class="col-md-4 col-xs-6 thumb">
                                    <div class="thumbnail slider" data-image-id="" data-toggle="modal" data-title=""
                                        data-image="<?php echo htmlspecialchars($picture_path) ?>" data-target="#image-gallery">
                                        <img class="img-thumbnail" src="<?php echo htmlspecialchars($picture_path) ?>" alt="Another alt text">
                                    </div>
                                </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
                <!-------------------------------------------------------------------------------------------->              


                <section>
                    <div>
                        <h3>Kommentare</h3>
                        <?php foreach($reviews as $review) { ?>
                            <div class="card px-3 py-3 mb-3">
                            <div class="row">
                                <div class="col-md-8 col-xs-12 my-2">
                                    <h5><?php echo htmlspecialchars($review->author); ?></h5>
                                    <?php echo htmlspecialchars($review->date); ?>
                                </div>
                                <div class="col-md-4 col-xs-12 my-2">
                                    <?php for($i = 0; $i < $review->stars; $i++) { ?>
                                        ⭐
                                    <?php } ?>
                                </div>
                            </div>
                            <p><?php echo htmlspecialchars($review->review_text); ?></p>
                        </div>
                        <?php } ?>

                    </div>

                    <hr>
                    <?php if(isset($_SESSION['name'])) { ?>                        
                        <div class="col-12">
                            <h3>Schreibe uns ein Kommentar!</h3>
                            <form class="form-group" action="blog_detail.php<?php echo htmlspecialchars("?id=".$articleID) ?>" method="post">
                            <input class="form-control" type="hidden" name="articleID" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                            <input class="form-control" type="hidden" name="name" id="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>"><br>
                                <textarea class="form-control" name="message" id="message" cols="30"
                                    rows="10">Deine Nachricht</textarea><br>
                                <h5>Bewerten Sie diesen See!</h5>
                                <input class="radio-inline my-2 mx-2" type="radio" name="review[]" id="review" value="5">5
                                <input class="radio-inline my-2 mx-2" type="radio" name="review[]" id="review" value="4">4
                                <input class="radio-inline my-2 mx-2" type="radio" name="review[]" id="review" value="3">3
                                <input class="radio-inline my-2 mx-2" type="radio" name="review[]" id="review" value="2">2
                                <input class="radio-inline my-2 mx-2" type="radio" name="review[]" id="review" value="1">1<br>
                                <input class="btn btn-danger" type="submit" value="Absenden">
                            </form>
                        </div>
                    <?php } else { ?>
                        <div class="col-12">
                            <h3>Nur eingeloggte Benutzer können einen Kommentar verfassen!</h3>
                        </div>  
                    <?php } ?>
                </section>
                <div style="margin-top: 50px; position: relative;">
                    <a href="map.php?search=<?php echo htmlspecialchars($entry->title); ?>"><img style="width: 100px" class="img-fluid mr-2" src="img\MapIcon.png"></a>
                    <a style="position: absolute; bottom: 0;" href="map.php?search=<?php echo htmlspecialchars($entry->title); ?>" class="red-text">Auf der Karte anzeigen...</a>
                </div>  
            </div>
            <div class="col-md-4 col-xs-6">
                <aside>
                    <div>
                        <h4>Beliebte Beiträge</h4>

                        <div class="container">
                            <?php if(sizeof($popularPosts) == 0) { ?>
                                <div class="row mb-3">
                                    <div class="col">
                                        <div class="alert alert-primary" role="alert">
                                            Momentan gibt es keine bewerteten Beiträge!
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php foreach($popularPosts as $popularPost) {
                                $firstImage =  count($popularPost->pictures) > 0 && file_exists(($popularPost->pictures)[0]) ? $popularPost->pictures[0] : "img\alt\alternative.png";             ?>
                                <div class="row mb-3">
                                <div class="col-4"><img class="img-fluid mr-2" src="<?php echo htmlspecialchars($firstImage) ?>"
                                        alt="Spaß in der Sonne"></div>
                                <div class="col-8">
                                    <h5><a class="popular-link" href="blog_detail.php?id=<?php echo $popularPost->id ?>"><?php echo htmlspecialchars($popularPost->title) ?></a></h5>
                                    <span class="mt-3"><?php echo htmlspecialchars($popularPost->author) ?></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                    </div>
                </aside>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Vorschau</h4>
        </div>
        <div class="modal-body mx-auto">
            <img src="" id="preview" style="width: 100%; height: 100%;" >
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        </div>
        </div>
    </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/main.js"></script>
    </body>

</html>