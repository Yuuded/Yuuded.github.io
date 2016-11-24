// JavaScript Document

window.onload = function() {
	window.addEventListener("message", receiveMessage, false);	
}

function receiveMessage(event)
{
  	var origin = event.origin || event.originalEvent.origin; // For Chrome, the origin property is in the 		event.originalEvent object.
  	if (origin !== "http://localhost") {
		console.log("Received: " + origin);
		alert("Who dafuq is this?");
    	return;
	}
	var username = event.data;
	console.log(username);
	
	//Updating topbar
	var topbarDiv = $('#topbar');
	var accountNameDiv = topbarDiv.find('#accountNameDiv');
	var accountDiv = topbarDiv.find('#account');
	accountNameDiv.css('display', 'table');
	accountDiv.css('display', 'table');
	var usernameP = accountNameDiv.find('#username');
	usernameP.get(0).innerHTML = "Welcome, " + username;
	changePage("doublePageContainer.html");
	
}

function changePage(pageName) {
	//Changing the page...
	var innerPageDiv = $('#innerPage');
	innerPageDiv.get(0).innerHTML = "<iframe src=" + pageName + "></iframe>";
}

function logout() {
	//Updating topbar
	var topbarDiv = $('#topbar');
	var accountNameDiv = topbarDiv.find('#accountNameDiv');
	var accountDiv = topbarDiv.find('#account');
	accountNameDiv.css('display', 'none');
	accountDiv.css('display', 'none');
	changePage("loginTest.html");	
}