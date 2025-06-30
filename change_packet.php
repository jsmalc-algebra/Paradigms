<?php
    require "User.php";
    require_once "MySQLiConfig.php";
    require "functions.php";
    require_once "patterns/LoggingActions.php";
    require_once "patterns/LogableActions.php";
    session_start();

    $u_id = findIDByUsername($_SESSION['user']->getUsername());
    $db = MySQLiConfig::getInstance();
    $db->Connect();
    $sql = "SELECT * FROM `logs` WHERE `event`=\"USER CHANGED PLAN\" AND `user_id`=$u_id AND `timestamp` <= NOW() - INTERVAL 1 DAY;";
    if (mysqli_fetch_all($db->Execute($sql))) $restrict = true;
    else $restrict = false;

    if (isset($_POST['new_plan']) && !$restrict) {
        $new_plan =(int)$_POST['tier_select'];
        $_SESSION['user']->setUserTier($new_plan);

        $Q="UPDATE users SET plan_id = $new_plan WHERE id = $u_id";

        (new LoggingActions(new LogableActions()))->UserPlanChange($db,$Q,$u_id);
    }
?>

<html lang="en">
    <head>
        <title>Landing page</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://kit.fontawesome.com/a3d8dd45fd.js" crossorigin="anonymous"></script>
    </head>

    <body class="bg-dark">
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-black">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?php if ($_SESSION['user']->getUserRole()==1) echo 'user_page.php'; else echo '#'?>">
                        <img src="images/pfps/<?php
                        if (isset($_SESSION['user'])) echo $_SESSION['user']->getUsername(); else echo "anonymous";
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
                                <a class="nav-link active <?php if(!isset($_SESSION['user'])) echo "disabled"; ?>"
                                   href="#" aria-current="page" <?php if(!isset($_SESSION['user'])) echo 'aria-disabled="true"'?>>Change plan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">LOGOUT</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="container px-4 bg-body-tertiary">
            <form action="" method="post">
                <?php if (isset($_POST['new_plan']) && !$restrict) echo '<h2 style="color: limegreen">PLAN CHANGED</h2>' ?>
                <h2>CHANGE PLAN</h2>
                <div class="form-floating">
                    <select class="form-select form-select-lg mb-3" name="tier_select" required>
                        <option selected disabled value="">Please select a subscription tier</option>
                        <option value="1">FREE</option>
                        <option value="2">PRO</option>
                        <option value="3">GOLD</option>
                    </select>
                </div>
                <div class="form-floating">
                    <input type="submit" name="new_plan" class="btn btn-primary w-100 py-2" value="EDIT PLAN">
                </div>
            </form>
        </main>
    </body>
</html>