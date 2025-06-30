<?php
    require "User.php";
    require "functions.php";
    require_once "MySQLiConfig.php";
    require_once "patterns/LoggingActions.php";
    require_once "patterns/LogableActions.php";
    session_start();

    if(isset($_POST['upload'])) {
        if (strlen($_POST['floatingTextarea'])) $desc = $_POST['floatingTextarea']; else $desc = null;
        $hashtags = $_POST['hashtags'];
        $hash_array=[];
        if(strlen($hashtags)) $hash_array = explode(" ", $hashtags);
        if(sizeof($hash_array) > 5) $overhash_flag = true; else $overhash_flag = false;
        $edit = $_POST['picture_options'];

        if (!$overhash_flag) {
            $tempPath=$_FILES['photo']['tmp_name'];
            $name = $_FILES['photo']['name'];

            if ($edit =="AsIs") {
                $new_name = $name;
                move_uploaded_file($tempPath,"images/photos/".$name);
            }
            elseif ($edit =="PNG") {
                $new_name_path="images/photos/".pathinfo($name,PATHINFO_FILENAME).".png";
                $new_name = pathinfo($name,PATHINFO_FILENAME).".png";
                $sourceImage = convertToGD($tempPath);

                if (!$sourceImage || !makePNG($sourceImage,$new_name_path)) echo "Error image upload file!";
                imagedestroy($sourceImage);
            }
            elseif ($edit =="JPG") {
                $new_name_path="images/photos/".pathinfo($name,PATHINFO_FILENAME).".jpg";
                $new_name = pathinfo($name,PATHINFO_FILENAME).".jpg";
                $sourceImage = convertToGD($tempPath);

                if (!$sourceImage || !imagejpeg($sourceImage,$new_name_path)) echo "Error image upload file!";
                imagedestroy($sourceImage);
            }
            elseif ($edit =="WEBP") {
                $new_name_path="images/photos/".pathinfo($name,PATHINFO_FILENAME).".webp";
                $new_name = pathinfo($name,PATHINFO_FILENAME).".webp";
                $sourceImage = convertToGD($tempPath);

                if (!$sourceImage || !imagewebp($sourceImage,$new_name_path)) echo "Error image upload file!";
                imagedestroy($sourceImage);
            }

            $db = MySQLiConfig::getInstance();
            $db->connect();

            $username = $_SESSION['user']->getUsername();
            $esc_usrname = $db->EscapeString($username);
            $find_user = "SELECT id FROM users WHERE username = '$esc_usrname'";
            $result = mysqli_fetch_array($db->Execute($find_user));
            $user_id = array_pop($result);
            $esc_name = $db->EscapeString($new_name);

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



            $Q = "INSERT INTO photos (photo_location,user_id";
            if (sizeof($hash_array)) {
                for ($i = 0; $i < sizeof($hash_keys); $i++) {
                    $Q .= ',' . $data_fields[$i];
                }
            }
            if (isset($desc)){
                $esc_description = $db->EscapeString($desc);
                $Q .= ',description';
            }

            $Q .= ") VALUES ('$esc_name',$user_id";
            if (sizeof($hash_array)) {
                for ($i = 0; $i < sizeof($hash_keys); $i++) {
                    $Q .= ',' . $hash_keys[$i];
                }
            }
            if (isset($desc)) $Q .= ",'$esc_description'";
            $Q .= ")";

            (new LoggingActions(new LogableActions()))->UserPhotoUpload($db, $Q, $user_id);

            $_SESSION['user']->addConsumption($db,$user_id);
        }
    }
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Upload photo</title>
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
                            <a aria-current="page" class="nav-link active <?php if(!isset($_SESSION['user'])) echo "disabled"; ?>"
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
            <main class="w-100 m-auto">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php
                        if (isset($_POST['upload']) && !$overhash_flag){
                            $db = MySQLiConfig::getInstance();
                            $db->connect();
                            if (!empty(mysqli_fetch_array($db->Execute("SELECT * FROM photos WHERE id = $img_id"))))
                                echo "<h2 style='color: limegreen'>PHOTO SUCCESSFULLY UPLOADED</h2>";
                        }
                    ?>
                    <h2>UPLOAD PHOTO</h2>

                    <div class="form-floating">
                        <textarea class="form-control" placeholder="Add a description to your photo" id="floatingTextarea" name="floatingTextarea"></textarea>
                        <label for="floatingTextarea">Add a description to your photo</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" name="hashtags" id="hashtags" class="form-control">
                        <label for="hashtags">Add up to 5 hashtags seperated by spaces</label>
                        <?php if(isset($_POST['upload']) && $overhash_flag) echo "<h2 style='color: red'>PLEASE ONLY ADD UP TO 5 HASHTAGS</h2>"?>
                    </div>

                    <div class="form-floating">
                        <p style="font-weight: bold">Do you wish to change your photos format?</p>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="btn-check" name="picture_options" id="radio1" value="AsIs" checked autocomplete="off">
                            <label class="btn" for="radio1">Do not switch image format</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="btn-check" name="picture_options" id="radio2" value="PNG" autocomplete="off">
                            <label class="btn" for="radio2">PNG</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="btn-check" name="picture_options" id="radio3" value="JPG" autocomplete="off">
                            <label class="btn" for="radio3">JPG</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="btn-check" name="picture_options" id="radio4" value="WEBP" autocomplete="off">
                            <label class="btn" for="radio4">WEBP</label>
                        </div>
                    </div>

                    <div class="form-floating">
                        <input class="form-control form-control-sm" type="file" id="photo" name="photo" required>
                        <label for="photo" class="form-label">Please submit your photo</label>
                    </div>

                    <div class="form-floating">
                        <input type="submit" name="upload" class="btn btn-primary w-100 py-2" value="UPLOAD">
                    </div>
                </form>
            </main>
        </div>
    </body>
</html>
