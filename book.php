<?php

/* 
Book.php
Usage: Allows for editting, adding, viewing, and deleting entries into the users address book.
*/

$login_required = true;

require_once('include/site.php');

$Addr = new AddressBook($User->id);

$action = $_GET['action'];

switch($action) {

	default:
		// display listings here.
		addr_show_top('Address Book','');
		$listing_count = $Addr->listing_count();
		echo '<table border="0" width="85%"><tr><td align="left"><img src="'.SITE_URL.'images/addr_book.gif" border="0"></td>
		<td align="right"><a href="'.SITE_URL.'book.php?action=add">Add an Entry</a></td></tr></table>
		<br>';
		if($listing_count > 0) {
			echo 'Pages: ';
			$pages = ceil($listing_count / PAGE_SIZE);
			for($i = 1; $i <= $pages; $i++) {
				echo '<a href="?page='.$i.'">'.$i.'</a>';
				if($i != $pages)
					echo ', ';
			}
			if($_GET['page'] > $pages) 
				$page = $pages;
			elseif($_GET['page'] <= 1 || $_GET['page'] == "")
				$page = 1;
			else
				$page = $_GET['page'];
			echo '<table border="0" width="90%">';
			$Addr->show_listings($page);
			echo '</table>
			Pages: ';
			for($i = 1; $i <= $pages; $i++) {
				echo '<a href="?page='.$i.'">'.$i.'</a>';
				if($i != $pages)
					echo ', ';
			}
		} else {
			echo '<center>It looks as though you don\'t have any entries in your address book! <br><br>
			Why don\'t you add one by clicking
				<a href="'.SITE_URL.'book.php?action=add"><b>here</b></a>. <br><br>
				It is very simple and entries can be added in just seconds!</center>';
		}
		addr_show_bottom();
	break;
	
	/* Page for adding new entries */
	case 'add':
	
		// Check for errors in the form
		if($_GET['do'] == "enter") {
			$results = $Addr->add_entry($_POST['name'], $_POST['lname'], $_POST['email'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['phone'], $_POST['cellphone']);
			$error = $results["error"];			
		}
		
		// Page display for entry forms
		if($_GET['do'] != "enter" || $error == true) {
			addr_show_top("Add Entry","");
			echo '<center><b>Add New Entry</b><br><br>'.($error ? "There was an error with your entry! Make sure you have completed all of the required fields." : null).'<form method="POST" action="?action=add&do=enter">
			<table border="0" cellspacing="2" cellpadding="2" width="50%">
			<tr><td>First Name: </td><td><input type="text" name="name" value="'.$_POST['name'].'" class="inputreg"></td><td>'.$results["errorName"].'</td></tr><tr><td>
			Last Name: </td><td><input type="text" name="lname" value="'.$_POST['lname'].'" class="inputreg"></td><td>'.$results['errorLName'].'</td></tr><tr><td>
			Street Address: </td><td><textarea name="street" class="inputreg">'.$_POST['street'].'</textarea></td><td>'.$results["errorStreet"].'</td></tr><tr><td>
			City: </td><td><input type="text" name="city" value="'.$_POST['city'].'" class="inputreg"></td><td>'.$results["errorCity"].'</td></tr><tr><td>
			State: </td><td><input type="text" name="state" value="'.$_POST['state'].'" class="inputreg"></td><td>'.$results["errorState"].'</td></tr><tr><td>
			Zip Code: </td><td><input type="text" name="zip" value="'.$_POST['zip'].'" class="inputreg"></td><td>'.$results["errorZip"].'</td></tr><tr><td>
			Home Phone: </td><td><input type="text" name="phone" value="'.$_POST['phone'].'" class="inputreg"></td><td>'.$results["errorPhone"].'</td></tr><tr><td>
			Cell Phone: </td><td><input type="text" name="cellphone" value="'.$_POST['cellphone'].'" class="inputreg"></td><td>'.$results["errorPhone"].'</td></tr><tr><td>
			Email: </td><td><input type="text" name="email" value="'.$_POST['email'].'" class="inputreg"></td><td>'.$results["errorEmail"].'</td></tr>
			<tr><td align="center" colspan="2">
			<input type="image" src="'.SITE_URL.'images/add_contact.gif"></td><td></td></tr></table></form>';
			addr_show_bottom();
		} else {
			addr_show_top("Entry Added - ".$_POST['name'],"");
			echo '<center>'.$results["msg"].'<br><br>';
			addr_show_bottom();
		}
	break;
	
	/* Page for editting entries */
	case 'edit':
	
		$error = false;
		
		if($_GET['listingID'] == "") {
			addr_show_top("Edit Entry","");
			addr_show_error("Missing Listing ID","For some reason you are missing the listing ID, so we're not sure 
			which listing you are attempting to edit. Please go back and click the edit link again!");
		}
		
		// Check for errors in the form
		if($_GET['do'] == "enter") {
			$results = $Addr->edit_entry($_GET['listingID'], $_POST['name'], $_POST['lname'], $_POST['email'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['phone'], $_POST['cellphone']);
			$error = $results["error"];			
		}
	
		
		// Page display for entry forms
		if($_GET['do'] != "enter" || $error == true) {
			addr_show_top("Edit Entry","");
			$values = $Addr->get_entry_values($_GET['listingID']);
			echo '<center><b>Edit Entry</b><br><br>'.($error ? "There was an error with your entry! Make sure you have completed all of the required fields." : null).'
			<form method="POST" action="?action=edit&do=enter&listingID='.$_GET['listingID'].'">
			<table border="0" width="50%" cellspacing="2" cellpadding="2">
			<tr><td>
			First Name: </td><td><input type="text" name="name" class="inputreg" value="'.($results["errorName"] ? $_POST['name'] : $values['firstname']).'"></td><td>'.$results["errorName"].'</td></tr><tr><td>
			Last Name: </td><td><input type="text" name="lname" class="inputreg" value="'.($results["errorLName"] ? $_POST['lname'] : $values['lastname']).'"></td><td>'.$results["errorName"].'</td></tr><tr><td>
			Street Address: </td><td><textarea name="street" class="inputreg">'.($results["errorStreet"] ? $_POST['street'] : $values['street']).'</textarea></td><td>'.$results["errorStreet"].'</td></tr><tr><td>
			City: </td><td><input type="text" name="city"  class="inputreg" value="'.($results["errorCity"] ? $_POST['city'] : $values['city']).'"></td><td>'.$results["errorCity"].'</td></tr><tr><td>
			State: </td><td><input type="text" name="state"  class="inputreg" value="'.($results["errorState"] ? $_POST['state'] : $values['state']).'"></td><td>'.$results["errorState"].'</td></tr><tr><td>
			Zip Code: </td><td><input type="text" name="zip"  class="inputreg" value="'.($results["errorZip"] ? $_POST['zip'] : $values['zip']).'"></td><td>'.$results["errorZip"].'</td></tr><tr><td>
			Home Phone: </td><td><input type="text" name="phone"  class="inputreg" value="'.($results["errorPhone"] ? $_POST['phone'] : $values["homephone"]).'"></td><td>'.$results["errorPhone"].'</td></tr><tr><td>
			Cell Phone: </td><td><input type="text" name="cellphone"  class="inputreg" value="'.($results["errorPhone"] ? $_POST['cellphone'] : $values["cellphone"]).'"></td><td>'.$results["errorPhone"].'</td></tr><tr><td>
			Email: </td><td><input type="text" name="email" class="inputreg"  value="'.($results["errorEmail"] ? $_POST['email'] : $values["email"]).'"></td><td>'.$results["errorEmail"].'</td></tr>
			<tr><td align="center" colspan="3">
			<input type="image" src="'.SITE_URL.'images/edit_contact.gif"></td></form>
			</tr></table>';
			addr_show_bottom();
		} else {
			addr_show_top("Entry Editted - ".$_POST['name'],"");
			echo '<center>'.$results["msg"];
			addr_show_bottom();
		}
	break;
	
	/* Page for deleting entries */
	case 'delete':
	
		if($_GET['listingID'] == "") {
			addr_show_top("Edit Entry","");
			addr_show_error("Missing Listing ID","For some reason you are missing the listing ID, so we're not sure 
			which listing you are attempting to delete. Please go back and click the delete link again!");
		}
		
		if($_GET['confirm'] != "yes") {
			addr_show_top("Delete Entry","");
			echo '<center><b>Delete Entry</b><br><br>';
			$Addr->display_listing($_GET['listingID']);
			echo '<br>Are you sure you wish to delete this entry?<br><br>
			<a href="?action=delete&confirm=yes&listingID='.$_GET['listingID'].'">Yes</a> - <a href="?action=view&listingID='.$_GET['listingID'].'">No</a>';
			addr_show_bottom();
		}
		elseif($_GET['confirm'] == "yes") {
			addr_show_top("Delete Entry","");
			$success = $Addr->delete_entry($_GET['listingID']);
			echo '<center>';
			if($success > 0) {
				echo "The entry has been deleted.";
			} else {
				echo "There was an error deleting the entry. Please go back and try again.";
			}
			addr_show_bottom();
		}
		
	break;
	
	case 'view':
	// View an individual listing	
		if($_GET['listingID'] == "") {
			addr_show_top("Edit Entry","");
			addr_show_error("Missing Listing ID","For some reason you are missing the listing ID, so we're not sure 
			which listing you are attempting to view. Please go back and click the view link again!");
		}
		$Addr->get_name($listingID);
		addr_show_top("View Entry - ".$Addr->name,"");
		echo '<center><b>View Entry</b><br><br>';
		$Addr->display_listing($_GET['listingID']);
		echo '<br><br><a href="?action=edit&listingID='.$listingID.'">Edit</a> - <a href="?action=delete&listingID='.$listingID.'">Delete</a>';
		addr_show_bottom();
	break;
	
}


?>