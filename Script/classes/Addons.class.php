<?php

namespace SocialKit;

class Addons
{
	public static function get_addons($type)
	{
		if (!isset($_SESSION['addons'][$type])) return false;

		$get = $_SESSION['addons'][$type];

		if (!is_array($get)) return false;

		return $get;
	}

	public static function invoke($Array)
	{
		if (!is_array($Array)) return false;

		$key = $Array['key'];
		$return = $Array['return'];
		$type = $Array['type'];
		$content = $Array['content'];

		if (!is_array($content)) return false;
		$data = $content['data'];

		$collectiveAddons = self::get_addons($key);
		if (!is_array($collectiveAddons))
		{
			switch ($type)
			{
				case 'NO_APPEND':
					return false;
					break;
				
				default:
					return $data;
					break;
			}
		}

		$collectiveAddons = array_unique($collectiveAddons);
		foreach ($collectiveAddons as $addonName)
		{
			if (function_exists($addonName))
			{
				$newAddition = call_user_func($addonName, $content);

				if (!empty($newAddition))
				{
					if ($return === "string")
					{
						if (is_array($data)
							|| $type === "NO_APPEND") $data = "";
						$data .= $newAddition;
					}
					elseif ($return === "array")
					{
						$data = array_merge($data, $newAddition);
					}
					else
					{
						$data = false;
					}
				}
			}
		}
		return $data;
	}

	public static function register($type, $func)
	{
		$name = $func;
		$func_invalid = (preg_match('/[A-Za-z0-9_]/i', $name)) ? false : true;

		if (isset($_SESSION['addons'][$type])
			&& in_array($name, $_SESSION['addons'][$type])) return false;
		
		if (!preg_match('/[A-Za-z0-9_]/i', $type) or $func_invalid) return false;

		$type = strtolower($type);
		$_SESSION['addons'][$type][] = $func;
	}

	public static function call()
	{
		$args = func_get_args();
		$type = $args[0];
		$func = $args[1];
		unset($args[0], $args[1]);

		return call_user_func_array($func, $args);
	}
}
