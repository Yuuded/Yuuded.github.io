// JavaScript Document

startRegistValidations = function() {
	var frmvalidator = new Validator("loginForm");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("email","req","Please provide your email address.");
    frmvalidator.addValidation("email","email","Please provide a valid email address.");
    
    frmvalidator.addValidation("password","req","Please provide your password.");
}

startRegistValidations();