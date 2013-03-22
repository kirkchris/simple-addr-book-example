<?php

$login_required = false;

require_once('include/site.php');

addr_show_top("Site Map","");

echo '<center><a href="index.php">Home</a><br>
<a href="contact.php">Contact Us</a><br>
<a href="register.php">Sign Up</a><br>
<a href="login.php">Login</a><br>
<a href="myaccount.php">My Account</a><br>
<a href="book.php">My Address Book</a><br>
<a href="print.php">Print Labels</a><br>
<a href="tour.php">Tour</a><br><br>';

addr_show_bottom();

?>