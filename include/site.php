<?php if (basename($_SERVER['PHP_SELF']) == 'site.php') exit;

/* 
Site.php File
Created: 3/7/10: Chris Kirk
Purpose: This file connects to the database and will create a user object if the user is logged in. It contains two functions 
that will display the header and footer of all pages. 
Usage: 
	$login_required = false; --> DEFAULTS to true. If TRUE will display an error message for pages where users need to be logged in.
	require_once("include/site.php");
	$title = 'Page Title';
	$header = 'Things inside <head></head>';
	addr_show_top($title,$header);
	echo 'Page Info';
	addr_show_bottom();
*/

define('PATH', dirname(__FILE__).'/');

require_once(PATH.'define.php');
require_once(PATH.'functions/db_connection.php');	
require_once(PATH.'functions/string_cleaning.php');
require_once(PATH.'functions/errors.php');
require_once(PATH.'functions/redirect.php');
require_once(PATH.'functions/password.php');
require_once(PATH.'classes/user.class.php');
require_once(PATH.'classes/addressbook.class.php');
require_once(PATH.'classes/branding.class.php');

// MySQL Database Connection
$mysqli = addr_mysql_conn();

$user = addr_escape_string(isset($_COOKIE['user']) ? $_COOKIE['user'] : null);
$pass = addr_escape_string(isset($_COOKIE['pass']) ? $_COOKIE['pass'] : null);

// Define the User & Branding Class
if(($user && $pass) || $login_required == true) {
	if($user) {
		// Verifies that user has correct login creditentials
		$query = $mysqli->prepare("SELECT `password`,`id` FROM `users` WHERE `username`=? LIMIT 1");
		$query->bind_param("s", $user);
		$query->execute();
		$query->bind_result($passw, $uID);
		$query->fetch();
		$query->close();
		
		$passMatch = addr_escape_string(sha1_encrypt($passw, $uID));
		
		if($passMatch == $pass) {
			// Password & Username Cookies are VALID. Initialize User class & Branding class.
			$User = new User($uID);
			$Branding = new Branding($uID);
			$User->update_move();
		} else {
			// Error with creditentials. Remove Cookies
			session_start();
			setcookie("user","",time()-1000);
			setcookie("pass","",time()-1000);
			session_write_close();
			echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL='.SITE_URL.'login.php?p='.basename($_SERVER['PHP_SELF']).'">';
			addr_track_error("Cookie Error","There is an error with your username/password cookies. Please login again.",$_SERVER['PHP_SELF']);

		}
		
	} elseif($login_required == true) {
		// Login was required & user is not logged in. displays error.
		addr_show_top();
		echo "<center>";
		addr_show_error('Login Required','You must be logged in to view this page. Please login <a href="'.SITE_URL.'login.php">here</a>.');
	}

}

/* Function: addr_show_top();
Called by pages to display the header and top of the site.
*/
function addr_show_top($title='Address Books Made Simple, Print Party Invite Lists, Print Party Phone Lists',$header='') {
	global $User, $Branding;
	$script = SITE_URL.'include/js/suggest.js';
	echo '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd"><link REL="SHORTCUT ICON" HREF="http://www.teckfusion.com/favicon.ico">
<link rel="stylesheet" href="'.SITE_URL.'style.css" type="text/css"/>
	<HTML>
	<head>
	<script type="text/javascript" src="'.$script.'"></script>
	<title>'.SITE_NAME.' - '.$title.'</title>
	<meta name="description" content="Address Books Made Simple, Print Party Invite Lists, Print Party Phone Lists. Easy to use printed party labels for weddings, parties, and events. Manage your contacts in an all in one place and add them easily. Also offering easy to use lists for phoning your contacts about a party, wedding, or event.">
    <meta name="keywords" content="wedding, invite, party, birthday, label, envelope, phone, address, book, contacts">
    <meta name="author" content="SimplyAddress.com">
	</head>
	<body>
	<div id="suggest_div" style="display:none;position:absolute;height:auto;top:100px;left:800px;z-index:99;width:150px;">&nbsp;</div>
	<div id="container">
	<div id="header"><div id="search">'.($User ? 'Logged In As <a href="myaccount.php">'.$User->username.'</a>&nbsp;&nbsp;&nbsp;' : null).'<table border="0" width="100%"><tr valign="middle"><form method="POST" action="'.SITE_URL.'search.php"><td width="800"></td><td><img src="'.SITE_URL.'images/search_text.gif" border="0"></td><td><input type="text" name="search_box" id="q" class="inputreg"></td><td><input type="image" src="'.SITE_URL.'images/go.gif"></td></form></tr></table></div>
	<div id="logo"><a href="'.SITE_URL.'"><img src="'.($Branding->logo ? $Branding->logo : SITE_URL."images/logo.gif").'" border="0"></a></div>
	<div id="menu"><table border="0" width="100%"><tr><td align="center">
	<img src="'.SITE_URL.'images/bar.gif" border="0"></td></tr>
	<tr><td align="center">';
	if(!$User) {
		echo '<a href="'.SITE_URL.'"><img src="'.SITE_URL.'images/home.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'tour.php"><img src="'.SITE_URL.'images/tour.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'register.php"><img src="'.SITE_URL.'images/signup.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'login.php"><img src="'.SITE_URL.'images/login.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'contact.php"><img src="'.SITE_URL.'images/contact.gif" border="0"></a>';
	} else {
		echo '<a href="'.SITE_URL.'book.php"><img src="'.SITE_URL.'images/addressbook.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'myaccount.php"><img src="'.SITE_URL.'images/my_account.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'print.php"><img src="'.SITE_URL.'images/print_labels.gif" border="0"></a>
		<img src="'.SITE_URL.'images/dash.gif" border="0">
		<a href="'.SITE_URL.'logout.php"><img src="'.SITE_URL.'images/logout.gif" border="0"></a>';
	}
		echo '</td></tr>
	<tr><td align="center"><img src="'.SITE_URL.'images/bar.gif" border="0"></td></tr></table>
	</div>
	</div>
	<div id="page">';
}

/* Function: addr_show_bottom();
Called by pages to display the bottom of the site template.
*/
function addr_show_bottom() {

	global $mysqli;
	echo '<br><br><br><center><font class="bottom">Copyright 2010 <a href="'.SITE_URL.'" class="bottom">Simply Address.com</a> - <a href="'.SITE_URL.'tour.php" class="bottom">Tour</a> - <a href="'.SITE_URL.'contact.php" class="bottom">Contact Us</a> - <a href="'.SITE_URL.'sitemap.php" class="bottom">Site Map</a></div></div>
	<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-1268452-5");
pageTracker._trackPageview();
} catch(err) {}</script>
</body></HTML>';
	addr_mysql_close($mysqli);

}




?>