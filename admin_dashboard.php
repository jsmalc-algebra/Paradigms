<?php
    require "User.php";
    require_once "MySQLiConfig.php";
    session_start();

    $db = MySQLiConfig::getInstance();
    $db->connect();
    $Q = "SELECT * FROM users WHERE role_id !=1";
    $rez = mysqli_fetch_all($db->Execute($Q));
    $db->Disconnect();

    function generateUserShowcaseFromArray($array)
    {
        echo "<div class='col img_showcase'>";
        if ($array[4]) echo "<img src=images/pfps/".$array[1].".png height='200' width='200'>";
        else echo "<img src='images/pfps/anonymous.png' height='200' width='200'>";
        echo "<a href='user_showcase.php?id=".$array[0]."' class='d-block text-decoration-none'>
            <button type='button' class='btn btn-danger btn-lg w-100'>USER ".$array[1]." SHOWCASE</button></a>";
        echo "</div>";

    }
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Admin dashboard</title>
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
        <main class="container px-4 bg-gradient bg-black">
            <?php
                $break_counter=0;
                foreach ($rez as $photo_array) {
                    if ($break_counter%5==0) echo "<div class='row'>";
                    generateUserShowcaseFromArray($photo_array);
                    $break_counter++;
                    if ($break_counter%5==0) echo "</div>";
                    if ($break_counter%5==0) echo "<div class='seperator'></div>";
                }
                if ($break_counter%5!=0) echo "</div>";
            ?>
        </main>
    </body>
</html>
