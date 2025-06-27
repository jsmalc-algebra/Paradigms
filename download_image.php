<?php
    require "User.php";
    require_once "ConnectToDatabase.php";
    require "functions.php";
    require_once "patterns/LogableActions.php";
    require_once "patterns/LoggingActions.php";
    session_start();

    $id = $_GET['id'];
    $db = ConnectToDatabase::getInstance();
    $db->Connect();
    $Query = "SELECT * FROM photos WHERE id = '$id'";
    $rez = mysqli_fetch_row($db->Execute($Query));

    if (isset($_POST['download'])) {
        $u_id =findIDByUsername($_SESSION['user']->getUsername());
        (new LoggingActions(new LoggingActions()))->UserPhotoDownload($db, $u_id,$id);

        $path='images/photos/'.$rez[1];
        $source_img = convertToGD($path);

        if ($_POST['options-sepia'] == "YES") {
            imagefilter($source_img, IMG_FILTER_GRAYSCALE);
            imagefilter($source_img, IMG_FILTER_COLORIZE,100,50,0);
        }

//        $blur =(int)$_POST['options-blur'];
//        for ($i = 0; $i < $blur; $i++) {
//            imagefilter($source_img, IMG_FILTER_SELECTIVE_BLUR);
//        }

        $edit = $_POST['options-base'];
        $exp_path = explode(".", $rez[1]);

        if ($edit == "AsIs") {
            switch (end($exp_path)){
                case "webp":
                    header("Content-Type: image/webp");
                    header('Content-Disposition: attachment; filename="'.$rez[1].'"');
                    imagewebp($source_img);
                    break;

                case "png":
                    imagealphablending($source_img, false);
                    imagesavealpha($source_img, true);
                    header("Content-type: image/png");
                    header('Content-Disposition: attachment; filename="'.$rez[1].'"');
                    imagepng($source_img);
                    break;

                case "jpg":
                    header("Content-type: image/jpeg");
                    header('Content-Disposition: attachment; filename="'.$rez[1].'"');
                    imagejpeg($source_img);
            }
        }
        elseif ($edit == "PNG") {
            imagealphablending($source_img, false);
            imagesavealpha($source_img, true);
            header("Content-type: image/png");
            header('Content-Disposition: attachment; filename="'.reset($exp_path).'"');
            imagepng($source_img);
        }
        elseif ($edit == "JPG") {
            header("Content-type: image/jpeg");
            header('Content-Disposition: attachment; filename="'.reset($exp_path).'"');
            imagejpeg($source_img);
        }
        elseif ($edit == "WEBP") {
            header("Content-type: image/webp");
            header('Content-Disposition: attachment; filename="'.reset($exp_path).'"');
            imagewebp($source_img);
        }

        imagedestroy($source_img);
    }
    $db->Close();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Download image</title>
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
                        <h2>DOWNLOAD PHOTO</h2>

                        <div class="form-floating">
                            <p style="font-weight: bold">Do you wish to change your download format?</p>
                            <div class="form-check">
                                <input class="form-check-input btn-check" type="radio" name="options-base" id="AsIs" autocomplete="off" value="AsIs" checked>
                                <label class="btn" for="AsIs">Do not switch image format</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input btn-check" type="radio" name="options-base" id="PNG" autocomplete="off" value="PNG">
                                <label class="btn" for="PNG">PNG</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input btn-check" type="radio" name="options-base" id="JPG" autocomplete="off" value="JPG">
                                <label class="btn" for="JPG">JPG</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input btn-check" type="radio" name="options-base" id="WEBP" autocomplete="off" value="WEBP">
                                <label class="btn" for="WEBP">WEBP</label>
                            </div>
                        </div>

<!--                        <div class="form-floating">-->
<!--                            <p style="font-weight: bold">Do you wish to blur your photo download?</p>-->
<!--                            <div class="form-check">-->
<!--                                <input class="form-check-input btn-check" type="radio" name="options-blur" id="AsIs" autocomplete="off" value="0" checked>-->
<!--                                <label class="btn" for="AsIs">Do not apply blur</label>-->
<!--                            </div>-->
<!--                            <div class="form-check">-->
<!--                                <input class="form-check-input btn-check" type="radio" name="options-blur" id="once" autocomplete="off" value="1">-->
<!--                                <label class="btn" for="once">Once</label>-->
<!--                            </div>-->
<!--                            <div class="form-check">-->
<!--                                <input class="form-check-input btn-check" type="radio" name="options-blur" id="twice" autocomplete="off" value="2">-->
<!--                                <label class="btn" for="twice">Twice</label>-->
<!--                            </div>-->
<!--                            <div class="form-check">-->
<!--                                <input class="form-check-input btn-check" type="radio" name="options-blur" id="thrice" autocomplete="off" value="3">-->
<!--                                <label class="btn" for="thrice">Thrice</label>-->
<!--                            </div>-->
<!--                        </div>-->

                        <div class="form-floating">
                            <p style="font-weight: bold">Do you wish to apply the Sepia filter to your photo download?</p>
                            <div class="form-check">
                                <input class="form-check-input btn-check" type="radio" name="options-sepia" id="No" autocomplete="off" value="NO" checked>
                                <label class="btn" for="No">NO</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input btn-check" type="radio" name="options-sepia" id="Yes" autocomplete="off" value="YES">
                                <label class="btn" for="Yes">YES</label>
                            </div>
                        </div>

                        <div class="form-floating">
                            <input type="submit" name="download" class="btn btn-primary w-100 py-2" value="DOWNLOAD">
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </body>
</html>