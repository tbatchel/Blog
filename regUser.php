<?php
// Allow guests to register on the site
session_start(); // initiate the PHP session
if (isset($_SESSION['username'])) // check if the user is logged in
{
	$loggedIn = true;
	$username = $_SESSION['username']; // set the username into an easier variable we can use
}
else
{
	$loggedIn = false;
	$username = ''; // username holder
}	

$error = ''; // error message holder
$email = '';  // email holder
$name = ''; // name holder

// check if we are acting on a form submission
if (isset($_POST['signup']))
{
	require('functions.php'); // require that our functions file is present and include it

	// retrieve data from form post
	$signup = $_POST['signup'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$name = $_POST['name'];

	if (isset($signup) && !empty($username) && !empty($password) && !empty($email) && !empty($name)) // only act if data is valid
	{
		$mysqli = connect(); // connect to the database server
		$username = $mysqli->escape_string($username); // escape MySQL characters in the string
		$password = md5($mysqli->escape_string($password)); // escape MySQL characters in the string and encrypt the result with the MD5 method
		$email = $mysqli->escape_string($email); // escape MySQL characters in the string
		$name = $mysqli->escape_string($name); // escape MySQL characters in the string

		// check if username or email already exists
		$queUN = 'SELECT username FROM users WHERE username="' . $username . '" OR email="' . $email . '"';
		$resUN = $mysqli->query($queUN); // send our query to the server
		$numUN = $resUN->num_rows; // the number of results found
		if ($numUN > 0)
			$error = "Sorry, either that username or email is already registered with this site.<br>\n";
		else
		{
			$query = 'INSERT INTO users (username, password, email, name) VALUES ("' . $username . '", "' . $password . '", "' . $email . '", "' . $name . '")';
			$result = $mysqli->query($query); // send our query to the server
			disconnect($mysqli); // disconnect from the server

			$_SESSION['username'] = $name;
			$error = 'Username successfully created! <a href="index.php">Return home</a><br>' . "\n";
		}
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
		<form action="regUser.php" method="post">
		<?php echo '<strong>' . $error . '</strong>'; // display any error messages ?>
		Please choose a username and password (no limitations on either).<br>
		<table>
			<tr>
				<td>Username:</td>
				<td><input type="text" name="username" value="<?php echo $username ?>"></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password"></td>
			</tr>
			<tr>
				<td>Email address:</td>
				<td><input type="text" name="email" value="<?php echo $email ?>"></td>
			</tr>
			<tr>
				<td>Display name:</td>
				<td><input type="text" name="name" value="<?php echo $name ?>"></td>
			</tr>
			<tr>
				<td colspan="2" align="right"><input type="submit" name="signup" value="Sign up!"></td>
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