<?php
    require_once "User.php";
    require_once "functions.php";
    require_once "ConnectToDatabase.php";
    require_once "patterns/AuthorizationDecorator.php";
    session_start();

    $db = ConnectToDatabase::getInstance();
    $db->connect();

    $id = findIDByUsername($_SESSION['user']->getUsername());
    $Q = "SELECT * from photos where user_id='".$id."'";
    $_SESSION['user_photos'] = mysqli_fetch_all($db->Execute($Q));

    $db->Close();
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>User profile dashboard</title>
    </head>

    <body class="bg-dark">
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-black">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?php if ((new AuthorizationDecorator(null, $_SESSION['user']->getUserRole()))->dashboard_acess()) echo 'admin_dashboard.php'; else echo '#'?>">
                        <img src="images/pfps/<?php
                        if (isset($_SESSION['user']) && $_SESSION['user']->getPfp()==1) echo $_SESSION['user']->getUsername(); else echo "anonymous";
                        ?>.png" alt="profile picture" width="30" height="24" class="d-inline-block align-text-top">
                        <?php
                        if (isset($_SESSION['user'])) echo strtoupper($_SESSION['user']->getUsername());
                        ?>
                    </a>
                    <div id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="main_page.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="all_pictures.php">Browse all</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if(!isset($_SESSION['user'])) echo "disabled"; ?>"
                                   href="upload_image.php" <?php if(!isset($_SESSION['user'])) echo 'aria-disabled="true"'?>>Upload</a>
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
        <div class="container-fluid <?php
            if ($_SESSION['user']->getUserTier()==3) echo "bg-warning";
            elseif (checkUserConsumption($_SESSION['user'])) echo "bg-success";
            else echo "bg-danger";
        ?>">
            <div class="row">
                <img src="images/pfps/<?php if ($_SESSION['user']->getPfp()==1) echo $_SESSION['user']->getUsername(); else echo 'anonymous'?>.png" class="col">


                <h2 class="col">CURRENT PLAN: <?php
                    switch ($_SESSION['user']->getUserTier()) {
                        case 1: echo "FREE";
                            break;
                        case 2: echo "PRO";
                            break;
                        case 3: echo "GOLD";
                    }
                    ?></h2>
                <h2 class="col">CURRENT CONSUMPTION: <?php
                    switch ($_SESSION['user']->getUserTier()) {
                        case 1: $limit = 7;
                            break;
                        case 2: $limit = 35;
                            break;
                        case 3: $limit = '∞';
                    }
                    echo $_SESSION['user']->getWeeklyPicturesUploaded().'/'.$limit;
                    ?></h2>
            </div>
        </div>
        <div class="seperator"></div>
        <div class="container-fluid px-4 bg-gradient bg-black">
            <?php
            $break_counter=0;
            foreach ($_SESSION['user_photos'] as $photo_array) {
                if ($break_counter%5==0) echo "<div class='row'>";
                generateImageShowcaseFromArray($photo_array);
                $break_counter++;
                if ($break_counter%5==0) echo "</div>";
                if ($break_counter%5==0) echo "<div class='seperator'></div>";
            }
            if ($break_counter%5!=0) echo "</div>";
            ?>
        </div>
    </body>
</html>