<?php
 $ip = "192.168.123.123";
 $port = "43243";
 $id_server = "1";
 ?>

<html>
<head>
	<title>Modify</title>
	<link href="css/styles.css" rel = "stylesheet" type = "text/css" media = "screen">
	<link href="css/topbar.css" rel = "stylesheet" type = "text/css" media = "screen">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="css/modify.css" rel = "stylesheet" type = "text/css" media = "screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="js/modify.js"></script>
</head>
<body>
    <div id="containerDiv">
        <div id = "headerDiv">
      	    <h1>Modify Server</h1>
        </div><br>
        <div id = "loadingDiv">
    	    <p>Loading server information...</p>
        </div>
        <div id = "Div1">
            <form id= "Form1">
                <label>Name :</label><section class = "right"><input type="text" id="name" name="email" value=""><span title = "The name that you want to associate to the server in this program.">?</span></section><br>
                <span id = "nameErr" style = "visibility: visible"></span><br>
                <label>Type :</label><!--<input type="text" id="serverType" name ="ServerType" value="" readonly>--><section class = "right"><a id = "serverType"></a>
                <span title = "The type of the server. This is not modificable. If you really want to modify it, you need to delete the actual server first.">?</span></section><br><br>
                <label>Capacity :</label> <a id="curUser" ></a> / <input type="text" id="totalUser" name="capacity" value=""><section class = "right">
                <span title = "On the left, there's the current number of users using the server. &#13At the right, it's the max number of users (capacity) that may be using the server simultaneously.">?</span></section><br>
                <span id = "totalUserErr" style = "visibility: visible"></span><br>
                <label id = "onlineLabel">Server is currently offline. Do you want to turn it on? </label><input type = "checkbox" id = "isOnline"><br><br>
                <label>Online time by schedule :</label><section class = "right"><input type="checkbox" id = "isOpTime" onChange="show_hideSchedule()">
                <span title = "Check this box if you want your server to only be online during a certain shedule.&#13Otherwise, if you pretend it to be always online or you want to control it's uptime manually, leave this box unchecked.&#13Note that, even if you check this box, if the server is offline you'll have to turn it on by yourself the first time.">?</span></section><br><br>
                <div id="schedule">
                    <label> Schedule :</label><section class = "right" id = "scheduleSec"><input type="time" id="startAt" name="TimeStart" value="08:00"> : <input type="time" id="stopAt" name="TimeEnd" value="23:59"><span title = "Define at what time the server should start up and at what time it should shut down." id = "scheduleSpan" style = "display: none">?</span></section><br><br>
                </div>
                <label> Host :</label> <input id = "host"  type="text" name="Ip/Localiztion"><span title = "The IP/URL of the device that should host the server.">?</span><br>
                <span id = "hostErr" style = "visibility: visible"></span><br>
                <label> Port :</label> <input id = "port" type="number" name="Port"><span title = "The port to which your server should listen to.">?</span><br>
                <span id = "portErr" style = "visibility: visible"></span><br>
                <label id="AdminsLabel"> Administrators :</label><span title = "Here you can control which other users of this program can manage your server.">?</span><br>
            <!--
            <textarea id= "adminL" rows="4" cols="50" readonly></textarea><br>
            <button id="addB" type="button" data-toggle="modal" data-target="#modalAdd">Add</button> <button id="remB" type="button" data-toggle="modal" data-target="#modalRemove">Remove</button><br>
            -->
                <div id = "adminsDiv">
                    <p>
                    <table class = "table table-hover" id = "adminsTable">
                        <thead>
                            <th id = "nameHeader" align = "center">Username</th>
                            <th id = "emailHeader" align = "center">Email</th>
                            <th id = "removeHeader" align = "center">Remove</th>
                        </thead>
                        <tbody id = "adminBody">
                    
                        </tbody>
                    </table>
                    </p>
                </div>
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
                <button id="addB" type="button" data-toggle="modal" data-target="#modalAdd">Add admin<img src = "images/55088-person-user-symbol-outline_30.png"></img></button>
                <button id = "remAdmin" type = "button" onClick = "removeAdmins()">Remove selected admins<img src = "images/user_delete_30.png"></img></button><br><br>
            <!--
            <div id="modalAdd" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content--><!--
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Add Admin</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Write the User Name of the new admin</p>
                                    <input id="newAdminEdit" type="text" name="teste" value="" >
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                    </div>
            </div>
            <div id="modalRemove" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content--><!--
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Remove Admin</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Write the User Name of the admin to Remove<br>(Must be in the list)</p>
                                    <input id="remAdminEdit" type="text" name="teste" value="" >
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                    </div>
            </div>
            -->
                <br><div id = "modifyServer" style = "display: none">Saving server configuration changes, please wait...<br></div><br>
                <div id = "buttonDiv2"><button id="saveB" type="button" onClick="saveToLocal()">Save <img src = "images/Programming-Save-icon_40.png"></img></button> <button id = "deleteServer" type = "button" onClick = "delServer()">Delete Server <img src = "images/trash_40.png"></img></button> <button id="cancelB" type="button" onClick = "cancel()">Cancel <img src = "css/images/close_4_40.png"></img></button></div>
                <br>
                <input type="hidden" id="id_server" name= "id_server" value="<?php echo $id_server ?>"> 
            </form>
        </div>
    </div>
</body>

</html>