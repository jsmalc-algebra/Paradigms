<?php
    require_once "MySQLiConfig.php";
    require "functions.php";
    require "User.php";
    session_start();

    $db = MySQLiConfig::getInstance();
    $db->Connect();

    if (!isset($_POST['filter'])) {
        $Q="SELECT * FROM photos ORDER BY `upload timestamp` DESC";
    }
    else {
        $Q="SELECT * FROM photos WHERE 1";

        $hashtags = $_POST['hashtags'];
        $hash_array=[];
        if(strlen($hashtags)) {
            $hash_string_array = explode(" ", $hashtags);
            $hash_ids=[];
            foreach ($hash_string_array as $hash) {
                $find_hash = "SELECT id FROM hashtags WHERE hashtag_content='$hash'";
                $hash_id = mysqli_fetch_row($db->Execute($find_hash));
                if ($hash_id) $hash_ids[] = $hash_id[0];
                else $hash_ids[] = 0;
            }
            if (!empty($hash_ids)) {
                $Q .= " AND 
                          (hashtag_1 IN (".implode(",", $hash_ids).") 
                               OR hashtag_2 IN (".implode(",", $hash_ids).")
                               OR hashtag_3 IN (".implode(",", $hash_ids).")
                               OR hashtag_4 IN (".implode(",", $hash_ids).") 
                               OR hashtag_5 IN(".implode(",", $hash_ids)."))";
            }
        }

        if (strlen($_POST['author'])) {
            $author = $_POST['author'];
            $author_id = findIDByUsername($author);
            if ($author_id) $Q.=" AND user_id=$author_id";
            else $Q.=" AND user_id=0";
        }

        if (!empty($_POST['datetime_from']) && !empty($_POST['datetime_to'])) {
            $start = $_POST['datetime_from'];
            $end = $_POST['datetime_to'];

            $start_sql = str_replace('T', ' ', $start) . ':00';
            $end_sql = str_replace('T', ' ', $end) . ':00';

            $Q.=" AND `upload timestamp` BETWEEN '$start_sql' AND '$end_sql'";
        }
        elseif (!empty($_POST['datetime_from'])) {
            $start = $_POST['datetime_from'];
            $start_sql = str_replace('T', ' ', $start) . ':00';
            $Q.=" AND `upload timestamp` >= '$start_sql'";
        }
        elseif (!empty($_POST['datetime_to'])) {
            $start = $_POST['datetime_to'];
            $start_sql = str_replace('T', ' ', $start) . ':00';
            $Q.=" AND `upload timestamp` <= '$start_sql'";
        }

        $Q.=" ORDER BY `upload timestamp` DESC";
    }

    $_SESSION['all_photos'] = mysqli_fetch_all($db->Execute($Q));
    $db->Disconnect();
?>

<html lang="en">
    <head>
        <title>All photos</title>
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
                        if (isset($_SESSION['user'])) echo "USER ".$_SESSION['user']->getUsername();
                        else echo "USER anonymous";
                        ?>
                    </a>
                    <div id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="main_page.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="all_pictures.php">Browse all</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php if(!isset($_SESSION['user'])|| !checkUserConsumption($_SESSION['user'])) echo "disabled"; ?>"
                                   href="upload_image.php" <?php if(!isset($_SESSION['user'])|| !checkUserConsumption($_SESSION['user'])) echo 'aria-disabled="true"'?>>Upload</a>
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
        <div class="d-flex justify-content-center">
            <button class="btn btn-success btn-lg" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">Filter search</button>
        </div>
        <div class="container collapse" id="collapseExample">
            <form action="" method="post">
                <div class="form-floating">
                    <input type="text" name="hashtags" id="hashtags" class="form-control">
                    <label for="hashtags">Input hashtags seperated by spaces you wish to filter by</label>
                </div>
                <div class="form-floating">
                    <input type="text" name="author" id="author" class="form-control">
                    <label for="author">Input author name you wish to filter by</label>
                </div>
                <div class="form-floating">
                    <input type="datetime-local" name="datetime_from" id="datetime_from" class="form-control">
                    <label for="datetime_from">Input datetime from when you wish to filter</label>
                </div>
                <div class="form-floating">
                    <input type="datetime-local" name="datetime_to" id="datetime_to" class="form-control">
                    <label for="datetime_to">Input datetime to when you wish to filter</label>
                </div>
                <div class="form-floating">
                    <input type="submit" name="filter" class="btn btn-success w-100 py-2" value="FILTER">
                </div>
            </form>
        </div>

        <main>
            <div class="container px-4 bg-gradient bg-black">
                <?php
                $break_counter=0;
                foreach ($_SESSION['all_photos'] as $photo_array) {
                    if ($break_counter%5==0) echo "<div class='row'>";
                    generateImageShowcaseFromArray($photo_array);
                    $break_counter++;
                    if ($break_counter%5==0) echo "</div>";
                    if ($break_counter%5==0) echo "<div class='seperator'></div>";
                }
                if ($break_counter%5!=0) echo "</div>";
                ?>
            </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    </body>


</html>
