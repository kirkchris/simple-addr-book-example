<?php if (basename($_SERVER['PHP_SELF']) == 'password.php') exit;

// Function for the encryption method, mixup algorithm.
function sha1_encrypt($password, $ID) {
	
	$unique = substr(0,8,$password);
	
	$passA = sha1($password.'18072927201241');
	$passB = sha1($passA.$ID);
	$passB = sha1($passB.$unique_salt);
	$passC = $unique . $passB;
	
	return $passC;

}

?>