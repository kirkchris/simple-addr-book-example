<?php

/* 
Register.php File
Purpose: This file allows users to register for our service.
*/

$login_required = false;
require_once('include/site.php');

addr_show_top('Sign Up','');

echo '<center>';

if($User) {
	// If user is logged in, displays this message so they cannot create a new account while logged in.
	addr_show_error("You already have any account!","Hey! What are you doing over at the sign up page? You already have an account! Why not go to your <a href='".SITE_URL."book.php'><b>Address Book</b></a>?<br><br>
	Or if you are trying to share our awesome service with a friend, then you have to <a href='".SITE_URL."'logout.php'>logout</a> before they can create a new account!");
}

if($_GET['action'] == "register") {
	// Checks for any errors with the users input.
	if($_POST['username'] == "") {
		$error = true;
		$errorUser = "You must enter a username";
	} else {
		$query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `username`=? LIMIT 1");
		$query->bind_param("s", $userN);
		$userN = $_POST['username'];
		$query->execute();
		$query->bind_result($id);
		$query->fetch();
		if($id) {
			$error = true;
			$errorUser = "This username is already <b>taken</b>.";
		}
		$query->close();
	}
	if($_POST['name'] == "") {
		$error = true;
		$errorName = "You must enter a name.";
	}
	if($_POST['email'] == "") {
		$error = true;
		$errorEmail = "You must enter an email address.";
	}
	if($_POST['password'] == "") {
		$error = true;
		$errorPass = "You must enter a password.";
	}
	
	$query = $mysqli->prepare("SELECT `id` FROM `users` WHERE `email`=? LIMIT 1");
	$query->bind_param("s", $_POST['email']);
	$query->execute();
	$query->bind_result($idE);
	$query->fetch();
	if($idE) {
		// Ther email has already been used. Stops them.
		addr_show_error("Email Already Used","This email has already been used for an account. You should <a href='".SITE_URL."login.php'>login here!</a>");
	}
	$query->close();
	
	if($error == false) {
		// NO ERRORS. Register the User!
		$stmt = $mysqli->prepare("INSERT INTO `users` (`username`,`password`,`email`,`name`,`regdate`) 
			VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssi", $username, $password, $email, $name, $time);
		$username = $_POST['username'];
		$password = sha1($_POST['password']);
		$email = $_POST['email'];
		$name = $_POST['name'];
		$time = time();
		$stmt->execute();
		$query = $mysqli->prepare("INSERT INTO `user_branding` (`userID`) VALUES (?)");
		$query->bind_param("i", $id);
		$id = $stmt->insert_id;
		$query->execute();
		$mysqli->commit();
		
		echo "You have successfully been registered! Please <a href='".SITE_URL."login.php'>login now!</a>";
		
		$query->close();
		$stmt->close();
	}
}

if($_GET['action'] != "register" || $error == true) {
	// Displays the registration input.
	echo '
	<img src="'.SITE_URL.'images/signup_msg.gif" border="0"><br><br>
	'.($error ? "<font color='red'>There was an error with your registration. Please see below.</font>" : null).'
	<table border="0" width="60%"><tr valign="top"><td>
	<table border="0" cellspacing="2" cellpadding="2"><tr>
	<form method="POST" action="?action=register">
	<td>Full Name: </td><td><input type="text" name="name" class="inputreg" value="'.$_POST['name'].'"></td><td>'.$errorName.'</td></tr><tr><td>
	Username: </td><td><input type="text" name="username" class="inputreg" value="'.$_POST['username'].'"></td><td>'.$errorUser.'</td></tr><tr><td>
	Password: </td><td><input type="password" name="password" class="inputreg" value="'.$_POST['password'].'"></td><td>'.$errorPass.'</td></tr><tr><td>
	Email: </td><td><input type="email" name="email" class="inputreg" value="'.$_POST['email'].'"></td><td>'.$errorEmail.'</td></tr><tr>
	<td align="center" colspan="3">
	<input type="image" src="'.SITE_URL.'images/signup_button.gif">
	</td></form></tr></table>
	</td><td width="30"><td>It takes just 30 seconds!<br><br>
	And it is completely <B>FREE</b>!</td></tr>
	</table>';
}
		
addr_show_bottom();


?>