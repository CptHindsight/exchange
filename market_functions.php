<?php
//made by Alec Repczynski
include('./market/ajax/dbmanupulate.php');

/**
 * Format a timestamp to display its age (5 days ago, in 3 days, etc.).
 *
 * @param   int     $timestamp
 * @param   int     $now
 * @return  string
 */
function timetostr($timestamp, $now = null) {
    $age = ($now ?: time()) - $timestamp;
    $future = ($age < 0);
    $age = abs($age);

    $age = (int)($age);        // seconds ago
    if ($age == 0) return $future ? "momentarily" : "just now";

    $scales = [
        ["seconds", "seconds", 60],
        ["minute", "minutes", 60],
        ["hour", "hours", 24],
        ["day", "days", 7],
        ["week", "weeks", 4.348214286],     // average with leap year every 4 years
        ["month", "months", 12],
        ["year", "years", 10],
        ["decade", "decades", 10],
        ["century", "centuries", 1000],
        ["millenium", "millenia", PHP_INT_MAX]
    ];

    foreach ($scales as list($singular, $plural, $factor)) {
        if ($age == 0)
            return $future
                ? "in less than 1 $singular"
                : "less than 1 $singular ago";
        if ($age == 1)
            return $future
                ? "in 1 $singular"
                : "1 $singular ago";
        if ($age < $factor)
            return $future
                ? "in $age $plural"
                : "$age $plural ago";
        $age = (int)($age / $factor);
    }
}

function getpagination($pages,$rower) {
	$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn->select_db('dbname');
	
	$sql = "select * from `market_listings` order by listingid desc";
	$rows  = $conn->query($sql);
	$rows  = $rows->num_rows;
	
	mysqli_close($conn);
	if($rower == '1') {
		return $rows;	
	}
	return pagination(5,2,$rows,$pages);
}

function candelete($userhash, $checkinghash = 0) {
	$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	//is checkinghash set? if so, is it equal to userhash?
	if($userhash == $checkinghash && $checkinghash != 0) {
		mysqli_close($conn);
		return 1;
	}
	
	$conn->select_db('web3_db1');
	
	$sql = "SELECT `userid` FROM `session` WHERE `sessionhash` = '$userhash' ORDER BY `lastactivity` DESC LIMIT 1";
	$rows  = $conn->query($sql);
	$row = $rows->fetch_array(MYSQLI_ASSOC);
	
	$sql1 = "SELECT `usergroupid` FROM `user` WHERE `userid` = '$row[userid]' ORDER BY `lastactivity` DESC LIMIT 1";
	$rows1  = $conn->query($sql1);
	$row1 = $rows1->fetch_array(MYSQLI_ASSOC);
	
	//usergroup permissions
	if($row1['usergroupid'] == '5' || $row1['usergroupid'] == '6' || $row1['usergroupid'] == '7') {
		mysqli_close($conn);
		return 1;
	} else {
		mysqli_close($conn);
		return 0;
	}
}

function getsessionhash($thisuserid) {
	$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn->select_db('web3_db1');
	
	$sql = "SELECT * FROM `session` WHERE `userid` = $thisuserid ORDER BY `lastactivity` DESC LIMIT 1";
	$rows  = $conn->query($sql);
	$row = $rows->fetch_array(MYSQLI_ASSOC);
	
	mysqli_close($conn);
	return $row['sessionhash'];
}

function deletelisting($listid) {
	$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn->select_db('web3_db1');
	
	$sql = "DELETE FROM `market_listings` WHERE `market_listings`.`listingid` = $listid";
	$rows  = $conn->query($sql);
	
	mysqli_close($conn);
	return $rows; //true or false
}

function print_row($query, $conn, $realuserid) {
	$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	
	$conn->select_db('web3_db1');

	$result = $conn->query($query);
	$error = '';
	
	if(!$result) {
		$error .= 'Error making listing!';
	}
	
	$listings .= '';
	
	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$sessionhash = getsessionhash($row['userid']);
		$realhash = getsessionhash($realuserid);
		$catimg = $row['categoryid'] == 1 ? 'limegreen' : 'crimson';
		$type = $row['type'] == 0 ? "Crypto Currency" : "eCurrency";
		$catname = $row['categoryid'] == 1 ? 'Sell' : 'Buy';
		$dealname = $row['dealid'] == 1 ? 'CG Escrow' : 'Direct Deal';
		$first = $row['categoryid'] == 1 ? $row['second_currency'].' '.$row['rate'].' / USD' : $row['first_currency'];
		$second = $row['categoryid'] == 1 ? $row['first_currency'] :  $row['second_currency'].' '.$row['rate'].' / USD';
		$deletecode = candelete($realhash, $sessionhash) == 0 ?: ' 
			<a href="./market.php?delete=1&listing_id='.$row['listingid'].'&listing_userid='.$row['userid'].'&securitytoken='.$sessionhash.'" 
			class="deletefunction smallfont" value="'.$row['listingid'].'" style="float: right; margin: 6px 3px 0 0;">[delete listing]</a>';
		$listings .= '
		<tr id="listing'.$row['listingid'].'">
			<td class="alt1" style="min-width: 23px; min-height: 25px;" name="categoryimage_'.$row['listingid'].'">
				<i class="fa fa-money" style="font-size: 20px; color: '.$catimg.';" alt="'.$catname.'"></i>
			</td>
			<td class="alt2 smallfont"  nowrap="nowrap" name="categoryname_'.$row['listingid'].'">'. $catname .'</td>
			<td class="alt1">
				<div name="title_'.$row['listingid'].'">'.$row['title'].''.$deletecode.'</div>
				<div class="smallfont">
					<span name="username_'.$row['listingid'].'" style="cursor:pointer" onclick="window.open(\'member.php?u='.$row['userid'].'\', \'_self\')">'.$row['username'].'</span>
				</div>
			</td>
			<td class="alt2 smallfont" name="date_'.$row['listingid'].'">'.timetostr($row['dateline'], time()).'</td>
			<td class="alt1 smallfont" name="have_'.$row['listingid'].'">'.$first.'</td>
			<td class="alt2 smallfont" name="want_'.$row['listingid'].'">'.$second.'</td>
			<td class="alt1 smallfont" name="price_'.$row['listingid'].'">$'.$row['price'].'</td>
			<td class="alt2 smallfont" name="dealtype_'.$row['listingid'].'">'.$dealname.'</td>
			<td class="alt1 smallfont" name="contact_'.$row['listingid'].'"><a href="tel:'.$row['phone_number'].'">'.$row['phone_number'].'</a>
			<span name="pmuser_'.$row['listingid'].'" style="cursor:pointer; float: right;" onclick="window.open(\'private.php?do=newpm&u='.$row['userid'].'\', \'_self\')">PM</span>
			</td>
		</tr>';
	}
	
	mysqli_close($conn);
	
	return $listings;
}

?>
