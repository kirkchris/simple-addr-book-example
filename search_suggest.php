<?php

/*
search_suggest.php
Usage: File that is called via AJAX to load search results.
*/

$login_required = false;

require_once('include/site.php');

$limit = 6;//number of results to return
$term = $_POST['q'];

if(empty($term) || !isset($term)){echo 1;return false;}//error code 1 means empty string

//at this point, we simply search the database and either return an array of at most 6 closest matches or nothing at all
$additional = null;

$table = 'users';
$column = 'username';

//we escape all underscores with a query since underscores are wild cards when using LIKE
$term = str_replace("_","\_",$term);

$query = "SELECT `name` FROM `listings` WHERE `userID`=? AND `name` LIKE ? LIMIT $limit";

$term2 = "%".$term."%";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("is", $userID, $term2);
$userID = $User->id;
$stmt->execute();
$stmt->bind_result($fullname);
$stmt->fetch();


if($fullname == ""){echo 3;return false;}//tells the script that no results were found
else {
	$term = stripslashes($term);//we added the slashes for the query, we do not need them for the output
	$result = null;
	$result[] = preg_replace("/($term)/i","<b>$1</b>",$fullname,1);
	while($stmt->fetch()){
		$result[] = preg_replace("/($term)/i","<b>$1</b>",$fullname,1);
	}

	if(empty($result)){echo 4;return false;}
	for($i=0;$i<sizeof($result);$i++){$result[$i]=addslashes($result[$i]);}
	$results = implode('","',$result);
	echo 'var returnedArray = new Array("'.$results.'")';//the javascript will evaluate this
	return true;
}

?>
