<?php
    require "User.php";
    require_once "MySQLiConfig.php";
    require "functions.php";
    session_start();

    $id = $_GET['id'];
    $db = MySQLiConfig::getInstance();
    $db->Connect();
    $Query = "SELECT * FROM photos WHERE id = '$id'";
    $rez = mysqli_fetch_row($db->Execute($Query));
    $db->Disconnect();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Full image showcase</title>
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
                        else echo "USER anonymous";
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
        <div class="row">
            <img src="images/photos/<?php echo $rez[1]?>">
        </div>
        <div class="seperator"></div>
        <div class="vstack">
            <a href="download_image.php?id=<?php echo $id?>" class="d-block text-decoration-none"><button type="button" class="btn btn-primary btn-lg w-100">DOWNLOAD</button></a>
            <?php
                if(isset($_SESSION['user'])) {
                    $gen_flag = false;
                    $admin_flag = false;

                    if($_SESSION['user']->getUserRole()==1) $admin_flag = true;
                    else{
                        $u_id =findIDByUsername($_SESSION['user']->getUsername());
                        if ($u_id == $rez[4]) $gen_flag = true;
                    }

                    if($admin_flag || $gen_flag) echo '<a href="edit_image.php?id='.$id.'" class="d-block text-decoration-none" ><button type="button" class="btn btn-lg btn-success w-100">EDIT</button></a>';
                    if($admin_flag) echo '<a href="delete%20image.php?id='.$id.'" class="d-block text-decoration-none"><button type="button" class="btn btn-danger btn-lg w-100">DELETE</button></a>';
                }
            ?>
        </div>
    </body>
</html>
