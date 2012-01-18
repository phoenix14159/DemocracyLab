<?php
session_start();
$_SESSION['page'] = 'home';
header('Location: http://local.democracylab.com/');
?>