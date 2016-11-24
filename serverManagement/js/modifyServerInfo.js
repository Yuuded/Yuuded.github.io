// JavaScript Document

function modifyServerInfo() {
	id_server = 1
	name = "G8 REKT M8";
    $.ajax({
        type: "POST",
        url: "modifyServerInfo.php",
        data: { id_server: id_server}, 
        success: function(data){
			//data = JSON.parse(data);
			console.log(data);
        }
    });
}