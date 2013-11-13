<?php
/*	Only authorized users may add posts. This script validates the user and allows user to fill
	in a form that adds a blog post. It then redirects to index.html */
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

// check if we are acting on a form submission
if (isset($_POST['submit']))
{
	require('functions.php'); // require that our functions file is present and include it

	$id = $_POST['id']; // post ID we are working with
	$title = $_POST['title'];
	if ($title == "") // if the user somehow bypasses our JavaScript check
		$title = '(untitled)';
	$body = $_POST['body'];
	if ($body == "") // if the user somehow bypasses our JavaScript check
		$body = '(no content)';
	edit_post($id, $title, $body); // convert special characters to HTML entities
	header('Location: index.php?st=' . $_POST['st'] . '&dir=n'); // redirect to the front page
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
		<!-- check if the form is empty before submission and set focus on the empty box if found -->
		<!-- return value is necessary to either continue with the submission or cancel it -->
		function check_form(form)
		{
			if (form.title.value == "")
			{
				alert("Please enter a title for the entry.")
				form.title.focus();
				return false;
			}
			else if (form.body.value == "")
			{
				alert("Please enter content for the entry.")
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
		if (!$loggedIn) // only registered and logged in users can edit a post
			echo "You must be logged in to edit a blog post. <button onclick=\"history.go(-1);\">Go back</button>\n";
		else
		{
			require('functions.php'); // require that our functions file is present and include it
			$mysqli = connect(); // connect to the database server
			$query = 'SELECT name FROM users, posts WHERE users.username = posts.username AND posts.postID=' . $id;
			$result = $mysqli->query($query); // send our query to the server
			$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
			// verify ownership of the post
			if ($username == $row['name'] || $username == 'Administrator') // only the owner or admin can edit posts
			{
				$query = 'SELECT * FROM posts WHERE postID=' . $id; // search query which will provide a table with the averages from each group
				$result = $mysqli->query($query); // send our query to the server
				$p = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
		?>
		<form action="edit.php" method="post" onsubmit="return check_form(this);">
			<input type="hidden" name="st" value="<?php echo $start; ?>">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<table>
				<tr>
					<td>Title:</td>
					<td><input type="text" size="104" name="title" value="<?php echo $p['title']; ?>"></td>
				</tr>
				<tr>
					<td valign="top">Entry:</td>
					<td><textarea cols="80" rows="15" name="body"><?php echo $p['body']; ?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="right"><a href="index.php?st=<?php echo $start; ?>&dir=n"><input type="button" name="cancel" value="Cancel"></a> <input type="submit" name="submit" value="Edit"></td>
				</tr>
			</table>
		</form>
		<?php
			}
			else
				echo 'You cannot edit this post because it is not yours. <button onclick="history.go(-1);">Go back</button>' . "\n";
			$result->close(); // free result set
			disconnect($mysqli); // disconnect from the server
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