// JavaScript Document

loadNewServerPage = function() {
	changePage("registServer.php");
}

function changePage(pageName) {
	//Changing the page...
	var rightPageDiv = window.parent.document.getElementById('rightDiv');
	rightPageDiv.innerHTML = "<iframe src=" + pageName + "></iframe>";
}