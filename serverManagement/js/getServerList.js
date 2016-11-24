// JavaScript Document

//window.onload = getServerList();

function getServerList() {
    $.ajax({
        type: "POST",
        url: "getServerList.php",
        success: function(data){
			var data = JSON.parse(data);
			for (var i = 0; i < data.length; i++)
				console.log(data[i]);
        }
    });
}