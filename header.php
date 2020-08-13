<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="vendor/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script type='text/javascript' src='vendor/js/jquery.js'></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
    integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="//wpcc.io/lib/1.0.2/cookieconsent.min.css"/><script src="//wpcc.io/lib/1.0.2/cookieconsent.min.js"></script><script>window.addEventListener("load", function(){window.wpcc.init({"border":"thin","corners":"small","colors":{"popup":{"background":"#f6f6f6","text":"#000000","border":"#555555"},"button":{"background":"#ff3c3c","text":"#ffffff"}},"position":"bottom","content":{"href":"privacy.php","message":"Diese Website verwendet Cookies, um sicherzustellen, dass Sie das beste Ergebnis auf unserer Website erzielen.","link":"Erfahren Sie mehr","button":"Verstanden!"}})});</script> 

</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <img class="navbar-brand pull-sm-left" src="img/SeenInOldenburg.png"
                    alt="Seen in Oldenburg Logo" width="120" height="70">

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item ml-2">
                        <a class="nav-link menu-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item ml-2">
                        <a class="nav-link menu-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item ml-2">
                        <a class="nav-link menu-link" href="map.php">Map</a>
                    </li>
                    <li class="nav-item ml-2">
                        <a class="nav-link menu-link" href="contact.php">Kontakt</a>
                    </li>
                </ul>

                <ul class="navbar-nav pull-sm-right">
                    <?php
                        if(!isset($_SESSION)){
                            session_start();
                        }
                        if(!isset($_SESSION['name'])){ ?>
                            <li class="nav-item ml-2">
                                    <a class="nav-link menu-link" href="login.php">Login</a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item ml-2">
                                    <a class="nav-link menu-link" href="blog_add.php">Beitrag</a>
                                </li>
                                <li class="nav-item ml-2">
                                    <a class="nav-link menu-link" href="logout.php">Logout</a>
                                </li>
                        <?php } ?>
                    <?php if(!isset($_SESSION['name'])){ ?>
                    <li class="nav-item ml-2">
                        <a class="nav-link menu-link" href="register.php">Register</a>
                    </li>
                    <?php } ?>
                    </li>
                </ul>

            </div>
        </nav>
        <script>
            $(function(){
                $('a').each(function(){
                    if ($(this).prop('href') == window.location.href) {
                        if($(this).attr('class') == "popular-link") {
                            // nothing
                        } else {
                            $(this).addClass('current-menu-link'); 
                            $(this).parents('li').addClass('active');
                        }
                        
                    }
                });
            });
        </script>
        <noscript>
            <div class="alert-danger center" role="alert">JavaScript ist deaktiviert!
                Um alle Funktionen nutzen zu können, sollten Sie JavaScript aktivieren!</div>
        </noscript>
    </header>