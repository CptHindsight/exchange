$(function(){
	$.ajax({
		url:"./market/ajax/dbmanupulate.php",
				type:"POST",
				data:"actionfunction=showData&refresh=1&page="+$("#curpagvar").text()+"&realuserid="+$("#listing_userid").val(),
		cache: false,
		success: function(response){
			var curpage = $("#curpagvar").text();
			$('.noscriptpage').remove();
			$('.ajaxcss').remove();
			$('.pagination').html(response);
			$.ajax({
				url:"./market/ajax/dbmanupulate.php",
						type:"POST",
						data:"actionfunction=showData&page="+$("#curpagvar").text()+"&realuserid="+$("#listing_userid").val(),
				cache: false,
				success: function(response){
					$('#listings').html(response);
				}
			});
			
			$(".pagination").prepend("<td class=\"vbmenu_control\" style=\"font-weight:normal\">Page "+curpage+" of "+$("#lastpagevar").text()+"</td>");
		}
	});
	
	function buildPage($pagenum) {
		$.ajax({
			url:"./market/ajax/dbmanupulate.php",
					type:"POST",
					data:"actionfunction=showData&refresh=1&page="+$pagenum+"&realuserid="+$("#listing_userid").val(),
			cache: false,
			success: function(response){
				$('.pagination').html(response);
				$.ajax({
					url:"./market/ajax/dbmanupulate.php",
							type:"POST",
							data:"actionfunction=showData&page="+$pagenum+"&realuserid="+$("#listing_userid").val(),
					cache: false,
					success: function(response){
						$('#listings').html(response);
					}
				});

				$(".pagination").prepend("<td class=\"vbmenu_control\" style=\"font-weight:normal\">Page "+$pagenum+" of "+$("#lastpagevar").text()+"</td>");
			}
		});
	}
	
	$('#listings').on('click', '.deletefunction', function(e) {
		e.stopPropagation();
		e.preventDefault(); 
		
		var conf = confirm('Are you sure want to delete this listing?');
   		if(conf) {
			$.ajax({
				url:"./market/ajax/dbmanupulate.php",
						type:"POST",
						data:"actionfunction=showData&delete=1&page=" + $('.current:first').text() + "&listing_id=" + $(this).attr('value') + "&realuserid="+$("#listing_userid").val() + "&securitytoken=" + $('#securitytoken').val(),
				cache: false,
				success: function(response){
					$pagenav = $('.current:first').text();
					buildPage($pagenav);
				}
			});
		}
	});
	 
	$('.pagination').on('click','.page-numbers',function(){
		$page = $(this).attr('href');
		$pageind = $page.indexOf('page=');
		$page = $page.substring(($pageind+5));
       
		buildPage($page);
		
	return false;
	});
});