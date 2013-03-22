<?php if (basename($_SERVER['PHP_SELF']) == 'string_cleaning.php') exit;

// prevent SQL injection
function addr_escape_string($val)
{
	global $mysqli;
	if (get_magic_quotes_gpc())
		$val = stripslashes($val);
	if (!is_numeric($val))
		$val = $mysqli->real_escape_string($val);
	return $val;
}

?>