<?PHP
require_once("./include/membersite_config.php");

$success = $fgmembersite->GetAllUsers($_POST['email']);
echo json_encode($success, JSON_PRETTY_PRINT);

?>