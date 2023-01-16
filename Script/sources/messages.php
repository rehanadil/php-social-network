<?php
if (!isLogged())
{
    if ($ajax)
        $ajaxUrl = smoothLink('index.php?a=logout');
    else
        header('Location: ' . smoothLink('index.php?a=logout'));
}

if ($user['subscription_plan']['send_messages'] == 0)
{
    if ($ajax)
        $ajaxUrl = subscriptionUrl();
    else
        header('Location: ' . subscriptionUrl());
}

$themeData['page_title'] = $lang['messages_label'];

/* */
if (! empty($_GET['recipient_id']))
{
	if (! is_numeric($_GET['recipient_id']))
	{
		$recipientId = (int) getUserId($conn, $_GET['recipient_id']);
	}
	else
	{
		$recipientId = (int) $_GET['recipient_id'];
	}

    $recipientObj = new \SocialKit\User();
    $recipientObj->setId($recipientId);
    $recipient = $recipientObj->getRows();
}

if (! isset($recipient['id'])) $recipient['id'] = 0;

foreach ($recipient as $key => $value)
{
	if (is_array($value))
	{
		foreach ($value as $key2 => $value2)
		{
			if (! is_array($value2))
			{
				$themeData['recipient_' . $key . '_' . $key2] = $value2;
			}
		}
	}
	else
	{
		$themeData['recipient_' . $key] = $value;
	}
}
/* */
$themeData['chat_friends'] = json_encode(array());
$themeData['no_text'] = \SocialKit\UI::view('messages/no-text');
$themeData['page_content'] = \SocialKit\UI::view('messages/content');
