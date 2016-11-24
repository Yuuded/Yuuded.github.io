// JavaScript Document

window.onload = function() {
	var jsData = document.getElementById("jsData");
	var isLoggingOut = jsData.getAttribute("data-logout");
	if (isLoggingOut == "true")
		logout();
	else {
		var topbarDiv = $('#topbar');
		var accountNameDiv = topbarDiv.find('#accountNameDiv');
		var accountDiv = topbarDiv.find('#account');
		accountNameDiv.css('display', 'table');
		accountDiv.css('display', 'table');
		var usernameP = accountNameDiv.find('#username');
		usernameP.get(0).innerHTML = "Welcome, " + localStorage.getItem("username");
		console.log("Yo");
	}
}

function logout() {
	//Updating topbar
	var topbarDiv = $('#topbar');
	var accountNameDiv = topbarDiv.find('#accountNameDiv');
	var accountDiv = topbarDiv.find('#account');
	accountNameDiv.css('display', 'none');
	accountDiv.css('display', 'none');
	localStorage.removeItem("username");
	localStorage.removeItem("email");
	//changePage("loginTest.php");
	window.location = "index.php";
}

function changePage(pageName) {
	//Changing the page...
	var innerPageDiv = $('#innerPage');
	innerPageDiv.get(0).innerHTML = "<iframe src=" + pageName + "></iframe>";
}