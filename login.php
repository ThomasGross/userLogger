<?php

	if(isset($_POST["username"])){	
		$username = $_POST["username"];
	}

	if(isset($_POST["password"])){
		$password = $_POST["password"];
	}

	if(!$password || !$username)
	{
		exit;
	}


	// Create connection
	$servername = "localhost";
	$dbUsername = "root";
	$dbPassword = "root";
	$dbName = "mydb";

	$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

	$sql = 'SELECT * FROM Users WHERE Users.`username` = "'.$username.'"';
	$result = $conn->query($sql);


	// LOG USER
	$sql = 'CALL logUser("'.$username.'","'.$password.'")';
	$conn->query($sql);


	if ($result->num_rows == 1) {
		// output data of each row
		while($row = $result->fetch_assoc()) {

			// Check if user is disabled
			if ($row["active"] === "0") {

				$dateStart = new DateTime($row["disabledWhen"]); 
				$dateEnd   = new DateTime();

				$dateDiff = $dateStart->diff($dateEnd);

				if ($dateStart->modify('+5 minutes') < $dateEnd) {
					echo "enabled";
					$sql = 'CALL activateUser("'.$row["idUsers"].'")';
					$result = $conn->query($sql);
				}

				echo $dateStart->format("h:i:s");
				echo $dateEnd->format("h:i:s");
				echo "User is disabled";
				echo $dateDiff->format("%H:%I:%S");
				exit;
			} 

			// LOG PASSWORD FAILED LOGIN
			if ($row["password"] !== $password) {
				echo "wrong password";
			}
		}
	} else {
		
		// LOG USERNAME FAILED LOGIN
		echo "wrong username";
		exit;

	}

	$conn->close();


?>