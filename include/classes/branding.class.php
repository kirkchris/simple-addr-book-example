<?php if (basename($_SERVER['PHP_SELF']) == 'branding.class.php') exit;

/* Class to handle the User Branding with custom font & logo 
*/

class Branding {

	var $userID, $logo, $font;
	
	// Initializes & retreives the users branding info.
	function Branding($uID){
		global $mysqli;
		
		$query = $mysqli->prepare("SELECT `logo`,`font` FROM `user_branding` WHERE `userID`=? LIMIT 1");
		$query->bind_param("i", $userID);
		$userID = $uID;
		$query->execute();
		$query->bind_result($logo, $font);
		$query->fetch();
		
		$this->userID = $uID;
		$this->logo = $logo;
		$this->font = $font;
		
	}
	
	// Returns an array of the available fonts.
	function font_array() {
		$fonts = array('Calibri', 'Arial', 'Helvetica', 'Verdana', 'Comic Sans MS', 'Impact', 'Tahoma', 'Palatino Linotype');
		return $fonts;
	}
	
	// Updates the users font and logo.
	function update_info($font, $logo) {
		global $mysqli;
		$query = $mysqli->prepare("UPDATE `user_branding` SET `font`=?, `logo`=? WHERE `userID`=? LIMIT 1");
		$query->bind_param("ssi", $font, $logo, $userID);
		$userID = $this->userID;
		$this->font = $font;
		$this->logo = $logo;
		$query->execute();
		$mysqli->commit();
		$query->close();
	}


}


?>