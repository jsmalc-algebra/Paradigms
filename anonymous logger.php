<?php
    require_once "MySQLiConfig.php";
    require_once "patterns/LogableActions.php";
    require_once "patterns/LoggingActions.php";

    $db = MySQLiConfig::getInstance();
    $db->connect();
    ((new LoggingActions(new LogableActions()))->AnonymousUserLogin($db));
    $db->Disconnect();
    header("Location: main_page.php");
