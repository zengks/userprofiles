<?php

//clear all session data after logging out
session_start();

$_SESSION = array();

session_destroy();

header('Location: index.php');

?>