<?php 
//Quelle https://www.webslesson.info/2017/06/make-dynamic-xml-sitemap-in-php-script.html
include_once 'database/database.php';


try {
    $database = new SQLiteDB();
    $article_ids = $database->getArticleIds();

    function url(){
        return sprintf(
        "%s://%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/"
    );
  }

    $base_url = url();

    header("Content-Type: application/xml; charset=utf-8");

    echo '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL; 

    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'index.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' .  PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'blog.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'map.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'contact.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'login.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'register.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'logout.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'blog_add.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'privacy.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    echo '<url>' . PHP_EOL;
    echo '<loc>'.$base_url.'imprint.php</loc>' . PHP_EOL;
    echo '<changefreq>daily</changefreq>' . PHP_EOL;
    echo '</url>' . PHP_EOL;

    foreach($article_ids as $id) {
        echo '<url>' . PHP_EOL;
        echo '<loc>'.$base_url.'blog_detail.php?id='.$id.'</loc>' . PHP_EOL;
        echo '<changefreq>daily</changefreq>' . PHP_EOL;
        echo '</url>' . PHP_EOL;

        echo '<url>' . PHP_EOL;
        echo '<loc>'.$base_url.'blog_edit.php?id='.$id.'</loc>' . PHP_EOL;
        echo '<changefreq>daily</changefreq>' . PHP_EOL;
        echo '</url>' . PHP_EOL;
    }

    echo '</urlset>' . PHP_EOL;

} catch(Exception $e) {
    echo $e->getMessage();
}

?>
