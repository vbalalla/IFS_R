$(document).ready(
function() 
{
    $('#deletePanel').click(function(){
		var emp = new Array();
		//var empPanel = new Array();
		
		var defpan = $('#defaultPanel').val();
		var deftrue = false;
		
		$('input[name="selectPanel"]:checked').each(function() {
			if(defpan==(this.id)){
				deftrue = true;
			}else{
				emp.push(this.id);
			}			
		});
						
		var datastr = emp.join(',');
		//alert(datastr+" len "+defpan);
		
		if(deftrue && (emp.length)==0){
			swal("Cancelled", "Can't delete default interview panel", "error");   
		}else if((emp.length)==0){
			swal("Please select interview panel/(s) to delete", "", "error");
			//alert("Please select interview panel/(s) to delete");
		}else{
			swal({   
			title: "Delete?",   
			text: "Are you sure to delete the selected interview panels",   
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Yes",   
			cancelButtonText: "No",   
			closeOnConfirm: false,   
			closeOnCancel: true }, 
			function(isConfirm){   
			if (isConfirm) {     
				if(deftrue){
					swal("Cancelled", "Can't delete default interview panel", "error");   
				}
				swal("Deleted!", "Interview panel/(s) successfully deleted", "success");   
				window.location.replace("deletePanel.php?panel="+datastr);
			} else {     
			//swal("Cancelled", "Selected interview panels were not deleted", "error");   
			} });
			
			//swal("Interview panel/(s) successfully deleted", "", "success");
			//swal({   title: "Auto close alert!",   text: "I will close in 2 seconds.",   timer: 2000,   showConfirmButton: false });
			
		}
		
		//alert(datastr);
		/*$.ajax({
                 type: "POST",
                 url: "CreateNewPanel.php",
                 data: {data : emp.join(',')}, 
                 cache: false,
                 success: function(){
                     alert("OK");
                }
        });*/
		
		/*$.post("CreateNewPanel.php",
		    {
				data : emp.join(',')			
				
			},
			function(data)
			{
				alert(emp.join(','));
			}
		);*/
		
		
				
	});
}
);
