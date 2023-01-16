<?php
/**
 * @package Social Kit - Social Networking Platform v2.5.0.2
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2016 Rehan Adil. All rights reserved.
 */

require_once('assets/includes/core.php');
if (isset($_POST['timezone'])) date_default_timezone_set($_POST['timezone']);

$data = array(
    'status' => 417
);

if (isset($_GET['v']))
{
	$v = $_GET['v'];
	$versionDir = 'api/v' . $v;
	
	if (file_exists($versionDir)) include_once('api/v' . $v . '/init.php');
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($data);
$conn->close();
exit();