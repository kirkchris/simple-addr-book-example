<?php if (basename($_SERVER['PHP_SELF']) == 'db_connection.php') exit;

/* MySQLI Connection File
Created on: 3/7/10 @ 1:52PM.
Last Update: 3/7/10 @ 1:52PM by Chris Kirk. Created Functions for connection & disconnection
*/

// Connection Function 
function addr_mysql_conn() {
	$mysqli = new mysqli("198.64.248.114", "ampush_user", "Q1jMAsdi", "ampush_main");
	$mysqli->autocommit(FALSE);
	return $mysqli;
}

// Disconnection Function
function addr_mysql_close($mysqli) {
	$mysqli->close();
}

?>