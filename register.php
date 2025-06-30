<?php
    if (isset($_POST['create'])){
        require "patterns/UserBuilderFactory.php";
        require_once "MySQLiConfig.php";

        $submitflag = true;
        if (strcmp($_POST['password'],$_POST['confirm_password'])==0) {
            $passmatch=true;
            $universal_flag=false;
        }
        else {
            $passmatch=false;
            $universal_flag=true;
        }
    } else {$submitflag=false;}

    if ($submitflag && $passmatch){
        $uname=$_POST['username'];
        $pass=$_POST['password'];
        $email=$_POST['email'];
        $tier=$_POST['tier_select'];

        $db =MySQLiConfig::getInstance();
        try {
            $db->Connect();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $q = "select * from users where username LIKE '$uname'";
        try {
            $result = $db->Execute($q);
        } catch (Exception $e) {
            echo $e->getMessage();
        }


        if(!empty(mysqli_fetch_array($result))){
            $double_uname = true;
        } else {$double_uname=false;}

        $q = "select * from users where email = '$email'";
        try {
            $result = $db->Execute($q);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if(!empty(mysqli_fetch_array($result))){
            $double_email = true;
        } else {$double_email=false;}

        if (!$double_uname && !$double_email){
            $universal_flag = false;

            if (!empty($_FILES['pfp']['tmp_name']) && $_FILES['pfp']['error'] == 0) {
                echo "Not empty";
                var_dump($_FILES['pfp']);
                require "functions.php";

                $tempPath=$_FILES['pfp']['tmp_name'];

                if (getimagesize($tempPath)['mime'] == 'image/png') {
                    $pfp=true;
                }
                else {
                    $new_name_path="images/pfps/".$uname.".png";
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

            } else {
                $pfp=false;
            }

            try {
//                $user_build = new BrandNewLocalUser();
//                $user_build->setUsername($uname);
//                $user_build->setEmail($email);
//                $user_build->setPassword($pass);
//                $user_build->setUserTier($tier);
//                $user_build->setUserRole(2);
//                $user_build->setProfilePicture($pfp);
//                $user_build->setWeeklyPicturesUploaded(0);
//                $user_build->setUserType(1);

                $user = UserBuilderFactory::createBrandNewLocalUserBuilder()
                    ->setUsername($uname)
                    ->setEmail($email)
                    ->setPassword($pass)
                    ->setUserTier($tier)
                    ->setUserRole(2)
                    ->setProfilePicture($pfp)
                    ->setWeeklyPicturesUploaded(0)
                    ->setUserType(1)
                    ->getUser();



                $id=$user->addToDB($db);
                session_start();
                $_SESSION['created_user_id'] = $id;
            } catch (Exception $e) {
                echo $e->getMessage();
            }

        } else $universal_flag=true;
        $db->Disconnect();
    }
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Register user</title>
    </head>

    <body class="bg-dark">
        <div class="container justify-content-center d-flex align-items-center py-4 bg-body-tertiary">
            <main class="w-100 m-auto">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php
                        if(isset($_POST['create']) && !$universal_flag) {
                        $db =MySQLiConfig::getInstance();
                            try {
                                $db->Connect();
                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                            $id = $_SESSION['created_user_id'];
                        session_destroy();
                            try {
                                if (!empty(mysqli_fetch_array($db->Execute("SELECT * FROM users WHERE id LIKE '$id'"))))
                                    echo "<h2 style='color: limegreen'>USER REGISTERED SUCCESSFULLY!</h2>";
                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                        }
                    ?>
                    <h2>REGISTER NEW USER</h2>

                    <div class="form-floating">
                        <input type="text" name="username" id="username" required class="form-control">
                        <label for="username">Username:</label>
                        <?php if (isset($_POST['create']) && $passmatch && $double_uname) echo "<h2 style='color: red'>USERNAME $uname TAKEN, PLEASE CHOOSE ANOTHER ONE</h2>"?>
                    </div>

                    <div class="form-floating">
                        <input type="email" name="email" id="email" class="form-control" required>
                        <label for="email">Email:</label>
                        <?php if(isset($_POST['create']) && $passmatch && $double_email) echo "<h2 style='color: red'>EMAIL $email TAKEN, PLEASE CHOOSE ANOTHER ONE</h2>"?>
                    </div>

                    <div class="form-floating">
                        <input type="password" name="password" id="password" required class="form-control">
                        <label for="password">Password:</label>
                    </div>

                    <div class="form-floating">
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                        <label for="confirm_password">Confirm Password</label>
                        <?php if(isset($_POST['create']) && !$passmatch) echo "<h2 style='color: red'>PASSWORDS ENTERED ARE NOT THE SAME</h2>"?>
                    </div>

                    <div class="form-floating">
                        <input class="form-control form-control-sm" type="file" id="pfp" name="pfp">
                        <label for="pfp" class="form-label">Please submit a profile picture</label>
                    </div>

                    <div>
                        <select class="form-select form-select-lg mb-3" name="tier_select" required>
                            <option selected disabled value="">Please select a subscription tier</option>
                            <option value="1">FREE</option>
                            <option value="2">PRO</option>
                            <option value="3">GOLD</option>
                        </select>
                    </div>

                    <div class="form-floating">
                        <input type="submit" name="create" class="btn btn-primary w-100 py-2" value="REGISTER USER">
                    </div>

                    <div class="seperator"></div>
                </form>

                <a href="landing_page.php"><button class="btn btn-danger w-100 py-2">LOGIN PAGE</button></a>
            </main>
        </div>
    </body>
</html>
