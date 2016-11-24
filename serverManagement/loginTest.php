<?PHP
require_once("./include/membersite_config.php");

//Declaring variables, to avoid errors while generating the html
$username = '';
$password = '';
$email = '';
$usernameRegist = '';
$passwordRegist = '';
$repasswordRegist = '';
$emailRegist = '';
$action = '';
$err = '';
$usernameLogin = '';
$passwordLogin = '';
$emailLogin = '';

//Register
if(isset($_POST['userName'])) {
	$action = "Regist";
	if($fgmembersite->OurRegisterUser()) {
		$usernameRegist = $fgmembersite->Sanitize($_POST['userName']);
		$passwordRegist = $fgmembersite->Sanitize($_POST['password']);
		$emailRegist = $fgmembersite->Sanitize($_POST['email']);
		$repasswordRegist = $fgmembersite->Sanitize($_POST['repassword']);
		$username = $usernameRegist;
		$password = $passwordRegist;
		$email = $emailRegist;
   	}
	else {
		echo "<script>console.log('Regist failed :/');</script>";
		$err = $fgmembersite->error_message;
		$usernameRegist = $fgmembersite->Sanitize($_POST['userName']);
		$passwordRegist = $fgmembersite->Sanitize($_POST['password']);
		$emailRegist = $fgmembersite->Sanitize($_POST['email']);
		$repasswordRegist = $fgmembersite->Sanitize($_POST['repassword']);
		$username = $usernameRegist;
		$password = $passwordRegist;
		$email = $emailRegist;
	}
}
//Login
else if (isset($_POST['email'])) {
	$action = "Login";
	if ($fgmembersite->OurLogin()) {
		$passwordLogin = $fgmembersite->Sanitize($_POST['password']);
		$emailLogin = $fgmembersite->Sanitize($_POST['email']);
		$usernameLogin = $fgmembersite->UserName();
		$username = $usernameLogin;
		$email = $emailLogin;
		$password = $passwordLogin;
	}
	else {
		echo "<script>console.log('Login failed :/');</script>";
		$err = $fgmembersite->error_message;
		$passwordLogin = $fgmembersite->Sanitize($_POST['password']);
		$emailLogin = $fgmembersite->Sanitize($_POST['email']);
		$usernameLogin = $fgmembersite->UserName();
		$username = $usernameLogin;
		$email = $emailLogin;
		$password = $passwordLogin;
	}
}
?>

<!DOCTYPE html>
<html>
<head>

	<title>Server Management</title>

    <link href="css/login.css" rel = "stylesheet" type = "text/css" media = "screen">
	<script src='js/jquery-1.12.3.min.js' type='text/javascript'></script>
	<script src = 'js/registUser.js' type = 'text/javascript'></script>
    <script src = "js/gen_validatorv4.js" type = "text/javascript"></script>

</head>

<body>
    <form id = "loginForm" action = "<?php echo $fgmembersite->GetSelfScript(); ?>" method = "post" accept-charset = "UTF-8">
		<p>Login with your account:</p>
    	<p>
            <label for = "email">Email:</label>
            <input type="text" id = "email" name="email" placeholder = "someone@example.com" value = '<?php echo $emailLogin ?>'>
    		<span id='loginForm_email_errorloc' class='error'></span>
        </p>
        <p>
            <label for = "password">Password:</label>
            <input type = "password" id = "password" name = "password" value = '<?php echo $passwordLogin ?>'>
    		<span id='loginForm_password_errorloc' class='error'></span>
			
        </p>
		<p>
			<label for = "">New Acount:</label>
			<input  type="button" id="RegisB" name="RegisB" value="Register" onClick="ShowRegis()">
		</p>
        <input id="loginButton" type="submit" value="Login">
	</form>
    <!--<p>Or register a new account:</p>-->
    <form id= "registerForm" action = "<?php echo $fgmembersite->GetSelfScript(); ?>" method = 'post' accept-charset = 'UTF-8' hidden="true">
         <p>
         	<label for = "email">Email:</label>
            <input type="text"     id="email2"   name="email"    placeholder = "someone@example.com" value = '<?php echo $emailRegist ?>'>
            <!--This span is for diplaying erros. It's id is: formId + _ + labelId + _ + errorloc-->
    		<span id='registerForm_email_errorloc' class='error'></span>
         </p>
         <p>
         	<label for = "userName">Username:</label>
         	<input type="text"     id="userName2" name="userName"  maxlength = "30"   placeholder = "someone" value = '<?php echo $usernameRegist ?>'>
    		<span id='registerForm_userName_errorloc' class='error'></span>
         </p>
         <p>
            <label for = "password">Password:</label>
        	<input type="password" id="password2" name="password" value = '<?php echo $passwordRegist ?>'>
    		<span id='registerForm_password_errorloc' class='error'></span>
         </p>
         <p>
            <label for = "repassword">Retype password:</label>
         	<input type="password" id="repassword2"  name="repassword" value = '<?php echo $repasswordRegist ?>'>
    		<span id='registerForm_repassword_errorloc' class='error'></span>
         </p>
        <input type = "submit" value = "Register"/>
        <input type = "button" value = "Cancel" onClick = "cancelRegist()"/>
    </form>
    <!-- This must always be after the form code -->
    <script src = "js/loginValidator.js" type = "text/javascript"></script>
    <script src = "js/registValidator.js" type = "text/javascript"></script>
    <div id = "jsData" data-username = "<?php echo $username ?>" data-password = "<?php echo $password ?>"
    data-email = "<?php echo $email ?>" data-action = "<?php echo $action ?>" data-err = "<?php echo $err ?>"></div>
</body>
</html>

<?php
	$ip = "192.168.123.123";
	$port = "43243";
?>
