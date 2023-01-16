<?php
/**
 * @package Social Kit - Social Networking Platform v2.5.0.2
 * @author Rehan Adil (ThemePhysics) http://codecanyon.net/user/ThemePhysics
 * @copyright 2017 Rehan Adil. All rights reserved.
 */

$version = '2.5.0.2';
$api_version = '1.0';
$themeData = array();

ini_set('gd.jpeg_ignore_warning', 1);
error_reporting(0);
set_time_limit(0);
date_default_timezone_set('Asia/Dhaka');
session_start();

// Includes
require('assets/includes/config.php');
require('assets/includes/definitions.php');

/* API Version */
$themeData['api_version'] = $api_version;

// Connect to SQL Server
$conn = new mysqli($sql_host, $sql_user, $sql_pass, $sql_name);
$conn->set_charset("utf8");

// Check connection
if ($conn->connect_errno)
{
    exit($conn->connect_errno);
}

// Include all core functions
$functions = glob('assets/includes/functions/*.php');
foreach ($functions as $func)
{
    require_once($func);
}

require_once('classes/autoload.php');
require_once('addons/autoload.php');
require_once('assets/includes/connect.php');
require_once('library/init.php');

/* Additional Files*/
require_once('cache/skemoji/skemoji.php');
require_once('cache/banned_ips.php');

if (in_array(getRealIp(), $bannedIps)) die('');

if (!isset($_SESSION['reset_time'])) $_SESSION['reset_time'] = time();
if ($config['reset_time'] > $_SESSION['reset_time'])
{
    session_destroy();
    header("Location: " . $config['site_url']);
}

$escapeObj = new \SocialKit\Escape();