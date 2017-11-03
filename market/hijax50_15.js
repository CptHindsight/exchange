var getQuery = function() {
	var category = $('input:radio[name=category_id]:checked').val();
	var username = document.getElementById("listing_username").value;
	var userid = document.getElementById("listing_userid").value;
	var realname = document.getElementById("listing_realname").value;
	var title = document.getElementById("listing_title").value;
	var price = document.getElementById("listing_price").value;
	var rate = document.getElementById("listing_rate").value;
	var cashid = $('input:radio[name=cash_id]:checked').val();
	var firstcurname = cashid == 1 ? "&ecurrency=" : "&cryptocurrency=";
	var firstcur = cashid == 1 ? document.getElementById("ecurrency").value : document.getElementById("cryptocurrency").value;
	var secondcur = document.getElementById("currencies").value;
	var phone = document.getElementById("listing_phone").value;
	var securitytoken = document.getElementById("securitytoken").value;
	var dealid = $('input:radio[name=deal_id]:checked').val();
	return "ajax=true&listing_username=" + encodeURI(username) + "&listing_userid=" + encodeURI(userid) + "&listing_title=" + encodeURI(title) + "&listing_price=" + encodeURI(price) + "&listing_realname=" + encodeURI(realname) + firstcurname + encodeURI(firstcur) + "&currencies=" + encodeURI(secondcur)  + "&listing_phone=" + encodeURI(phone) + "&deal_id=" + encodeURI(dealid) + "&category_id=" + encodeURI(category) + "&cash_id=" + encodeURI(cashid) + "&listing_rate=" + encodeURI(rate) + "&securitytoken=" + encodeURI(securitytoken);
}

var setQuery = function() {
	var frm = document.getElementById("market_submit");
	    frm.onsubmit = function(){
			var query = getQuery();
			myHijax(query);
			
			$(function() {
				$.ajax({
					url:"./market/ajax/dbmanupulate.php",
							type:"POST",
							data:"actionfunction=showData&refresh=1&page=1&realuserid="+$("#listing_userid").val(),
					cache: false,
					success: function(response){
						$('.pagination').html(response);
						$('#curpagvar').html("1");

						$.ajax({
							url:"./market/ajax/dbmanupulate.php",
									type:"POST",
									data:"actionfunction=showData&page=1&realuserid="+$("#listing_userid").val(),
							cache: false,
							success: function(response){
								$('#listings').html(response);
							}
						});

						$(".pagination").prepend("<td class=\"vbmenu_control\" style=\"font-weight:normal\">Page 1 of "+$("#lastpagevar").text()+"</td>");
					}
				});
			});
			
			return false;
		}
}

var myHijax = function(qs) {
	var x = 	new AO("./market/post.php",qs);
		x.onload = function() {
			if (x.init && x.status == "200")
				var el = document.getElementById('listings'),
				elChild = document.createElement('tr');
				/*elChild.setAttribute('id', 'list_test');

				// Prepend it
				//el.insertBefore(elChild, el.firstChild);
				x.putHere('list_test');*/
		}
		x.post();
	return false;
}

window.onload = function() {
	var bSupport = new AO();
	if (bSupport.init) { // test for support of Ajax
		setQuery();
		bSupport = null;
	}
	else return false;
}