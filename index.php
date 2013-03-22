<?php

/*
index.php
Usage: Welcomes people to the site.
*/

$login_required = false;
require_once('include/site.php');

addr_show_top();

echo '<center><img src="'.SITE_URL.'images/welcome.gif" border="0"><br><br>
We are providing our users with a <b>new</b> way to manage your address book, and easily organize your contact with friends!<br><br>
We focus on the 3 core values:<br><br>
<font size="4">1. <b>Simplicity</b> -- It takes under 30 seconds to sign up and start using our service!<br>
2. <b>Usefulness</b> -- You can print out envelope mailing lists and party lists to call people for your next event in a matter of seconds!<br>
3. <b>Management</b> -- Create, Edit, and Remove your address book entries easily and quickly!<br><br>
<table border="0" cellspacing="4"><tr valign="top"><td><a href="'.SITE_URL.'tour.php"><img src="'.SITE_URL.'images/tour_go.gif" border="0"></a>
</td><td><a href="'.SITE_URL.'register.php"><img src="'.SITE_URL.'images/signup_now.gif" border="0"></a></td></tr>
</table>';

addr_show_bottom();

?>