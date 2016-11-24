var ownerEmail = localStorage.getItem("email");
var closeInterval;
var remainingAdmins = {};
var remainingAdminsIDs = {};
var admins = {};
var adminsIDs = {};
//Doesnt include admins but it doesnt matter.
var originalData = {};
var last = false;

window.onload = function () {
    var server_id = localStorage.getItem("id_server");
    getServerInfo(server_id);
	setInterval(function() {
		getClients(server_id);	
	}, 2000);
}

function getServerInfo(server_id) {
	if (closeInterval == undefined) {
		closeInterval = setInterval(function() {
			if (localStorage.getItem("remove") == server_id) {
				localStorage.removeItem("remove");
				goToMainPage();
			}
		}, 400);
		/*
		usersInterval = setInterval(function() {
			var nUsers = localStorage.getItem(server_id + "nUsers");
			var canContinue = true;
			var previous = last;
			var tmp = Date.now();
			if (previous == false || tmp - previous >= 2000) {
				last = tmp;
				localStorage.removeItem(server_id + "nUsers");
				if (nUsers != undefined) {
					try {
						var capacity = document.getElementById("totalUser").value;
						setNumberUsers(nUsers, capacity);
					}
					catch (e) {
						
					}
				}
			}
		}, 500);*/
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
						var loadingDiv = document.getElementById("loadingDiv").style.display = "none";
						admins = {};
						for (var i = 0; i < keys.length; i+= 3) {
							var adminUsername = usersData[keys[i]];
							var adminEmail = usersData[keys[i + 1]];
							var adminId = usersData[keys[i + 2]];
							if (adminsData.indexOf(usersData[keys[i + 2]]) != -1) {
								admins[adminEmail] = adminUsername;
								adminsIDs[adminEmail] = adminId;
							}
							else {
								remainingAdmins[adminEmail] = adminUsername;
								remainingAdminsIDs[adminEmail] = adminId;
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
						setCapacity(data.nUsers,data.capacity);
						setIsOnline(data.isOnline);
						setFixedOpTime(data.isSchedule);
						setSchedulle(data.isSchedule);
						setStartAt(data.startsAt);
						setStopAt(data.stopsAt);
						//setAdmins(["Andre","Joao","Pedro","Ikilier"]);
						newSetAdmins(admins);
						setHost(data.ip);
						setPort(data.port);
						setServerId(server_id, data.lastActiveUser, data.onlineSince);
						updateRemainingAdminsTable();
					}
					catch (e) {
						goToMainPage();
						//console.log(e);
						//console.log(data);
					}
				}
			});
		}
		catch (e) {
			goToMainPage();
//			doThat();
//			console.log(e);
//			console.log(data);
		}
	}
	});
}

setServerName = function(name) {
	document.getElementById("name").value = name;
}

setServerType = function(type) {
	document.getElementById("serverType").innerHTML = type;
}

setNumberUsers = function(nUsers, capacity) {
	document.getElementById("curUser").innerHTML = nUsers;
	if (nUsers / capacity < 0.5) {
		document.getElementById("curUser").style.color = "#33cc33";
	}
	else if (nUsers / capacity < 0.85) {
		document.getElementById("curUser").style.color = "#ffdd00";
	}
	else {
		document.getElementById("curUser").style.color = "#f15a25";
	}
}

setCapacity = function(nUsers, capacity) {
	document.getElementById("curUser").innerHTML = nUsers;
	document.getElementById("totalUser").value = capacity;
	originalData['capacity'] = capacity;
	if (nUsers / capacity < 0.5) {
		document.getElementById("curUser").style.color = "#33cc33";
	}
	else if (nUsers / capacity < 0.85) {
		document.getElementById("curUser").style.color = "#ffdd00";
	}
	else {
		document.getElementById("curUser").style.color = "#f15a25";
	}
}

setIsOnline = function(isOnline) {
	var onlineLabel = document.getElementById("onlineLabel");
	onlineLabel.setAttribute("data-isOnline", JSON.stringify(isOnline));
	if (isOnline)
		onlineLabel.innerHTML = "Server is currently online. Do you want to turn it off?";
	else
		onlineLabel.innerHTML = "Server is currently offline. Do you want to turn it on?";
}

setFixedOpTime = function(isSchedule) {
	if (isSchedule) {
		document.getElementById("isOpTime").setAttribute("checked", "");
	}
	else {
		document.getElementById("isOpTime").removeAttribute("checked");
	}
}

setSchedulle = function(isSchedule) {
	if (isSchedule) {
		document.getElementById("schedule").style.display = "block";
	}
	else {
		document.getElementById("schedule").style.display = "none";
	}
	
}

setStartAt = function(startsAt) {
	document.getElementById("startAt").value = JSON.parse(startsAt);
}

setStopAt = function(stopsAt) {
	document.getElementById("stopAt").value = JSON.parse(stopsAt);
}

newSetAdmins = function(admins) {
	console.log("Starting newSetAdmins");
	var keys = Object.keys(admins);
	var table = document.getElementById("adminsTable");
	var tableBody = document.getElementById("adminBody");
	var newTableBody = document.createElement("tbody");
	newTableBody.setAttribute("id", "adminBody");
	addAdminToMainTable(admins[ownerEmail], ownerEmail, newTableBody, true);
	for (var i = 0; i < keys.length; i++) {
		if (ownerEmail != keys[i])
			addAdminToMainTable(admins[keys[i]], keys[i], newTableBody, false);
	}
	table.removeChild(tableBody);
	table.appendChild(newTableBody);
}

setHost = function(ip) {
	document.getElementById("host").value = ip;
	originalData["ip"] = ip;
}

setPort = function(port) {
	document.getElementById("port").value = port;
	originalData["port"] = port;
}

setServerId = function(id_server, lastActiveUser, onlineSince) {
	var hidden = document.getElementById("id_server");
	hidden.value = id_server;
	hidden.setAttribute("data-lastActiveUser", lastActiveUser);
	hidden.setAttribute("data-onlineSince", onlineSince);
}
/*
setValue = function () {
    setServerName();
    setServerType();
    setCapacity();
    setFixedOpTime();
    setSchedulle();
    setAdmins();
    setHost();
    setPort();
	setServerId();
}

setServerName = function () {
    document.getElementById("name").value = localStorage.getItem("name");
}

setServerType = function () {
    document.getElementById("serverType").value = localStorage.getItem("type");
}

setCapacity = function () {
    document.getElementById("curUser").innerHTML = localStorage.getItem("nUsers");
    document.getElementById("totalUser").value = localStorage.getItem("capacity");
}

setFixedOpTime = function () {
    var x = localStorage.getItem("isSchedule");
    if (x == 1 || x == true || x == "on") {
        document.getElementById("isOpTime").setAttribute("checked", "");
    }
    else {
        //document.getElementById("isOpTime").setAttribute("unchecked", "");
		document.getElementById("isOpTime").removeAttribute("checked");
    }
}

setSchedulle = function () {
    var x = document.getElementById("isOpTime").checked;
    console.log(x);
    if (x == 0 || x == false || x == "off") {
        //document.getElementById("schedule").setAttribute("hidden", true);
        document.getElementById("schedule").style.display = "none";
    }
    else {
        document.getElementById("schedule").style.display = "block";
    }
}

setAdmins = function () {
    document.getElementById("adminL").value = localStorage.getItem("Admins");;
}

setHost = function () {
    document.getElementById("host").value = localStorage.getItem("ip");
}

setPort = function () {
    document.getElementById("port").value = localStorage.getItem("port");
}

setServerId = function() {
	document.getElementById("id_server").value = localStorage.getItem("id_server");	
}
*/

resetValidation = function(id) {
	var elementErr = document.getElementById(id + "Err");
	elementErr.setAttribute("data-hasErr", "false");
	elementErr.innerHTML = "";
}

validateReq = function(id, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	if (element.value.length == 0 && elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
		return false;
	}
	return true;
}

validateMinLength = function(id, value, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	if (element.value.length < value && elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
		return false;
	}
	return true;
}

validateMaxLength = function(id, value, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	if (element.value.length > value && elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
		return false;
	}
	return true;
}

validateNum = function(id, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	var value = element.value;
	if ($.isNumeric(value) && Math.floor(value) == value && value > 0) {
		return true;
	}
	else if (elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
	}
	return false;
}

validateIP = function(id, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	var value = element.value;
	if (value.length > 0) {
		var validIP = IsServerIPValid(value);
		if (validIP) {
			return true;
		}
		else if (elementErr.getAttribute("data-hasErr") != "true") {
			elementErr.innerHTML = message;
			elementErr.setAttribute("data-hasErr", "true");
		}
	}
	else if (elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
	}
	return false;
}

validateAtLeast = function(id, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	var value = element.value;
	if ($.isNumeric(value) && Math.floor(value) == value && value > 0) {
		return true;
	}
	else if (elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
	}
	return false;
}

validateAtMost = function(id, message) {
	var element = document.getElementById(id);
	var elementErr = document.getElementById(id + "Err");
	var value = element.value;
	if ($.isNumeric(value) && Math.floor(value) == value && value <= 65536) {
		return true;
	}
	else if (elementErr.getAttribute("data-hasErr") != "true") {
		elementErr.innerHTML = message;
		elementErr.setAttribute("data-hasErr", "true");
	}
	return false;	
}

getInfoToDataBase = function () {

	resetValidation("name");
	resetValidation("totalUser");
	resetValidation("host");
	resetValidation("port");
	
	var reqName = validateReq("name", "Please provide a name for the server.");
	var minName = validateMinLength("name", 3, "Server name must be at least 3 characters long.");
	var maxName = validateMaxLength("name", 60, "Server name must be at most 60 characters long.");
	
	var reqCapacity = validateReq("totalUser", "Please provide a server capacity.");
	var numCapacity = validateNum("totalUser", "Server capacity must be an integer positive number.");
	
	var reqIP = validateReq("host", "Please provide a host for the server.");
	var validIP = validateIP("host", "Invalid IP/URL. Please check that you have typed it propelly.");
	
	var reqPort = validateReq("port", "Please provide a port for the server.");
	var numPort = validateNum("port", "Port must be an integer positive number.");
	var minPort = validateAtLeast("port", "Port must be between 1 and 65536.");
	var maxPort = validateAtMost("port", "Port must be between 1 and 65536.");
	
	//TODO: Support server restart when ip/port/capacity are changed.
	if (reqName && minName && maxName && reqCapacity && numCapacity && reqIP && validIP && reqPort && numPort && minPort && maxPort) {
	
		var id_server =  document.getElementById("id_server").value;
		var serverName = document.getElementById("name").value;
		var serverType = document.getElementById("serverType").innerHTML;
		var Capacity = document.getElementById("totalUser").value;
		var FixedOpTime;
		if (!document.getElementById("isOpTime").getAttribute("checked"))
			FixedOpTime = 0;
		else
			FixedOpTime = 1;
		//var FixedOpTime = document.getElementById("isOpTime").getAttribute("checked");
		var startT = JSON.stringify(document.getElementById("startAt").value);
		var endT = JSON.stringify(document.getElementById("stopAt").value);
		//var Admins = document.getElementById("adminL").value;
		var Host = document.getElementById("host").value;
		var Port = document.getElementById("port").value;
		var turnOffOn;
		if (!document.getElementById("isOnline").checked)
			turnOffOn = 0;
		else
			turnOffOn = 1;
		var isServerOnline = document.getElementById("onlineLabel").getAttribute("data-isOnline");
		console.log("isServerOnline: " + isServerOnline);
		var isOnline;
		var wasOnline;
		if (isServerOnline == "true") {
			console.log("Server is online. isOnline = 1");
			isOnline = 1;
			wasOnline = 1;
		}
		else {
			console.log("Server is offline. isOnline = 0");
			isOnline = 0;
			wasOnline = 0;
		}
		if (turnOffOn == 1 && isOnline == 1)
			isOnline = 0;
		else if (turnOffOn == 1)
			isOnline = 1;
		var nUsers;
		if (isOnline)
			nUsers = document.getElementById("curUser").innerHTML;
		else
			nUsers = 0;
		var adminsKeys = Object.keys(admins);
		var adminsEmails = [];
		for (var i = 0; i < adminsKeys.length; i++) {
			if (adminsKeys[i] != ownerEmail && admins[adminsKeys[i]] != undefined) {
				adminsEmails.push(adminsKeys[i]);	
			}
		}
		adminsEmails = JSON.stringify(adminsEmails);
		var onlineSince = JSON.stringify(document.getElementById("id_server").getAttribute("data-onlineSince"));
		var lastActiveUser = JSON.stringify(document.getElementById("id_server").getAttribute("data-lastActiveUser"));
		/*
		if (!turnOffOn || !isOnline)
			onlineSince = null;
		else {
			onlineSince = JSON.stringify(Date.now());
			lastActiveUser = JSON.stringify(Date.now());
		}
		*/
		onlineSince = JSON.stringify(Date.now());
		lastActiveUser = JSON.stringify(Date.now());
		
		/*
		console.log(id_server);
		console.log(serverName);
		console.log(serverType);
		console.log(nUsers);
		console.log(Capacity);
		console.log(Host);
		console.log(Port);
		console.log(isOnline);
		console.log(FixedOpTime);
		console.log(startT);
		console.log(endT);
		console.log(onlineSince);
		console.log(lastActiveUser);
		console.log(adminsEmails);
		
		
		console.log("TurnOffOn: " + turnOffOn);
		console.log("isOnline: " + isOnline);*/
		
		//Server keeps online
		var canContinue = true;
		if (wasOnline && isOnline) {
			if (originalData['capacity'] != Capacity || originalData['ip'] != Host || originalData['port'] != Port) {
				console.log(originalData['capacity'] + " VS " + Capacity);
				console.log(originalData['ip'] + " VS " + Host);
				console.log(originalData['port'] + " VS " + Port);
				nUsers = -1;
				canContinue = confirm("Warning!\n\nIn order to apply these changes the server will need to be restarted. Do you want to proceed?");
			}
		}
		
		if (canContinue) {
			document.getElementById("modifyServer").style.display = "block";
			$.ajax({
				type: "POST",
				url: "modifyServerInfo.php",
				data:{ id_server:id_server, name: serverName, type:serverType, nUsers:nUsers, capacity:Capacity ,ip: Host, port: Port ,isOnline:isOnline, isSchedule:FixedOpTime, startsAt:startT, stopsAt:endT, onlineSince:onlineSince, lastActiveUser: lastActiveUser, 
				turnOffOn: turnOffOn, admins:adminsEmails},
				success: function (data) {
					try {
						document.getElementById("modifyServer").style.display = "none";
						data = JSON.parse(data);
						if (data == true) {
							alert("Success!\n\nServer changes applied successfully.");
							localStorage.setItem("newServer", "true");
							goToReport();
						}
					}
					catch (e) {
						alert("Error!\n\nFailed to apply server changes. Please try again later.");
					}
					if (data != true) {
						goToMainPage();
					}
					console.log(data);
				}
			});
		}
		else {
			alert("Information.\n\nChanges not applied.");
			goToReport();	
		}
	}
}

IsServerIPValid = function(ip) {
		if (ip == "localhost")
			return true;
		if (startWith(ip, "http://"))
			return true;
		if (startWith(ip, "https://"))
			return true;
		if (startWith(ip, "tcp://"))
			return true;
		if (startWith(ip, "udp://"))
			return true;
		if (startWith(ip, "ip://"))
			return true;
		if (startWith(ip, "pop3://"))
			return true;
		if (startWith(ip, "smtp://"))
			return true;
		if (startWith(ip, "ftp://"))
			return true;
		if (startWith(ip, "ftps://"))
			return true;
		//echo "Yo";
		var $total = 0;
		//echo $ip.";".strlen($ip).";";
		var $ipLength = ip.length;
		for(var $i = 0; $i < 4; $i++) {
			var $current = "";
			var $j = 0;
			var $currentNum = ip.charAt($total);
			while ($currentNum != "." && $total < $ipLength) {
				//console.log("Checking " + $currentNum);
				if (!$.isNumeric($currentNum)) {
					return false;
				}
				if ($j > 3) {
					return false;
				}
				if ($j == 3) {
					if ($currentNum[0] > 2) {
						return false;
					}
					if ($currentNum[0] == 2 && $currentNum[1] > 5) {
						return false;
					}
					if ($currentNum[0] == 2 && $currentNum[1] == 5 && $currentNum[2] > 5) {
						return false;
					}
				}
				$current += $currentNum;
				$j++;
				$total++;
				$currentNum = ip.charAt($total);
			}
			$total++;
			//console.log($current);
			if (!$.isNumeric($current) || $current > 255) {
				return false;	
			}
		}
		if ($total - 1 != ip.length) {
			return false;
		}
		return true;
	}


saveToLocal = function () {
    getInfoToDataBase();
}

show_hideSchedule = function () {
    console.log("FOO");
    var x = document.getElementById("isOpTime").checked;
    console.log(x);
    if (x) {
        console.log("FOO1");
        document.getElementById("schedule").style.display = "block";
    }
    else {
        console.log("FOO2");
        //document.getElementById("schedule").setAttribute("hidden", true);
		document.getElementById("schedule").style.display = "none";
    }
}

function setServerInfo(host, port ) {
    serverIP = host
    serverPort = port
    

}

addAdmins = function() {
	var tableBody = document.getElementById("adminBody");
	var remainingAdminsTableBody = document.getElementById("remainingAdminsBody");
	var adminsToAdd = remainingAdminsTableBody.children;
	var rowsToRemove = [];
	for (var i = 0; i < adminsToAdd.length; i++) {
		var checkbox = adminsToAdd[i].children[2].children[0];
		if (checkbox.checked) {
			var email = checkbox.getAttribute("data-email");
			var username = remainingAdmins[email];
			var adminID = remainingAdminsIDs[email];
			addAdminToMainTable(username, email, tableBody, false);
			delete remainingAdmins[email];
			delete remainingAdminsIDs[email];
			rowsToRemove.push(document.getElementById("remainingAdminRow" + email));
			admins[email] = username;
			adminsIDs[email] = adminID;
		}
		else {
		}
	}
	for (var i = 0; i < rowsToRemove.length; i++) {
		remainingAdminsTableBody.removeChild(rowsToRemove[i]);
	}
}

removeAdmins = function() {
	var tableBody = document.getElementById("adminBody");
	var remainingAdminsTableBody = document.getElementById("remainingAdminsBody");
	var adminsToRemove = tableBody.children;
	var rowsToRemove = [];
	for (var i = 0; i < adminsToRemove.length; i++) {
		var checkbox = adminsToRemove[i].children[2].children[0];
		if (checkbox.checked) {
			var email = checkbox.getAttribute("data-email");
			var username = admins[email];
			var adminID = adminsIDs[email];
			rowsToRemove.push(document.getElementById("adminRow" + email));
			delete admins[email];
			addAdminToRemainingAdmins(username, email, remainingAdminsTableBody);
			remainingAdmins[email] = username;
			remainingAdminsIDs[email] = adminID;
		}
		else {	
		}
	}
	for (var i = 0; i < rowsToRemove.length; i++)
		tableBody.removeChild(rowsToRemove[i]);
}


updateRemainingAdminsTable = function() {
	var keys = Object.keys(remainingAdmins);
	var tableBody = document.getElementById("remainingAdminsBody");
	for (var i = 0; i < keys.length; i++) {
		addAdminToRemainingAdmins(remainingAdmins[keys[i]], keys[i], tableBody);
	}
}

addAdminToMainTable = function(username, email, tableBody, isOwner) {
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
	
	if (!isOwner) {
		var checkboxCell = document.createElement("td");
		var checkbox = document.createElement("input");
		checkbox.setAttribute("type", "checkbox");
		checkbox.setAttribute("name", "admin");
		checkbox.setAttribute("data-email", email);
		//checkboxCell.setAttribute("class", "checkboxCell");
		checkboxCell.appendChild(checkbox);
		row.appendChild(checkboxCell);
	}
	else {
		var checkboxCell = document.createElement("td");
		var checkbox = document.createElement("input");
		checkbox.setAttribute("type", "checkbox");
		checkbox.setAttribute("name", "admin");
		checkbox.setAttribute("data-email", email);
		checkbox.setAttribute("style", "display: none");
		//checkboxCell.setAttribute("class", "checkboxCell");
		checkboxCell.appendChild(checkbox);
		row.appendChild(checkboxCell);
	}
	
	tableBody.appendChild(row);
}

addAdminToRemainingAdmins = function(username, email, tableBody) {
	var row = document.createElement("tr");
	row.setAttribute("id", "remainingAdminRow" + email);
	var usernameCell = document.createElement("td");
	usernameCell.innerHTML = username;
	//username.setAttribute("width", "auto !important");
	var emailCell = document.createElement("td");
	emailCell.innerHTML = email;
	//email.setAttribute("width", "auto !important");
	var checkboxCell = document.createElement("td");
	var checkbox = document.createElement("input");
	checkbox.setAttribute("type", "checkbox");
	checkbox.setAttribute("name", "remainingAdmin");
	checkbox.setAttribute("data-email", email);
	checkboxCell.appendChild(checkbox);
	row.appendChild(usernameCell);
	row.appendChild(emailCell);
	row.appendChild(checkboxCell);
	tableBody.appendChild(row);
}

startWith = function(wholeString, part) {
	var length = part.length;
	if (wholeString.length < length)
		return false;
	for (var i = 0; i < part.length; i++)
		if(wholeString.charAt(i) != part.charAt(i))
			return false;
	return true;
}

//In case server gets deleted.
function goToMainPage() {
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

function cancel() {
	var toList = localStorage.getItem("toList");
	if (toList == "false")
		goToReport();
	else
		goToMainPage();
}

function goToReport() {
	if (!window.parent || !window.parent.document)
		window.location.href = "report.php";
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
			console.log("report.php");
			changePage("report.php", window.parent.document.getElementById('leftDiv'));
		}
		else {
			console.log("report.php");
			changePage("report.php", window.parent.document.getElementById('rightDiv'));
		}
	}
}

function changePage(pageName, div) {
	//Changing the page...
	//var rightPageDiv = window.parent.document.getElementById('rightDiv');
	//rightPageDiv.innerHTML = "<iframe src=" + pageName + "></iframe>";
	div.innerHTML = "<iframe src = " + pageName + "></iframe>";
}

function delServer() {
	var hidden = document.getElementById("id_server");
	var id_server = hidden.value;
	var nUsers = document.getElementById("curUser").innerHTML;
	
	var isOk = false;
	if (nUsers > 0)
		isOk = confirm("Confirmation.\n\nThis server has " + nUsers + " users currently online. Are you sure you want to delete it?");
		
	
	var onlineLabel = document.getElementById("onlineLabel");
	var isOnline = onlineLabel.getAttribute("data-isOnline");
	if (nUsers == 0 && isOnline == "true") {
		isOk = confirm("Confirmation\n\nThis server is online but no users are currently logged in. Are you sure you want to delete it?");
	}
	else if (isOnline == "false") {
		isOk = confirm("Confirmation\n\nThis server is offline. Are you sure you want to delete it?");
	}
	if (isOk) {		
		$.ajax({
			type: "POST",
			url: "deleteServer.php",
			data: { id_server: id_server, email: ownerEmail}, 
			success: function(data){
				try {
					data = JSON.parse(data);
					if (data == true) {
						localStorage.setItem("newServer", "true");
						goToMainPage();
					}
				}
				catch (e) {
					
				}
				if (data != true) {
					localStorage.setItem("newServer", "true");
					goToMainPage();
				}
			}
		});
	}
}

getClients = function(server_id) {
	$.ajax({
	type: "POST",
	url: "getServerInfo.php",
	data: { id_server: server_id }, 
	success: function(data){
		try {
			data = JSON.parse(data);
			setNumberUsers(data.nUsers, data.capacity);
		}
		catch (e) {
			goToMainPage();
			//console.log(e);
			//console.log(data);
		}
	}	
	});
}