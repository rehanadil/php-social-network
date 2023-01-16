<?php

namespace SocialKit;

class UI {
	private $filename;

	public static function view($file, $addon=null)
	{
		global $config;

		$page = 'themes/' . $config['theme'] . '/layout/' . $file . '.phtml';
		$contentOpen = fopen($page, 'r');
		$content = @fread($contentOpen, filesize($page));
		fclose($contentOpen);

		if (is_array($addon))
		{
			if (!isset($addon['content'])) $addon['content'] = array();
			$addon['content']['data'] = $content;
			$content = \SocialKit\Addons::invoke(array(
				'key' => $addon['key'],
				'return' => $addon['return'],
				'type' => $addon['type'],
				'content' => $addon['content']
			));
		}

		$content = preg_replace_callback(
	        '/@([a-zA-Z0-9_]+)@/',

	        function ($matches)
	        {
	        	global $lang;
	        	$matches[1] = strtolower($matches[1]);
	        	return (isset($lang[$matches[1]]) ? $lang[$matches[1]] : "");
	        },

	        $content
	    );

	    $content = preg_replace_callback(
	        '/{{([A-Z0-9_]+)}}/',

	        function ($matches)
	        {
	        	global $themeData;
	        	$matches[1] = strtolower($matches[1]);
	        	return (isset($themeData[$matches[1]]) ? $themeData[$matches[1]] : "");
	        },

	        $content
	    );

	    $content = preg_replace_callback(
	        '/\[([0-9]+)::(.*?)\]/',

	        function ($matches)
	        {
	        	$t = $matches[1];
	        	$f = $matches[2];
	        	return date($f, $t);
	        },

	        $content
	    );
		
		return $content;
	}
}