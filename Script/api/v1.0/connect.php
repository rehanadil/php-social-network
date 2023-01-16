<?php
/**
 * @package Social Kit - Social Networking Platform v2.5.0.2
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2017 Rehan Adil. All rights reserved.
**/

function strescape($content, $nullvalue=false)
{
	global $conn;
	
	if (empty($content) && $nullvalue != false)
	{
		$content = $nullvalue;
	}

	$content = trim($content);
	$content = $conn->real_escape_string($content);
	$content = htmlspecialchars($content, ENT_QUOTES);
	$content = stripslashes($content);
	
	return $content;
}

require_once('../../assets/includes/config.php');
require_once('../../assets/includes/tables.php');

// Connect to SQL Server
$conn = new mysqli($sql_host, $sql_user, $sql_pass, $sql_name);
$conn->set_charset("utf8");

// Check connection
if ($conn->connect_errno)
{
    exit($conn->connect_errno);
}

$config = array();
$confQuery = $conn->query("SELECT * FROM " . DB_CONFIGURATIONS);
$config = $confQuery->fetch_array(MYSQLI_ASSOC);

$config['site_url'] = $site_url;
$config['theme_url'] = $site_url . '/themes/' . $config['theme'];
$config['script_path'] = str_replace('index.php', '', $_SERVER['PHP_SELF']);
$config['request_source'] = $config['script_path'] . 'request.php';
$config['page_path'] = $config['script_path'] . 'page.php';
