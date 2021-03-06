<?php
	// Carries over session from login.
	session_start();

	// Set their username.
	$username = $_SESSION["username"];

	// If delete is set then delete the file delete is set to.
	if(isset($_GET['delete']))
	{
		$ftd = $_GET['delete'];
		unlink("../ftpserver/$username/$ftd");
		$url = 'https://skopossecurity.com/user/home';
        header( "Location: $url" );
	}

	// Check sessionID against the database.
	// Create connection to the database.
    $link = mysqli_connect("localhost", "root", "juicy", "skopos");
    
    // Check if connected. If not throw error.
    if($link === false)
    {
        die("ERROR: Unable to establish connection to the database: " . mysqli_connect_error()); // Function outputs the cause of the error after the string.
	}

	// Check them against the database for a match.
    $sql = "SELECT sessionID FROM sessions WHERE username='$username';";
	$result = mysqli_query($link, $sql);
	$testSession = mysqli_fetch_row($result);

	// If the user show their page, if not show an error.
	if (isset($_SESSION["sessionID"]) && $_SESSION["sessionID"] == $testSession[0])
        {
			?>

            <html>
			<head>
				<title>SKOPOS - <?php echo ucfirst($username); ?>'s Home</title>
				<link rel="stylesheet" type="text/css" href="home.css"/>
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
			</head>
			<body style="background-color:rgb(12, 170, 149);">

			<ul>
      			<li><a class="active" href="https://skopossecurity.com/user/home">Home</a></li>
      			<li><a href="https://skopossecurity.com/user/schedule">Schedule</a></li>
      			<li><a href="https://skopossecurity.com/user/settings">Settings</a></li>
				<li><form action="logout.php"><input type="submit" value="Logout" id="logout"/></form></li>
			</ul>

			<!--THIS BUTTON EXECUTES LOG OUT-->
			<form action="logout.php">
					<input type="submit" value="Logout" id="logout"/>
				</form>

			<?php // Grabs images from the ftp server.
				
				// Change the working directory to the user's account folder.
				chdir("../ftpserver/$username");

				// Grab all file names from the user's account folder with
				// the .jpg extension and store them in the images array.
				foreach(glob('*jpg') as $filename){
					$images[] = (basename($filename));
				}

				// Grab all file names from the user's account folder with
				// the .webm extension and store them in the clips array.
				foreach(glob('*webm') as $filename){
					$clips[] = (basename($filename));
				}

			?>

				<div class="jumbotron">
					<div class="col sm-4" id ="videos">
                        <h2 align = 'center'>Videos</h2>
						<!--LOOP DISPLAYING ALL VIDEOS IN USER'S ACCOUNT FOLDER-->
						<?php for($i = 0; $i < sizeof($clips); $i++): ?>
							<div class="row">
								<video class="clip-margin" width="512" height="384" controls>
									<source src="../ftpserver/<?php echo "$username/$clips[$i]"; ?>" type="video/webm">
									Your browser does not support the video tag.
                                </video>
                            </div>
                            <div style="text-align: center;">
								<a id="download" href="../ftpserver/<?php echo "$username/$clips[$i]"; ?>" download>Download</a>
                                <a id="delete" href="?delete=<?php echo $clips[$i] ?>">Delete</a>
                            </div>
						<?php endfor; ?>
					</div>

					<div class="col sm-4" id="images">
                        <h2 align = 'center'>Images</h2>
						<!--LOOP DISPLAYING ALL IMAGES IN USER'S ACCOUNT FOLDER-->
						<?php for($i = 0; $i < sizeof($images); $i++): ?>
							<div class="row">
								<img class="with-margin" src="../ftpserver/<?php echo "$username/$images[$i]"; ?>"  width="512" height="384">
                            </div>
                            <div style="text-align: center;">
								<a id="download" href="../ftpserver/<?php echo "$username/$images[$i]"; ?>" download>Download</a>
                                <a id="delete" href="?delete=<?php echo $images[$i] ?>">Delete</a>
                            </div>
						<?php endfor; ?>
					</div>
				</div>
				
			</body>
			</html>
		
		<?php
		}
		else
		{
			echo "Please login.";
		}

	// Close link to the database.
	mysqli_close($link);
?>
