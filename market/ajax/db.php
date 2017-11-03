<?php
//made by Alec Repczynski
$limit = 5;
$adjacent = 2;
$con = mysqli_connect("localhost","dbuser","dbpassword","dbname");
if(mysqli_connect_errno()){
	echo "Database did not connect";
	exit();
}

?>
