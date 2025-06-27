<?php
    require_once "ConnectToDatabase.php";
    require "User.php";
    require_once "patterns/LogableActions.php";
    require_once "patterns/LoggingActions.php";
    session_start();

    $db = ConnectToDatabase::getInstance();
    $db->connect();

    if(isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
        $esc_user = $db->escapeString($user->getUsername());
        $query = "SELECT id FROM users WHERE username = '$esc_user'";
        $rez = mysqli_fetch_array($db->Execute($query));
        $id = array_pop($rez);
        (new LoggingActions(new LogableActions()))->UserLogout($db,$id);
    }
    else (new LoggingActions(new LogableActions()))->AnonymousUserLogout($db);
    $db->close();
    session_destroy();
    header("Location: landing_page.php");

