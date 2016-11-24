// JavaScript Document

startRegistValidations = function() {
	var frmvalidator = new Validator("registerForm");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("email","req","Please provide your email address.");
    frmvalidator.addValidation("email","email","Please provide a valid email address.");

    frmvalidator.addValidation("userName","req","Please provide an username.");
	frmvalidator.addValidation("userName","minlen=3", "Username must be at least 3 characters long.");
	frmvalidator.addValidation("userName","maxlen=30", "Username must be at most 30 characters long.");
    
    frmvalidator.addValidation("password","req","Please provide a password.");
    frmvalidator.addValidation("repassword","req","Please confirm your password.");
	frmvalidator.addValidation("repassword", "eqelmnt=password", "Passwords don't match.");
	frmvalidator.addValidation("password", "minlen=5", "Password must be at least 5 characters long.");
}

startRegistValidations();