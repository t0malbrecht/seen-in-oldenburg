<footer class="footer mt-auto bg-danger">
    <div class="container py-4">
        <div class="row py-4">
            <div class="col-xs-12 col-md-4">
                <img class="img-fluid" src="img/SeenInOldenburgWeiss.png" alt="Seen in Oldenburg Logo">
            </div>

            <div class="col-xs-12 col-md-3 mt-5">
                <h5 class="text-white border-bottom">Rechtliches</h5>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="text-white" href="privacy.php">Datenschutz</a></li>
                    <li class="nav-item"><a class="text-white" href="imprint.php">Impressum</a></li>
                    <li class="nav-item"><a class="text-white" href="sitemap.php">Sitemap</a></li>
                </ul>
            </div>

            <div class="col-xs-12 col-md-3 mt-5">
                <ul class="navbar-nav">
                    <h5 class="text-white border-bottom">Navigation</h5>
                    <li class="nav-item"><a class="text-white" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="text-white" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="text-white" href="map.php">Map</a></li>
                </ul>
            </div>

            <div class="col-xs-12 col-md-2 mt-5 mb-5">
                <h5 class="text-white border-bottom">Konto</h5>
                <ul class="navbar-nav">
                <?php
                        if(!isset($_SESSION)){
                            session_start();
                        }
                        if(!isset($_SESSION['name'])){ ?>
                            <li class="nav-item"><a class="text-white" href="login.php">Login</a></li>
                            <li class="nav-item"><a class="text-white" href="register.php">Register</a></li>
                            <?php } else { ?>
                                <li class="nav-item">
                                    <a class="text-white" href="blog_add.php">Beitrag erstellen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="text-white" href="logout.php">Logout</a>
                                </li>
                        <?php } ?>
                </ul>
            </div>

        </div>

        <div class="icon-credits">Icons made by 
        <a href="https://www.flaticon.com/authors/xnimrodx" title="xnimrodx" target="_blank">xnimrodx</a>,
        <a href="https://www.freepik.com/?__hstc=57440181.9251095d0060207e6c60fc45d059723e.1562088038140.1562088038140.1562088038140.1&__hssc=57440181.5.1562088038141&__hsfp=4244638016" title="Freepik" target="_blank">Freepik</a>,
        <a href="https://www.flaticon.com/authors/photo3idea-studio" title="photo3idea_studio"  target="_blank">photo3idea_studio</a>,
        <a href="https://www.flaticon.com/authors/smashicons" title="Smashicons" target="_blank">Smashicons</a>,
        <a href="https://www.flaticon.com/authors/dave-gandy" title="Dave Gandy" target="_blank">Dave Gandy</a>,
        <a href="https://www.flaticon.com/authors/popcorns-arts" title="Icon Pond">Icon Pond</a>
        from <a href="https://www.flaticon.com/"                 title="Flaticon" target="_blank">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
    </div>

    </div>

        
</footer>