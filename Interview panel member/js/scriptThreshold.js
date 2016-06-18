$(document).ready(
function() 
{
    $('#btnID').click(function(){
		swal({   title: "Change Threshold Period", 
		  text: "Are you sure you want to change the threshld period?",
		     type: "warning",
			    showCancelButton: true,
				   confirmButtonColor: "#DD6B55", 
				     confirmButtonText: "             Yes",
					    cancelButtonText: "No",
						   closeOnConfirm: false,  
						    closeOnCancel: false },
							 function(isConfirm){  
							  if (isConfirm) {
								  	 
	$.post("changeThreshold.php",{
		years : $('#thresholdYears').val()
		},
	function(data){ 
		
	//$('#thresholdYears').val(data);
		}
	);
								       swal("Changed!", "The Company's threshold period is now changed", "success");   } 
								else {     
								document.getElementById("Form").reset();
								swal("Cancelled", "", "error");   } });
		
		
		
		
	
			
	});
	
});
	
