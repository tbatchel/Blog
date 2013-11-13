<?php
// Closes the session for logout
session_start(); // initiate the PHP session
session_destroy(); // destroys all data registered to a session
header('Location: index.php'); // redirect to the front page
?>