<?php
if ($continue)
{
	$msgs = array();
	$newMessages = "";
	$updatedMessages = "";
	$totalMessages = "";

	$newInfo = array();
	$newInfo['limit'] = 0;

	if ($b === "old"
		&& isset($_POST['beforeid']))
	{
		$beforeId = (int) $_POST['beforeid'];
		$newInfo['before_id'] = $beforeId;
	}
	elseif ($b === "new"
		&& isset($_POST['afterid']))
	{
		$afterId = (int) $_POST['afterid'];
		$newInfo['after_id'] = $afterId;
		
		if (isset($_POST['updateids']))
		{
			$updateIdsString = $escapeObj->stringEscape($_POST['updateids']);
			$updateIds = json_decode($updateIdsString, true);

			if (is_array($updateIds))
			{
				$updatedMessages = getMessages(array(
					"ids" => $updateIds
				));
			}
		}
	}

	$newMessages = getMessages($newInfo);
	$totalMessages = $newMessages;

	if (is_array($updatedMessages))
	{
		if (is_array($newMessages))
			$totalMessages = array_merge($newMessages, $updatedMessages);
		else
			$totalMessages = $updatedMessages;
	}

	if (is_array($totalMessages))
	{
		foreach ($totalMessages as $key => $value)
		{
			if ($value['media_id'] > 0)
			{
				if ($value['media']['type'] == "photo")
				{
					$msgs[] = array(
						"id" => $value['id'],
						"type" => $value['media']['type'],
						"data" => $config['site_url'] . '/' . $value['media']['each'][0]['complete_url'],
						"data_name" => "",
						"time" => $value['time'],
						"seen" => $value['seen'],
						"sender" => array(
							"id" => $value['timeline']['id'],
							"username" => $value['timeline']['username'],
							"name" => $value['timeline']['name'],
							"image" => $value['timeline']['avatar_url'],
							"cover" => $value['timeline']['cover_url'],
							"birthday" => $value['timeline']['birthday'],
							"gender" => $value['timeline']['gender'],
							"location" => $value['timeline']['location'],
							"hometown" => $value['timeline']['hometown'],
							"time" => $value['time'],
							"color" => $value['timeline']['color'],
							"about" => $value['timeline']['about']
						),
						"friend" => array(
							"id" => $value['receiver']['id'],
							"username" => $value['receiver']['username'],
							"name" => $value['receiver']['name'],
							"image" => $value['receiver']['avatar_url'],
							"cover" => $value['receiver']['cover_url'],
							"birthday" => $value['receiver']['birthday'],
							"gender" => $value['receiver']['gender'],
							"location" => $value['receiver']['location'],
							"hometown" => $value['receiver']['hometown'],
							"time" => $value['time'],
							"color" => $value['receiver']['color'],
							"about" => $value['receiver']['about']
						)
					);
				}
				else
				{
					$msgs[] = array(
						"id" => $value['id'],
						"type" => $value['media']['type'],
						"data" => $config['site_url'] . '/' .$value['media']['complete_url'],
						"data_name" => $value['media']['name'],
						"time" => $value['time'],
						"seen" => $value['seen'],
						"sender" => array(
							"id" => $value['timeline']['id'],
							"username" => $value['timeline']['username'],
							"name" => $value['timeline']['name'],
							"image" => $value['timeline']['avatar_url'],
							"cover" => $value['timeline']['cover_url'],
							"birthday" => $value['timeline']['birthday'],
							"gender" => $value['timeline']['gender'],
							"location" => $value['timeline']['location'],
							"hometown" => $value['timeline']['hometown'],
							"time" => $value['time'],
							"color" => $value['timeline']['color'],
							"about" => $value['timeline']['about']
						),
						"friend" => array(
							"id" => $value['receiver']['id'],
							"username" => $value['receiver']['username'],
							"name" => $value['receiver']['name'],
							"image" => $value['receiver']['avatar_url'],
							"cover" => $value['receiver']['cover_url'],
							"birthday" => $value['receiver']['birthday'],
							"gender" => $value['receiver']['gender'],
							"location" => $value['receiver']['location'],
							"hometown" => $value['receiver']['hometown'],
							"time" => $value['time'],
							"color" => $value['receiver']['color'],
							"about" => $value['receiver']['about']
						)
					);
				}
				
			}
			else
			{
				$text = $value['text'];

				if (preg_match_all('/\<i class\=\"skemoji skemoji\-(.*?)\"\>\<\/i\>/i', $value['text'], $matches))
				{
					foreach ($matches[0] as $mKey => $mValue)
					{
						$text = str_replace($matches[0][$mKey], ':skemoji_' . str_replace('-', '_', $matches[1][$mKey]) . ':', $text);
					}
				}

				$msgs[] = array(
					"id" => $value['id'],
					"type" => "text",
					"data" => $text,
					"data_name" => "",
					"time" => $value['time'],
					"seen" => $value['seen'],
					"sender" => array(
						"id" => $value['timeline']['id'],
						"username" => $value['timeline']['username'],
						"name" => $value['timeline']['name'],
						"image" => $value['timeline']['avatar_url'],
						"cover" => $value['timeline']['cover_url'],
						"birthday" => $value['timeline']['birthday'],
						"gender" => $value['timeline']['gender'],
						"location" => $value['timeline']['location'],
						"hometown" => $value['timeline']['hometown'],
						"time" => $value['time'],
						"color" => $value['timeline']['color'],
						"about" => $value['timeline']['about']
					),
					"friend" => array(
						"id" => $value['receiver']['id'],
						"username" => $value['receiver']['username'],
						"name" => $value['receiver']['name'],
						"image" => $value['receiver']['avatar_url'],
						"cover" => $value['receiver']['cover_url'],
						"birthday" => $value['receiver']['birthday'],
						"gender" => $value['receiver']['gender'],
						"location" => $value['receiver']['location'],
						"hometown" => $value['receiver']['hometown'],
						"time" => $value['time'],
						"color" => $value['receiver']['color'],
						"about" => $value['receiver']['about']
					)
				);
			}
		}
	}

	$data = array(
		"status" => 200,
		"messages" => $msgs
	);
}