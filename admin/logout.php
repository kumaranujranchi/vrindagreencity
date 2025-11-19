<?php
require_once 'config.php';
initSession();
session_destroy();
header('Location: login.php');
exit();
?>
