var ownerEmail = localStorage.getItem("email");
var closeInterval;

window.onload=function()
{
	var server_id = localStorage.getItem("id_server");
    getServerInfo(server_id);
	setInterval(function() {
		getServerInfo(server_id);	
	}, 2000);
}

doThis=function()
{
    if (!window.parent || !window.parent.document)
		window.location.href = "modify.php";
	else {
		var rightDiv = $('#rightDiv', window.parent.document);
		var rightDivWidth = rightDiv.css('width');
		//Takes out "px"
		rightDivWidth = rightDivWidth.substring(0, rightDivWidth.length - 2);
		var completeWidth = $('#outerDiv', window.parent.document).css('width');
		completeWidth = completeWidth.substring(0, completeWidth.length - 2);
		console.log(rightDivWidth);
		console.log(completeWidth);
		//2%, basically the same as not being shown
		if (rightDivWidth/completeWidth < 0.02)
			changePage("modify.php", window.parent.document.getElementById('leftDiv'));
		else
			changePage("modify.php", window.parent.document.getElementById('rightDiv'));
	}
}

function doThat() {
	if (!window.parent || !window.parent.document)
		window.location.href = "serverList.html";
	else {
		var rightDiv = $('#rightDiv', window.parent.document);
		var rightDivWidth = rightDiv.css('width');
		//Takes out "px"
		rightDivWidth = rightDivWidth.substring(0, rightDivWidth.length - 2);
		var completeWidth = $('#outerDiv', window.parent.document).css('width');
		completeWidth = completeWidth.substring(0, completeWidth.length - 2);
		console.log(rightDivWidth);
		console.log(completeWidth);
		//2%, basically the same as not being shown
		if (rightDivWidth/completeWidth < 0.02) {
			console.log("serverList.html");
			changePage("serverList.html", window.parent.document.getElementById('leftDiv'));
		}
		else {
			console.log("serverListHelper.html");
			changePage("serverListHelper.html", window.parent.document.getElementById('rightDiv'));
		}
	}
}

function changePage(pageName, div) {
	//Changing the page...
	//var rightPageDiv = window.parent.document.getElementById('rightDiv');
	//rightPageDiv.innerHTML = "<iframe src=" + pageName + "></iframe>";
	div.innerHTML = "<iframe src = " + pageName + "></iframe>";
}

setServerName=function(name)
{
    document.getElementById("name").innerHTML = name;
    localStorage.setItem("ServerName",name);
}

setServerType=function(type)
{
    document.getElementById("serverType").innerHTML = type;
    localStorage.setItem("ServerType",type);
}

setLastUserTime = function(isOnline, lastActiveUser) {
	if (isOnline == 0) {
		document.getElementById("lastUserDiv").style.display = "none";
	}
	else {
		var timeNow = Date.now();
		var dif = timeNow - lastActiveUser;
		console.log(timeNow);
		console.log(lastActiveUser);
		var difDate = new Date(dif);
		var seconds = difDate.getSeconds();
		if (("" + seconds).length == 1)
			seconds = "0" + seconds;
		var minutes = difDate.getMinutes();
		if (("" + minutes).length == 1)
			minutes = "0" + minutes;
		var hours = difDate.getHours();
		var days = dif/86400000;
		/*(1000 * 60 * 60 * 24)*/
		if (days >= 1)
			hours += Math.floor(days) * 24;
		if (("" + hours).length == 1)
			hours = "0" + hours;
        document.getElementById("userTime").innerHTML = hours + ":" + minutes + ":" + seconds + "(hh:mm:ss)";
		document.getElementById("lastUserDiv").style.display = "inline";
	}
}

setOnlineTime=function(isOnline,time)
{
    if(isOnline == 0)
    {
		document.getElementById("Ontime").innerHTML = "Offline";
		document.getElementById("lastUserDiv").style.display = "none";
        //document.getElementById("userTime").setAttribute("hidden",true);
    }
    else
    {
		var timeNow = Date.now();
		var timeStarted = time;
		var dif = timeNow - timeStarted;
		var difDate = new Date(dif);
		var seconds = difDate.getSeconds();
		if (("" + seconds).length == 1)
			seconds = "0" + seconds;
		var minutes = difDate.getMinutes();
		if (("" + minutes).length == 1)
			minutes = "0" + minutes;
		var hours = difDate.getHours();
		var days = dif/86400000;
		/*(1000 * 60 * 60 * 24);*/
		if (days >= 1)
			hours += Math.floor(days) * 24;
		if (("" + hours).length == 1)
			hours = "0" + hours;
        document.getElementById("Ontime").innerHTML = hours + ":" + minutes + ":" + seconds + "(hh:mm:ss)";
		document.getElementById("lastUserDiv").style.display = "block";
        //document.getElementById("userTime").setAttribute("hidden",true);
        //setLastUserOnline(userTime)
    }
}

/*
setLastUserOnline=function(userTime)
{
    document.getElementById("userTime").innerHTML=userTime/60+"h";
}
*/

setCapacity=function(curUsers,curCap)
{
    document.getElementById("curUser").innerHTML = curUsers;
    document.getElementById("totalUser").innerHTML = "/" + curCap;
    localStorage.setItem("curUser",curUsers);
    localStorage.setItem("capacity",curCap);
	if (curUsers / curCap < 0.5) {
		document.getElementById("curUser").style.color = "#33cc33";
	}
	else if (curUsers / curCap < 0.85) {
		document.getElementById("curUser").style.color = "#ffdd00";
	}
	else {
		document.getElementById("curUser").style.color = "#ff4444";
	}
}

setFixedOpTime=function(isFixed)
{
    localStorage.setItem("FOpTime",isFixed);
    if(isFixed)
    {
        document.getElementById("isOpTime").innerHTML = "Yes";
    }
    else
    {
        document.getElementById("isOpTime").innerHTML = "No";
    }
}

setSchedulle=function(isFixed)
{
    if(isFixed)
    {
		document.getElementById("schedule").style.display = "block";
    }
    else
    {
        document.getElementById("schedule").style.display = "none";
    }
}

setStartAt = function(startAt) {
	document.getElementById("startAt").innerHTML = startAt;
}

setStopAt = function(stopAt) {
	document.getElementById("stopAt").innerHTML = stopAt;
}

setAdmins=function(names)
{
    var pStr = names[0];
    for(var i = 0; i < names.length ;i++)
    {
        pStr += ","+names[i] 
    }
    document.getElementById("adminL").value = pStr;
    //localStorage.setItem("Admins",names);
}

setHost=function(host)
{
    document.getElementById("host").innerHTML = host;
    //localStorage.setItem("host",host);
}

setPort=function(port)
{
    document.getElementById("port").innerHTML = port;
    //localStorage.setItem("port",port);
}

newSetAdmins = function(admins) {
	var keys = Object.keys(admins);
	var table = document.getElementById("adminsTable");
	var tableBody = document.getElementById("adminBody");
	var newTableBody = document.createElement("tbody");
	newTableBody.setAttribute("id", "adminBody");
	for (var i = 0; i < keys.length; i++) {
		addAdminToAdminsTable(admins[keys[i]], keys[i], newTableBody);
	}
	table.removeChild(tableBody);
	table.appendChild(newTableBody);
}

addAdminToAdminsTable = function(username, email, tableBody) {
	isOwner = false;
	if (ownerEmail == email)
		isOwner = true;
	var row = document.createElement("tr");
	row.setAttribute("id", "adminRow" + email);
	
	var nameCell = document.createElement("td");
	//nameCell.setAttribute("class", "nameCell");
	if (isOwner)
		nameCell.innerHTML = username + " (you)";
	else
		nameCell.innerHTML = username;
	row.appendChild(nameCell);
	
	var emailCell = document.createElement("td");
	//emailCell.setAttribute("class", "emailCell");
	emailCell.innerHTML = email;
	row.appendChild(emailCell);
	
	tableBody.appendChild(row);
}

function getServerInfo(server_id) {
	if (closeInterval == undefined) {
		closeInterval = setInterval(function() {
			if (localStorage.getItem("remove") == server_id) {
				localStorage.removeItem("remove");
				doThat();
			}
		}, 400);
	}
	$.ajax({
	type: "POST",
	url: "getServerInfo.php",
	data: { id_server: server_id }, 
	success: function(data){
		try {
			data = JSON.parse(data);
			email = localStorage.getItem('email');
			adminsData = JSON.parse(data.admins);
			$.ajax({
				type: "POST",
				url: "getAllUsers.php",
				data: { email: email }, 
				success: function(usersData){
					try {
						usersData = JSON.parse(usersData);
						var keys = Object.keys(usersData);
						admins = {};
						var loadingDiv = document.getElementById("loadingDiv").style.display = "none";
						for (var i = 0; i < keys.length; i+= 3) {
							if (adminsData.indexOf(usersData[keys[i + 2]]) != -1) {
								var adminUsername = usersData[keys[i]];
								var adminEmail = usersData[keys[i + 1]];
								var adminId = usersData[keys[i + 2]];
								admins[adminEmail] = adminUsername;
							}
						}
						admins[email] = localStorage.getItem('username');
						
						if (data.isOnline == 0)
							data.isOnline = false;
						else
							data.isOnline = true;
						if (data.isSchedule == 0)
							data.isSchedule = false;
						else
							data.isSchedule = true;
						
						//localStorage.setItem("id_server",data.id_server);
						setServerName(data.name);
						setServerType(data.type);
						setOnlineTime(data.isOnline,data.onlineSince);
						setLastUserTime(data.isOnline, data.lastActiveUser);
						setCapacity(data.nUsers,data.capacity);
						setFixedOpTime(data.isSchedule);
						setSchedulle(data.isSchedule);
						setStartAt(data.startsAt);
						setStopAt(data.stopsAt);
						//setAdmins(["Andre","Joao","Pedro","Ikilier"]);
						newSetAdmins(admins);
						setHost(data.ip);
						setPort(data.port);
					}
					catch (e) {
						//doThat();
						console.log(e);
					}
				}
			});
		}
		catch (e) {
			//doThat();
			console.log(e);
		}
	}
	});
}

