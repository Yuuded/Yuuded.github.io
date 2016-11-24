<?PHP
require_once("./include/membersite_config.php");

$success = $fgmembersite->ModifyServerProperty($_POST['id_server']);
echo json_encode($success, JSON_PRETTY_PRINT);

?>