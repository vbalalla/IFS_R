$(document).ready(
function() 
{
    $('#createInterviewPanelAddMemberBtn').click(function(){
		var emp = new Array();
		var empPanel = new Array();
		
		$('input[name="default"]:checked').each(function() {
			emp.push(this.id);
		});
		
		emp.push($('#pnlid').val());
		emp.push($('#panelName').val());
		var name = $('#panelName').val();
		
		$('input[name="empid"]:checked').each(function() {
			//alert(this.id);
			//console.log(this.value);
			emp.push(this.id);  
			empPanel.push(this.id);
		});
				
		var datastr = emp.join(',');
		//alert(datastr);
		
		if(name=="" && (empPanel.length)==0){
			swal("Please enter name of interview panel and select interview panel members", "", "error");
			//alert("Please enter name of interview panel and select interview panel members");
		}else if(name==""){
			swal("Please enter name of interview panel", "", "error");
			//alert("Please enter name of interview panel");
		}else if((empPanel.length)==0){
			swal("Please select interview panel members", "", "error");
			//alert("Please select interview panel members");
		}else{
			//alert("Interview panel saved successfully");
			swal("Interview panel saved successfully", "", "success");
			window.location.replace("editPanel.php?panel="+datastr);
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
