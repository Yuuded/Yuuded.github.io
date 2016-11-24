<?PHP
require_once("./include/membersite_config.php");
//Declaring variables, to avoid errors while generating the html
$serverName = '';
$serverType = '';
$capacity = '';
$ip = '';
$port = '';
$isOnline = '';
$isSchedule = '';
$startsAt = '';
$stopsAt = '';
$email = '';
$userName = '';
$user_id = '';
$server_id = '';

$err = '';

/*
if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("index.php");
    exit;
}
else {
	echo "<script>console.log('Is logged in!');</script>";
	$userName = $fgmembersite->UserName();
	$email = $fgmembersite->OurUserEmail();
}

if(isset($_POST['submit'])) {
	if($fgmembersite->RegistServer()) {
		$serverName = $fgmembersite->Sanitize($_POST['name']);
		$serverType = $fgmembersite->Sanitize($_POST['type']);
		$capacity = $fgmembersite->Sanitize($_POST['capacity']);
		$ip = $fgmembersite->Sanitize($_POST['ip']);
		$port = $fgmembersite->Sanitize($_POST['port']);
		$isOnline = $fgmembersite->Sanitize($_POST['isOnline']);
		if (isset($_POST['isSchedule']))
			$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		else
			$formvars['isSchedule'] = "off";
		if ($isSchedule == "on") {
			$startsAt = $fgmembersite->Sanitize($_POST['startsAt']);
			$stopsAt = $fgmembersite->Sanitize($_POST['stopsAt']);
		}
		echo "<script>console.log('Server regist sucessfull (PHP)');</script>";
	}
	else {
		echo "<script>console.log('Server creation failed :/');</script>";
		$err = $fgmembersite->error_message;
		$serverName = $fgmembersite->Sanitize($_POST['name']);
		$serverType = $fgmembersite->Sanitize($_POST['type']);
		$capacity = $fgmembersite->Sanitize($_POST['capacity']);
		$ip = $fgmembersite->Sanitize($_POST['ip']);
		$port = $fgmembersite->Sanitize($_POST['port']);
		$isOnline = $fgmembersite->Sanitize($_POST['isOnline']);
		if (isset($_POST['isSchedule']))
			$formvars['isSchedule'] = $this->Sanitize($_POST['isSchedule']);
		else
			$formvars['isSchedule'] = "off";
		if ($isSchedule == "on") {
			$startsAt = $fgmembersite->Sanitize($_POST['startsAt']);
			$stopsAt = $fgmembersite->Sanitize($_POST['stopsAt']);
		}
	}
}
*/
?>

<!DOCTYPE html>
<html>
<head>

	<title>Server Management</title>
    
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-1.12.3.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <script src = "js/registServer.js" type="text/javascript"></script>
    <script src = "js/gen_validatorv4.js" type = "text/javascript"></script>
    
    <link href="css/registServer.css" rel = "stylesheet" type = "text/css" media = "screen">
</head>

<body>
	<div id = "containerDiv">
    	<div id = "headerDiv">
        	<h1>Regist/Create Server</h1>
        </div>
        <br>
        <div id = "optionsDiv">
            <form id = "form" name = "form" action = "">
                <div id = "inputDiv">
                    <p><label for = "isScratch">Server from scratch: </label><input type= "checkbox" class = "checkbox" id = "isScratch" name = "isScratch"/></p>
                    <p><label for = "name">Name : </label><input type = "text" class = "text" id = "name" name = "name"/>
                    <span id = "nameErr" style = "visibility: visible"></span></p>
                    <p><label for = "type">Type : </label>
                    <select id = "type" name = "type">
                        <option value = "WebServer">WebServer</option>
                        <option value = "Database">Database</option>
                    </select></p>
                    <p><label for = "capacity">Capacity : </label><input type = "number" id = "capacity" name = "capacity" min = "0" step = "1"/></p><span id = "capacityErr" style = "visibility: visible"></span></p>
                    <p><label for = "isSchedule">Online time by schedule : </label><input type = "checkbox" class = "checkbox" id = "isSchedule" name = "isSchedule" onClick = "showHideSchedule()"/></p>
                    <p><div id="schedule" style = "display: none">
        		        <label id = "scheduleLabel">Schedule : </label><input type="time" id="stopAt" name="TimeEnd" value="23:59"/> <p id = "scheduleDot">:</p> <input type="time" id="startAt" name="TimeStart" value="08:00"/>
		            </div></p>
                    <p><label>Host (IP or URL) : </label><input type = "text" id = "ip" name = "ip"/><span id = "ipErr" style = "visibility: visible"></span></p>
                    <p><label>Port : </label><input type = "text" id = "port" name = "port"/><span id = "portErr" style = "visibility: visible"></span></p>
                	
                  	
                    <div id="modalAdd" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Add Admins</h4>
                                </div>
                                <div class="modal-body">
									<p class = "text-center">Select one or more users to be admins of your server: </p>
                                    <table class = "table table-hover" id = "newAdminsTable">
                                    	<thead>
                                        	<th>Username</th>
                                            <th width: "auto !important">Email</th>
                                            <th>Select</th>
                                        </thead>
                                        <tbody id = "remainingAdminsBody">
                                        
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal" onClick = "addAdmins()">Ok</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                    </div>
                </div>
                </div>
                <div id = "infoDiv">
                    <span title="Check this box if you pretend to create a completly new server.&#13Otherwise, if the server already exists and you just want to register it in this program, leave this box unchecked.">?</span>
                    <span title = "The name that you want to associate to the server in this program.">?</span>
                    <span title = "The type of the server.">?</span>
                    <span title = "The max number of users that may be using the server simultaneously.">?</span>
                    <span title = "Check this box if you want your server to only be online during a certain shedule.&#13Otherwise, if you pretend it to be always online or you want to control it's uptime manually, leave this box unchecked.&#13Note that, even if you check this box, you still need to turn it on the first time by yourself.">?</span>
                    <span title = "Define at what time the server should start up and at what time it should shut down." id = "scheduleSpan" style = "display: none">?</span>
                    <span title = "The IP/URL of the device that should host (or, in the case of an already existing server, the device that is hosting) the server.">?</span>
                    <span title = "The port to which your server should listen to (or, in the case of an already existing server, the port to which it is already listening to)">?</span>
                    
                </div>
                <div id = "adminLabelDiv"><label id="AdminsLabel">Administrators : </label><span title = "Here you can control which other users of this program can manage your server.">?</span></div>
                <div id = "adminsDiv">
                    	<table class = "table table-hover" id = "adminsTable">
                        	<thead>
                            	<th id = "nameHeader" align = "center">Username</th>
                                <th id = "emailHeader" align = "center">Email</th>
                                <th id = "removeHeader" align = "center">Remove</th>
                            </thead>
                            <tbody id = "adminBody">
                            
                            </tbody>
                        </table>
                    </div>
                <div id = "adminsButtons">
                        <button id="addB" type="button" data-toggle="modal" data-target="#modalAdd">Add admin<img src = "images/55088-person-user-symbol-outline_30.png"></img></button>
                        <button id = "remAdmin" type = "button" onClick = "removeAdmins()">Remove selected admins<img src = "images/user_delete_30.png"></img></button>
                </div>
                
                <br>
                <div id = "creatingDiv" style = "display: none">
                	Creating server, please wait...
                </div>
                <div id = "registingDiv" style = "display: none">
                	Registing server, please wait...
                </div><br>
                <!--Just to make this center the button... -->
                <div id = "createServerButtonDiv">
                	<!--
                    <button id = "create" onClick="createServer()">Regist server</button>
                    -->
                    <button type = "button" id = "create" onClick = "registServer()" value = "Regist server">Regist server <img src = "images/black-tick-icon-26_40.png"></img></button>
                    <!-- The type is needed in order to avoid the submit -->
                    <button type = "reset" id = "cancel" onClick="cancelServer()">Cancel <img src = "css/images/close_4_40.png"></img></button>
                </div>
                <br>
            </form>
        </div>
    </div>
     <div id = "jsData" data-serverName = "<?php echo $serverName ?>" data-serverType = "<?php echo $serverType ?>"
    data-capacity = "<?php echo $capacity ?>" data-ip = "<?php echo $ip ?>" data-err = "<?php echo $err ?>"
    data-port = "<?php echo $port ?>" data-isOnline = "<?php echo $isOnline ?>" data-isSchedule = "<?php echo $isSchedule ?>" data-startsAt = "<?php echo $startsAt ?>" data-stopsAt = "<?php echo $stopsAt ?>" data-email = "<?php echo $email ?>"
    data-username = "<?php echo $userName ?>">
    </div>
</body>
</html>