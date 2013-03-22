<?php

/*
myaccount.php
Usage: Allows the user to update their account settings & branding
*/

$login_required = true;
require_once("include/site.php");

addr_show_top("My Account","");

if($_GET['action'] == "update") {

	// Updates the users account if no errors.
	$results = $User->update_info($_POST['name'],$_POST['email'],$_POST['curpass'],$_POST['newpass']);
	if($results["error"] == false) {
		$Branding->update_info($_POST['font'], $_POST['logo']);
	}
	
	if($results["passChange"] == true) {
		// User changed their pass. Needs to re-login.
		redirect(SITE_URL."logout.php?p=login.php",3);
		echo '<center>'.$results["msg"].'</center>';
		addr_show_bottom();
		exit();
	}
	
}

// Displays the page to allow inputs for user to update settings.
echo '<center><img src="'.SITE_URL.'images/my_account_big.gif" border="0"><br><br>
Use this page to customize your account and preferences.<br><br>
<b>Customization</b><br><br>
'.($results["msg"] ? $results["msg"].'<br><br>' : null).'
<table border="0"><tr><form method="POST" action="?action=update"><td>
Custom Logo URL: </td><td><input type="text" name="logo" class="inputreg" size="30" value="'.$Branding->logo.'"></td><td>(Not Required)</tr>
<tr><td>Printing Font Preference: </td><td><select name="font">';

$array = $Branding->font_array();

for($i = 0; $i < count($array); $i++ ) {
	echo '<option'.($Branding->font == $array[$i] ? " SELECTED" : null).'>'.$array[$i].'</option>';
}

echo '</select></td></tr>
<tr><td>Name: </td><td><input type="text" name="name" value="'.$User->name.'" class="inputreg"></td><td>'.$results["errorName"].'</td></tr>
<tr><td>Email: </td><td><input type="text" name="email" value="'.$User->email.'" class="inputreg"></td><td>'.$results["errorEmail"].'</td></tr>
<tr height="20"><td colspan="2"></td></tr>
<tr><td>Current Password: </td><td><input type="password" name="curpass" class="inputreg"></td><td>'.($results["errorPass"] ? $results["errorPass"] : "Not required unless changing your password").'</td></tr>
<tr><td>New Password: </td><td><input type="password" name="newpass" class="inputreg"></td></tr>
<tr><td colspan="2" align="center"><input type="image" src="'.SITE_URL.'images/update.gif"></td></tr>
</form></table>';

addr_show_bottom();


?>