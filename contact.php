<?php

    if($_SERVER['REQUEST_METHOD'] == "POST") {

        $allRequiredSend = (
            (isset($_POST["subject"]) && is_string($_POST["subject"])) &&
            (isset($_POST["name"]) && is_string($_POST["name"])) &&
            (isset($_POST["email"]) && is_string($_POST["email"])) &&
            (isset($_POST["description"]) && is_string($_POST["description"])) &&
            (isset($_POST["disclaimer"]))
        );

        if($allRequiredSend && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $message = htmlspecialchars($_POST["description"]);
            $subject = htmlspecialchars($_POST["subject"]);
            $email = htmlspecialchars($_POST["email"]);
            $name = htmlspecialchars($_POST["name"]);

            require_once 'vendor/autoload.php';
            try{ 
                $transport = (new Swift_SmtpTransport('smtp.sendgrid.net', 465, "ssl"))
                ->setUsername('apikey')
                ->setPassword('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')
                ;

                $mailer = new Swift_Mailer($transport);
                $message = (new Swift_Message($subject))
                ->setFrom(['webprogrammierungprojekt@gmail.com' => $name])
                ->setTo(['webprogrammierungprojekt@gmail.com' => 'Seen in Oldenburg'])
                ->setBody($message."\n\n E-Mail des Absender: ".$email)
                ;

                $result = $mailer->send($message);

                if($result == 1) {
                    header('Location: contact.php?msg=messageSuccess');
                } else {
                    header('Location: contact.php?msg=messageFailed');
                }
            } catch(Exception $e) {
                header('Location: contact.php?msg=smtpError');
            }
            
        } else {
            header('Location: contact.php?msg=messageFailed');
        }

    }

?>


<!doctype html>
<html>

<head>
    <title>Kontakt | Seen in Oldenburg</title>
    <meta charset="utf-8">
    <meta name="description" content="Die schönsten Seen in Oldenburg zum genießen! Mit reiner Wasserqualität. " />
    <meta name="keywords"
        content="Seen, Oldenburg, Sommer, Sonnenbaden, Badesee, Schwimmen, Urlaub, Niedersachen, Deutschland, FKK" />
    <meta name="author" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="copyright" content="Tom Albrecht, Andree Hildebrandt, Nick Garbusa" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <noscript>
        <style>
            .jsRequired {
                display: none
            }
        </style>
    </noscript>

    <?php include 'header.php'; ?>

    <div class="container-fluid my-3">
        <?php if (isset($_GET['msg'])) {
                if ($_GET['msg'] == 'messageSuccess') { ?>
        <p class="center margin-top-bottom-05rem border-box-green">Ihr Nachricht wurde erfolgreich versendet!</p>
        <?php } else if ($_GET['msg'] == 'messageFailed') { ?>
        <p class="red-text center margin-top-bottom-05rem border-box-red">Beim Senden der Nachricht ist etwas schief
            gelaufen!</p>
        <?php } else if ($_GET['msg'] == 'smtpError') { ?>
        <p class="red-text center margin-top-bottom-05rem border-box-red">Leider konnten wir Ihre Nachricht momentan nicht zustellen. Unser Mail-Server hat momentan Probleme.</p>
        <?php }
            }
        ?>
        <div class="row py-4 content">
            <div class="col-md-6 col-sm-12 margin-left-15 center">
                <h1>Kontakt</h1>
                <p>Falls Sie jegliche Fragen haben, können Sie uns gerne durch dieses Formular kontaktieren!</p>

                <form action="contact.php" class="jsRequired" name="contactForm" onsubmit="" method="POST">
                    <div class="form-group">
                        <label for="subject">Betreff</label>
                        <input type="text" class="form-control" name="subject" id="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-Mail</label>
                        <input type="text" class="form-control" name="email" id="email" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea name="description" class="form-control" onkeyup="return validateForm()"
                            id="description" cols="30" rows="10" required minlength="50"></textarea>
                        <p id="alert"></p>
                    </div>

                    <div class="form-group">
                        <label><input name="disclaimer" type="checkbox" class="form-control" style="width:16px;height:auto;display:inline;"> Ich habe die Hinweise zum 
                        <a href="privacy.php" target="_blank" alt="Datenschutz">Datenschutz </a> gelesen und akzeptiere diese. </label>
                    </div>


                    <div class="form-group">
                        <input type="submit" class="btn btn-danger" value="Absenden" name="submit">
                    </div>
                </form>
                <noscript>
                    <p class="red-text center margin-top-bottom-05rem border-box-red">
                        Bitte aktivieren Sie JavaScript, sonst können Sie uns leider nicht kontaktieren!
                    </p>
                </noscript>
            </div>
        </div>
    </div>

        <?php include 'footer.php'; ?>

        <script src="js/main.js"></script>

        </body>

</html>