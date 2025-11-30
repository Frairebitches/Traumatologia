<?php
session_start();
session_unset();
session_destroy();

// Volver al login
header("Location: auth.php");
exit;
?>
