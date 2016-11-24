// JavaScript Document

var remainingAdmins = {};
var remainingAdminsIDs = {};
var admins = {};
var adminsIDs = {};
var hostEmail = "";

window.onload = function() {
	getUserList();
	addMyselfAsAdmin();
	prepareValidation();
}

prepareValidation = function() {
	/*
	var frmvalidator = new Validator("form");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("name","req","Please provide a name for the server.");
	frmvalidator.addValidation("capacity", "req", "Please provide a server capacity.");
	*/
	/*
		
		$validator = new FormValidator();
        $validator->addValidation("name","req","Please provide an username.");
		$validator->addValidation("name","minlen=3", "Server name must be at least 3 characters long.");
		$validator->addValidation("name","maxlen=60", "Server name must be at most 60 characters long.");
		
        $validator->addValidation("capacity","req","Please provide a server capacity.");
        $validator->addValidation("capacity","num","Server capacity must be a number.");
		
        $validator->addValidation("ip","req","Please provide a host for the server.");
        $validator->addValidation("port","req","Please provide a port for the server.");
		$validator->addValidation("port", "num", "Port must be a number.");
		$validator->addValidation("port", "lessthan=65536", "Port must be between 1 and 65536.");
		$validator->addValidation("port", "greaterthan=0", "Port must be between 1 and 65536.");
		
		if ($formvars['isSchedule'] == "on") {
			$validator->addValidation("startsAt","req","Please provide a start time for the server.");
			$validator->addValidation("ip","req","Please provide a finish time for the server.");
		}
		
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
			echo "<script>console.log('Fail in ValidateForm()');</script>";
            return false;
        }
		if (!$this->IsServerIPValid($formvars['ip'])) {
			return false;
		}
        return true;
		*/
}

addMyselfAsAdmin = function() {
	var tableBody = document.getElementById("adminBody");
	//var row = document.createElement("tr");
	var username = localStorage.getItem("username");
	var email = localStorage.getItem("email");
	addAdminToMainTable(username, email, tableBody, true);
	admins[email] = username;
	hostEmail = email;
}

//TODO: Put a warning mensage saying that all the changes will be lost.
cancelServer = function() {
	goBack();
}

goBack = function() {
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

//TODO: This chooseDiv will have to, later on, have some way to distinguish which create/Regist server to cancel when there's two happening at the same time... or maybe prevent that?
chooseDiv = function(pageName) {
	var rightDiv = window.parent.document.getElementById("rightDiv");
	console.log(rightDiv.childNodes[0].src);
	if (endsWidth(rightDiv.childNodes[0].src, "registServer.php"))
		changePage(pageName, rightDiv);
	else
		changePage(pageName, window.parent.document.getElementById("leftDiv"));
}

function changePage(pageName, div) {
	//Changing the page...
	//var rightPageDiv = window.parent.document.getElementById('rightDiv');
	//rightPageDiv.innerHTML = "<iframe src=" + pageName + "></iframe>";
	div.innerHTML = "<iframe src = " + pageName + "></iframe>";
}

function endsWidth(testStr, compareStr) {
	var testStrLength = testStr.length;
	for (var i = 0; i < compareStr.length; i++)
		if (testStr[testStrLength - 1 - i] != compareStr[compareStr.length - 1 - i])
			return false;
	return true;
}

getUserList = function() {
	var email = localStorage.getItem('email');
	console.log(email);
	 $.ajax({
        type: "POST",
        url: "getAllUsers.php",
        data: { email: email }, 
        success: function(data){
			try {
				data = JSON.parse(data);
				console.log(data);
				var keys = Object.keys(data);
				for (var i = 0; i < keys.length; i+= 3) {
					var adminUsername = data[keys[i]];
					var adminEmail = data[keys[i + 1]];
					var adminId = data[keys[i + 2]];
					if (adminEmail != email) {
						console.log("ok!");
						remainingAdmins[adminEmail] = adminUsername;
						remainingAdminsIDs[adminEmail] = adminId;
					}
				}
				updateRemainingAdminsTable();
			}
			catch (e) {
				console.log(data);
			}
			//TODO: error processing
			if (data != true) {
				
			}
        }
    });
}

updateRemainingAdminsTable = function() {
	console.log("yo");
	var keys = Object.keys(remainingAdmins);
	var tableBody = document.getElementById("remainingAdminsBody");
	for (var i = 0; i < keys.length; i++) {
		console.log("ye");
		addAdminToRemainingAdmins(remainingAdmins[keys[i]], keys[i], tableBody);
		console.log("ya");
	}
}

addAdmins = function() {
	var tableBody = document.getElementById("adminBody");
	var remainingAdminsTableBody = document.getElementById("remainingAdminsBody");
	var adminsToAdd = remainingAdminsTableBody.children;
	var rowsToRemove = [];
	console.log(adminsToAdd.length);
	for (var i = 0; i < adminsToAdd.length; i++) {
		var checkbox = adminsToAdd[i].children[2].children[0];
		console.log(checkbox);
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
	console.log("Starting remove admins...");
	var tableBody = document.getElementById("adminBody");
	var remainingAdminsTableBody = document.getElementById("remainingAdminsBody");
	var adminsToRemove = tableBody.children;
	var rowsToRemove = [];
	for (var i = 0; i < adminsToRemove.length; i++) {
		console.log(adminsToRemove[i]);
		var checkbox = adminsToRemove[i].children[2].children[0];
		console.log(checkbox);
		if (checkbox.checked) {
			console.log("Checked");
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
			console.log("Unchecked");	
		}
	}
	for (var i = 0; i < rowsToRemove.length; i++)
		tableBody.removeChild(rowsToRemove[i]);
}

addAdminToRemainingAdmins = function(username, email, tableBody) {
	console.log("Starting to add to remaining admins...");
	var row = document.createElement("tr");
	row.setAttribute("id", "remainingAdminRow" + email);
	var usernameCell = document.createElement("td");
	usernameCell.innerHTML = username;
	//username.setAttribute("width", "auto !important");
	var emailCell = document.createElement("td");
	emailCell.innerHTML = email;
	//email.setAttribute("width", "auto !important");
	console.log("yep");
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

addAdminToMainTable = function(username, email, tableBody, isOwner) {
	console.log("Starting addAdminToMainTable");
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
	console.log("Finishing addAdminToMainTable");
}

registServer = function() {
	var success = validate();
	var isScratch = document.getElementById("isScratch").checked;
	var name =  document.getElementById("name").value;
	var type =  document.getElementById("type");
	var selectedTypeIndex = type.selectedIndex;
	type = type.options[selectedTypeIndex].value;
	var capacity = document.getElementById("capacity").value;
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	
	
	if (isScratch)
		isScratch = 1;
	else
		isScratch = 0;
}

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

validate = function() {
	resetValidation("name");
	resetValidation("capacity");
	resetValidation("ip");
	resetValidation("port");
	
	var reqName = validateReq("name", "Please provide a name for the server.");
	var minName = validateMinLength("name", 3, "Server name must be at least 3 characters long.");
	var maxName = validateMaxLength("name", 60, "Server name must be at most 60 characters long.");
	
	var reqCapacity = validateReq("capacity", "Please provide a server capacity.");
	var numCapacity = validateNum("capacity", "Server capacity must be an integer positive number.");
	
	var reqIP = validateReq("ip", "Please provide a host for the server.");
	var validIP = validateIP("ip", "Invalid IP/URL. Please check that you have typed it propelly.");
	
	var reqPort = validateReq("port", "Please provide a port for the server.");
	var numPort = validateNum("port", "Port must be an integer positive number.");
	var minPort = validateAtLeast("port", "Port must be between 1 and 65536.");
	var maxPort = validateAtMost("port", "Port must be between 1 and 65536.");
	
	if (reqName && minName && maxName && reqCapacity && numCapacity && reqIP && validIP && reqPort && numPort && minPort && maxPort) {

    	var serverName = document.getElementById("name").value;
	    var serverType = document.getElementById("type").value;
	    var capacity = document.getElementById("capacity").value;
		var fixedOpTime;
		if (!document.getElementById("isSchedule").checked)
			fixedOpTime = 0;
		else
			fixedOpTime = 1;
	    //var FixedOpTime = document.getElementById("isOpTime").getAttribute("checked");
	    var startT = document.getElementById("startAt").value;
		startT = JSON.stringify(startT);
	    var endT = document.getElementById("stopAt").value;
		endT = JSON.stringify(endT);
		//console.log(startT);
		//console.log(endT);
	    var adminsString;
		var adminsKeys = Object.keys(admins);
		var adminsEmails = [];
		for (var i = 0; i < adminsKeys.length; i++) {
			if (adminsKeys[i] != hostEmail && admins[adminsKeys[i]] != undefined) {
				adminsEmails.push(adminsKeys[i]);	
			}
		}
		adminsEmails = JSON.stringify(adminsEmails);
	    var Host = document.getElementById("ip").value;
	    var Port = document.getElementById("port").value;
		var lastActiveUse = JSON.stringify(Date.now());
		var onlineSince = JSON.stringify(Date.now());
		
		//console.log(fixedOpTime);
		
		if(!document.getElementById("isScratch").checked)
			document.getElementById("creatingDiv").style.display = "block";
		else
			document.getElementById("registingDiv").style.display = "block";
		
		$.ajax({
        type: "POST",
        url: "registNewServer.php",
        data:{ name: serverName, type:serverType, nUsers:"0", capacity:capacity ,ip: Host, port: Port ,isOnline:0, isSchedule:fixedOpTime, startsAt:startT,stopsAt:endT,lastActiveUser:lastActiveUse, onlineSince:onlineSince, admins: adminsEmails},
        success: function (data) {
			/*
			try {
				data = JSON.parse(data);
				if (data == true) {
					alert("Server registed successfully");
				}
			}
			catch (e) {
				
			}
			*/
			//TODO: error processing
			//if (data != true) {
				if (data == "Detected that already has server.false") {
					document.getElementById("creatingDiv").style.display = "none";
					document.getElementById("registingDiv").style.display = "none";
					alert("Error!\n\nYou already have a server in your list with that IP and Port.");
				}
				else {
					var wasSucessfull = true;
					var index = data.indexOf("true");
					if (index == -1) {
						wasSucessfull = false;
						index = data.indexOf("false");
					}
					var server_id = data.substring(0, index);
					//console.log(server_id);
					$.ajax({
						type: "POST",
						url: "getServerInfo.php",
						data: { id_server: server_id }, 
						success: function(data){
							document.getElementById("creatingDiv").style.display = "none";
							document.getElementById("registingDiv").style.display = "none";
							//console.log(data);
							data = JSON.parse(data);
							//console.log(data);
							var serverName = data.name;
							if (!wasSucessfull)
								alert("Warning!\n\nThe server that you were trying to add was already added before by another user.\nThe server has been added to your list but the server configurations that you have sent were not applied.\nYou'll be redirected to the server report so, if you need to, you can modify it's settings.");
							else
								alert("Success!\n\nServer registed successfully");
							localStorage.setItem("id_server", server_id);
							localStorage.setItem("newServer", "true");
							chooseDiv("report.php");
						}
					});
				}
			//}
			console.log(data);
        }
    });
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

showHideSchedule = function() {
	var schedule = document.getElementById("isSchedule");
	var scheduleDiv = document.getElementById("schedule");
	var isChecked = schedule.checked;
	console.log(isChecked);
    if (!isChecked) {
        //document.getElementById("schedule").setAttribute("hidden", true);
		console.log("Hidding");
		scheduleDiv.style.display = "none";
		document.getElementById("scheduleSpan").style.display = "none";
    }
    else {
		console.log("Showing");
        scheduleDiv.style.display = "block";
		document.getElementById("scheduleSpan").style.display = "block";
    }
	console.log(scheduleDiv);
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



