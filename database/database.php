<?php

abstract class DatabaseDAO {
    abstract function register($user, $email, $password);
    abstract function isUserInDataBase($user);
    abstract function isPasswordCorrect($user, $password);
    abstract function addUserRow($user, $email, $password);
    abstract function addArticleRow($title, $name, $coordinates, $image, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author);
    abstract function getAllArticles();
    abstract function addReviewRow($author, $description, $review, $blogID);
    abstract function updateArticle($id, $title, $name, $coordinates, $image, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author);
    abstract function getReviewsByBlogID($id);
    abstract function getAverageRating($articleID);
    abstract function getPopularPosts();
}

class SQLiteDB extends DatabaseDAO {

    public $db = null;
    public $file = "database/database.db";

    public function connect($dsn) {
        try {
            if (!is_writable($dsn)) {
                chmod($dsn, 0777);
            }
            $this->db = new PDO("sqlite:".$dsn);
            return $this->db;
        } catch (PDOException $e) {
            throw new Exception("Derzeit kann keine Verbindung zur Datenbank hergestellt werden. Bitte versuchen Sie es später erneut!");
        }      
    }

    public function disconnect() {
        $this->db = null;
    }

    public function register($user, $email, $password){
        try {
            $db = $this->connect($this->file);
            $cmd = $db;
            $cmd->exec();
            $cmd->beginTransaction();
            $success = false;


            $isUserInDatabase = $this->isUserInDataBase($user);
            if(!$isUserInDatabase){
                $this->addUserRow($user, $email, $password);
                $success = true;
            }
            
            $cmd->commit();
            return $success;
            
        } catch (Exception $ex) {
            $cmd->rollback();
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
            return false;
        }
    }

    public function login($user, $password){
        try {
            $db = $this->connect($this->file);
            $cmd = $db;
            $cmd->exec();
            $cmd->beginTransaction();
            $success = false;


            $isUserInDatabase = $this->isUserInDataBase($user);
            if($isUserInDatabase){
                $isPasswordCorrect = $this->isPasswordCorrect($user, $password);
                if($isPasswordCorrect){
                    $success = true;
                }
            }
            
            $cmd->commit();
            return $success;
            
        } catch (Exception $ex) {
            $cmd->rollback();
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
            return false;
        }
    }

    public function getAllArticles() {
        try {
            $db = $this->connect($this->file);
            $sql = "SELECT * FROM article";
            $cmd = $db->prepare($sql);
            $cmd->execute();
            $this->disconnect();
        
            $result = array();

            if ($cmd->execute()) {
                while ($row = $cmd->fetchObject()) {

                    $row->pictures = $this->getPicturesByArticle($row->id);
                    array_push($result, $row);
                }
            }
            return $result;

        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function getArticleIds() {
        try {
            $db = $this->connect($this->file);
            $sql = "SELECT * FROM article";
            $cmd = $db->prepare($sql);
            $cmd->execute();
            $this->disconnect();
        
            $result = array();

            if ($cmd->execute()) {
                while ($row = $cmd->fetchObject()) {
                    array_push($result, $row->id);
                }
            }
            return $result;

        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function isPasswordCorrect($user, $password){
        try{
            $user = htmlspecialchars($user);
            $db = $this->connect($this->file);
            $sql = "SELECT * FROM author WHERE username = :user";
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":user", $user);
            $cmd->execute();
            $passwordHash = $cmd->fetchObject()->password;
            $boolean = $this->verifyPassword($password, $passwordHash);
            $this->disconnect();
            return $boolean;
        }catch (Exception $ex){
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function isUserInDataBase($user){
        try{
            $user = htmlspecialchars($user);
            $db = $this->connect($this->file);
            $sql = "SELECT * FROM author WHERE username = :user";
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":user", $user);
            $cmd->execute();

            if(!$cmd->fetchObject() == null){
            return true;
            }else{
            return false;
            }
            $this->disconnect();
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function addUserRow($user, $email, $password){
        try{
            $user = htmlspecialchars($user);
            $email = htmlspecialchars($email);
            $password = $this->encodePassword(htmlspecialchars($password));

            $db = $this->connect($this->file);
            $sql = "INSERT INTO author (username, email, password) VALUES (:user, :mail, :pass);";
            $cmd = $db->prepare($sql);

            $cmd->bindParam(":user", $user);
            $cmd->bindParam(":mail", $email);
            $cmd->bindParam(":pass", $password);
            $cmd->execute();
            $this->disconnect();
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    private function encodePassword($password){
        $encoded = password_hash($password, PASSWORD_DEFAULT);
        return $encoded;
    }

    private function verifyPassword($password, $hash){
        if (password_verify($password, $hash)) {
            return true;
        }
        return false;
    }

    
    public function addArticleRow($title, $name, $coordinates, $pictures, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author) {
        try{
            $title = htmlspecialchars($title);
            $name = htmlspecialchars($name);
            $coordinates = htmlspecialchars($coordinates);
            $description = htmlspecialchars($description);

            $db = $this->connect($this->file);
            $sql = "INSERT INTO article (title, lakename, coordinates, description, badesee, angelsee, hundestrand, wc, grillen, wlan, author) VALUES (:title, :lakename, :coordinates, :description, :badesee, :angelsee, :hundestrand, :wc, :grillen, :wlan, :author);";
            $cmd = $db->prepare($sql);

            $cmd->bindParam(":title", $title);
            $cmd->bindParam(":lakename", $name);
            $cmd->bindParam(":coordinates", $coordinates);
            $cmd->bindParam(":description", $description);
            $cmd->bindParam(":badesee", $badesee);
            $cmd->bindParam(":angelsee", $angelsee);
            $cmd->bindParam(":hundestrand", $hundestrand);
            $cmd->bindParam(":wc", $wc);
            $cmd->bindParam(":grillen", $grillen);
            $cmd->bindParam(":wlan", $wlan);
            $cmd->bindParam(":author", $author);
        
            $cmd->execute();

            $id = $db->lastInsertId();

            $this->disconnect();

            $this->setArticlePictures($id,$pictures);

        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }

    }

    public function deleteArticleRow($id) {
        try{
            $id = intval($id);

            $db = $this->connect($this->file);
            $images = $this->getPicturesPathsOfArticle($id);
            //var_dump($images);
            foreach($images as $image) {
                $full_path = $image["path"];
                echo $full_path."<br>";
                if(file_exists($full_path)) {
                    if(unlink($full_path)){
                        
                    } else {
                        throw new Exception("Bild konnte nicht gelöscht werden.");
                    }
                } else {
                    throw new Exception("Das zu löschende Bild existiert nicht.");
                }
            }

            $sql = "DELETE FROM article WHERE id = :id ";
            $db->exec("PRAGMA foreign_keys = ON");
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $id);
            $cmd->execute();
            $this->disconnect();

            return true;

        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
            $db = $this->connect($this->file);
            $sql = "DELETE FROM article WHERE id = :id ";
            $db->exec("PRAGMA foreign_keys = ON");
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $id);
            $cmd->execute();
            $this->disconnect();
            return false;
        }
    }

    private function getPicturesPathsOfArticle($id) {
        try{
            $id = intval($id);
            $db = $this->connect($this->file);

            $sql = "SELECT path FROM pictures WHERE article_id = :id";
            $db->exec("PRAGMA foreign_keys = ON");

            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $id);
            $cmd->execute();
            $result = $cmd->fetchAll();
            $this->disconnect();

            return $result;

        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function getArticleByID($id) {
        try{
            $id = intval($id);
            $db = $this->connect($this->file);
            $sql = "SELECT * FROM article WHERE id = :id";
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $id);
            $cmd->execute();
            $this->disconnect();

            $result =   $cmd->fetchObject();
            $result->pictures = $this->getPicturesByArticle($id);

            return $result;
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }

    }

    public function getSearchResults($text) {
        try{
            $text = htmlspecialchars($text);
            $db = $this->connect($this->file);
            $sql = "SELECT * FROM article WHERE title LIKE :searchOne OR lakename LIKE :searchTwo";
            $cmd = $db->prepare($sql);
            $cmd->bindValue(':searchOne', '%' . $text . '%');
            $cmd->bindValue(':searchTwo', '%' . $text . '%');
            $cmd->execute();
            $this->disconnect();


            $result = array();

            if ($cmd->execute()) {
                while ($row = $cmd->fetchObject()) {
                    $row->pictures = $this->getPicturesByArticle($row->id);
                    array_push($result, $row);
                }
            }
            return $result;
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }

    }

    
    public function addReviewRow($author, $description, $review, $blogID) {
        try{
            $author = htmlspecialchars($author);
            $description = htmlspecialchars($description);
            $blogID = htmlspecialchars($blogID);
            date_default_timezone_get();
            $date = date('d/m/Y', time());
        
            $db = $this->connect($this->file);
            $sql = "INSERT INTO review (author, date, review_text, stars, article_id) VALUES (:author, :date, :review_text, :stars, :article_id);";
            $cmd = $db->prepare($sql);

            $cmd->bindParam(":author", $author);
            $cmd->bindParam(":date", $date);
            $cmd->bindParam(":review_text", $description);
            $cmd->bindParam(":stars", $review[0]);
            $cmd->bindParam(":article_id", $blogID);
            $cmd->execute();
            $this->disconnect();
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function getReviewsByBlogID($id){
        try{
            $id = htmlspecialchars($id);

           $db = $this->connect($this->file);
            $sql = "SELECT * FROM review WHERE article_id = :id";
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $id);

            $this->disconnect();

            $result = array();

            if($cmd->execute()) {
                while($row = $cmd->fetchObject()) {
                    array_push($result, $row);
                }
            }

            return $result;
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function hasUserAlreadyCommented($article_id, $username) {
        try{
            $article_id = intval($article_id);
            $db = $this->connect($this->file);
            $sql = "SELECT COUNT(*) AS numberOfComments FROM review WHERE author = :username AND article_id = :article_id";
            $stmt = $db->prepare($sql);

            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":article_id", $article_id);
            $stmt->execute();
            $obj = $stmt->fetchObject();
            $result = intval($obj->numberOfComments);

            $this->disconnect();
            if($result > 0) {
                return true;
            } else {
                return false;
            }
                 
        } 
        catch(Exception $e){
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function getAverageRating($id) {
        try{
            $id = htmlspecialchars($id);

            $db = $this->connect($this->file);
            $sql = "SELECT avg(stars) AS avg_rating FROM review WHERE article_id = :id GROUP BY article_id LIMIT 1";
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $id);

            $this->disconnect();
            if($cmd->execute()) {
                $obj = $cmd->fetchObject();
                if($obj){
                    return intval(floor($obj->avg_rating));
                } else {
                    return null;
                }
            }

            return null;
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function getPopularPosts() {
        try{
            $db = $this->connect($this->file);
            $sql = "SELECT article_id, avg(stars) AS avg_stars FROM review GROUP BY article_id ORDER BY avg_stars DESC LIMIT 5";
            $cmd = $db->prepare($sql);

            $result = array();

            if ($cmd->execute()) {
                while ($row = $cmd->fetchObject()) {
                    $currentArticle = $this->getArticleByID($row->article_id);
                    if(is_object($currentArticle)){
                        $currentArticle->pictures = $this->getPicturesByArticle($row->article_id);
                        array_push($result, $currentArticle);
                    }
                }
            }

            return $result;
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    private function getPicturesByArticle($articleID) {
        try{
            $db = $this->connect($this->file);
            $sql = "SELECT path FROM pictures WHERE article_id = :id";
            $cmd = $db->prepare($sql);
            $cmd->bindParam(":id", $articleID);

            $pictures = array();

            if ($cmd->execute()) {
                while($row = $cmd->fetchObject()) {
                    $pictures[] = $row->path;
                }

            }

            $this->disconnect();
            return $pictures;
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    private function setArticlePictures($articleID, $pictures) {
        $oldPictures = $this->getPicturesByArticle($articleID);

        try{
            $db = $this->connect($this->file);
            $sql = "INSERT INTO pictures (path, article_id) VALUES (:path, :id);";
            //TODO bei neuem Artikel && beim Bearbeiten eines Artikels
            foreach ($pictures as $picture) {
                if(!in_array($picture,$oldPictures)) {
                    $cmd = $db->prepare($sql);
                    $cmd->bindParam(":path",$picture);
                    $cmd->bindParam(":id",$articleID);
                    $cmd->execute();
                }
            }
            $this->disconnect();
        }catch (Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }
    
    public function deleteArticlePictures($articleID, $pictures) {
        try {
            $db = $this->connect($this->file);
            $sql = "DELETE FROM pictures WHERE article_id = :id AND path = :picture_path";

            foreach($pictures as $picture) {
                $cmd = $db->prepare($sql);
                $cmd->bindParam(":id", $articleID);
                $cmd->bindParam(":picture_path", $picture);
                $cmd->execute();
            }
        } catch(Exception $ex) {
            echo "<div class=\"mx-auto my-2 w-25 alert alert-danger\" role=\"alert\">Fehler: " . $ex->getMessage()."</div>";
        }
    }

    public function updateArticle($id, $title, $name, $coordinates, $pictures, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author) {
        try{
            $id = intval($id);
            $title = htmlspecialchars($title);
            $name = htmlspecialchars($name);
            $coordinates = htmlspecialchars($coordinates);
            $description = htmlspecialchars($description);
            $author = htmlspecialchars($author);

            $db = $this->connect($this->file);
            $cmd = $db->prepare("UPDATE article SET  title = :title, 
                                lakename = :lakename, 
                                coordinates = :coordinates, 
                                description = :description, 
                                badesee = :badesee, 
                                angelsee = :angelsee,
                                hundestrand = :hundestrand, 
                                wc = :wc, 
                                grillen = :grillen, 
                                wlan = :wlan, 
                                author = :author
                                WHERE id = :id");

            $cmd->bindValue(":id", $id);
            $cmd->bindParam(":title", $title);
            $cmd->bindParam(":lakename", $name);
            $cmd->bindParam(":coordinates", $coordinates);
            $cmd->bindParam(":description", $description);
            $cmd->bindParam(":badesee", $badesee);
            $cmd->bindParam(":angelsee", $angelsee);
            $cmd->bindParam(":hundestrand", $hundestrand);
            $cmd->bindParam(":wc", $wc);
            $cmd->bindParam(":grillen", $grillen);
            $cmd->bindParam(":wlan", $wlan);
            $cmd->bindParam(":author", $author);
        
            $cmd->execute();
            $this->disconnect();

            $this->setArticlePictures($id,$pictures);


        }catch (Exception $ex) {
            echo "<div class=\"alert alert-danger\" role=\"alert\"" . $ex->getMessage()."</div>";
        }
    }

    function findUnusedPicturePath($pictureExtension) {
        $result = null;
        for($i = 0; $i <=100; $i++) {
            $pictureFileName = "img\\uploads\\".rand(10000, 99999).".".$pictureExtension;
            if(!file_exists($pictureFileName)) {
                $result = $pictureFileName;
                break;
            }

        }
        return $result;
    }

}

class txtFileDatabaseDAO{

    function isUserInDataBase($user){
        $userdata = $this->splitTxtToArray("database.txt");

        if(isset($userdata[$user])){
            return true;
        }else{
            return false;
        }
    }

    function isPasswordCorrect($user, $password){
        $userdata = $this->splitTxtToArray("database.txt");
        return $userdata[$user];
    }

    function addUserRow($user, $email, $password){
        $file = fopen("database.txt", "a");
        $entry = $user . "," . $email . "," . $password . ";";
        echo fwrite($file, $entry);
        fclose($file);
    }

    function writeDebug($debug){
        $file = fopen("database.txt", "a");
        $entry = "!!!" . $debug . "!!!";
        echo fwrite($file, $entry);
        fclose($file);
    }

    function getLastId($filename) {
        $result = array();
        $all = file_get_contents($filename, true);
        $tupels = explode(";" , $all);
        if(sizeof($tupels)-2 == -1) {
            return 1;
        }
        $result = $tupels[sizeof($tupels)-2];
        $result = explode(",", $result);
        $result = $result[0];
        return $result;


    }

    function getLastIdAlternative($filename) {
        $all = $this->txtAsArray($filename);
        return isset($all) ? all[count($all)-1][0] : 0;
    }

    function addArticleRow($title, $name, $coordinates, $image, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author) {
        $file = fopen("articles.txt", "a");
        $id = $this->getLastId("articles.txt");

        if($id == "") {
            $id = "1";
        } else {
            $id++;
        }
        
        $entry = $id.",".$title.",".$name.",".$coordinates.",".$image.",".$description.",".$badesee.",".$angelsee.",".$hundestrand.",".$wc.",".$grillen.",".$wlan.",".$author.";";
        fwrite($file, $entry);
        fclose($file);
    }

    function addReviewRow($author, $description, $review, $blogID) {
        $file = fopen("reviews.txt", "a");
        $id = $this->getLastId("reviews.txt");
        date_default_timezone_get();
        $date = date('d/m/Y', time());
        
        if($id == "") {
            $id = "1";
        } else {
            $id++;
        }

        $entry = $id.",".$author.",".$date.",".$description.",".$review[0].",".$blogID.";";
        fwrite($file, $entry);
        fclose($file);
    }

    function splitTxtToArray($filename){
        $result = array();

        $all = file_get_contents($filename, true);
        $tupels = explode(";", $all);
        foreach ($tupels as $tupel){
            $tupelSplit = explode(",", $tupel);
            $result[$tupelSplit[0]] = $tupelSplit[2];
        }

        return $result;
    }

    function txtAsArray($filename){
        $result = array();

        $all = file_get_contents($filename, true);
        $tuples = explode(";", $all);

        foreach ($tuples as $tupel){
            if(strlen($tupel) > 0) {
                $entry = explode(",", $tupel);
                $badCharacters = array("[", "]");
                $test = explode(":", str_replace($badCharacters,"", $entry[4]));
                array_push($entry, $test);
                $result[] = $entry;
            }
        }

        return $result;
    }

    function getAllArticles() {
        return $this->txtAsArray("articles.txt");
    }

    function getRandomArticle() {
        $all = $this->getAllArticleIds();
        if(count($all) > 0 ) {
            $selected = random_int(0,count($all)-1);
            return $this->getArticleByID($all[$selected]);
        }
    }

    function getArticleByID($id) {
        $result = null;
        $all = $this->txtAsArray("articles.txt");
        foreach ($all as $entry){
            if($entry[0] == $id) {
                $result = $entry;
                break;
            }
        }
        return $result;
    }

    function getAllArticleIds() {
        $result = array();
        $articles = $this->txtAsArray("articles.txt");
        foreach($articles as $article) {
            array_push($result, $article[0]);
        }

        return $result;
    }

    function updateArticle($id, $title, $name, $coordinates, $image, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author) {
        $all = $this->txtAsArray("articles.txt");

        file_put_contents("articles.txt", "");
        $file = fopen("articles.txt", "a");
        var_dump($all);
        foreach ($all as $entry){
            if(is_array($entry) && sizeof($entry) > 0) {
                if ($entry[0] == $id) {
                    var_dump($entry);
                    $entry = [$id, $title, $name, $coordinates, $image, $description, $badesee, $angelsee, $hundestrand, $wc, $grillen, $wlan, $author];
                }
                var_dump(implode("," , $entry) . ";");
                fwrite($file, str_replace(",Array", "", implode("," , $entry) . ";"));
            }
        }

        fclose($file);
    }

    

    function getReviewsByBlogID($id){
        $result = array();
        $all = $this->txtAsArray("reviews.txt");
        foreach ($all as $entry){
            if(!isset($entry[5])) continue;
            if($entry[5] == $id) {
                array_push($result, $entry);
            }
        }
        return $result;
    }

    function getAverageRating($articleID) {
        $averageRating = null;
        $article = $this->getArticleByID($articleID);
        if(isset($article)) {
            $reviews = $this->getReviewsByBlogID($articleID);
            if(count($reviews) > 0) {
                $ratings = array();
                foreach($reviews as $review) {
                    $ratings[] = $review[4];
                }
                $averageRating = array_sum($ratings) / count($ratings);
            } else
                $averageRating = 0;
        }
        return $averageRating;
    }

    function getPopularPosts() {
        $averageReviewOfPost = array();
        $allIds = $this->getAllArticleIds();
        $articleReviewArray = array();
        foreach($allIds as $id) {
            $reviews = $this->getReviewsByBlogID($id); // ["1,asdads,asdasd;1", "2,asdasd,asdasd;1"] irgendwie so
            if(count($reviews) == 0) {
                continue;
            }

            $onlyReviews = array();
            
            foreach($reviews as $review) {
                array_push($onlyReviews, $review[4]);
            }
            
            $averageOfId = array_sum($onlyReviews)/count($onlyReviews);
            
            $averageReviewOfPost["$id"] = $averageOfId;
        }

        //Order By Highest Review Average
        arsort($averageReviewOfPost);

        return array_keys($averageReviewOfPost);

    }

}

?>