<?PHP
require_once("./include/membersite_config.php");

$success = $fgmembersite->ModifyServerOnline($_POST['id_server'], $_POST['isOnline'], $_POST['onlineSince'], $_POST['lastActiveUser']);
echo json_encode($success, JSON_PRETTY_PRINT);

?>