<?php
    require "User.php";
    require "functions.php";
    require_once "ConnectToDatabase.php";
    require_once "patterns/LoggingActions.php";
    require_once "patterns/LogableActions.php";
    session_start();

    $db = ConnectToDatabase::getInstance();
    $db->Connect();
    $id = $_GET['id'];

    if(isset($_POST['edit'])) {
        if (strlen($_POST['floatingTextarea'])) $desc = $_POST['floatingTextarea']; else $desc = null;

        $hashtags = $_POST['hashtags'];
        $hash_array=[];
        if(strlen($hashtags)) $hash_array = explode(" ", $hashtags);
        if(sizeof($hash_array) > 5) $overhash_flag = true; else $overhash_flag = false;

        if (!$overhash_flag) {
            if(sizeof($hash_array)) {
                foreach ($hash_array as $hashtag) {
                    $esc_hash = $db->escapeString($hashtag);
                    $find_hash_q = "SELECT id FROM hashtags WHERE hashtag_content = '$esc_hash'";
                    $result = mysqli_fetch_array($db->Execute($find_hash_q));

                    if ($result) $hash_keys[] =  array_pop($result);

                    else{
                        $insert_hash = "INSERT INTO hashtags (hashtag_content) VALUES ('$esc_hash')";
                        $db->Execute($insert_hash);
                        $hash_keys[] = $db->FetchLastInsertId();
                    }

                }

                $data_fields = ['hashtag_1','hashtag_2','hashtag_3','hashtag_4','hashtag_5'];
            }

            if(isset($desc)) {
                $esc_description = $db->EscapeString($desc);
                $dsc_flag = true;
            } else $dsc_flag = false;

            if (sizeof($hash_array)) {
                $hash_flag = true;
            } else $hash_flag = false;

            if ($hash_flag || $dsc_flag) {
                $query = "UPDATE photos SET ";

                if ($dsc_flag) {
                    $query .='description = "'.$esc_description.' " ';
                }
                if ($hash_flag) {
                    if($dsc_flag) $query .=", ";
                    for ($j = 0; $j < sizeof($hash_keys); $j++) {
                        $query.=$data_fields[$j].' = '.$hash_keys[$j].' ';
                    }
                }
                $query .= " WHERE id = '$id'";
                $u_id = findIDByUsername($_SESSION['user']->getUsername());
                (new LoggingActions(new LogableActions()))->UserPhotoEdit($db,$query,$u_id,$id);

            }
        }
    }

    $Query = "SELECT * FROM photos WHERE id = '$id'";
    $rez = mysqli_fetch_row($db->Execute($Query));

    $hashtag_ids = [];
    $hashtags = [];
    for ($i = 0; $i < 5; $i++) if ($rez[$i+5] != null) $hashtag_ids[] = $rez[$i+5];
    foreach ($hashtag_ids as $hashtag_id) $hashtags[] = findHashtagByID($hashtag_id);
    $db->Close();
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Edit photo</title>
    </head>

    <body class="bg-dark">
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-black">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?php if(isset($_SESSION['user'])) echo "user_page.php"; else echo "#"?>">
                        <img src="images/pfps/<?php
                        if (isset($_SESSION['user'])) echo $_SESSION['user']->getUsername(); else echo "anonymous";
                        ?>.png" alt="profile picture" width="30" height="24" class="d-inline-block align-text-top">
                        <?php
                        if (isset($_SESSION['user'])) echo "USER ".$_SESSION['user']->getUsername();
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

        <div class="container justify-content-center d-flex align-items-center py-4 bg-body-tertiary">
            <div class="row">
                <img class="col" src="images/photos/<?php echo $rez[1]?>">
                <main class="w-100 m-auto col">
                    <form action="" method="post">
                        <?php if(isset($_POST['edit']) && !$overhash_flag) echo "<h2 style='color: limegreen'>PHOTO EDITED</h2>"?>
                        <h2>EDIT PHOTO</h2>

                        <div class="form-floating">
                            <textarea class="form-control" placeholder="Add a description to your photo" id="floatingTextarea" name="floatingTextarea"><?php echo $rez[3]?></textarea>
                            <label for="floatingTextarea">Edit the description of your photo</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" name="hashtags" id="hashtags" class="form-control" value="<?php echo implode(' ',$hashtags); ?>">
                            <label for="hashtags">Add up to 5 hashtags seperated by spaces</label>
                            <?php if(isset($_POST['upload']) && $overhash_flag) echo "<h2 style='color: red'>PLEASE ONLY ADD UP TO 5 HASHTAGS</h2>"?>
                        </div>

                        <div class="form-floating">
                            <input type="submit" name="edit" class="btn btn-success w-100 py-2" value="EDIT">
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </body>
</html>