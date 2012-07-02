<?php
session_start();
unset($_SESSION['democracylab_user_id']);
unset($_SESSION['democracylab_user_role']);
header('Location: index.php');
?>