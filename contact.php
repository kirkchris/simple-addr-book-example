<?php

/*
contact.php
Usage: Page displays the contact info
*/

$login_required = false;

require_once('include/site.php');

addr_show_top("Contact Us","");

echo "<center><b>Contact Us</b><br><br>
Have any questions, comments, concerns or feature requests?<br><br>
Then send us an email: staff [at] simplyaddress.com<br><br>
";

addr_show_bottom();

?>