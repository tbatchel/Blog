<?php
/*
•	This is the front page of the blog. It displays the five most recent blog postings, most recent
	first! Each post includes the author’s name and date of creation. [Extra Credit: Display only the
	first 200 words of every post, with a link to get more]
•	At the bottom of the page there are two links – one for previous 5 postings, and one for next 5
	posts.
•	At the bottom of each post, there are two links, one to add a comment to this post, and one to
	read comments to this post. Make sure that these links work!
•	At the bottom of the page there are also options to login or add a post or logout.
*/
session_start(); // initiate the PHP session
if (isset($_SESSION['username'])) // check if the user is logged in
{
	$loggedIn = true;
	$username = $_SESSION['username']; // set the username into an easier variable we can use
}
else
	$loggedIn = false;

require('functions.php'); // require that our functions file is present and include it

// retrieve start and direction vlues from the URI
if (!isset($_GET['st']) && !isset($_GET['dir'])) // defaults values if not set
{
	$start = 9999; // kludge....
	$dir = 'p';
}
else
{
	$start = $_GET['st'];
	$dir = $_GET['dir'];
}
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
		<!-- confirm that the user wants to delete the selected post -->
		<!-- return value is necessary to either continue with the submission or cancel it -->
		function confirm_delete()
		{
			var msg = confirm("Are you sure you want to delete this post?");

			if (msg == true)
				return true;
			else
				return false;
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
		<?php get_posts($start, $dir, $loggedIn); ?>
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