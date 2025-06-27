<?php
    require_once "ConnectToDatabase.php";
    require "User.php";
    require "functions.php";
    session_start();

    $id = $_GET['id'];
    $u_id = findIDByUsername($_SESSION['user']->getUsername());
    $Q="DELETE FROM photos WHERE id=$id";

    $DB = ConnectToDatabase::getInstance();
    (new LoggingActions(new LogableActions()))->AdminPhotoDelete($DB,$Q,$u_id);
    header("Location: main_page.php");
