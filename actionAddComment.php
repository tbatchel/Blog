<?php
/*	Only authorized users may add comments. This script validates the user and allows the user to
	fill in a form that adds a comment. It this redirects to the page that shows all comments on
	that particular post. */
session_start(); // initiate the PHP session
if (isset($_SESSION['username'])) // check if the user is logged in
{
	$loggedIn = true;
	$username = $_SESSION['username']; // set the username into an easier variable we can use
}
else
	$loggedIn = false;
require('functions.php'); // require that our functions file is present and include it

$id = $_POST['id']; // post id
$st = $_POST['st'] - 1; // start index
$body = $_POST['body']; // comment body
if ($body != "") // if the user somehow bypasses our JavaScript check
	add_comment($id, htmlspecialchars($body), $username);

header('Location: viewComment?st=' . $st . '&id=' . $id); // redirect to the front page
?>