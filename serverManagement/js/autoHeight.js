// JavaScript Document

fixHeight = function() {
	//console.log("yolo");
	var wrapperDiv = $("#wrapper");
	var innerPageDiv = document.getElementById("innerPage");
	//topbar size + margin
	innerPageDiv.style.height = wrapperDiv.height() - 80+ "px";
	//console.log(wrapperDiv.height());
	//console.log(innerPageDiv.style.height);
}

window.addEventListener("load", fixHeight);