<?php if (basename($_SERVER['PHP_SELF']) == 'addressbook.class.php') exit;

/* AddressBook class
Used for addressbook functions
*/

class AddressBook {

	var $userID;
	// only initialized sometimes
	var $name, $street, $email, $city, $state, $zip, $cellphone, $homephone;
	var $firstname, $lastname;
	
	// Sets the class to be specific for this user
	function AddressBook($uID) {
		$this->userID = $uID;
	}
	
	// Adds an entry to the users address book.
	function add_entry($name, $lname, $email, $street, $city, $state, $zip, $phone, $cellphone) {
		global $mysqli;
		
		$error=false;
		if($name == "") {
			$error = true;
			$errorName = "You must enter a first name";
		}
		if($lname == "") {
			$error = true;
			$errorLName = "You must enter a last name.";
		}
		if($street == "") {
			$error = true;
			$errorStreet = "You must enter the street address.";
		}
		if($city == "") {
			$error = true;
			$errorCity = "You must enter the city.";
		}
		if($state == "") {
			$error = true;
			$errorState = "You must enter the state.";
		}
		if($zip == "") {
			$error = true;
			$errorZip = "You must enter the zip.";
		}
		if($phone == "" && $_POST['cellphone'] == "") {
			$error = true;
			$errorPhone = "You must provide at least one phone number.";
		}
		if($email == "") {
			$error = true;
			$errorEmail = "You must enter an email.";
		}
		
		if($error == false) {
			// No Errors. Execute Entry.
			$fullname = $name." ".$lname;
			$query = $mysqli->prepare("INSERT INTO `listings` (`userID`,`name`,`firstname`,`lastname`,`email`,`street`,`city`,`zip`,`state`,`cellphone`, 
				`homephone`)
				VALUES
				(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")  or addr_track_error("Database Error", "There was an error with the database. Please try again.", $_SERVER['PHP_SELF']);
			$query->bind_param("issssssisss", $userID, $fullname, $name, $lname, $email, $street, $city, $zip, $state, $cellphone, $phone);
			$userID = $this->userID;
			$query->execute();
			$mysqli->commit();
			
			$query->close();
			$msg = $fullname." has been successfully added to your address book.";
		}
			
		
		$returnA = array(
			"error" => $error,
			"errorName" => $errorName,
			"errorLName" => $errorLName,
			"errorStreet" => $errorStreet,
			"errorCity" => $errorCity,
			"errorState" => $errorState,
			"errorZip" => $errorZip,
			"errorPhone" => $errorPhone,
			"errorEmail" => $errorEmail,
			"msg" => $msg
		);
		
		return $returnA;
	}
	
	
	// Displays the listing in order o flast name.
	function show_listings($page=1) {
		global $mysqli;
		$newpage = $page-1;
		$limits = $newpage*PAGE_SIZE;
		$stmt = $mysqli->prepare("SELECT `id`,`name`,`street`,`city`,`state`,`zip`,`cellphone`,`homephone`,`email` FROM `listings` WHERE `userID`=? ORDER BY `lastname` LIMIT ".$limits.",".PAGE_SIZE);
		$stmt->bind_param("s", $userID);
		$userID = $this->userID;
		$stmt->execute();
		$stmt->bind_result($listingID, $name, $street, $city, $state, $zip, $cell, $home, $email);
		while($stmt->fetch()) {
			echo "<tr><td><b><a href='?action=view&listingID=$listingID'>$name ".($home ? "Home: $home" : "Cell: $cell")."</b></a></td><td align='right'><a href='?action=edit&listingID=$listingID'>Edit</a> - <a href='?action=delete&listingID=$listingID'>Delete</a></tr>
			<tr><td><font size='2'>$street $city, $state $zip $email ".($home ? "Home: $home" : null)." ".($cell ? "Cell: $cell" : null)."</td></tr>";
		}
		$stmt->close();
		
	}
	
	// Displays a specific listing.
	function display_listing($listingID) {
		$this->get_entry_values($listingID);
		echo '<font size="4"><b>'.$this->name.'</b></font><br>
		'.nl2br($this->street).'<br>
		'.$this->city.', '.$this->state.', '.$this->zip.'.<br><br>
		'.($this->homephone ? "Home Phone: ".$this->homephone."<br>" : null).
		($this->cellphone ? "Cell Phone: ".$this->cellphone."<br>" : null).'
		Email: '.$this->email.'<br>';
	}
	
	// Returns the amount of listings.
	function listing_count() {
		global $mysqli;
		$result = $mysqli->query("SELECT `id` FROM `listings` WHERE `userID`='$this->userID'");
		return ($result->num_rows);
		$result->close();		
	}
	
	// Returns the values for a specific listing
	function get_entry_values($listingID) {
		global $mysqli;
		$stmt = $mysqli->prepare("SELECT `name`,`firstname`,`lastname`,`street`,`city`,`state`,`zip`,`cellphone`,`homephone`,`email` FROM 
			`listings` WHERE `userID`=? AND `id`=? LIMIT 1");
		$stmt->bind_param("ii", $userID, $listingID);
		$userID = $this->userID;
		$stmt->execute();
		$stmt->bind_result($name, $firstname, $lastname, $street, $city, $state, $zip, $cellphone, $homephone, $email);
		$stmt->fetch();
		
		$returnA = array(
			"id" => $listingID,
			"name" => $name,
			"firstname" => $firstname,
			"lastname" => $lastname,
			"street" => $street,
			"city" => $city,
			"state" => $state,
			"zip" => $zip,
			"cellphone" => $cellphone,
			"homephone" => $homephone,
			"email" => $email
		);
		
		$this->name = $name;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->street = $street;
		$this->city = $city;
		$this->state = $state;
		$this->zip = $zip;
		$this->cellphone = $cellphone;
		$this->homephone = $homephone;
		$this->email = $email;
		
		$stmt->close();
		
		return $returnA;
	}
	
	// Edits a specific listing
	function edit_entry($listingID, $name, $lname, $email, $street, $city, $state, $zip, $phone, $cellphone) {
		global $mysqli;
		
		$error=false;
		if($name == "") {
			$error = true;
			$errorName = "You must enter a first name";
		}
		if($lname == "") {
			$error = true;
			$errorLName = "You must enter a last name.";
		}
		if($street == "") {
			$error = true;
			$errorStreet = "You must enter the street address.";
		}
		if($city == "") {
			$error = true;
			$errorCity = "You must enter the city.";
		}
		if($state == "") {
			$error = true;
			$errorState = "You must enter the state.";
		}
		if($zip == "") {
			$error = true;
			$errorZip = "You must enter the zip.";
		}
		if($phone == "" && $_POST['cellphone'] == "") {
			$error = true;
			$errorPhone = "You must enter provide at least one phone number.";
		}
		if($email == "") {
			$error = true;
			$errorEmail = "You must enter an email.";
		}
		
		if($error == false) {
			// No Errors. Execute Entry.
			$fullname = $name." ".$lname;
			$query = $mysqli->prepare("UPDATE `listings` 
				SET `name`=?, `firstname`=?, `lastname`=?, `email`=?, `street`=?, `city`=?, `zip`=?, `state`=?, `cellphone`=?, `homephone`=? 
				WHERE `userID`=? AND `id`=? LIMIT 1")  or addr_track_error("Database Error", "There was an error with the database. Please try again.", $_SERVER['PHP_SELF']);
			$query->bind_param("ssssssisssii", $fullname, $name, $lname, $email, $street, $city, $zip, $state, $cellphone, $phone, $userID, $listingID);
			$userID = $this->userID;
			$query->execute();
			$mysqli->commit();
			
			$query->close();
			$msg = "You have successfully editted the entry for <a href='".SITE_URL."book.php?action=view&listingID=$listingID'>$fullname</a>.";
		}
			
		
		$returnA = array(
			"error" => $error,
			"errorName" => $errorName,
			"errorLName" => $errorLName,
			"errorStreet" => $errorStreet,
			"errorCity" => $errorCity,
			"errorState" => $errorState,
			"errorZip" => $errorZip,
			"errorPhone" => $errorPhone,
			"errorEmail" => $errorEmail,
			"msg" => $msg
		);
		
		return $returnA;
	}
	
	// Removes a listing
	function delete_entry($listingID) {
		global $mysqli;
		$query = $mysqli->prepare("DELETE FROM `listings` WHERE `userID`=? AND `id`=? LIMIT 1");
		$query->bind_param("ii", $userID, $listingID);
		$userID = $this->userID;
		$query->execute() or die($query->error);
		$mysqli->commit();
		return $query->affected_rows;
		$query->close();
	}
	
	// Gets the fullname for a listing.
	function get_name($listingID) {
		global $mysqli;
		$query = $mysqli->prepare("SELECT `name` FROM `listings` WHERE `userID`=? AND `id`=? LIMIT 1");
		$query->bind_param("ii", $userID, $listingID);
		$userID = $this->userID;
		$query->execute();
		$query->bind_result($name);
		$query->fetch();
		$this->name = $name;
		$query->close();
	}
	
	// Display labels for printing on envelopes
	function display_labels() {
		global $mysqli;
		$query = $mysqli->prepare("SELECT `name`,`street`,`city`,`zip`,`state` FROM `listings` WHERE `userID`=? ORDER BY `lastname`");
		$query->bind_param("i", $userID);
		$userID = $this->userID;
		$query->execute();
		$query->bind_result($name, $street, $city, $zip, $state);
		$count=1;
		while($query->fetch()) {
			echo '<td width="300" align="center">'.$name.'<br>
			'.$street.'<br>
			'.$city.', '.$state.' '.$zip.'</td>';
			if($count == 21) {
				echo '</td></tr></table>
				<hr class="break">
				<table border="0" width="1000"><tr>';
				$count=0;
			}
			elseif($count % 3 > 0) 
				echo '<td width="30"></td>';
			else {
				echo '</tr><tr height="30"><td></td></tr>';
				
			}
			$count++;
		}
	}
	
	// Displays a list with phone numbers for printing
	function display_lists() {
		global $mysqli;
		$query = $mysqli->prepare("SELECT `name`,`cellphone`,`homephone` FROM `listings` WHERE `userID`=? ORDER BY `lastname`");
		$query->bind_param("i", $userID);
		$userID = $this->userID;
		$query->execute();
		$query->bind_result($name, $cellphone, $homephone);
		$count=1;
		while($query->fetch()) {
			echo '<tr><td width="40"><img src="'.SITE_URL.'/images/check_box.gif"></td><td width="700" align="left">'.$name.' - '.($homephone ? ($cellphone ? "Home Phone: $homephone -" : "Home Phone: $homephone") : null).'
			'.($cellphone ? "Cell Phone: $cellphone" : null).'</td></tr>';
			$count++;
		}
	
	}


}

?>