<?php
define('DB_HOST', 'localhost'); // constant for hostname
define('DB_USER', 'eng'); // constant for username
define('DB_PASS', 'trousers'); // constant for password
define('DB_BASE', 'TE_blog'); // constant for database name

// connect to the database server and select the database
function connect()
{
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_BASE); // open a new connection to the MySQL server
	if ($mysqli->connect_error) // error connecting to the server
	{
		echo "Error: unable to connect to the database. Please try again later.\n";
		exit;
	}

	return $mysqli; // returns an object which represents the connection to a MySQL server
}

// close and release the connection to the server
function disconnect($mysqli)
{
	$mysqli->close(); // closes the database connection
}

// add html chars
function get_posts($start, $dir, $loggedIn)
{
	$mysqli = connect(); // connect to the database server

	// determine the highest post id
	$query = 'SELECT postID FROM posts ORDER BY postID DESC LIMIT 1';
	$result = $mysqli->query($query); // send our query to the server
	$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
	$high = $row['postID'];

	// determine the lowest post id
	$query = 'SELECT postID FROM posts ORDER BY postID ASC LIMIT 1';
	$result = $mysqli->query($query); // send our query to the server
	$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
	$low = $row['postID'];

	// kludge -- if the requested start is > the highest id then the next if check will default to retrieve all posts
	if ($start > $high)
		$dir = '';

	// the query depends on the 'direction' that the user is requesting to go
	if ($dir == 'p') // prev goes towards newer posts; take the index given, search > ordered by ASC limit 5, reorder by DESC
		$query = 'SELECT * FROM ( SELECT * FROM posts WHERE postID > ' . $start . ' ORDER BY postID ASC LIMIT 5 ) q ORDER BY postID DESC';
	else if ($dir == 'n') // next goes towards older posts
		$query = 'SELECT * FROM posts WHERE postID < ' . $start . ' ORDER BY postID DESC';
	else // retrieve all posts
		$query = 'SELECT * FROM posts ORDER BY postID DESC';

	$result = $mysqli->query($query); // send our query to the server
	$num_results = $result->num_rows; // the number of results found

	if ($num_results == 0)
		echo "There are no posts to view here...\n";
	else
	{
		if ($num_results >= 5) // if more than 5 posts are returned, limit the for loop to only 5
			$num_results = 5;

		for ($i=0; $i<$num_results; $i++) // loop through all the results
		{
			$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
			$queComm = 'SELECT COUNT(comments.commID) FROM comments, posts WHERE comments.postID=posts.postID AND posts.postID=' . $row['postID'];
			$resComm = $mysqli->query($queComm); // send our query to the server
			$comm = $resComm->fetch_assoc(); // retrieve the next row in the results and increment the position counter
			$numComments = $comm['COUNT(comments.commID)']; // comment count for this post
			if ($i == 0) // previous 5 will be > the first result's id
				$start = $row['postID'];
			if (($num_results == 5 && $i == 4) || ($num_results < 5 && $i == $num_results)) // next 5 will be < the last result's id
				$next = $row['postID'];

			// format and display the post
			echo '<div><em>' . htmlspecialchars_decode($row['title']) . '</em><br>' . "\n"
				. '<font size="1">Posted by: ' . $row['username'] . ' at ' . $row['datetime'] . '</font><br>' . "\n" // TODO: convert username to print name, click name to return all posts by that user
				. htmlspecialchars_decode($row['body']) . "<br>\n";
			echo '<div style="text-align: right"><font size="1">'; // format and display edit, delete, and comment links
			if ($loggedIn) // only display edit and delete links if logged in
				echo '<a href="edit.php?st=' . $start . '&id=' . $row['postID'] . '">Edit post</a> | <a href="delete.php?st=' . $start . '&id=' . $row['postID'] . '" onclick="return confirm_delete();">Delete post</a> | ';
			echo '<a href="viewComment?st=' . $start . '&id=' . $row['postID'] . '">' . $numComments . ' comments</a></font></div></div><br><br>' . "\n"; // display comment count and link
		}

		// display directions to retrieve more posts
		echo '<center>';
		if ($start >= $high) // disable the link for previous if we are at the highest index
			echo 'Prev 5';
		else
			echo '<a href="index.php?st=' . $start . '&dir=p">Prev 5</a>';
		echo ' | ';
		if ($next <= $low) // disable the link for next if we are at the lowest index
			echo 'Next 5';
		else
			echo '<a href="index.php?st=' . $next . '&dir=n">Next 5</a>';
		echo "</center>\n";
	}
	$result->close(); // free result set
	disconnect($mysqli); // disconnect from the server
}

// retrieve a single post for viewing on the comment page
function get_post($id)
{
	$mysqli = connect(); // connect to the database server
	$query = 'SELECT * FROM posts WHERE postID=' . $id;
	$result = $mysqli->query($query); // send our query to the server
	$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
	echo '<div><em>' . htmlspecialchars_decode($row['title']) . '</em><br>' . "\n"
		. '<font size="1">Posted by: ' . $row['username'] . ' at ' . $row['datetime'] . '</font><br>' . "\n" // TODO: convert username to print name, click name to return all posts by that user
		. htmlspecialchars_decode($row['body']) . "<br>\n";
	$mysqli->query($query); // send our query to the server
	disconnect($mysqli); // disconnect from the server
}

// deletes a specified post id and its associated comments
function delete_post($id)
{
	$mysqli = connect(); // connect to the database server
	$result = $mysqli->query('DELETE FROM posts WHERE postID=' . $id); // delete post with the given id
	$result = $mysqli->query('DELETE FROM comments WHERE postID=' . $id); // delete associated comments
	disconnect($mysqli); // disconnect from the server
}

// add a post to the blog
function add_post($title, $body)
{
	session_start(); // initiate the PHP session
	if (isset($_SESSION['username'])) // check if the user is logged in
	{
		$loggedIn = true;
		$username = $_SESSION['username']; // set the username into an easier variable we can use
	}
	else
		$loggedIn = false;

	$mysqli = connect(); // connect to the database server

	// retrieve the username based on the display name
	$query = 'SELECT username FROM users WHERE name="' . $username . '"';
	$result = $mysqli->query($query); // send our query to the server
	$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
	$uname = $row['username']; // username

	$datetime = date('Y-m-d g:i:s A'); // format the date and time
	$title = $mysqli->escape_string(htmlspecialchars($title)); // escape MySQL characters in the string
	$body = $mysqli->escape_string(htmlspecialchars($body)); // escape MySQL characters in the string
	$query = 'INSERT INTO posts (title, body, username, datetime) VALUES ("' . $title . '", "' . $body . '", "' . $uname . '", "' . $datetime . '")';
	$result = $mysqli->query($query); // send our query to the server
	disconnect($mysqli); // disconnect from the server
}

// edit a specified post
function edit_post($id, $title, $body)
{
	$mysqli = connect(); // connect to the database server
	$title = $mysqli->escape_string(htmlspecialchars($title)); // escape MySQL characters in the string
	$body = $mysqli->escape_string(htmlspecialchars($body)); // escape MySQL characters in the string
	$query = 'UPDATE posts SET title="' . $title . '", body="' . $body . '" WHERE postID=' . $id;
	$mysqli->query($query); // send our query to the server
	disconnect($mysqli); // disconnect from the server
}

// retrieve the comments associated with a post
function get_comments($id)
{
	$mysqli = connect(); // connect to the database server
	$query = 'SELECT comments.username, comments.body, comments.datetime FROM comments, posts WHERE comments.postID=posts.postID AND posts.postID=' . $id .' ORDER BY comments.commID DESC';
	$result = $mysqli->query($query); // send our query to the server
	$num_results = $result->num_rows; // the number of results found
	for ($i=0; $i<$num_results; $i++) // loop through all the results
	{
		$row = $result->fetch_assoc(); // retrieve the next row in the results and increment the position counter
		echo '<em><font color="#D6FFAD">' . $row['username'] . '</font></em> at ' . $row['datetime'] . "<br>\n";
		echo htmlspecialchars_decode($row['body']) . "<br><br>\n";
	}
	$result->close(); // free result set
	disconnect($mysqli); // disconnect from the server
}

// add acomment to a post
function add_comment($id, $body, $username)
{
	$mysqli = connect(); // connect to the database server
	$datetime = date('Y-m-d g:i:s A'); // format the date and time
	$body = $mysqli->escape_string(htmlspecialchars(htmlspecialchars($body))); // escape MySQL characters in the string
	$query = 'INSERT INTO comments (username, body, postID, datetime) VALUES ("' . $username . '", "' . $body . '", ' . $id . ', "' . $datetime . '")';
	$mysqli->query($query); // send our query to the server
	disconnect($mysqli); // disconnect from the server
}
?>