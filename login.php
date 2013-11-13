<?php
session_start(); // initiate the PHP session
if (isset($_SESSION['username'])) // check if the user is logged in
{
	$loggedIn = true;
	$username = $_SESSION['username']; // set the username into an easier variable we can use
}
else
	$loggedIn = false;

$error = ''; // error message holder

// check if we are acting on a form submission
if (isset($_POST['login']))
{
	require('functions.php'); // require that our functions file is present and include it

	// retrieve data from form post
	$login = $_POST['login'];
	$username = $_POST['username'];
	$password = md5($_POST['password']); // our form is unsafe because the password is transferred in plaintext over http

	if (isset($login) && !empty($username) && !empty($password)) // only act if data is valid
	{
		$mysqli = connect(); // connect to the database server
		$query = 'SELECT * FROM users WHERE username="' . $username . '" AND password="' . $password . '"';
		$result = $mysqli->query($query); // send our query to the server
		$num_results = $result->num_rows; // the number of results found
		$p = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
		$result->close(); // free result set
		disconnect($mysqli); // disconnect from the server

		if ($num_results > 0) // valid login found
		{
			$_SESSION['username'] = $p['name']; // set session variable
			header('Location: index.php'); // redirect to the front page
		}
		else
			$error = "Incorrect username or password, try again.\n";
	}
	else
		$error = "Please check that all fields are filled in.<br>\n";
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
		<form action="login.php" method="post">
		<?php echo '<strong>' . $error . '</strong>'; // display any error messages ?>
		<table>
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password"></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><input type="submit" name="login" value="Login"></td>
			</tr>
		</table>
		</form>
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