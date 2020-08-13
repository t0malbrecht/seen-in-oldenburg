<?php
        if(!isset($_SESSION)){
          session_start();
        }
        include 'database/database.php';
        $database = new SQLiteDB();
  
  
        if(isset($_POST['login'])){
          if(!empty($_POST['name']) && !empty($_POST['password'])){
            $_name = $_POST['name'];
            $_password = $_POST['password'];
            $isCorrect = $database->login($_name, $_password);
  
            if($isCorrect == true){
              $_SESSION['name'] = $_name;
              if(isset($_SESSION['name'])){
                header("Location: index.php");
            }
            }else{
              header("Location: login.php?msg=wrongCredentials");
            }
          }else{
            header("Location: login.php?msg=emptyFields");
          }
        }
?>
<!doctype html>
<html lang="de">

<head>
  <title>Einloggen | Seen in Oldenburg</title>
  <meta charset="utf-8">
  <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
  <meta name="keywords"
    content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
  <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
  <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

<?php include 'header.php'; ?>

  <section>
  <?php if(isset($_SESSION['name'])) { ?>
    <p class="red-text center margin-top-bottom-05rem border-box-red">Du bist bereits angemeldet!</p>
    <?php } else { ?>
    <div class="container my-5">
      <div class="margin-bottom">
        <h1 class="h1-dark border-bottom-red center">Jetzt Anmelden</h1> 
              <?php
              if(isset($_GET['msg'])) {
                if($_GET['msg'] == 'wrongCredentials') { ?>
                 <p class="red-text center margin-top-bottom-05rem border-box-red">Du hast die falschen Login-Informationen eingegeben!</p>
                <?php } else if($_GET['msg'] == 'emptyFields') { ?>
                  <p id="alert" class="red-text center margin-top-bottom-05rem border-box-red">Bitte füllen Sie alle Felder aus!</p>
                <?php }
              } ?>


        <form name="login" action="login.php" method="post">
          <div class="form-group">
            <label for="yourname">Dein Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
          </div>

          <div class="form-group">
            <label for="password">Dein Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
          </div>

          <div class="form-group center">
            <input type="submit" class="btn btn-danger center" value="Anmelden" name="login">
          </div>

          <p class="center">Du besitzt noch keinen Account? <a href="register.php" class="red-text">Jetzt
              Registrieren</a></p>
        </form>
      </div>
    <?php } ?>
  </section>

  <?php include 'footer.php'; ?>
  </body>

</html>