<?PHP
require_once("./include/membersite_config.php");

$success = $fgmembersite->ModifyServerClients($_POST['id_server'], $_POST['nUsers'], $_POST['lastActiveUser']);
echo json_encode($success, JSON_PRETTY_PRINT);

?>