<?PHP
require_once("./include/membersite_config.php");

$success = $fgmembersite->DeleteServer($_POST['id_server'], $_POST['email']);
echo json_encode($success, JSON_PRETTY_PRINT);

?>