<?php
// Detect the current session
session_start();

// End the current session
session_destroy();


//Navigate to login page
header("Location: login.php");


?>