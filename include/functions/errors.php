<?php if (basename($_SERVER['PHP_SELF']) == 'errors.php') exit;

/* errors.php
Function page for displaying errors around the site.
*/

// Just displays an error.
function addr_show_error($title, $msg) {
	// use this to display errors & end the page.
	echo '<center><b>'.$title.'</b><br><br>'.$msg.'<br>';
	addr_show_bottom();
	exit();
}

// Logs the error in the DATABASE. AND if $display=true then displays an error to the user. If false then does not display error, still logs to DB.
function addr_track_error($title, $msg, $page, $display='true') {	
	global $mysqli;
	$db = $mysqli->prepare("INSERT INTO `error_log` (`title`,`msg`,`page`,`ip`,`time`) VALUES (?,?,?,?,?)");
	$db->bind_param("ssssi", $title, $msg, $page, $_SERVER['REMOTE_ADDR'], time());
	$db->execute();
	$db->close();
	
	if($display == true) {
		addr_show_error($title,$msg);
	}

}

?>