<?php
//made by Alec Repczynski
$conn = new mysqli('localhost', 'dbuser', 'dbpassword');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

include('./market_functions.php');
// ####################### SET PHP ENVIRONMENT ###########################
error_reporting(E_ALL & ~E_NOTICE & ~8192);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// #################### DEFINE IMPORTANT CONSTANTS #######################
//define('NO_REGISTER_GLOBALS', 1);
//define('THIS_SCRIPT', 'market'); // change this depending on your filename

// ################### PRE-CACHE TEMPLATES AND DATA ######################
// get special phrase groups
$phrasegroups = array(

);

// get special data templates from the datastore
$specialtemplates = array(
    
);

// pre-cache templates used by all actions
$globaltemplates = array(
    'CG Classic as at 22 Sep 2017',
);

// pre-cache templates used by specific actions
$actiontemplates = array(

);

// ######################### REQUIRE BACK-END ############################
require_once('./global.php');
define('DIE_QUIETLY', 1);
define('NO_REGISTER_GLOBALS', 1);
define('SESSION_BYPASS', 1);
define('THIS_SCRIPT', 'market');
define('CSRF_PROTECTION', true); 
require_once('./includes/init.php');
$vbphrase = init_language();
require('./includes/functions_user.php');

// #######################################################################
// ######################## START MAIN SCRIPT ############################
// #######################################################################

function getthisuser() {
	return $vbulletin->userinfo[userid];	
}

$navbits = array();
$navbits[$parent] = 'Exchange Marketplace';

$navbits = construct_navbits($navbits);

$page = $_GET['page'] <= 0 ? 1 : $_GET['page'];

if($page == 1) {
	$start = 0;  
} else {
	$start = ($page - 1) * $limit;
}

$delete = $_GET['delete'];
$listinguserid = $_GET['listing_userid'];

if($delete == 1 & $listinguserid != 0) {
	//delete variables
	$listinguserid = $_GET['listing_userid'];
	$securitytoken = $_GET['securitytoken'];
	$listingid = $_GET['listing_id'];
	$sessionhash = getsessionhash($listinguserid);
	
	if(candelete($securitytoken, $sessionhash) == 1) {
		deletelisting($listingid);
	}
}

$pag = getpagination($page,'1');
$rows = $pag/5;
$rows = ceil($rows);

$sessionhash = getsessionhash($vbulletin->userinfo[userid]);

$initial_load = "SELECT * FROM `market_listings` ORDER BY `market_listings`.`listingid` DESC LIMIT $start,5";
$market = '
<input type="checkbox" name="one" id="item-1" style="display: none;">
<label for="item-1" style="
position: absolute;
font-weight: 900;
color: white;
left: calc(64.5% + 40px);
padding-top: 7.5px;"></label>
<table class="tborder posthead" style="border-bottom: 0;" width="30%" align="center" cellspacing="1" cellpadding="6" border="0">
<tbody>
<tr>
<td class="tcat" width="100%">Post a New Listing 
</td>
</tr>
</tbody>
</table>
<table class="tborder fieldset posthead2" width="30%" align="center" cellspacing="1" cellpadding="3" border="0">
<td class="alt1">
<style>
input.categorysell[type=radio]:checked ~ label[for="price"]::after {
    content: "Bid";
}
input.categorysell[type=radio]:checked ~ label[for="have"]::after {
    content: "want";
}
input.categorysell[type=radio]:checked ~ label[for="want"]::after {
    content: "have";
}
input.categorybuy[type=radio]:checked ~ label[for="price"]::after {
    content: "Ask";
}
input.categorybuy[type=radio]:checked ~ label[for="have"]::after {
    content: "have";
}
input.categorybuy[type=radio]:checked ~ label[for="want"]::after {
    content: "want";
}
input.cryptobutton[type=radio]:checked ~ .ecurrency {
    display: none;
}
input.cashbutton[type=radio]:checked ~ .cryptocurrency {
    display: none;
}
@keyframes fademeout {
  from {opacity: 1;}
  to {opacity: 0;}
}
@keyframes fademein {
  from {opacity: 0;}
  to {opacity: 1;}
}
.posthead2 {
	display: block;
}
.posthead2 td {
	width: 100vw;
}

input[type=checkbox]:checked ~ label[for="item-1"]::before {
	content: "[+]";
	cursor: pointer;
}
input[type=checkbox]:not(:checked) ~ label[for="item-1"]::before {
    content: "[ - ]";
	cursor: pointer;
}
input[type=checkbox]:not(:checked) ~ .posthead2 {
	opacity: 1;
    max-height: 1000px;
	animation-name: fademein;
	animation-duration: 1s;
	animation-timing-function: ease-in;
    transition: max-height 1s ease-in;
}
input[type=checkbox]:checked ~ .posthead2 {
	opacity: 0;
    max-height: 0;
	animation-name: fademeout;
	animation-duration: 1s;
	animation-timing-function: ease-out;
    transition: max-height 1s ease-out;
    overflow: hidden;
}
select#ecurrency option[value="C-Gold"] {
	background: url(https://vignette.wikia.nocookie.net/words/images/9/9e/Gold-letter-c-.jpg/revision/latest?cb=20131209174723) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="InstaForex"] {
	background: url(https://lh3.googleusercontent.com/qtQH70zz5_Vj7D-B-1mKkf1zSqhXCnov9dI7XkmCM1xGKaKlNn4ovqbN_vWLCDB8Uw=w300) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="PayCo"] {
	background: url(http://image.toast.com/aaaaac/paycoNoti/payco_com.jpg) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="Payeer"] {
	background: url(https://images-na.ssl-images-amazon.com/images/I/41sEsPEXXOL.png) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="Paypal"] {
	background: url(https://img.etimg.com/thumb/msid-60762134,width-672,resizemode-4,imglength-9757/small-biz/startups/paypal-reduces-remittance-certificate-charges-by-50-for-small-sellers.jpg) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="Payza"] {
	background: url(https://i.vimeocdn.com/portrait/3597985_640x640) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="Perfect Money"] {
	background: url(https://lh3.ggpht.com/A0-25O4FaUEAWFUAc6a4UQm6Qz3kuKzjTp93jvkBYF3Yv3UxcVx2TfHupfOUQqHcuqj2=w300) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="Neteller"] {
	background: url(https://member.neteller.com/static/k38bu0WljDTxaehlaAfXd3qXV3T9gUIbd5aANIKKgS9.png) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="Skrill"] {
	background: url(https://www.skrill.com/fileadmin/templates/images/skrill-share.png) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#ecurrency option[value="SolidTrustPay"] {
	background: url(http://www.payout-anleitung.com/images/STP%20oben.png) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#cryptocurrency option[value="BTC"] {
	background: url(https://www.cryptocompare.com/media/19633/btc.png?width=200) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#cryptocurrency option[value="ETH"] {
	background: url(http://files.coinmarketcap.com.s3-website-us-east-1.amazonaws.com/static/img/coins/200x200/ethereum.png) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#cryptocurrency option[value="LTC"] {
	background: url(http://files.coinmarketcap.com.s3-website-us-east-1.amazonaws.com/static/img/coins/200x200/litecoin.png) no-repeat;
	background-size: 16px 16px;
	padding-left: 20px;
}
select#currencies option[value="MYR"] {
	background: url(https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/Flag_of_Malaysia.svg/1200px-Flag_of_Malaysia.svg.png) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="IDR"] {
	background: url(https://www.cia.gov/library/publications/the-world-factbook/graphics/flags/large/id-lgflag.gif) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="SGD"] {
	background: url(https://upload.wikimedia.org/wikipedia/commons/thumb/4/48/Flag_of_Singapore.svg/255px-Flag_of_Singapore.svg.png) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="THB"] {
	background: url(https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Flag_of_Thailand.svg/255px-Flag_of_Thailand.svg.png) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="BND"] {
	background: url(https://upload.wikimedia.org/wikipedia/commons/5/56/Flag_of_Brunei_%28WFB_2004%29.gif) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="USD"] {
	background: url(https://upload.wikimedia.org/wikipedia/commons/0/09/Flag_of_the_United_States_%28WFB_2004%29.gif) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="GBP"] {
	background: url(https://pmcdeadline2.files.wordpress.com/2010/11/british-flag.png?w=605) no-repeat;
	background-size: 17px 13px;
	padding-left: 20px;
}
select#currencies option[value="RUB"] {
	background: url(https://upload.wikimedia.org/wikipedia/en/thumb/f/f3/Flag_of_Russia.svg/320px-Flag_of_Russia.svg.png) no-repeat;
	background-size: 18px 12px;
	padding-left: 20px;
}
</style>
<form id="market_submit" action="./market/post.php" method="post">
  Type:
  <br>
  <input class="categorybuy buyselect" id="category_id" name="category_id" value="0" type="radio" checked="checked"> Buy 
  <input class="categorysell sellselect" id="category_id" name="category_id" value="1" type="radio"> Sell
  <br><br>
  I <label for="have"></label>: 
  <br>
  <input class="cryptobutton" id="cash_id" name="cash_id" value="0" type="radio" checked="checked"> Crypto Currency
  <input class="cashbutton" id="cash_id" name="cash_id" value="1" type="radio"> eCurrency
  <br><br>
  <select id="ecurrency" class="ecurrency" name="ecurrency">
	<option value="C-Gold">C-Gold</option>
	<option value="InstaForex">InstaForex</option>
	<option value="PayCo">PayCo</option>
	<option value="Payeer">Payeer</option>
	<option value="Paypal">Paypal</option>
	<option value="Payza">Payza</option>
	<option value="Perfect Money">Perfect Money</option>
	<option value="Neteller">Neteller</option>
	<option value="Skrill">Skrill</option>
	<option value="SolidTrustPay">SolidTrustPay</option>
  </select>
  <select id="cryptocurrency" class="cryptocurrency" name="cryptocurrency">
	<option value="BTC">Bitcoin BTC</option>
	<option value="ETH">Ethereum ETH</option>
	<option value="LTC">LiteCoin LTC</option>
  </select>
  <br><br>
  I <label for="want"></label> (currency): 
  <br>
  <select id="currencies" class="currencies" name="currencies">
	<option value="MYR">Malaysia MYR</option>
	<option value="IDR">Indonesia IDR</option>
	<option value="SGD">Singapore SGD</option>
	<option value="THB">Thailand THB</option>
	<option value="BND">Brunei BND</option>
	<option value="USD">United States USD</option>
	<option value="GBP">United Kingdom GBP</option>
	<option value="RUB">Russia RUB</option>
  </select>
  <br><br>
  Deal type:
  <br>
  <input class="directdeal" id="deal_id" name="deal_id" value="0" type="radio" checked="checked"> Direct Deal 
  <input class="cgescrow" id="deal_id" name="deal_id" value="1" type="radio" disabled="disabled"> CG Escrow Service (will launch soon)
  <br><br>
  Title:
  <br>
  <input type="text" id="listing_title" name="listing_title" placeholder="Example Title">&emsp;
  <br><br>
  Price to <label for="price"></label> (eg $ <strong>###.##</strong>):
  <br>
  $ <input type="text" id="listing_price" name="listing_price" placeholder="0.00">
  <br><br>
  Rate (Currency / USD):
  <br>
  <input type="text" id="listing_rate" name="listing_rate" placeholder="1.00">
  <br><br>
  Name: 
  <br>
  <input type="text" id="listing_realname" name="listing_realname" placeholder="Your name goes here">
  <br><br>
  Phone number: 
  <br>
  <input type="text" id="listing_phone" name="listing_phone" placeholder="123-456-7890">
  <br><br>
  <input type="hidden" id="listing_username" name="listing_username" value="'.$vbulletin->userinfo[username].'">
  <input type="hidden" id="listing_userid" name="listing_userid" value="'.$vbulletin->userinfo[userid].'">
  <input type="hidden" id="securitytoken" name="securitytoken" value="'.$sessionhash.'">
  <input class="submitform" type="submit" style="
		display: flex;
		margin: auto;
		border-radius: 4px;
		background: #4b7cc3;
		background: -webkit-linear-gradient(#4b7cc3,#315993);
		background: -o-linear-gradient(#4b7cc3,#315993);
		background: -moz-linear-gradient(#4b7cc3,#315993);
		background: linear-gradient(#4b7cc3,#315993);" value="Post New Listing">
</form>
</td>
</table>
<br />
<p class="ajaxcss">
<style>
.ajaxcss,
.ajaxpag {
  display: none;
}
</style>
</p>
<table class="tborder noscriptpage" style="float: right; margin-bottom: 5px;" cellspacing="1" cellpadding="3" border="0">
<tbody>
<tr id="pagination" class="pagination pagenav">
<td id="curpagvar" style="display: none;">'.$page.'</td>
<td class="vbmenu_control" style="font-weight:normal">Page '.$page.' of '.$rows.'</td>
'.getpagination($page,'0').'
</tr>
</tbody>
</table>
<table class="tborder ajaxpag" style="float: right;" cellspacing="1" cellpadding="3" border="0">
<tbody>
<tr id="pagination" class="pagination pagenav">
<td id="curpagvar" style="display: none;">'.$page.'</td>
</tr>
</tbody>
</table>
<br>
<br />
<br />
<table class="tborder" style="border-bottom: 0;" cellpadding="'.$stylevar[cellpadding].'" cellspacing="'.$stylevar[cellspacing].'" border="0" width="100%" align="center">
<tbody>
<tr>
    <td class="tcat" width="100%">Exchange Marketplace</td>
</tr>
</tbody>
</table>
<table class="tborder" border="0" width="100%" align="center" cellspacing="1">
<tbody>
<tr>
<td class="thead" colspan="2" nowrap="nowrap">Type</td>
<td class="thead" width="100%">Listing / Listing Starter</td>
<td class="thead" width="150" nowrap="nowrap" align="center"><span style="white-space:nowrap">Listing Postdate</span></td>
<td class="thead" nowrap="nowrap" align="center"><span style="white-space:nowrap">Have Name/Rate</span></td>
<td class="thead" nowrap="nowrap" align="center"><span style="white-space:nowrap">Want Name/Rate</span></td>
<td class="thead" nowrap="nowrap" align="center"><span style="white-space:nowrap">Price</span></td>
<td class="thead" nowrap="nowrap" align="center"><span style="white-space:nowrap">Deal Service</span></td>
<td class="thead" nowrap="nowrap" align="center"><span style="white-space:nowrap">User Contact / PM</span></td>
</tr>
</tbody>
<tbody id="listings" value="'.$sessionhash.'">
'.print_row($initial_load, $conn, $vbulletin->userinfo[userid]).'
</tbody>
</table>
<table class="tborder noscriptpage" style="float: right; margin-top: 5px;" cellspacing="1" cellpadding="3" border="0">
<tbody>
<tr id="pagination" class="pagination pagenav">
<td class="vbmenu_control" style="font-weight:normal">Page '.$page.' of '.$rows.'</td>
'.getpagination($page,'0').'
</tr>
</tbody>
</table>
<table class="tborder ajaxpag" style="float: right; margin-top: 5px;" cellspacing="1" cellpadding="3" border="0">
<tbody>
<tr id="pagination" class="pagination pagenav">
<td id="curpagvar" style="display: none;">'.$page.'</td>
</tr>
</tbody>
</table>
<br>
<br />
<br />
<br />';

eval('$navbar = "' . fetch_template('navbar') . '";');
eval('print_output("' . fetch_template('market') . '");');

mysqli_close($conn);

?>
