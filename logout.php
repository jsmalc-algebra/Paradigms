<?php
    require_once "MySQLiConfig.php";
    require "User.php";
    require_once "patterns/LogableActions.php";
    require_once "patterns/LoggingActions.php";
    session_start();

    $db = MySQLiConfig::getInstance();
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
    $db->Disconnect();
    session_destroy();
    header("Location: landing_page.php");

