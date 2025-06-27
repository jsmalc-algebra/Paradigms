<?php
    require "patterns/UserBuilderFactory.php";
    require_once "functions.php";
    require_once "ConnectToDatabase.php";
    require "patterns/LogsIterator.php";
    session_start();

    $db = ConnectToDatabase::getInstance();
    $db->connect();

    if (!isset($_SESSION['reverse_order'])) $_SESSION['reverse_order'] = false;
    $reverse_order = $_SESSION['reverse_order'];

    $id = $_GET['id'];
    $Q="SELECT * FROM users WHERE id=$id";
    $rez = mysqli_fetch_array($db->Execute($Q));

    $logs = "SELECT * FROM logs WHERE user_id=$id";
    $user_logs = new LogsIterator();
    $user_logs->addItems(mysqli_fetch_all($db->Execute($logs)));
    $db->close();

try {
    $user_showcase = UserBuilderFactory::createShowcasedUserBuilder()
        ->setUsername($rez['username'])
        ->setUserTier($rez['plan_id'])
        ->setProfilePicture($rez['profile_picture'])
        ->setWeeklyPicturesUploaded($rez['weekly pictures uploaded'])
        ->getUser();

    $_SESSION['showcased user'] = $user_showcase;
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/style.css">
        <title>User profile showcase</title>
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
        <div class="container-fluid <?php
        if ($user_showcase->getUserTier()==3) echo "bg-warning";
        elseif (checkUserConsumption($user_showcase)) echo "bg-success";
        else echo "bg-danger";
        ?>">
            <div class="row">
                <img src="images/pfps/<?php if ($user_showcase->getPfp()==1) echo $user_showcase->getUsername(); else echo 'anonymous'?>.png" class="col">


                <h2 class="col">CURRENT PLAN: <?php
                    switch ($user_showcase->getUserTier()) {
                        case 1: echo "FREE";
                            break;
                        case 2: echo "PRO";
                            break;
                        case 3: echo "GOLD";
                    }
                    ?></h2>
                <h2 class="col">CURRENT CONSUMPTION: <?php
                    switch ($user_showcase->getUserTier()) {
                        case 1: $limit = 7;
                            break;
                        case 2: $limit = 35;
                            break;
                        case 3: $limit = '∞';
                    }
                    echo $user_showcase->getWeeklyPicturesUploaded().'/'.$limit;
                    ?></h2>
            </div>
            <div class="row">
                <a href="edit_user.php?id=<?php echo $id?>" class="d-block text-decoration-none"><button type="button" class="btn btn-danger btn-lg w-100">EDIT USER</button></a>
            </div>
            <div class="row">
                <a href="user_showcase.php?id=<?php echo $id; $_SESSION['reverse_order']=!$_SESSION['reverse_order'] ?>" class="d-block text-decoration-none"><button type="button" class="btn btn-info btn-lg w-100">REVERSE LOG ORDER</button></a>
            </div>

            <div class="row">
                <form>
                    <table class="table">
                        <thead class="thead-dark">
                        <tr>
                            <td>ID</td>
                            <td>TIMESTAMP</td>
                            <td>EVENT</td>
                            <td>PHOTO_ID</td>
                        </tr>
                        </thead>

                        <tbody>
                        <?php
                        if ($reverse_order) $iterator = $user_logs->getReverseIterator();
                        else $iterator = $user_logs->getIterator();
                        foreach ($iterator as $log) {
                            $id=$log[0];
                            $time=$log[2];
                            $event=$log[3];
                            $photo_id=$log[4];

                            echo "<tr>";
                            echo "<td>$id</td>";
                            echo "<td>$time</td>";
                            echo "<td>$event</td>";
                            echo "<td>$photo_id</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </body>
</html>

