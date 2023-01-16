<?php
function propagateLanguages()
{
	global $conn;
	$langMaxCount = 0;
	$langPrepareQuery = $conn->query("SELECT COUNT(id) AS cid,type FROM " . DB_LANGUAGES. " GROUP BY type");
	$langPrepareTypes = array();
	while ($langPrepareData = $langPrepareQuery->fetch_array(MYSQLI_ASSOC))
	{
		if ($langPrepareData['cid'] > $langMaxCount)
		{
			$langMaxCount = $langPrepareData['cid'];
			$prepareLangType = $langPrepareData['type'];
		}

		$langPrepareTypes[$langPrepareData['type']] = $langPrepareData['cid'];
	}

	foreach($langPrepareTypes as $langType => $langCount)
	{
		if ($langCount < $langMaxCount)
		{
			$langNewKeysQuery = $conn->query("SELECT keyword,text FROM " . DB_LANGUAGES . " WHERE type='$prepareLangType' AND keyword NOT IN (SELECT keyword FROM " . DB_LANGUAGES . " WHERE type='$langType')");
			while ($langNewKeys = $langNewKeysQuery->fetch_array(MYSQLI_ASSOC))
			{
				$newKeyInsertionQuery = $conn->query("INSERT INTO " . DB_LANGUAGES . " (type,keyword,text) VALUES ('$langType','" . $langNewKeys['keyword'] . "','" . $langNewKeys['text'] . "')");
			}
		}
	}
}