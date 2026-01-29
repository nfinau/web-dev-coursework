<?php
require_once 'config.php';

// destroy all session data and redirect to login
$_SESSION = [];
session_unset();
session_destroy();

header('Location: index.php');
exit;
