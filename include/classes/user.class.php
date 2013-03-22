<?php if (basename($_SERVER['PHP_SELF']) == 'user.class.php') exit;

/* User Class
Handles all of the User functions
*/

class User {

	var $username, $id, $email, $name;
	private $pass;
	
	// Initializes the User & retrieves info.
	function User($id=0,$name='') {
		global $mysqli;
		if($id == 0 && $name == '') {
			addr_track_error('Class Error','The User class was initialized incorrectly.',$_SERVER['PHP_SELF']);
		}
		
		$query = 'SELECT `id`,`username`,`name`,`email`,`password`
					  FROM `users` WHERE ';
		if ($id > 0) {
			$query .= '`id`=?';
			$stmt = $mysqli->prepare($query);
			$stmt->bind_param("i", $id);
			}
		else {
			$query .= 'username=?';
			$stmt = $mysqli->prepare($query);
			$stmt->bind_param("s", $name);
		}
		$stmt->execute();
		
		$stmt->bind_result($idR,$usernameR,$nameR,$emailR,$passR);
		$stmt->fetch();
		
		// Binds the Results to variables
		$this->id = $idR;
		$this->username = $usernameR;
		$this->name = $nameR;
		$this->email = $emailR;
		$this->pass = $passR;
		
		$stmt->close();
	}
	
	// Marks the users latest move, to track who's logged in & how long since they have used the site.
	function update_move() {
		global $mysqli;
		// Logs the users activity to view who's online.
		$stmt = $mysqli->prepare("UPDATE `users` SET `lastmove`='".time()."' WHERE `id`=? LIMIT 1");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();
		$mysqli->commit();
		$stmt->close();
	}
	
	// Updates the users info OR displays errors as to why it cannot be.
	function update_info($name, $email, $curpass, $newpass) {
		global $mysqli;
		
		$error = false;
		$passChangeT = false;
		if($name == "") {
			$error = true;
			$errorName = "You must enter your name.";
		}
		if($email == "") {
			$error = true;
			$errorEmail = "You must enter your email address.";
		}
		if((sha1($curpass) != $this->pass) && $newpass != "") {
			$error = true;
			$errorPass = "This is the incorrect old password.";
		}
		
		if($error == false) {
			$msg = "Your account settings were successfully updated.";
			$stmt = $mysqli->prepare("UPDATE `users` SET `name`=?, `email`=?, `password`=? WHERE `id`=? LIMIT 1");
			$stmt->bind_param("sssi", $name, $email, $passChange, $userID);
			if($newpass) {
				$passChange = sha1($newpass);
				$msg = "Your settings have been updated and your password changed. You must re-login now. You will be redirected.";
				$passChangeT = true;
			} else {
				$passChange = $this->pass;
			}
			$userID = $this->id;
			$stmt->execute();
			$mysqli->commit();
			$this->name = $name;
			$this->email = $email;
			$stmt->close();
		} else {
			$msg = "There was an error updating your account settings. Please see below.";
		}
		
		$array = array(
			"error" => $error,
			"errorName" => $errorName,
			"errorEmail" => $errorEmail,
			"errorPass" => $errorPass,
			"msg" => $msg,
			"passChange" => $passChangeT
		);
		
		return $array;
	
	}

}