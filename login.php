<?php

	$username = $_POST["username"];
	$password = $_POST["password"];

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

				$dteStart = new DateTime($row["disabledWhen"]); 
				$dteEnd   = new DateTime();

				$dteDiff = $dteStart->diff($dteEnd);

				if ($dteStart->modify('+5 minutes') < $dteEnd) {
					echo "enabled";
					$sql = 'CALL activateUser("'.$row["idUsers"].'")';
					$result = $conn->query($sql);
				}

				echo $dteStart->format("h:i:s");
				echo $dteEnd->format("h:i:s");
				echo "User is disabled";
				echo $dteDiff->format("%H:%I:%S");
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

	// // function validates the password 
	// function fnValidatePassword( $sPassword ) {
	// 	// regular expression for validating password (letters, numbers, special character)
	// 	return filter_var( preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/', $sPassword));
	// }

?>