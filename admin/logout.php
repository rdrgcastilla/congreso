<?php
/**
 * Cierra la sesión del administrador.
 */
session_start();
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
