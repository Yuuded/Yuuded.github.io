// JavaScript Document

var userEmail = localStorage.getItem('email');
var servers = {};
var testingEmails = [];

window.onload = function() {
	loadTestingEmails();
	loadServers();
	//fakeLoadServers();
}

loadTestingEmails = function() {
	testingEmails.push("test1@gmail.com");
	testingEmails.push("test2@gmail.com");
	testingEmails.push("test3@gmail.com");
	testingEmails.push("test4@gmail.com");
	testingEmails.push("test5@gmail.com");
	testingEmails.push("test6@gmail.com");
	testingEmails.push("test7@gmail.com");
}

loadServers = function() {
	localStorage.setItem("newServer", "false");
	var serverDiv = $('#serverListDiv');
	var serverInfo = serverDiv.find('#serverListInfo');
	//serverInfo.get(0).innerHTML = "No server added yet. You can add one by clicking on the Create Server button."
	serverInfo.get(0).innerHTML = "Loading server list, please wait...";
	getServerList();
	var count = 0;
	setInterval(function() {
		var newServer = localStorage.getItem("newServer");
		if (newServer == "true" || newServer == true) {
			localStorage.setItem("newServer", "false");
			getServerList();
		}
		else if (count == 40) {
			count = 0;
			getServerList();
		}
		else if (count > 40) {
			count = 0;	
		}
		count++;
	}, 300);
}

getServerList = function() {
	$.ajax({
		type: "POST",
		url: "getServerList.php",
		success: function(data){
			try {
				var data = JSON.parse(data);
				if (data == "No servers") {
					document.getElementById('serverListInfo').innerHTML = "No servers.";
					document.getElementById('table').setAttribute("style", "display: none");
				}
				else {
					putServerListToHTML(data);
				}
			}
			catch (e) {
				
			}
		}
	});
}

putServerListToHTML = function(data, serverDiv, serverInfo) {
	var serverDiv = document.getElementById('serverListInfo');
	serverDiv.innerHTML = "";
	var tableBody = document.getElementById('tableBody');
	var table = document.getElementById('table');
	var newTableBody = document.createElement('tbody');
	newTableBody.setAttribute("id", "tableBody");
	for (var i = 0; i < data.length; i++) {
		//console.log(data[i]);
		putServerToTable(data[i], newTableBody);
	}
	table.removeChild(tableBody);
	table.appendChild(newTableBody);
	table.setAttribute("style", "display: block");
}

putServerToTable = function(server, tableBody) {
	var row = document.createElement("tr");
	var id_server = server['id_server'];
	var serverName = server['name'];
	var nUsers = server['nUsers'];
	var capacity = server['capacity'];
	var isOnline = server['isOnline'];
	if (isOnline == 0)
		isOnline = false;
	else
		isOnline = true;
	var lastUser = server['lastActiveUser'];
	var ip = server['ip'];
	var port = server['port'];
	servers[id_server] = server;
	
	row.setAttribute("id", "row" + id_server);
	row.setAttribute("data-serverId", id_server);
	row.setAttribute("data-ip", ip);
	row.setAttribute("data-port", port);
	var nameCell = document.createElement("td");
	nameCell.innerHTML = serverName;
	row.appendChild(nameCell);
	
	var stateCell = document.createElement("td");
	var stateImage = document.createElement("img");
	stateImage.setAttribute("src", getServerStatusImg(nUsers, capacity, isOnline, lastUser));
	stateImage.setAttribute("id", "stateImage" + id_server);
	stateCell.appendChild(stateImage);
	var stateHover = document.createElement("span");
	stateCell.appendChild(stateHover);
	row.appendChild(stateCell);
	
	var usersCell = document.createElement("td");
	usersCell.innerHTML = nUsers + "/" + capacity;
	usersCell.setAttribute("id", "usersCell" + id_server);
	usersCell.setAttribute("data-users", nUsers);
	usersCell.setAttribute("data-capacity", capacity);
	row.appendChild(usersCell);
	
	var onlineCell = document.createElement("td");
	onlineCell.setAttribute("class", "clickable");
	var onlineImage = document.createElement("img");
	onlineImage.setAttribute("class", "buttonImage");
	onlineImage.setAttribute("id", "onlineImage" + id_server);
	onlineImage.setAttribute("data-lastUser", lastUser);
	onlineImage.setAttribute("data-online", isOnline);
	if (isOnline)
		onlineImage.setAttribute("src", "images/red on button_40.png");
	else
		onlineImage.setAttribute("src", "images/green on button_40.png");
	onlineImage.addEventListener("click", function() {
		toggleOnOffServer(id_server);
	});
	onlineCell.appendChild(onlineImage);
	row.appendChild(onlineCell);
	
	var deleteCell = document.createElement("td");
	deleteCell.setAttribute("class", "clickable");
	var deleteImage = document.createElement("img");
	deleteImage.setAttribute("class", "buttonImage");
	deleteImage.setAttribute("src", "images/trash_40.png");
	deleteImage.addEventListener("click", function() {
		deleteServer(id_server);
	});
	deleteCell.appendChild(deleteImage);
	row.appendChild(deleteCell);
	
	var modifyCell = document.createElement("td");
	modifyCell.setAttribute("class", "clickable");
	var modifyImage = document.createElement("img");
	modifyImage.setAttribute("class", "buttonImage");
	modifyImage.setAttribute("src", "images/icon_small_settings1_40.png");
	modifyImage.addEventListener("click", function() {
		callModifyServer(id_server);
	});
	modifyCell.appendChild(modifyImage);
	row.appendChild(modifyCell);
	
	var openCell = document.createElement("td");
	//openCell.setAttribute("class", "clickable");
	openCell.innerHTML = "&#187";
	openCell.setAttribute("data-id", id_server);
	openCell.setAttribute("class", "clickText");
	openCell.addEventListener("click", function() {
		callReportServer(id_server);
	});
	row.appendChild(openCell);
	
	randomUsersChange(id_server);
	
	tableBody.appendChild(row);
}

callReportServer = function(id_server) {
	cacheServerInfo(id_server);
	localStorage.setItem("toList", "false");
	loadNewPage("report.php", "Server Report");
}

callModifyServer = function(id_server) {
	cacheServerInfo(id_server);
	localStorage.setItem("toList", "true");
	loadNewPage("modify.php", "Server Modify");
}

cacheServerInfo = function(id_server) {
	console.log(id_server);
	var server = servers[id_server];
	var serverKeys = Object.keys(server);
	for (var i = 0; i < serverKeys.length; i++)
		localStorage.setItem(serverKeys[i], server[serverKeys[i]]);
	localStorage.setItem("server_keys", serverKeys);
}

//TODO: If the server is online, this method should ask if the user really wants to turn it off if the number of users is more than 0 (and refer that)
toggleOnOffServer = function(id_server) {
	var onlineImage = document.getElementById("onlineImage" + id_server);
	var isOnline = onlineImage.getAttribute("data-online");
	if (isOnline == "true")
		isOnline = true;
	else
		isOnline = false;
	isOnline = !isOnline;
	var onlineSince = JSON.stringify(Date.now());
	console.log(onlineSince);
	var lastUser = onlineImage.getAttribute("data-lastUser");
	if (isOnline)
		lastUser = JSON.stringify(Date.now());
	
	//console.log("Server id: " + id_server + "; Trying to set online status to: " + isOnline);
    $.ajax({
        type: "POST",
        url: "modifyServerOnline.php",
        data: { id_server: id_server, isOnline: isOnline, onlineSince:onlineSince, lastActiveUser:lastUser }, 
        success: function(data){
			try {
				//console.log("On/Off answer: " + data);
				data = JSON.parse(data);
				var stateImage = document.createElement("img");
				if (data == true) {
					onlineImage.setAttribute("data-online", isOnline);
					if (isOnline)
						onlineImage.setAttribute("src", "images/red on button_40.png");
					else
						onlineImage.setAttribute("src", "images/green on button_40.png");
						
					var usersCell = document.getElementById("usersCell" + id_server);
					usersCell.setAttribute("data-users", 0);
					var nUsers = usersCell.getAttribute("data-users");
					var capacity = usersCell.getAttribute("data-capacity");
					var lastUser = onlineImage.getAttribute("data-lastUser");
					usersCell.innerHTML = nUsers + "/" + capacity;
					var stateImage = document.getElementById("stateImage" + id_server);
					console.log("");
					console.log(isOnline);
					console.log(nUsers);
					console.log(capacity);
					stateImage.setAttribute("src", getServerStatusImg(nUsers, capacity, isOnline, lastUser));
					console.log("Updated state image");
				}
			}
			catch (e) {
				
			}
			//Ignore, server was already deleted and will soon be deleted from serverList
			if (data != true) {
				
			}
			console.log(data);
        }
    });
}

getUserList = function() {
	var email = localStorage.getItem('email');
	//console.log(email);
	 $.ajax({
        type: "POST",
        url: "getAllUsers.php",
        data: { email: email }, 
        success: function(data){
			try {
				data = JSON.parse(data);
				console.log(data);
				var keys = Object.keys(data);
				for (var i = 0; i < keys.length; i++)
					console.log(keys + ": " + data[keys[i]]);
			}
			catch (e) {
				console.log(data);
			}
			//This one doesn't fail.
			if (data != true) {
				
			}
        }
    });
}

deleteServer = function(id_server) {
	var usersCell = document.getElementById("usersCell" + id_server);
	var nUsers = usersCell.getAttribute("data-users");
	
	var isOk = false;
	if (nUsers > 0)
		isOk = confirm("Confirmation.\n\nThe server you are attempting to delete has " + nUsers + " users currently online.\nAre you sure you want to delete it?");
	var onlineImage = document.getElementById("onlineImage" + id_server);
	var isOnline = onlineImage.getAttribute("data-online");
	if (nUsers == 0 && isOnline == "true") {
		isOk = confirm("Confirmation.\n\nThe server you are attempting to delete is online but no users are currently logged in. Are you sure you want to delete it?");
	}
	else if (isOnline == "false") {
		isOk = confirm("Confirmation.\n\nThe server you are attempting to delete is offline. Are you sure you want to delete it?");
	}
	if (isOk) {		
		$.ajax({
			type: "POST",
			url: "deleteServer.php",
			data: { id_server: id_server, email: userEmail}, 
			success: function(data){
				try {
					data = JSON.parse(data);
					if (data == true) {
						var tableBody = document.getElementById('tableBody');
						var row = document.getElementById("row" + id_server);
						tableBody.removeChild(row);
						localStorage.setItem("remove", id_server);
					}
				}
				catch (e) {
					
				}
				if (data != true) {
					var tableBody = document.getElementById('tableBody');
					var row = document.getElementById("row" + id_server);
					if (row != undefined)
						tableBody.removeChild(row);
				}
			}
		});
	}
}

fakeLoadServers = function() {
}

createServerTab = function(serverDiv, serverName, nUsersOnline, capacity, isOnline) {
	console.log("Starting to create server tab...");
	var serverList = serverDiv.find("#serversList")[0];
	var listItem = document.createElement("li");
	serverList.appendChild(listItem);
	//Div just to make stuff centered
	var serverTabOuterDiv = document.createElement("div");
	serverTabOuterDiv.setAttribute("class", "serverTabOuterDiv");
	listItem.appendChild(serverTabOuterDiv);
	var serverTabDiv = document.createElement("div");
	serverTabDiv.setAttribute("class", "serverTabDiv");
	serverTabOuterDiv.appendChild(serverTabDiv);
	var infoList = document.createElement("ul");
	infoList.setAttribute("class", "serverTab");
	serverTabDiv.appendChild(infoList);
	var infos = [];
	for (var i = 0; i < 5; i++)
		infos[i] = document.createElement("li");
		
	infos[0].setAttribute("class", "serverName");
	infos[0].innerHTML = serverName;
	
	var serverStatusImg = document.createElement("img");
	serverStatusImg.setAttribute("id", serverName + "img");
	serverStatusImg.setAttribute("src", getServerStatusImg(nUsersOnline, capacity, isOnline));
	infos[1].appendChild(serverStatusImg);
	
	var serverOnButton = document.createElement("button");
	var serverOnImg = document.createElement("img");
	serverOnImg.setAttribute("id", serverName + "statusImg");
	if (isOnline)
		serverOnImg.setAttribute("src", "images/red on button_40.png");
	else
		serverOnImg.setAttribute("src", "images/green on button_40.png");
	serverOnButton.appendChild(serverOnImg);
	infos[2].appendChild(serverOnButton);
	
	var serverDeleteButton = document.createElement("button");
	var serverDeleteImg = document.createElement("img");
	serverDeleteImg.setAttribute("src", "images/trash_40.png");
	serverDeleteButton.appendChild(serverDeleteImg);
	infos[3].appendChild(serverDeleteButton);
	
	var serverReportButton = document.createElement("button");
	serverReportButton.innerHTML = "&#187";
	serverReportButton.setAttribute("class", "serverButtonClass");
	//console.log(serverReportButton.innerHTML);
	infos[4].appendChild(serverReportButton);
	infos[4].setAttribute("class", "lastLI");
	
	for (var i = 0; i < 5; i++)
		infoList.appendChild(infos[i]);
		
	console.log("Finished creating server tab...");
	return infoList;
}

//maxValue is exclusive
//This doesn't care if the server is online or not.
startFakeServerOnlineClientsChanger = function(serverTab, minValue, maxValue, capacity, frequency) {
	var range = maxValue - minValue;
	var serverName = serverTab.childNodes[0].innerHTML;
	var statusImg = serverTab.childNodes[1].childNodes[0];
	//console.log(serverTab.childNodes.length);
	//console.log(statusImg);
	setInterval(function() {
		var randomValue = Math.random();
		var usersOnline = Math.floor(randomValue * range) + minValue;
		//console.log("Server capacity: " + capacity);
		//console.log("Current users: " + usersOnline);
		//console.log(usersOnline);
		//statusImg.setAttribute("src", getServerStatusImg(usersOnline, capacity, true));
		statusImg.src = getServerStatusImg(usersOnline, capacity, true);
		//fakeUpdateClientsOnline(range, minValue, capacity, statusImg);
	}, frequency);
}

//This also doesn't care if the server is online or not.
fakeUpdateClientsOnline = function(range, minValue, capacity, statusImg) {
}

getServerStatusImg = function(nUsersOnline, capacity, isOnline, lastUserOnline) {
	var imgSrc = "images/circle_";
	//console.log("nUsersOnline/capacity: " + nUsersOnline / capacity)
	if (!isOnline)
		imgSrc += "grey";
	else if (isOnline && nUsersOnline == 0) {
		var now = Date.now();
		var lastUser = JSON.parse(lastUserOnline);
		var dif = now - lastUser;
		var days = dif/(1000 * 60 * 60 * 24);
		if (days > 2)
			return "images/exclamation-icon-26_50_40.png";
	}
	if (isOnline && nUsersOnline / capacity < 0.5)
		imgSrc += "green";
	else if (isOnline && nUsersOnline / capacity < 0.85)
		imgSrc += "yellow";
	else if (isOnline)
		imgSrc += "red";
	imgSrc += "_50_40.png";
	return imgSrc;
}

loadNewPage = function(pageName, targetPageName) {
	if (!window.parent || !window.parent.document)
		window.location.href = pageName;
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
			changePage(pageName, window.parent.document.getElementById('leftDiv'));
		else {
			console.log("Right");
			var rightDiv = window.parent.document.getElementById('rightDiv');
			var rightPage = rightDiv.children[0].getAttribute("src");
			if (rightPage == "modify.php"){
				var isOk = confirm("Warning!\n\nYou have a Modify Server page open on the right side.\nAre you sure you want to open a " + targetPageName + " page?\nAll your changes on the current Modify Server page will be lost.");
				if (isOk)
					changePage(pageName, rightDiv);
			}
			else if (rightPage == "registServer.php") {
				var isOk = confirm("Warning!\n\nYou have a Regist/Create Server page open on the right side.\nAre you sure you want to open a " + targetPageName + " page?\nAll your changes on the current Server Add/Register page will be lost.");
				if (isOk)
					changePage(pageName, rightDiv);
			}
			else
				changePage(pageName, rightDiv);
		}
	}
}

//TODO: Check if the rightDiv is already this page. In case that it is, ignore the request or send a warning noticing that all the changes will be lost
loadNewAddServerPage = function() {
	loadNewPage("registServer.php", "Register/Create Server");
}

function changePage(pageName, div) {
	//Changing the page...
	//var rightPageDiv = window.parent.document.getElementById('rightDiv');
	//rightPageDiv.innerHTML = "<iframe src=" + pageName + "></iframe>";
	div.innerHTML = "<iframe src = " + pageName + "></iframe>";
}

realLoadServers = function() {
	$.ajax({
		type: "GET",
		url: "getServerList.php",
		data: { email: userEmail },
		success: function(data) {
			console.log(data);	
		}
	});
}

getServerInfo = function() {
	serverIP = "192.168.213.212"
	serverPort = "43253"
    $.ajax({
        type: "POST",
        url: "getServerInfo.php",
        data: { ip: serverIP, port: serverPort }, 
        success: function(data){
			console.log(data);
        }
    });
}

searchServer = function() {
	alert("Error!\n\nNot implemented!");
}

isTestingAccount = function(email) {
	if (testingEmails.indexOf(email) != -1)
		return true;
	return false;
}

randomUsersChange = function(id_server) {
	var count = 0;
	var interval = setInterval(function() {
		if (!isTestingAccount(userEmail)) {
			try {
				var row = document.getElementById("row" + id_server);
				var stateImage = document.getElementById("stateImage" + id_server);
				var usersCell = document.getElementById("usersCell" + id_server);
				var onlineImage = document.getElementById("onlineImage" + id_server);
				
				var isOnline = onlineImage.getAttribute("data-online");
				if (isOnline == "true") {
					var nUsers = usersCell.getAttribute("data-users");
					var capacity = usersCell.getAttribute("data-capacity");
					var lastUser = onlineImage.getAttribute("data-lastUser");
					var category;
					if (nUsers / capacity < 0.5)
						category = 1;
					else if (nUsers / capacity < 0.85)
						category = 2;
					else
						category = 3;
					var changeCategory = Math.random();
					var newCategory;
					if (changeCategory <= 0.1) {
						if (nUsers >= 0) {
							if (category == 2) {
								var goesDown = Math.random();
								if (goesDown >= 0.5)
									newCategory = 3;
								else
									newCategory = 1;	
							}
							else if (category == 1)
								newCategory = 2;
							else
								newCategory = 2;
						}
						else
							newCategory = category;
					}
					else
						newCategory = category;
					nUsers = getRandomUsersFromCategory(newCategory, nUsers, capacity);
					lastActiveUser = onlineImage.getAttribute("data-lastUser");
					if (nUsers > 0)
						lastActiveUser = JSON.stringify(Date.now());
					$.ajax({
						type: "POST",
						url: "modifyServerClients.php",
						data: { id_server: id_server, nUsers: nUsers, lastActiveUser: lastActiveUser }, 
						success: function(data){
							usersCell.innerHTML = nUsers + "/" + capacity;
							usersCell.setAttribute("data-users", nUsers);
							stateImage.setAttribute("src", getServerStatusImg(nUsers, capacity, isOnline, lastUser));
							onlineImage.setAttribute("data-lastUser", lastActiveUser);
							localStorage.setItem(id_server + "nUsers", nUsers);
						}
					});
				}
				else {
						
				}
			}
			catch (e) {
				//console.log(e);
				clearInterval(interval);	
			}
		}
		else {
			try {
				var stateImage = document.getElementById("stateImage" + id_server);
				var usersCell = document.getElementById("usersCell" + id_server);
				var onlineImage = document.getElementById("onlineImage" + id_server);
				var isOnline = onlineImage.getAttribute("data-online");
				if (isOnline == "true") {
					/*
					var nUsers = usersCell.getAttribute("data-users");
					if (nUsers > 0) {
						var lastUser = JSON.stringify(Date.now());
						$.ajax({
						type: "POST",
						url: "modifyServerClients.php",
						data: { id_server: id_server, nUsers: nUsers, lastActiveUser: lastUser }, 
						success: function(data){
							onlineImage.setAttribute("data-lastUser", lastUser);
						}
					});
//					var lastUser = onlineImage.getAttribute("data-lastUser");
					}*/
					var row = document.getElementById("row" + id_server);
					var ip = row.getAttribute("data-ip");
					var port = parseInt(row.getAttribute("data-port"));
					var usersCell = document.getElementById("usersCell" + id_server);
					var capacity = parseInt(usersCell.getAttribute("data-capacity"));
					var random;
					var min;
					var max;
					var range;
					var value;
					//Always yellow
					if (ip == "100.100.100.101" && (port >= 40000 && port <= 40009)) {
						random = Math.random();
						min = Math.floor(0.5 * capacity);
						max = Math.floor(0.85 * capacity);
						range = max - min;
						value = Math.floor(random * range + min);
					}
					//The one offline that when online is always green
					else if (ip == "100.100.100.102" && (port >= 40010 && port <= 40019)) {
						random = Math.random();
						max = Math.floor(0.5 * capacity);
						value = Math.floor(random * max);
					}
					//The one inactive (always 0 users)
					else if (ip == "100.100.100.103" && (port >= 40020 && port <= 40029)) {
						value = 0;
					}
					//The one always red until capacity is increased
					else if (ip == "100.100.100.104" && (port >= 40030 && port <= 40039)) {
						random = Math.random();
						if (capacity <= 200) {
							min = Math.round(0.85 * capacity);
							max = 1 * capacity;
							if (min == max)
								value = max;
							else {
								range = max - min;
								value = Math.floor(random * range + min);
							}
						}
						//Stays yellow
						else if (capacity <= 300) {
							min = Math.floor(0.5 * capacity);
							max = Math.floor(0.85 * capacity);
							range = max - min;
							value = Math.floor(random * range + min);
						}
						//Stays green
						else {
							max = Math.floor(0.5 * capacity);
							min = 130;
							range = max - min;
							value = Math.floor(random * range + min);
						}
					}
					//The one always green
					else if (ip == "100.100.100.105" && (port >= 40040 && port <= 40049)) {
						random = Math.random();
						max = Math.floor(0.5 * capacity);
						value = Math.floor(random * max);
					}
					//Always stays green
					else {
						random = Math.random();
						max = Math.floor(0.5 * capacity);
						value = Math.floor(random * max);
					}
					nUsers = value;
					if (nUsers > 0) {
						var lastUser = JSON.stringify(Date.now());
						$.ajax({
						type: "POST",
						url: "modifyServerClients.php",
						data: { id_server: id_server, nUsers: nUsers, lastActiveUser: lastUser }, 
						success: function(data){
							usersCell.innerHTML = nUsers + "/" + capacity;
							usersCell.setAttribute("data-users", nUsers);
							stateImage.setAttribute("src", getServerStatusImg(nUsers, capacity, isOnline, lastUser));
							onlineImage.setAttribute("data-lastUser", lastUser);
							localStorage.setItem(id_server + "nUsers", nUsers);
						}
						});
					}
				}
			}
			catch (e) {
				clearInterval(interval);	
			}
		}
		count++;
		if (count == 3) {
			clearInterval(interval);
		}
	}, 4000);
}

getRandomUsersFromCategory = function(category, nUsers, capacity) {
	var randomValue = Math.random();
	var maxValue;
	var minValue;
	var range;
	var value;
	if (category == 1) {
		maxValue = Math.floor(capacity / 2);
		value = Math.floor(randomValue * maxValue)
	}
	else if (category = 2) {
		minValue = Math.floor(capacity / 2);
		maxValue = Math.floor(capacity * 0.85);
		range = maxValue - minValue;
		value = Math.floor(randomValue * range + minValue);
	}
	else {
		minValue = Math.floor(capacity * 0.85);
		maxValue = capacity;
		range = maxValue - minValue;
		value = Math.round(range + minValue);	
	}
	return value;
}