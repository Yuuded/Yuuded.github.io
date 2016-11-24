<?PHP
require_once("./include/membersite_config.php");

$success = $fgmembersite->RegistServer();
echo json_encode($success, JSON_PRETTY_PRINT);

?>