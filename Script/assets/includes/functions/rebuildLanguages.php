<?php
function rebuildLanguage($l, $cp=false)
{
	global $conn;
	if (empty($l)) return false;
	propagateLanguages();

	$escapeObj = new \SocialKit\Escape();
	$l = $escapeObj->stringEscape($l);
	$query = $conn->query("SELECT * FROM " . DB_LANGUAGES . " WHERE type='$l'");
	if ($query->num_rows == 0) return false;

	$fileData = '<?php
';
	while ($fetch = $query->fetch_array(MYSQLI_ASSOC))
	{
		$fetch['text'] = str_replace('&amp;', '&', $fetch['text']);
		$fileData .= '$lang[\'' . $fetch['keyword'] . '\'] = \'' . $fetch['text'] . '\';
';
		$conn->query("UPDATE " . DB_LANGUAGES . " SET text='" . $fetch['text'] . "' WHERE type='$l' AND keyword='" . $fetch['keyword'] . "'");
	}

	$dir = ($cp) ? '../cache/languages' : 'cache/languages';
	$file = $l . '.php';

	if (!file_exists($dir))
		mkdir($dir, 0777, true);
	else
		chmod($dir, 0777);

	file_put_contents($dir . '/' . $file, $fileData);
	chmod($dir, 0755);
	return true;
}