// JavaScript Document

window.onload = function() {
	var formValidator = new Validator("Form1");
  	formValidator.EnableOnPageErrorDisplaySingleBox();
  	formValidator.EnableMsgsTogether();
	formValidator.addValidation("email", "req", "Please enter an email.");
	formValidator.addValidation("email", "email", "Please enter a valid email.");
	formValidator.addValidation("userName", "req", "Please enter an userName.");
	formValidator.addValidation("userName", "maxlen=40", "Please choose a smaller username (40 characters long at most).");
	formValidator.addValidation("userName", "minlen=3", "Your username should be at least 3 characters long.");
	formValidator.addValidation("pass", "req", "Please enter a password.");
	formValidator.addValidation("pass", "maxlen=40", "Please choose a smaller password (40 characters long at most).");
	formValidator.addValidation("pass", "minlen=5", "Your password should be at least 5 characters long.");
	formValidator.addValidation("repass", "req", "Please confirm your password.");
	formValidator.addValidation("repass", "eqelmnt=pass", "Your password and the password confirmation don't match.");
}