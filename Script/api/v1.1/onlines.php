<?php
$data['status'] = 200;
$onlines = array();

foreach (getOnlines() as $k => $online)
{
	$onlineData = array(
    	"id" => $online['id'],
		"username" => $online['username'],
		"name" => $online['name'],
		"image" => $online['avatar_url'],
		"cover" => $online['cover_url'],
		"birthday" => $online['birthday'],
		"gender" => $online['gender'],
		"location" => $online['location'],
		"hometown" => $online['hometown'],
		"about" => $online['about'],
		"time" => $online['last_logged'],
		"color" => $online['color']
    );

    $onlines[] = $onlineData;
}

$data['onlines'] = $onlines;