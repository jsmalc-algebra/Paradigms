<?php
    require_once "User.php";
    require_once "ConnectToDatabase.php";
    require_once "functions.php";
    session_start();

    $db = ConnectToDatabase::getInstance();
    $db->connect();

    unset($_SESSION['latest_photos']);

    $Q = "SELECT * FROM photos ORDER BY `upload timestamp` DESC LIMIT 10";
    $_SESSION['latest_photos'] = mysqli_fetch_all($db->Execute($Q));
    $db->close();
?>

<html lang="en">
    <head>
        <title>Main app screen</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body class="bg-dark">
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-black">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?php if(isset($_SESSION['user'])) echo "user_page.php"; else echo "#"?>">
                        <img src="images/pfps/<?php
                            if (isset($_SESSION['user']) && $_SESSION['user']->getPfp()==1) echo $_SESSION['user']->getUsername(); else echo "anonymous";
                        ?>.png" alt="profile picture" width="30" height="24" class="d-inline-block align-text-top">
                        <?php
                            if (isset($_SESSION['user'])) echo "Welcome user ".$_SESSION['user']->getUsername();
                            else echo "Welcome anonymous user";
                        ?>
                    </a>
                    <div id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="main_page.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="all_pictures.php">Browse all</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if(!isset($_SESSION['user']) || !checkUserConsumption($_SESSION['user'])) echo "disabled"; ?>"
                                   href="upload_image.php" <?php if(!isset($_SESSION['user']) || !checkUserConsumption($_SESSION['user'])) echo 'aria-disabled="true"'?>>Upload</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if(!isset($_SESSION['user'])) echo "disabled"; ?>"
                                   href="change_packet.php" <?php if(!isset($_SESSION['user'])) echo 'aria-disabled="true"'?>>Change plan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">LOGOUT</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <div class="container px-4 bg-gradient bg-black">
                <div class="row">
                    <?php
                        for ($i = 0; $i < 5; $i++) {
                            generateImageShowcaseFromArray($_SESSION['latest_photos'][$i]);
                        } ?>
                </div>

                <div class="seperator"></div>

                <div class="row">
                    <?php
                    for ($i = 0; $i < 5; $i++) {
                        generateImageShowcaseFromArray($_SESSION['latest_photos'][$i+5]);
                    } ?>
                </div>
            </div>
        </main>
    </body>
</html>
