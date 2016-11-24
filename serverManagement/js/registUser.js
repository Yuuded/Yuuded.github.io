// JavaScript Document

window.onload = function () {
	var jsData = document.getElementById("jsData");
	var action = jsData.getAttribute("data-action");
	var err = jsData.getAttribute("data-err");
	console.log(jsData.getAttribute("data-email"));
	if (action == "Regist" && err == "") {
		sucessfulRegister(jsData);
	}
	else if (action == "Regist") {
		failedRegister(jsData);
	}
	else if (action == "Login" && err == "") {
		sucessfulLogin(jsData);
	}
	else if (action == "Login") {
		failedLogin(jsData);
	}
	/*
	jsData.setAttribute("data-action", "");
	jsData.setAttribute("data-username", "");
	jsData.setAttribute("data-password", "");
	jsData.setAttribute("data-email", "");
	jsData.setAttribute("data-err", "");
	*/
}

sucessfulRegister = function (jsData) {
	/*
	var username = jsData.getAttribute("data-username");
	var password = jsData.getAttribute("data-password");
	var email = jsData.getAttribute("data-email");
	*/
	alert("Success!\n\nRegist successful.\nYou can now login with your new account.");
	ShowLogin();
}

failedRegister = function (jsData) {
	var err = jsData.getAttribute("data-err");
	if (err.includes("email") && err.includes("registered")) {
		alert("Error!\n\nThe email " + jsData.getAttribute("data-email") + " is already registered!");
	}
	else if (err.includes("Database") && err.includes("failed")) {
		alert("Error!\n\nFailed to regist the account.\n\nThis is most possibly because the regist servers are down, please try again later.");
	}
	else {
		alert("Error!\n\nAn unknown error happened during the regist process.\nPlease try registering again or reloading the page.");
	}
	ShowRegis();
	//var emailRegister = document.getElementById("email2");
	//emailRegister.value = jsData.getAttribute("data-email");
	//emailRegister.value = "stuffBro";
	//clearAttributes(jsData);
	console.log(err);
}

sucessfulLogin = function (jsData) {
	var username = jsData.getAttribute("data-username");
	/*
	var topbarDiv = $('#topbar', window.parent.document);
	var accountNameDiv = topbarDiv.find('#accountNameDiv');
	var accountDiv = topbarDiv.find('#account');
	accountNameDiv.css('display', 'table');
	accountDiv.css('display', 'table');
	var usernameP = accountNameDiv.find('#username');
	usernameP.get(0).innerHTML = "Welcome, " + username;
	*/
	localStorage.setItem("username", username);
	localStorage.setItem("email", jsData.getAttribute("data-email"));
	clearAttributes(jsData);
	//changePage("doublePageContainer.html");
	window.parent.window.location = "serverManagement.php";
}

failedLogin = function (jsData) {
	var err = jsData.getAttribute("data-err");
	if (err.includes("Database") && err.includes("failed")) {
		alert("Error!\n\nFailed to check if the login is correct.\n\nThis is most possibly because the login servers are down, please try again later.");
	}
	else
		alert("Error!\n\nError logging in. Please check that your email and password are correct.");
}

function changePage(pageName) {
	//Changing the page...
	var innerPageDiv = $('#innerPage', window.parent.document);
	innerPageDiv.get(0).innerHTML = "<iframe src=" + pageName + "></iframe>";
}

function clearAttributes(jsData) {
	jsData.setAttribute("data-action", "");
	jsData.setAttribute("data-username", "");
	jsData.setAttribute("data-password", "");
	jsData.setAttribute("data-email", "");
	jsData.setAttribute("data-err", "");
}

function ShowRegis() {
	console.log("Teste1");
	document.getElementById("loginForm").style.display = "none";
	document.getElementById("registerForm").style.display = "block";
}

function ShowLogin() {
	console.log("Teste2	");
	document.getElementById("registerForm").style.display = "none";
	document.getElementById("loginForm").style.display = "block";
}

function cancelRegist() {
	ShowLogin();	
}