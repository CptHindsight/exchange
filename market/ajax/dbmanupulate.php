<?php
//made by Alec Repczynski
include('db.php');
include('../../market_functions.php');

if(isset($_REQUEST['actionfunction']) && $_REQUEST['actionfunction']!=''){
	$actionfunction = $_REQUEST['actionfunction'];
	call_user_func($actionfunction,$_REQUEST,$con,$limit,$adjacent);
}
function showData($data,$con,$limit,$adjacent){
	$returner = '';
	
	$realusersid = $data['realuserid'];
	$securitytoken = $data['securitytoken'];
	$listingid = $data['listing_id'];
	$sessionhash = getsessionhash($realusersid);
	$delete = $data['delete'];
	$page = $data['page'];
	if($page==1){
		$start = 0;  
	} else {
		$start = ($page-1)*$limit;
	}
	
	$sql = "select * from `market_listings` order by listingid desc";
	$rows  = $con->query($sql);
	$rows  = $rows->num_rows;
  
	$sql = "select * from `market_listings` order by listingid desc limit $start,$limit";
	
	//get realuserid from JS input to check delete permissions for printing rows
	if($delete == 1) {
		if(candelete($sessionhash, $securitytoken) != 1) {
			$returner .= '';
		} else {
			deletelisting($listingid);
			$returner .= '';
		}
	} else if($data['refresh'] != 1) {
		$str = print_row($sql, $con, $realusersid);
		$returner .= $str;
	} else {
		$returner .= pagination($limit,$adjacent,$rows,$page);
	}
	
	echo $returner;
}

function pagination($limit,$adjacents,$rows,$page){	
	$pagination='';
	if ($page == 0) $page = 1;					//if no page var is given, default to 1.
	$prev = $page - 1;							//previous page is page - 1
	$next = $page + 1;							//next page is page + 1
	$prev_ = '';
	$first = '';
	$lastpage = ceil($rows/$limit);	
	$next_ = '';
	$last = '';
	
	if($lastpage > 1) {	
		//previous button
		if ($page > 1)
			$prev_.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$prev\"> < </a></td>";
		else {
			//$pagination.= "<span class=\"disabled\">previous</span>";	
		}
		
		//pages	
		if ($lastpage < 5 + ($adjacents * 2)) {
		//not enough pages to bother breaking it up
			
		$first='';
			for ($counter = 1; $counter <= $lastpage; $counter++) {
				if ($counter == $page)
					$pagination.= "<td class=\"alt2\"><span class=\"current smallfont\">$counter</span></td>";
				else
					$pagination.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$counter\">$counter</a></td>";			
			}
			$last='';
		} elseif($lastpage > 3 + ($adjacents * 2)) {
			//enough pages to hide some{
			//close to beginning; only hide later pages
			$first='';
			if($page < 1 + ($adjacents * 2)) {
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
					if ($counter == $page)
						$pagination.= "<td class=\"alt2\"><span class=\"current\">$counter</span>";
					else
						$pagination.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$counter\">$counter</a></td>";					
				}
				$last.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$lastpage\">Last <strong>&raquo;</strong></a></td>";
			} elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
				//in middle; hide some front and some back
				$first.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=1\"><strong>&laquo;</strong> First</a></td>";	
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
					if ($counter == $page)
						$pagination.= "<td class=\"alt2\"><span class=\"current smallfont\">$counter</span></td>";
					else
						$pagination.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$counter\">$counter</a></td>";					
				}
				$last.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$lastpage\">Last <strong>&raquo;</strong></a></td>";	
			} else {
			//close to end; only hide early pages
			    $first.= "<td class=\"alt1\"><a class='page-numbers' href=\"?page=1\"><strong>&laquo;</strong> First</a></td>";	
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
					if ($counter == $page)
						$pagination.= "<td class=\"alt2\"><span class=\"current smallfont\">$counter</span></td>";
					else
						$pagination.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$counter\">$counter</a></td>";					
				}
				$last='';
			}
            
			}
		if ($page < $counter - 1) 
			$next_.= "<td class=\"alt1\"><a class='page-numbers smallfont' href=\"?page=$next\"> > </a></td>";
		else {
			//$pagination.= "<span class=\"disabled\">next</span>";
			}
		$last .= "<td id='lastpagevar' style='display: none;'>".$lastpage."</td>";
		$pagination = "".$first.$prev_.$pagination.$next_.$last;
		//next button
		
		$pagination.= "\n";
	}

	return $pagination;  
}
?>
