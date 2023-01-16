<?php
/**
 * @package Social Kit - Social Networking Platform v2.5.0.2
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2016 Rehan Adil. All rights reserved.
 */

$a = (isset($_GET['a'])) ? $_GET['a'] : "";
$b = (isset($_GET['b'])) ? $_GET['b'] : "";
$continue = false;

// App Verification
if (!isset($_POST['app_id'], $_POST['app_secret']))
{
	echo json_encode(array(
		"status" => 417,
		"error_type" => "APP_MISSING",
		"error_message" => "Application ID and/or Secret is missing."
	));
	die();
}

$appId = (int) $_POST['app_id'];
$appSecret = $escapeObj->stringEscape($_POST['app_secret']);
$appVerifyQuery = $conn->query("SELECT id FROM " . DB_DEV_APPS . " WHERE id=$appId AND secret='$appSecret' AND active=1");

if ($appVerifyQuery->num_rows != 1)
{
	echo json_encode(array(
		"status" => 417,
		"error_type" => "APP_BAD_CREDENTIALS",
		"error_message" => "Application ID and/or Secret is incorrect."
	));
	die();
}

if (isset($_POST['userid'])
	&& isset($_POST['userpassword']))
{
	$userId = (int) $_POST['userid'];
	$userPassword = trim($_POST['userpassword']);
	
	$userQuery = $conn->query("SELECT id FROM " . DB_ACCOUNTS . " WHERE id=$userId AND password='$userPassword' AND type='user' AND banned=0 AND banned=0");
	if ($userQuery->num_rows === 1)
	{
		$continue = true;

		$_SESSION['user_id'] = $userId;
	    $_SESSION['user_pass'] = $userPassword;

		$userObj = new \SocialKit\User();
		$userObj->setId($userId);
		$user = $userObj->getRows();
		$conn->query("UPDATE " . DB_ACCOUNTS . "
			SET last_logged=" . time() . "
			WHERE id=" . $user['id']);
	}
}

if (file_exists('api/v' . $v . '/' . $a . '.php'))
{
	include_once('api/v' . $v . '/' . $a . '.php');
}