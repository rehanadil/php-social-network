<?php

namespace SocialTrait;

trait Escape {
	
	public function postEscape($content, $nullvalue=false)
	{
		if (empty($content) && $nullvalue != false)
		{
			$content = $nullvalue;
		}

		$content = trim($content);
		$content = preg_replace('/<div>(.*?)<\/div>/i', '<br>$1', $content);
		$content = strip_tags($content, "<em><br>");
		$nobreakContent = str_replace('<br>', '', $content);
	    
		if (empty($nobreakContent)) return '';

		$content = $this->getConnection()->real_escape_string($content);
	    $content = htmlspecialchars($content, ENT_QUOTES);
	    $content = str_replace('\\r\\n', '<br>',$content);
	    $content = str_replace('\\r', '<br>',$content);
	    $content = str_replace('\\n\\n', '<br>',$content);
	    $content = str_replace('\\n', '<br>',$content);
	    $content = str_replace('\\n', '<br>',$content);
	    $content = str_replace('&amp;', '&',$content);
	    $content = stripslashes($content);

	    $searches = array();
	    $replaces = array();

	    $searches[0] = '/\&lt\;em class\=\&quot\;skemoji skemoji\-([A_Za-z0-9-]+)\&quot\; contenteditable\=\&quot\;false\&quot\;\&gt\;\&lt\;\/em\&gt\;/i';
	    $searches[1] = '/\&lt\;br(.*?)\&gt\;/i';
	    $searches[2] = '/\&(|amp\;)nbsp\;/i';
	    $searches[3] = '/\&lt\;em(.*?)\&gt\;(.*?)\&lt\;\/em\&gt\;/i';
	    
	    $replaces[0] = '<i class="skemoji skemoji-$1"></i>';
	    $replaces[1] = '<br>';
	    $replaces[2] = ' ';
	    $replaces[3] = '$2';
	    
	    $content = preg_replace($searches, $replaces, $content);
	    $content = strip_tags($content, "<i><br>");

	    if (preg_match('/(.*?)\<br\>$/i', $content, $matches)) $content = $matches[1];

	    global $config;
	    $censoredKeywords = explode(',', $config['censored_words']);
	    foreach ($censoredKeywords as $censoredKey)
	    {
	    	$trimmedKey = trim($censoredKey);
	    	$content = str_replace($trimmedKey, '', $content);
	    }

	    $useEmoticons = false;
	    if (isLogged())
	    {
	    	global $user;
	    	if ($user['subscription_plan']['use_emoticons'] == 1)
	    		$useEmoticons = true;
	    }

	    if (!$useEmoticons)
	    	$content = strip_tags($content, "<br>");
	    
	    return $content;
	}

	public function stringEscape($content, $nullvalue=false)
	{
		if (empty($content) && $nullvalue != false) {
			$content = $nullvalue;
		}

	    $content = trim($content);
	    $content = $this->getConnection()->real_escape_string($content);
	    $content = htmlspecialchars($content, ENT_QUOTES);
	    $content = stripslashes($content);
	    $content = str_replace('&amp;', '&',$content);

	    global $config;
	    $censoredKeywords = explode(',', $config['censored_words']);
	    foreach ($censoredKeywords as $censoredKey)
	    {
	    	$trimmedKey = trim($censoredKey);
	    	$content = str_replace($trimmedKey, '', $content);
	    }
	    
	    return $content;
	}

	public function reversePostEscape($content, $nullvalue=false) {
		if (empty($content) && $nullvalue != false) {
			$content = $nullvalue;
		}

	    $content = trim($content);
	    $content = str_replace('<br>',
'
',
$content);
	    $content = stripslashes($content);
	    
	    return $content;
	}

	public function reverseStringEscape($content, $nullvalue=false) {
		if (empty($content) && $nullvalue != false) {
			$content = $nullvalue;
		}

	    $content = trim($content);
	    $content = stripslashes($content);
	    
	    return $content;
	}

	public function get($g, $r='')
	{
		if (!isset($_GET[$g])) return $r;
	    return $_GET[$g];
	}

	public function post($p, $r='')
	{
		if (!isset($_POST[$p])) return $r;
	    return $_POST[$p];
	}
}
