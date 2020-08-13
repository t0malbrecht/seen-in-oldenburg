<?php
    include 'database/database.php';
    $database = new SQLiteDB();

    if(isset($_POST['submit'])){
        if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password']) && isset($_POST["disclaimer"])){
        $_name = $_POST['name'];
        $_email = $_POST['email'];
        $_password = $_POST['password'];

        if(filter_var($_email, FILTER_VALIDATE_EMAIL)) {
          
          if(!$database->register($_name, $_email, $_password)){
            header("Location: register.php?msg=usernameTaken");
          } else{
            header("Location: login.php");
          
          }
        } else {
          header("Location: register.php?msg=notValidEmail");
        }
  

      }else{
        header("Location: register.php?msg=emptyFields");
      }
    }
  ?>

<!doctype html>
<html lang="de">

<head>
  <title>Registrieren | Seen in Oldenburg</title>
  <meta charset="utf-8">
  <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
  <meta name="keywords"
    content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
  <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
  <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
  <meta name="viewport" content="width=device-width, initial-scale=1">


  <?php include 'header.php'; ?>

  <section>
    <div class="container my-5">
      <div class="margin-bottom">
        <h1 class="h1-dark border-bottom-red center">Erstelle einen Account</h1>

        <?php 
              if(isset($_GET['msg'])){
                if($_GET['msg'] == 'usernameTaken'){ ?>
                  <p class="red-text center margin-top-bottom-05rem border-box-red">Der Benutzername ist bereits vergeben!</p>
                <?php } else if($_GET['msg'] == 'emptyFields'){ ?>
                  <p class="red-text center margin-top-bottom-05rem border-box-red">Bitte füllen Sie alle Felder aus!</p>
                <?php } else if($_GET['msg'] == 'notValidEmail'){ ?>
                  <p class="red-text center margin-top-bottom-05rem border-box-red">Bitte geben Sie eine gültige E-Mail Adresse ein!</p>
                <?php }
              } ?>

        <form action="register.php" method="post" onsubmit="return validatePassword()">
          <div class="form-group">
            <label for="yourname">Dein Name</label>
            <input type="text" class="form-control" name="name" id="name"required>
          </div>

            <div class="form-group">
              <label for="email">Deine E-Mail</label>
              <input type="text" class="form-control" name="email" id="email" required>
            </div>

            <div class="form-group">
              <label for="password">Dein Password</label>
              <input type="password" class="form-control" name="password" onkeyup="return validatePassword()" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" id="password" required>
            </div>
            <div id="alert" class="hide">
              <p id="letter" class="invalid"></p>
              <p id="capital" class="invalid"></p>
              <p id="number" class="invalid"></p>
              <p id="length" class="invalid"></p>
            </div>
          
            <div class="form-group">
                        <label><input name="disclaimer" type="checkbox" class="form-control" style="width:16px;height:auto;display:inline;"> Ich habe die Hinweise zum 
                        <a href="privacy.php" target="_blank" alt="Datenschutz">Datenschutz </a> gelesen und akzeptiere diese. </label>
            </div>

            <div class="form-group center">
              <input type="submit" class="btn btn-danger center" value="Account erstellen" name="submit">
            </div>

        </form>

            <p class="center">Du besitzt bereits einen Account? <a href="login.php" class="red-text">Jetzt Anmelden</a>
            </p>
      </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/main.js"></script>
  </body>

</html>