<?php
session_start();
$_SESSION['page'] = 'entities';
$_SESSION['type'] = $_REQUEST['type'];
//header('Location: http://local.democracylab.com/');
header('Location: https://morning-ocean-5589.herokuapp.com/');
?>