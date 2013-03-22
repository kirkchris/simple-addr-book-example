<?php

/*
Search.php
Usage: This page returns results from the search box.

*/

$login_required = true;

require_once('include/site.php');

$stmt = $mysqli->prepare("SELECT `id` FROM `listings` WHERE `name`=? AND `userID`=? LIMIT 1");
$stmt->bind_param("si", $fullname, $userID);
$userID = $User->id;
$fullname = $_POST['search_box'];
$stmt->execute();
$stmt->bind_result($id);
$stmt->fetch();

if($id) {
	// Name matches exactly, redirect to listing.
	redirect(SITE_URL.'book.php?action=view&listingID='.$id,0);
	$stmt->close();
} else {
	// Name doesn't match exactly. Now search and display all results.
	$count=0;
	$stmt->close();
	$stmt = $mysqli->prepare("SELECT `id`,`name` FROM `listings` WHERE `name` LIKE ? AND `userID`=?");
	$stmt->bind_param("si", $lastname, $userID);
	$userID = $User->id;
	$lastname = "%".$_POST['search_box']."%";
	$stmt->execute();
	$stmt->bind_result($id, $fullname);
	$stmt->fetch();
	addr_show_top("Search","");
	echo '<center><b>Search Results for '.$_POST['search_box'].'</b><br><br>';
	if($id) {
		echo '<a href="'.SITE_URL.'book.php?action=view&listingID='.$id.'">'.$fullname.'</a><br>';
		$count++;
		while($stmt->fetch()) {
			echo '<a href="'.SITE_URL.'book.php?action=view&listingID='.$id.'">'.$fullname.'</a><br>';
			$count++;
		}
	} 
	
	if($count==0) {
		// No results. Display message.
		echo 'Your search returned 0 results. Maybe you need to add this person to your address book <a href="'.SITE_URL.'book.php?action=add">here</a>.<br><br><br>';
	}
	
	$stmt->close();
	
	addr_show_bottom();
}






?>