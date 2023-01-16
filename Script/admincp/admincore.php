<?php
error_reporting(E_ALL);
set_time_limit(0);
date_default_timezone_set('Asia/Dhaka');
session_start();

// Includes
require('../assets/includes/config.php');
require('../assets/includes/tables.php');

// Connect to SQL Server
$conn = new mysqli($sql_host, $sql_user, $sql_pass, $sql_name);
$conn->set_charset("utf8");

// Check connection
if ($conn->connect_errno)
{
    exit($conn->connect_errno);
}

$functions = glob('../assets/includes/functions/*.php');
foreach ($functions as $func)
{
    require_once($func);
}

$config = array();
$confQuery = $conn->query("SELECT * FROM " . DB_CONFIGURATIONS);
$config = $confQuery->fetch_array(MYSQLI_ASSOC);

$config['site_url'] = $site_url;
$config['theme_url'] = $site_url . '/themes/' . $config['theme'];

require('../library/init.php');

if (!isLogged()) header("Location: $site_url");

foreach ($config as $cnm => $cfg)
{
    if (is_array($cfg))
    {
        foreach ($cfg as $cfgi => $cfgv)
        {
            define(strtoupper($cfgi), $cfgv);
        }
    }
    else
    {
        define(strtoupper($cnm), $cfg);
    }
}

$lang = array();
if (file_exists('../cache/languages/' . $_SESSION['language'] . '.php')) require_once('../cache/languages/' . $_SESSION['language'] . '.php');

/* Admin */
require_once('../classes/admin_autoload.php');

$userObj = new \SocialKit\User();
$userObj->setId($_SESSION['user_id']);
$user = $userObj->getRows();

if ($user['admin'] != 1) header("Location: " . $config['site_url']);

$escapeObj = new \SocialKit\Escape();

$timezone = (empty($user['timezone'])) ? "Asia/Dhaka" : $user['timezone'];
date_default_timezone_set($timezone);

