<?php

/* Page: print.php
Usage: This page allows the user to print out their labels for enevelopes OR print a list for phone calls for a party invite.
*/

$login_required = true;

require_once('include/site.php');

$Addr = new AddressBook($User->id);

switch($_GET['action']) {

	default:
		// Asks the user which way they would like to print their addresses
		addr_show_top("Print Labels","");
		$listing_count = $Addr->listing_count();
		if($listing_count > 0) {
			echo '<center><b>Print Labels</b><br><br>
			<a href="?action=labels" target="_blank">Would you like to print labels for an envelope?</a>
			<br><br>
			OR <br><br>
			<a href="?action=lists" target="_blank">Would you like to print a calling list?</a>';
		} else {
			echo '<center><b>Print Labels</b><br><br>
			It is very hard to print labels for you when you don\'t have any entires in your address book!<br><br>
			Why don\'t you add some by clicking <a href="'.SITE_URL.'book.php?action=add"><b>here</b>!</a><br><br>';
		}
		addr_show_bottom();
	break;
	
	case 'labels':
		// Display page for label printing
		echo '
		<html><head><title>'.SITE_NAME.' - Print Labels</title>
		<style type="text/css">
		html,BODY {
			font-family: "'.$Branding->font.'";
		}
		</style>
		</head>
		<body>
		<center><table border="0" width="1000">
		<tr>';
		$Addr->display_labels();
		echo '</tr></table><br><br>
		Service provided by '.SITE_NAMEBC.'
		</body></html>';
	break;
	
	case 'lists':
		// Display page for phone call printing
		echo '
		<html><head><title>'.SITE_NAME.' - Print Lists</title>
		<style type="text/css">
		html,BODY {
			font-family: "'.$Branding->font.'";
		}
		</style>
		</head>
		<body>
		<center><table border="0" width="1000">
		<tr>';
		$Addr->display_lists();
		echo '</tr></table><br><br>
		Service provided by '.SITE_NAMEBC.'
		</body></html>';
	break;

}

?>