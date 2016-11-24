// JavaScript Document

login = function() {
	/*
	var topbarDiv = $('#topbar');
	var accountNameDiv = topbarDiv.find('accountNameDiv');
	var accountDiv = topbarDiv.find('account');
	*/
	
	//Assumes login username is in loginForm/usernameField
	var username = $('#loginForm').find("#usernameField");
	console.log(username.val());
	
	window.parent.postMessage(username.val(), "http://localhost:8000/serverManagement/index.html");
	/*
	var topbarDiv = window.parent.document.getElementById('topbar');
	var accountNameDiv = topbarDiv.getElementById('accountNameDiv');
	var accountDiv = topbarDiv.getElementById('account');
	
	accountNameDiv.css('display', 'table');
	accountDiv.css('display', 'table');
	topbarDiv.find("#username").innerHTML = username;
	//alert(username);
	*/
}