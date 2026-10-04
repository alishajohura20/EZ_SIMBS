<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../classes/Auth.php';
$auth = new Auth();
$auth->logout();
redirect(APP_URL . '/pages/auth/login.php');
