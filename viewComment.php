<?php
// Allows all users to view comments on whichever post is chosen
require('functions.php'); // require that our functions file is present and include it
session_start(); // initiate the PHP session
if (isset($_SESSION['username'])) // check if the user is logged in
{
	$loggedIn = true;
	$username = $_SESSION['username']; // set the username into an easier variable we can use
}
else
	$loggedIn = false;

$start = $_GET['st'] + 1; // start index
$dir = $_GET['dir']; // direction of travel
$id = $_GET['id']; // post ID we are working with
?>
<html>
<head>
	<title>Life and Times of a CS Student</title>
	<style type="text/css">
		.title { color: #FFFFFF; }
		a { text-decoration: none; color: #6600CC; }
		a:visited { text-decoration: none; }
		a:hover { text-decoration: underline; }
		table { border-spacing: 0; border-collapse: collapse; }
		td { padding: 10; }
		body { font-family: "Segoe UI Light"; }
	</style>

	<script type="text/javascript">
		<!-- check if the form is empty before submission and set focus on the empty box if found -->
		<!-- return value is necessary to either continue with the submission or cancel it -->
		function check_form(form)
		{
			if (form.body.value == "")
			{
				alert("Please enter a comment.")
				form.body.focus();
				return false;
			}
			else
				return true;
		}
	</script>
</head>

<body>
<center>
<table width="1000">
	<tr>
		<td colspan="2" background="bg.jpg" height="170"><center><span class="title"><font face="Segoe UI" size="6"><strong><em>Life and Times of a CS Student</em></strong></font><br>
			<font size="2">a computer science student at the college of staten island</font></span></center></td>
	</tr>
	<tr>
		<td width="800" valign="top"><!-- content begins -->
		<?php
		get_post($id);
		echo "<br>\n"
			. "Comments:<br>\n";
		get_comments($id);
		echo "<br>\n";
		if ($loggedIn) // only display the add comment form if logged in
		{
			echo 'Add comment<br>
		<form action="actionAddComment.php" method="post" onsubmit="return check_form(this);">
			<input type="hidden" name="st" value="' . $start . '">
			<input type="hidden" name="id" value="' .$id . '">
			<textarea cols="80" rows="15" name="body"></textarea><br>
			<a href="index.php?st=' . $start . '&dir=n"><input type="button" name="cancel" value="Cancel"></a> <input type="submit" name="submit" value="Submit">
		</form>';
		}
		?>
		<!-- content ends -->
		</td>
		<td width="200" valign="top" bgcolor="#D6FFAD">
			<?php
			if ($loggedIn) // display a greeting if logged in
				echo 'Hello, ' . $username . '!<br>'
					. '<a href="logout.php">Logout</a>' . "\n";
			else // otherwise give option to log in
				echo "<a href=\"login.php\">Login</a>\n";
			?><br><br>
			<a href="regUser.php">Register</a><br>
			<a href="index.php">Home</a><br>
			<?php if ($loggedIn) echo '<a href="addBlogPosting.php">New post</a>' // only display 'new post' link if logged in ?></td>
	</tr>
</table>
</center>

</body>
</html>