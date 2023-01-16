<?php
require_once('admincore.php');

$data = array(
  "status" => 401,
  "error_message" => "No requests"
);

$a = (isset($_POST['a'])) ? $_POST['a'] : ((isset($_GET['a'])) ? $_GET['a'] : "");
$b = (isset($_POST['b'])) ? $_POST['b'] : ((isset($_GET['b'])) ? $_GET['b'] : "");

if (file_exists('requests/' . $a . '.php'))
{
	include_once('requests/' . $a . '.php');
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();
?>