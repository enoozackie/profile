<?php
require __DIR__ . "/../vendor/autoload.php";

use Lourdian\BasicStudent\Model\User;

session_start();
$user = new User();
$user->logout();
