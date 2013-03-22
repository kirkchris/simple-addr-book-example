<?php
/*

Page: login.php
Uses: Page is used to verify a user's username and password, and if correct allow them to login to the system.
Sets cookies: user, pass.

*/

$login_required = false;
require_once('include/site.php');

switch($_POST['action']) {

	default:
		// Display login page.
		addr_show_top('Login','');
		echo '<center><img src="'.SITE_URL.'images/login_text.gif">
		<form method="POST">
		<input type="hidden" name="action" value="login">
		<table border="0" cellspacing="2" cellpadding="2"><tr><td>
		Username: </td><td><input type="text" name="username" class="inputreg"></td>
		<tr><td>
		Password: </td><td><input type="password" name="password" class="inputreg"></td></tr>
		<tr><td align="center" colspan="2"><input type="image" src="'.SITE_URL.'images/login_button.gif"></td></tr>
		</table>
		</form>
		Or if you don\'t have any account. <a href="'.SITE_URL.'register.php">Sign up here in under 30 seconds!</a><br>
		<img src="'.SITE_URL.'images/login_text.gif">';
		addr_show_bottom();
		
	break;
	
	case 'login':
		// Attempt to login
		if($_POST['username'] == "" || $_POST['password'] == "") {
			// Missing info. STOP.
			addr_show_top('Login','');
			addr_show_error('Login Error','Youre missing a username or password.');
		}
		
		$stmt = $mysqli->prepare("SELECT `id`,`password`,`username` FROM `users` WHERE `username`=? LIMIT 1");
		$stmt->bind_param("s", $_POST['username']);
		$stmt->execute();
		$stmt->bind_result($id, $passW, $userN);
		$stmt->fetch();
		if($passW == "" || $userN == "") {
			// Invalid username! STOP.
			addr_show_top('Login','');
			addr_show_error('Login Error','There was an error with your login credentials.');
		}
		elseif(sha1($_POST['password']) == $passW) {
			// LOGIN SUCCESSFUL. SET COOKIES & REDIRECT
			$passWE = sha1_encrypt($passW,$id);
			session_start();
			setcookie("user",$userN,time()+(60*60*24*7));
			setcookie("pass",$passWE,time()+(60*60*24*7));
			session_write_close();
			redirect(SITE_URL.'book.php',0);
			//echo 'You have been successfully logged in. You are being redirected now.';
		} else {
			// Invalid username and password. STOP!
			addr_show_top('Login','');
			addr_show_error('Login Error','Your username/password combo is incorrect.');
		}
	break;
	
}

?>