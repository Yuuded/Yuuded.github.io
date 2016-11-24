// JavaScript Document

var isLeftExpanded = false;
var isRightExpanded = false;
var isExpanding = false;
var targetWidth;
var leftDiv;
var rightDiv;
var separatorDiv;
var leftExpandButton;
var rightExpandButton;

window.onload = function() {
	leftDiv = $('#leftDiv');
	rightDiv = $('#rightDiv');
	separatorDiv = $('#separatorDiv');
	leftExpandButton = separatorDiv.find('#expandLeftButton');
	rightExpandButton = separatorDiv.find('#expandRightButton');
}

//TODO: (in expandLeft & expandRight): change the targetWidths to be in pixels, taking in consideration the frame widths + margin.
expandLeft = function() {
	if (!isLeftExpanded && !isExpanding) {

		var stepFunction;
		var animationSpeed = 1000;
		if (isRightExpanded) {
			//targetWidth = 47.4;
			targetWidth = 48.5;
			isRightExpanded = false;
			/*
			stepFunction = function(now, fx) {
				rightDiv.css('width', (94.4 - now) + "%");
			}
			*/
		}
		else {
			targetWidth = 0;
			isLeftExpanded = true;
			/*
			stepFunction = function(now, fx) {
				rightDiv.css('width', (94.7 - now) + "%");	
			}
			*/
		}
		leftDiv.animate({'width': + targetWidth + "%"}, 
			{
				duration: animationSpeed,
				step: function(now, fx) {
					//rightDiv.css('width', (94.7 - now) + "%");
					rightDiv.css('width', (97.2 - now) + "%");
				},
				/*
				step: stepFunction,*/
				complete: function() {
					isExpanding = false;
					if (isLeftExpanded) {
						leftExpandButton.hide();
						rightExpandButton.css('height', 100 + "%");
					}
					else {
						leftExpandButton.css('height', 50 + "%");
						rightExpandButton.show();
					}
				}
			});
	}
}

expandRight = function() {
	if (!isRightExpanded && !isExpanding) {
		var animationSpeed = 1000;
		if (isLeftExpanded) {
			//targetWidth = 47.4;
			targetWidth = 48.5;
			isLeftExpanded = false;
		}
		else {
			targetWidth = 0;
			isRightExpanded = true;
		}
		rightDiv.animate({'width': + targetWidth + "%"}, 
			{
				duration: animationSpeed,
				step: function(now, fx) {
					//leftDiv.css('width', (94.7 - now) + "%");
					leftDiv.css('width', (97.2 - now) + "%");
				},
				complete: function() {
					isExpanding = false;
					if (isRightExpanded) {
						rightExpandButton.hide();
						leftExpandButton.css('height', 100 + "%");
					}
					else {
						rightExpandButton.css('height', 50 + "%");
						leftExpandButton.show();
					}
				}
			});	
	}
}