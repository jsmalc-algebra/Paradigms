<?php
    require_once "User.php";
    require_once "patterns/UserBuilderFactory.php";
    require_once "patterns/LogableActions.php";
    require_once "patterns/LoggingActions.php";
    session_start();


    if (isset($_SESSION["user"])) header('location: main_page.php');

    if(isset($_POST['login'])) {
        require "ConnectToDatabase.php";



        $db= ConnectToDatabase::getInstance();
        $db->Connect();

        $submited_email=$_POST['email'];

        $Q="SELECT * FROM users WHERE email='$submited_email'";
        $rez=mysqli_fetch_array($db->Execute($Q));

        if (!empty($rez) && password_verify($_POST['password'], $rez['password'])) {
            $good_login=true;
            try {
                $user = UserBuilderFactory::createLoginUserBuilder()
                    ->setUsername($rez['username'])
                    ->setUserTier($rez['plan_id'])
                    ->setUserRole($rez['role_id'])
                    ->setProfilePicture($rez['profile_picture'])
                    ->setWeeklyPicturesUploaded($rez['weekly_pictures'])
                    ->getUser();
                (new LoggingActions(new LogableActions()))->UserLogin($rez[0],$db);
                $_SESSION["user"] = $user;

            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {$good_login=false;}
        $db->Close();
    }
?>

<html>
    <head>
        <title>Landing page</title>
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://kit.fontawesome.com/a3d8dd45fd.js" crossorigin="anonymous"></script>
    </head>

    <body class="bg-dark">
        <div class="container justify-content-center d-flex align-items-center py-4 bg-body-tertiary bg-dark-subtle">
            <main class="form-signin w-100 m-auto">
                <form method="post" action="">
                    <?php
                        if(isset($_POST['login'])) {
                            if (!$good_login) {
                                echo "<h2 style='color: red'>INCORRECT EMAIL/PASSWORD</h2>";
                                echo "<h2 style='color: red'>PLEASE TRY AGAIN</h2>";
                            } else {header("Location: main_page.php");}
                        }
                    ?>
                    <h2>WELCOME</h2>
                    <h2>SIGN IN, REGISTER, OR BROWSE ANONYMOUSLY</h2>
                    <div class="form-floating"><a class="btn-success btn w-100 py-2" href="register.php">Register account <span class="fa fa-id-card"></span></a></div>
                    <div class="form-floating"> <a class="btn btn-block btn-social btn-google btn-info w-100 py-2">Sign in with Google <span class="fa fa-google"></span></a></div>
                    <div class="form-floating"> <a class="btn btn-block btn-social btn-github btn-dark w-100 py-2">Sign in with GitHub <span class="fa fa-github"></span></a></div>
                    <div class="seperator"></div>
                    <div class="form-floating">
                        <input type="text" name="email" id="email" class="form-control" required>
                        <label for="email">Email:</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" name="password" id="password" required class="form-control">
                        <label for="password">Password:</label>
                    </div>
                    <input type="submit" name="login" class="btn btn-primary w-100 py-2" value="LOGIN">
                    <div class="form-floating">
                        <a href="anonymous%20logger.php"><button type="button" class="btn btn-link w-100 py-2" style="margin: auto">Browse anonymously</button></a>
                    </div>
                </form>
            </main>
        </div>
    </body>
</html>
