<?php
    require_once "ConnectToDatabase.php";
    require_once "patterns/LogableActions.php";
    require_once "patterns/LoggingActions.php";

    $db = ConnectToDatabase::getInstance();
    $db->connect();
    ((new LoggingActions(new LogableActions()))->AnonymousUserLogin($db));
    $db->close();
    header("Location: main_page.php");
