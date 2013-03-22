<?php

/*
Page: logout.php
Usage: to log the user out of their account. User can click and go to link Or be redirected.
Optional: redirect user to: logout.php?p=URL_of_Page_to_redirect_to_after_logout
*/

session_start();
setcookie("user","",time()-1000);
setcookie("pass","",time()-1000);
session_write_close();

require_once("include/functions/redirect.php");
require_once("include/define.php");

if($_GET['p']) {
	redirect(SITE_URL.$_GET['p'],0);
} else {
	redirect(SITE_URL,0);
}


?>