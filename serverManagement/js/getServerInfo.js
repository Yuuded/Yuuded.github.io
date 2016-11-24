// JavaScript Document

function getServerInfo() {
	serverIP = "192.168.213.212"
	serverPort = "43253"
    $.ajax({
        type: "POST",
        url: "getServerInfo.php",
        data: { ip: serverIP, port: serverPort }, 
        success: function(data){
			data = JSON.parse(data);
			console.log(data);
        }
    });
}