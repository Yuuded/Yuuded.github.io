<?PHP
require_once("./include/membersite_config.php");

$logout = "";

if(isset($_POST['logout'])) {
	$fgmembersite->LogOut();
	$logout = "true";
}

?>

<!DOCTYPE html>
<html>
<head>

	<title>Server Management</title>

	<link rel="shortcut icon" href="images/favicon.ico">

	<link href="css/styles.css" rel = "stylesheet" type = "text/css" media = "screen">
	<link href="css/topbar.css" rel = "stylesheet" type = "text/css" media = "screen">
    
    <script src="js/jquery-1.12.3.min.js" type="text/javascript"></script>
    <script src = "js/autoHeight.js" type="text/javascript"></script>
    <script src = "js/logout.js" type="text/javascript"></script>
    
</head>

<body style = "overflow: hidden">
	<div id = "wrapper">
        <div id = "topbar">
        
            <div id = "logo">
                   <!-- <p>SM</p> -->
            </div>
            
            <div id = "accountNameDiv">
                    <p id = "username">Welcome, Someone</p>
            </div>
            
            <div id = "account">
                
                <ul id = "accountOptions">
                    <!--<li><button type="button" id = "accountSettings">Account settings</button></li>-->
                    <li>
	                    <form id = "logoutForm" action = "<?php echo $fgmembersite->GetSelfScript(); ?>" method = "post" accept-charset = "UTF-8">
                        <input type='hidden' name='logout' id='logout' value='1'/>
    	                <input type="submit" id = "logoutButton" value = "Log out">
                        </form>
                    </li>
                    <!--
                    <li>
                    	<button type="button" id = "accountLogout" onClick = "logout()">Log out</button>
                    </li>
                    -->
                </ul>
                
            </div>    
    
        </div>
        
        <div id = "innerPage">
        
            <iframe src="doublePageContainer.html"></iframe>
        
        </div>
	
    </div>
    
    <div id = "jsData" data-logout = "<?php echo $logout ?>"></div>
    
</body>

</html>
