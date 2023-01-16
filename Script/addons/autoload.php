<?php
function addon_autoloader($conn)
{
	if (!isset($_SESSION['addons'])) $_SESSION['addons'] = array();

	$addons = glob("addons/add.*", GLOB_ONLYDIR);
	usort($addons, create_function('$a,$b', 'return filectime($a) - filectime($b);'));
	
	foreach ($addons as $addOn)
	{
		$init = $addOn . "/init.php";
		if (file_exists($addOn . "/enabled.html")) require $init;
	}

	return true;
}

addon_autoloader($conn);