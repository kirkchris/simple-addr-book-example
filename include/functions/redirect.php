<?php if (basename($_SERVER['PHP_SELF']) == 'redirect.php') exit;

// Redirects users to the page set.
function redirect($page, $time=1) {
	echo '<META HTTP-EQUIV=Refresh CONTENT="'.$time.'; URL='.$page.'">';

}

?>