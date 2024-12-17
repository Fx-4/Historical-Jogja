<?php
// process/check_auth.php
require_once 'Auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: ../adminlogin.php');
    exit;
}
?>
