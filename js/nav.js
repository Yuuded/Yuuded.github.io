// JavaScript Document

$(document).ready(function() {
	$("#navLinks li").hover(
		function() {
			$('ul', this).fadeIn();
		},
		
		function() {
			$('ul', this).fadeOut();	
		}
	);
});