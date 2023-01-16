<?php
function getVideoCallInfo($cid=0, $t="popup")
{
	if (!isLogged()) return false;
	global $conn, $config, $user, $themeData, $lang;

	$cid = (int) $cid;
	$selectSql = "SELECT * FROM " . DB_VIDEO_CALLS . " WHERE id=$cid";
	$selectQuery = $conn->query($selectSql);
	if ($selectQuery->num_rows == 1)
	{
		$selectFetch = $selectQuery->fetch_array(MYSQLI_ASSOC);

		$themeData['room'] = $selectFetch['room'];

		if ($selectFetch['caller_id'] === $user['id'])
		{
			$themeData['access_token'] = $selectFetch['caller_access_token'];
			$themeData['call_id'] = $selectFetch['receiver_call_id'];
			$themeData['is_invited'] = 0;

			$receiverObj = new \SocialKit\User();
			$receiverObj->setId($selectFetch['receiver_id']);
		}
		elseif ($selectFetch['receiver_id'] === $user['id'])
		{
			$themeData['access_token'] = $selectFetch['receiver_access_token'];
			$themeData['call_id'] = $selectFetch['caller_call_id'];
			$themeData['is_invited'] = 1;

			$receiverObj = new \SocialKit\User();
			$receiverObj->setId($selectFetch['caller_id']);
		}

		$receiver = $receiverObj->getRows();
		$themeData['receiver_url'] = $receiver['url'];
		$themeData['receiver_thumbnail_url'] = $receiver['thumbnail_url'];
		$themeData['receiver_name'] = $receiver['name'];

		if ($receiverObj->isFollowedBy())
		{
			$themeData['text_following'] = ($config['friends'] == 1) ? $lang['friends_label'] : $lang['following_label'];
			$themeData['friendly_status'] = \SocialKit\UI::view('video-call/friendly-status-1');
		}
		else
		{
			$themeData['friendly_status'] = \SocialKit\UI::view('video-call/friendly-status-0');
		}

		if ($t === "page") return \SocialKit\UI::view('video-call/content');
		return \SocialKit\UI::view('popups/video-call');
	}

	return false;
}