$(document).ready(
function() 
{
    $('#btnID').click(function(){
		$.post("main1.php",
		    {
				sessionname:$('#sessionID').val(),
				value:$('#jbposition').val()
				
			},
			function(data)
			{
			//	$('#aa').val(data); 
			}
		);
	});
}
);