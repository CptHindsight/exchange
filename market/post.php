<?php
//made by Alec Repczynski
include('../market_functions.php');

$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

$conn->select_db('dbname');

$title = $_POST['listing_title'];
$price = $_POST['listing_price'];
$rate = $_POST['listing_rate'];
$username = $_POST['listing_username'];
$userid = $_POST['listing_userid'];
$realname = $_POST['listing_realname'];
$categoryid = $_POST['category_id'];
$cashid = $_POST['cash_id'];
$securitytoken = $_POST['securitytoken'];
$dateline = time();
$views = '0';
$open = '1';
$visible = '1';
$sticky = '0';
$votenum = '0';
$votetotal = '0';
$first_currency = ($cashid == 1) ? $_POST['ecurrency'] : $_POST['cryptocurrency'];
$second_currency = $_POST['currencies'];
$phone_number = $_POST['listing_phone'];
$dealid = $_POST['deal_id'];

if(empty($_POST['listing_rate'])) {
	$rate = 1;
}

$userbanned = "SELECT COUNT(*) FROM `userban` WHERE `userid` = $userid";
$sqll = "SELECT * FROM `user` WHERE `userid` = $userid ORDER BY `lastactivity` DESC LIMIT 1";
$rowss  = $conn->query($sqll);
$roww = $rowss->fetch_array(MYSQLI_ASSOC);

$realusername = $roww['username'];

if($userid == '0') {
	header('Location: ../market.php?page=1');
} elseif ($username != $realusername) {
	header('Location: ../market.php?page=1');
} elseif ($userbanned != 0) {
	header('Location: ../market.php?page=1');
} else {

$sql="INSERT INTO `market_listings` (`categoryid`, `title`, `name`, `price`, `username`, 
		`userid`, `dateline`, `views`, `open`, `visible`, 
		`sticky`, `votenum`, `votetotal`, `first_currency`,
		`second_currency`, `phone_number`, `dealid`, `type`, `rate`) 
	VALUES ('$categoryid', '$title', '$realname', '$price',  '$username',
		'$userid', '$dateline', '$views', '$open', '$visible',
		'$sticky', '$votenum', '$votetotal', '$first_currency',
		'$second_currency', '$phone_number', '$dealid', '$cashid', '$rate')";

$error = '';

if(!$conn->query($sql)) {
	$error .= 'Error making listing!';
}
$sql2 = "SELECT listingid FROM `market_listings` ORDER BY listingid DESC LIMIT 1";
$latestid = $conn->query($sql2);
}

if (!isset($_POST['ajax'])) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Accessible Ajax Form Test.</title>
</head>

<body>

<?php
}

echo <<<LISTING_TEMPLATE
<td class="alt1" name="categoryimage_$userid"><i class="fa fa-money" style="font-size: 20px; color: limegreen;"></i></td>
<td class="alt2" name="categoryid_$userid">$categoryid</td>
<td class="alt1">
	<div name="title_$userid">$title</div>
	<div class="smallfont">
		<span name="username_$userid" style="cursor:pointer" onclick="window.open(\'member.php?u=$userid\', \'_self\')">$username</span>
	</div>
</td>
<td class="alt2" name="date_$userid">$dateline</td>
<td class="alt1" name="views_$userid">$views</td>
<td class="alt2" name="price_$userid">$listingtext</td>
LISTING_TEMPLATE;

if (!isset($_POST['ajax'])) {
	
	header('Location: ../market.php?page=1');
	
?>

</body>
</html>

<?php
mysqli_close($conn);
}
?>
