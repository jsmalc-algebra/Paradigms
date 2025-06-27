<?php
    require_once "ConnectToDatabase.php";
    require "User.php";
    require "functions.php";
    session_start();

    $spotlighted_user = $_SESSION['showcased user'];
    if (isset($_POST['edit'])) {
        $db = ConnectToDatabase::getInstance();
        $db->connect();

        $new_name = $_POST['username'];
        $new_tier = $_POST['tier_select'];

        if (!empty($_FILES['pfp']['tmp_name'])) {
            $tempPath=$_FILES['pfp']['tmp_name'];

            if (getimagesize($tempPath)['mime'] == 'image/png') {
                $pfp=true;
            }

            else {
                $new_name_path="images/pfps/".$new_name.".png";
                $sourceImage = convertToGD($tempPath);

                if ($sourceImage) {
                    $pfp = makePNG($sourceImage, $new_name_path);
                    if (!$pfp) echo "Error in image uploading";
                    imagedestroy($sourceImage);
                }
                else  {
                    echo "Error in image uploading";
                }
            }
        }
        else $pfp = false;

        $spotlighted_user->setUserName($new_name);
        $spotlighted_user->setUserTier($new_tier);
        $spotlighted_user->setPfp($pfp);

        $spotlighted_user->alterDB($db,findIDByUsername($_SESSION['user']->getUsername()),$_GET['id']);

    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Edit user</title>
</head>

<body class="bg-dark">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-black">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php if ($_SESSION['user']->getUserRole()==1) echo 'admin_dashboard.php'; else echo '#'?>">
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
            if(isset($_POST['edit'])) {
                    echo "<h2 style='color: limegreen'>USER EDITED SUCCESSFULLY!</h2>";}
            ?>
            <h2>EDIT USER</h2>

            <div class="form-floating">
                <input type="text" name="username" id="username" required class="form-control" value="<?php echo $spotlighted_user->getUsername()?>">
                <label for="username">Username:</label>
            </div>

            <div class="form-floating">
                <input class="form-control form-control-sm" type="file" id="pfp" name="pfp" <?php if ($spotlighted_user->getPfp()==1) echo 'value=images/pfps/'.$spotlighted_user->getUsername()?>>
                <label for="pfp" class="form-label">Please submit a profile picture</label>
            </div>

            <div>
                <select class="form-select form-select-lg mb-3" name="tier_select" required>
                    <option disabled value="">Please select a subscription tier</option>
                    <option <?php if ($spotlighted_user->getUserTier()==1) echo 'selected'?> value="1">FREE</option>
                    <option <?php if ($spotlighted_user->getUserTier()==2) echo 'selected'?> value="2">PRO</option>
                    <option <?php if ($spotlighted_user->getUserTier()==3) echo 'selected'?> value="3">GOLD</option>
                </select>
            </div>

            <div class="form-floating">
                <input type="submit" name="edit" class="btn btn-danger w-100 py-2" value="EDIT USER">
            </div>
        </form>
    </main>
</div>
</body>
</html>
