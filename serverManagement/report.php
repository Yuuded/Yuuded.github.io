<!DOCTYPE html>
<?php
 $ip = "192.168.123.123";
 $port = "43243";
 ?>
<html>
<head>
	<title>Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="css/report.css" rel = "stylesheet" type = "text/css" media = "screen">
    <script src="js/report.js"></script>
    <!--<meta http-equiv="refresh" content="2" >-->
</head>
<body>
    <div id ="containerDiv">
    <div id = "headerDiv">
      	<h1>Server Report</h1>
    </div><br>
    <div id = "loadingDiv">
    	<p>Loading server information...</p>
    </div>
    <form id= "Form1">
        <label> Name :</label> <label id="name" class="info"></label><br>
        <label> Type :</label> <label id="serverType" class="info"></label><br>
        <label> Host :</label> <label id="host" class="info"></label><br>
        <label> Port :</label> <label id="port" class="info"></label><br>
        <label> Capacity : </label> <label id="curUser" class="info"></label><label id="totalUser" class="info"></label> <br>
        <label> Time online : </label> <label id="Ontime" class="info"></label><br>
        <div id = "lastUserDiv" display = "none">
        	<label> Time since last user : </label> <label id="userTime" class="info"></label><br>
        </div>
            <label> Online time by schedule :</label> <label id="isOpTime" class="info"></label><br>
        <div id="schedule" display = "none">
            <label> Schedule :</label> <label id="startAt" class="info"></label>-><label id ="stopAt" class="info"></label><br>
        </div>
        <label id="AdminsLabel">Administrators : </label>
                    <div id = "adminsDiv">
                    	<p>
                    	<table class = "table table-hover" id = "adminsTable">
                        	<thead>
                            	<th id = "nameHeader" align = "center">Username</th>
                                <th id = "emailHeader" align = "center">Email</th>
                            </thead>
                            <tbody id = "adminBody">
                            
                            </tbody>
                        </table>
                        </p>
                    </div>
        <!--
        <textarea id= "adminL" rows="4" cols="50" readonly></textarea><br>
        -->
        <div><button id="settings" type="button" onclick="doThis()">Settings <img src = "images/icon_small_settings1_40.png"></img></button>
        <button id="ok" type="button" onclick="doThat()">Close <img src = "css/images/close_4_40.png"></img></button></div>
    </form>
    <div id = "jsData" data-ip = "<?php echo $ip ?>" data-port = "<?php echo $port ?>"></div>
    </div>
</body>

</html>