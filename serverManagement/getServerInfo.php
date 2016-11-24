<?PHP
require_once("./include/membersite_config.php");

if(isset($_POST['ip']) && isset($_POST['port'])) {
	$result = $fgmembersite->GetServerInfo($_POST['ip'], $_POST['port']);
	/*
	echo $_POST['ip'];
	echo $_POST['port'];
	*/
	echo json_encode($result, JSON_PRETTY_PRINT);
}
else if (isset($_POST['id_server'])) {
	$result = $fgmembersite->GetServerInfoById($_POST['id_server']);
	echo json_encode($result, JSON_PRETTY_PRINT);	
}

else {
	echo "No IP or Port provided";
}
//echo "Yo bro";
?>