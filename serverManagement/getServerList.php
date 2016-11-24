<?PHP
require_once("./include/membersite_config.php");

$result = $fgmembersite->GetServerList();

echo json_encode($result, JSON_PRETTY_PRINT);
/*
if(isset($_POST['ip']) && isset($_POST['port'])) {
	$result = $fgmembersite->GetServerInfo($_POST['ip'], $_POST['port']);
	/*
	echo $_POST['ip'];
	echo $_POST['port'];
	*/
	/*
	echo json_encode($result, JSON_PRETTY_PRINT);
}
else {
	echo "Not logged in.";
}
*/
//echo "Yo bro";
?>
